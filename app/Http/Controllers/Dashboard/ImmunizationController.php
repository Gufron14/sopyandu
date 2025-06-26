<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Api\LocationController;
use App\Http\Controllers\Controller;
use App\Models\FamilyChildren;
use App\Models\FamilyParent;
use App\Models\Immunization;
use App\Models\Medicine;
use App\Models\Vaccine;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ImmunizationController extends Controller
{
    private function checkOfficerPosition()
    {
        $user = Auth::user();

        $officer = $user->officers;

        // Periksa apakah posisi pejabat adalah 'Lurah' atau 'Kepala Lingkungan'
        if ($officer && ($officer->position === 'Lurah' || $officer->position === 'Kepala Lingkungan')) {
            abort(403, 'Unauthorized');
        }
    }

    // Fungsi untuk menghapus cache yang berkaitan dengan Immunization
    protected function clearImmunizationCache()
    {
        $currentYear = now()->year;

        // Ambil semua tahun yang tersedia dari database (DESC untuk cari tahun terbaru)
        $availableYears = Immunization::selectRaw('YEAR(immunization_date) as year')
            ->distinct()
            ->orderByDesc('year')
            ->pluck('year');

        // Tentukan tahun yang dipilih
        $selectedYear = $availableYears->contains($currentYear)
            ? $currentYear
            : $availableYears->first(); // Ambil tahun terbaru jika tahun saat ini tidak ada

        // Hapus cache berdasarkan tahun yang dipilih
        Cache::forget("immunization_children_{$selectedYear}");
    }

    public function index()
    {
        $user = Auth::user();
        $isParent = $user && $user->role === 'family_parent';
        $cacheKey = 'immunization_children';

        $immunizations = [];
        $availableYears = collect();
        $selectedYear = null;

        if ($isParent) {
            // Ambil data parent berdasarkan parent_id dari user
            $parent = FamilyParent::find($user->parent_id);

            if ($parent) {
                // Ambil semua anak milik parent tersebut
                $children = $parent->familyChildren()->orderBy('fullname', 'asc')->get();

                // Ambil data imunisasi untuk anak-anak parent
                $immunizations = Immunization::with(['familyChildren', 'vaccines', 'officers'])
                    ->whereIn('children_id', $children->pluck('id'))
                    ->orderBy('immunization_date', 'desc')
                    ->get();
            }
        } else {
            // Ambil semua tahun unik dari data imunisasi (DESC agar terbaru di atas)
            $availableYears = Immunization::selectRaw('YEAR(immunization_date) as year')
                ->distinct()
                ->orderByDesc('year')
                ->pluck('year');

            $currentYear = now()->year;
            $requestedYear = request('year');

            // Tentukan tahun yang akan digunakan
            $selectedYear = $requestedYear ?? ($availableYears->contains($currentYear) ? $currentYear : $availableYears->first());

            // Ambil data sesuai tahun
            $immunizations = Cache::remember("{$cacheKey}_{$selectedYear}", 300, function () use ($selectedYear) {
                return Immunization::with(['familyChildren', 'vaccines', 'officers'])
                    ->whereYear('immunization_date', $selectedYear)
                    ->orderBy('immunization_date', 'desc')
                    ->get();
            });
        }

        return view('dashboard.service.immunization.index', compact('immunizations', 'availableYears', 'selectedYear'));
    }

    public function show($id)
    {
        $this->checkOfficerPosition();

        $immunization = Immunization::with(['familyChildren', 'vaccines', 'officers', 'medicines'])->findOrFail($id);

        $province = $city = $subdistrict = $village = 'N/A';

        if ($immunization->familyChildren->familyParents->province) {
            $provinces = LocationController::getProvincesStatic();
            $province = collect($provinces)->firstWhere('id', $immunization->familyChildren->familyParents->province)['name'] ?? 'N/A';
        }

        if ($immunization->familyChildren->familyParents->city) {
            $cities = LocationController::getCitiesStatic($immunization->familyChildren->familyParents->province);
            $city = collect($cities)->firstWhere('id', $immunization->familyChildren->familyParents->city)['name'] ?? 'N/A';
        }

        if ($immunization->familyChildren->familyParents->subdistrict) {
            $districts = LocationController::getDistrictsStatic($immunization->familyChildren->familyParents->city);
            $subdistrict = collect($districts)->firstWhere('id', $immunization->familyChildren->familyParents->subdistrict)['name'] ?? 'N/A';
        }

        if ($immunization->familyChildren->familyParents->village) {
            $villages = LocationController::getVillagesStatic($immunization->familyChildren->familyParents->subdistrict);
            $village = collect($villages)->firstWhere('id', $immunization->familyChildren->familyParents->village)['name'] ?? 'N/A';
        }

        return view('dashboard.service.immunization.show', compact('immunization', 'province', 'city', 'subdistrict', 'village'));
    }

    public function create()
    {
        $this->checkOfficerPosition();

        $children = FamilyChildren::with('familyParents')
            ->select('id', 'nik', 'fullname', 'gender', 'birth_place', 'date_of_birth', 'parent_id')
            ->orderBy('fullname', 'asc')
            ->get();

        // Ambil vaksin yang belum kedaluwarsa
        $today = Carbon::today();
        $vaccines = Vaccine::whereDate('expiry_date', '>=', $today)
            ->orderBy('vaccine_name', 'asc')
            ->get();

        return view('dashboard.service.immunization.create', compact('children', 'vaccines'));
    }

    public function store(Request $request)
    {
        $this->checkOfficerPosition();

        $rules = [
            'children_id' => 'required',
            'immunization_date' => 'required|date',
            'age_in_checks' => 'required',
            'vaccine_status' => 'required',
            'notes' => 'nullable|string|max:255',
            'officer_id' => 'required',
        ];

        $messages = [
            'children_id.required' => 'Nama anak wajib dipilih.',
            'immunization_date.required' => 'Tanggal imunisasi wajib diisi.',
            'immunization_date.date' => 'Tanggal imunisasi harus berupa tanggal yang valid.',
            'age_in_checks.required' => 'Usia saat imunisasi wajib diisi.',
            'vaccine_status.required' => 'Status vaksinasi wajib dipilih.',
            'vaccine_category.required' => 'Kategori vaksinasi wajib dipilih.',
            'vaccine_id.required' => 'Nama vaksin wajib dipilih.',
            'side_effects.max' => 'Efek samping maksimal 255 karakter.',
            'notes.max' => 'Keterangan maksimal 255 karakter.',
            'officer_id.required' => 'Petugas wajib diisi.',
        ];

        if ($request->vaccine_status === 'Ya') {
            $rules['vaccine_id'] = 'required';
            $rules['vaccine_category'] = 'required';
            $rules['side_effects'] = 'nullable|string|max:255';
        }

        $data = $request->validate($rules, $messages);

        // Cek duplikasi
        $existing = Immunization::where('children_id', $data['children_id'])
            ->where('immunization_date', Carbon::parse($data['immunization_date'])->format('Y-m-d'))
            ->first();

        if ($existing) {
            $indonesianDate = Carbon::parse($data['immunization_date'])->locale('id')->isoFormat('D MMMM YYYY');
            return back()->withErrors([
                'children_id' => "Data imunisasi untuk anak ini pada tanggal {$indonesianDate} sudah ada."
            ])->withInput();
        }

        // Format dan siapkan data
        $data['immunization_date'] = Carbon::parse($data['immunization_date'])->format('Y-m-d');
        $data['vaccine_id'] = $data['vaccine_status'] === 'Ya' ? $data['vaccine_id'] : null;
        $data['vaccine_category'] = $data['vaccine_status'] === 'Ya' ? $data['vaccine_category'] : '-';
        $data['side_effects'] = $data['vaccine_status'] === 'Ya' ? ($data['side_effects'] ?? null) : null;

        try {
            DB::beginTransaction();

            // Kurangi stok vaksin jika dipilih
            if ($data['vaccine_id']) {
                $vaccine = Vaccine::where('id', $data['vaccine_id'])->lockForUpdate()->first();

                if (!$vaccine || $vaccine->stock <= 0) {
                    DB::rollBack();
                    return back()->withErrors(['stock' => 'Stok vaksin tidak mencukupi.'])->withInput();
                }

                $vaccine->decrement('stock');
            }

            Immunization::create([
                'children_id' => $data['children_id'],
                'immunization_date' => $data['immunization_date'],
                'age_in_checks' => $data['age_in_checks'],
                'vaccine_id' => $data['vaccine_id'],
                'vaccine_category' => $data['vaccine_category'],
                'side_effects' => $data['side_effects'],
                'notes' => $data['notes'],
                'officer_id' => $data['officer_id'],
            ]);

            DB::commit();

            // Hapus chace
            $this->clearImmunizationCache();

            return redirect(url('/immunization-data'))->with('success', "Data berhasil ditambahkan.");
        } catch (\Exception $e) {
            DB::rollBack();

            Log::error('Error: ' . $e->getMessage()); // Check 'storage/logs/laravel.log'

            return back()->with('error', 'Data gagal ditambahkan. Silakan coba kembali.');
        }
    }

    public function edit($id)
    {
        $this->checkOfficerPosition();

        $immunization = Immunization::findOrFail($id);

        $children = FamilyChildren::with('familyParents')
            ->select('id', 'nik', 'fullname', 'gender', 'birth_place', 'date_of_birth', 'parent_id')
            ->orderBy('fullname', 'asc')
            ->get();

        // Ambil vaksin yang belum kedaluwarsa
        $today = Carbon::today();
        $vaccines = Vaccine::whereDate('expiry_date', '>=', $today)
            ->orderBy('vaccine_name', 'asc')
            ->get();

        return view('dashboard.service.immunization.edit', compact('immunization', 'children', 'vaccines'));
    }

    public function update(Request $request, $id)
    {
        $this->checkOfficerPosition();

        $immunization = Immunization::findOrFail($id);

        $rules = [
            'children_id' => 'required',
            'immunization_date' => 'required|date',
            'age_in_checks' => 'required',
            'vaccine_status' => 'required',
            'notes' => 'nullable|string|max:255',
            'officer_id' => 'required',
        ];

        $messages = [
            'children_id.required' => 'Nama anak wajib dipilih.',
            'immunization_date.required' => 'Tanggal imunisasi wajib diisi.',
            'immunization_date.date' => 'Tanggal imunisasi harus berupa tanggal yang valid.',
            'age_in_checks.required' => 'Usia saat imunisasi wajib diisi.',
            'vaccine_status.required' => 'Status vaksinasi wajib dipilih.',
            'vaccine_category.required' => 'Kategori vaksinasi wajib dipilih.',
            'vaccine_id.required' => 'Nama vaksin wajib dipilih.',
            'side_effects.max' => 'Efek samping maksimal 255 karakter.',
            'notes.max' => 'Keterangan maksimal 255 karakter.',
            'officer_id.required' => 'Petugas wajib diisi.',
        ];

        if ($request->vaccine_status === 'Ya') {
            $rules['vaccine_id'] = 'required';
            $rules['vaccine_category'] = 'required';
            $rules['side_effects'] = 'nullable|string|max:255';
        }

        $existingImmunization = Immunization::where('children_id', $request->children_id)
            ->where('immunization_date', $request->immunization_date)
            ->where('id', '!=', $id)
            ->first();

        if ($existingImmunization) {
            $indonesianDateFormat = Carbon::parse($request->immunization_date)->locale('id')->isoFormat('D MMMM YYYY');
            return back()->withErrors([
                'children_id' => "Data imunisasi untuk anak ini pada tanggal {$indonesianDateFormat} sudah ada."
            ])->withInput();
        }

        $data = $request->validate($rules, $messages);

        $data['immunization_date'] = Carbon::parse($data['immunization_date'])->format('Y-m-d');
        $data['vaccine_id'] = $data['vaccine_status'] === 'Ya' ? $data['vaccine_id'] : null;
        $data['vaccine_category'] = $data['vaccine_status'] === 'Ya' ? $data['vaccine_category'] : '-';
        $data['side_effects'] = $data['vaccine_status'] === 'Ya' ? ($data['side_effects'] ?? null) : null;

        try {
            DB::beginTransaction();

            $oldVaccineId = $immunization->vaccine_id;
            $newVaccineId = $data['vaccine_id'];

            // Kembalikan stok vaksin lama jika vaksin diubah atau dihapus
            if (!is_null($oldVaccineId) && $oldVaccineId !== $newVaccineId) {
                Vaccine::where('id', $oldVaccineId)->lockForUpdate()->increment('stock');
            }

            // Jika memilih vaksin baru
            if (!is_null($newVaccineId)) {
                $newVaccine = Vaccine::where('id', $newVaccineId)->lockForUpdate()->first();

                if (!$newVaccine) {
                    DB::rollBack();
                    return back()->withErrors(['vaccine_id' => 'Vaksin tidak ditemukan.']);
                }

                if ($newVaccine->stock <= 0 && $oldVaccineId !== $newVaccineId) {
                    DB::rollBack();
                    return back()->withErrors(['stock' => 'Stok vaksin tidak mencukupi.']);
                }

                if ($oldVaccineId !== $newVaccineId) {
                    $newVaccine->decrement('stock');
                }
            }

            $immunization->update([
                'children_id' => $data['children_id'],
                'immunization_date' => $data['immunization_date'],
                'age_in_checks' => $data['age_in_checks'],
                'vaccine_id' => $data['vaccine_id'],
                'vaccine_category' => $data['vaccine_category'],
                'side_effects' => $data['side_effects'],
                'notes' => $data['notes'] ?? null,
                'officer_id' => $data['officer_id'],
            ]);

            DB::commit();

            // Hapus chace
            $this->clearImmunizationCache();

            return redirect(url('/immunization-data'))->with('success', 'Data berhasil diperbarui.');
        } catch (\Exception $e) {
            DB::rollBack();

            Log::error('Error: ' . $e->getMessage()); // Check 'storage/logs/laravel.log'

            return back()->with('error', 'Data gagal diperbarui. Silakan coba kembali.');
        }
    }

    public function destroy($id)
    {
        $this->checkOfficerPosition();

        // Temukan data imunisasi yang akan dihapus
        $immunization = Immunization::findOrFail($id);

        // Periksa jika vaksin terkait ada
        if (!is_null($immunization->vaccine_id)) {
            // Cari vaksin berdasarkan vaccine_id dengan menggunakan lockForUpdate
            $vaccine = Vaccine::where('id', $immunization->vaccine_id)->lockForUpdate()->first();

            // Jika vaksin ditemukan, kembalikan stoknya dengan increment
            if ($vaccine) {
                $vaccine->increment('stock', 1);  // Increment stok sebanyak 1
            }
        }

        // Hapus data imunisasi
        $immunization->delete();

        // Hapus cache
        $this->clearImmunizationCache();

        // Redirect dengan pesan sukses
        return redirect(url('/immunization-data'))->with('success', 'Data berhasil dihapus.');
    }

    public function printReport(Request $request)
    {
        // Validasi input
        $rules = [
            'early_period' => 'required|date',
            'final_period' => 'required|date|after_or_equal:early_period',
            'vaccine_status' => 'required|string|in:Ya,Tidak,Semua',
        ];

        $messages = [
            'early_period.required' => 'Periode awal wajib diisi.',
            'final_period.required' => 'Periode akhir wajib diisi.',
            'final_period.after_or_equal' => 'Periode akhir harus sama atau setelah periode awal.',
            'vaccine_status.required' => 'Status vaksin wajib dipilih.',
        ];

        $data = $request->validate($rules, $messages);

        // Ambil data dari request
        $early_period = $data['early_period'];
        $final_period = $data['final_period'];
        $vaccine_status = $data['vaccine_status'];

        // Query data imunisasi
        $query = Immunization::with(['familyChildren', 'officers'])
            ->whereBetween('immunization_date', [$early_period, $final_period]);

        if ($vaccine_status === 'Ya') {
            $query->whereNotNull('vaccine_id');
        } elseif ($vaccine_status === 'Tidak') {
            $query->whereNull('vaccine_id');
        }

        $immunizations = $query->orderBy('immunization_date', 'asc')->get();

        // Hapus cache (jika ada)
        $this->clearimmunizationCache();

        // Return view
        return view('dashboard.service.immunization.report', compact('immunizations', 'early_period', 'final_period', 'vaccine_status'));
    }

    public function manageMedicine($id)
    {
        $this->checkOfficerPosition();

        $immunization = Immunization::with(['familyChildren', 'vaccines', 'officers', 'medicines'])->findOrFail($id);

        $medicines = Medicine::where('expiry_date', '>=', Carbon::now()->addMonths(3))
            ->orderBy('expiry_date', 'asc')
            ->get();

        $province = $city = $subdistrict = $village = 'N/A';

        if ($immunization->familyChildren->familyParents->province) {
            $provinces = LocationController::getProvincesStatic();
            $province = collect($provinces)->firstWhere('id', $immunization->familyChildren->familyParents->province)['name'] ?? 'N/A';
        }

        if ($immunization->familyChildren->familyParents->city) {
            $cities = LocationController::getCitiesStatic($immunization->familyChildren->familyParents->province);
            $city = collect($cities)->firstWhere('id', $immunization->familyChildren->familyParents->city)['name'] ?? 'N/A';
        }

        if ($immunization->familyChildren->familyParents->subdistrict) {
            $districts = LocationController::getDistrictsStatic($immunization->familyChildren->familyParents->city);
            $subdistrict = collect($districts)->firstWhere('id', $immunization->familyChildren->familyParents->subdistrict)['name'] ?? 'N/A';
        }

        if ($immunization->familyChildren->familyParents->village) {
            $villages = LocationController::getVillagesStatic($immunization->familyChildren->familyParents->subdistrict);
            $village = collect($villages)->firstWhere('id', $immunization->familyChildren->familyParents->village)['name'] ?? 'N/A';
        }

        return view('dashboard.service.immunization.manage-medicine', compact('immunization', 'medicines', 'province', 'city', 'subdistrict', 'village'));
    }

    public function storeMedicine(Request $request, $id)
    {
        $this->checkOfficerPosition();

        // Validasi input
        $rules = [
            'medicine_ids' => 'required|array',
            'medicine_ids.*' => 'exists:medicines,id',
        ];

        $messages = [
            'medicine_ids.required' => 'Pilih setidaknya satu obat.',
            'medicine_ids.*.exists' => 'Obat yang dipilih tidak valid.',
        ];

        // Validasi data
        $data = $request->validate($rules, $messages);
        $medicineIds = $data['medicine_ids'];

        try {
            // Ambil semua medicine_id yang sudah ada untuk immunization_id ini
            $existingMedicineIds = DB::table('medicine_usages')
                ->where('immunization_id', $id)
                ->pluck('medicine_id')
                ->toArray();

            // Array untuk nama obat duplikat dan yang berhasil ditambahkan
            $duplicateMedicines = [];
            $addedMedicines = [];

            foreach ($medicineIds as $medicineId) {
                // Cek apakah sudah ada
                $exists = in_array($medicineId, $existingMedicineIds);

                $medicineName = DB::table('medicines')
                    ->where('id', $medicineId)
                    ->value('medicine_name');

                if ($exists) {
                    $duplicateMedicines[] = $medicineName;
                } else {
                    // Simpan data
                    DB::table('medicine_usages')->insert([
                        'immunization_id'     => $id,
                        'pregnancy_check_id'  => null,
                        'elderly_check_id'    => null,
                        'medicine_id'         => $medicineId,
                        'quantity'            => null,
                        'dosage_instructions' => null,
                        'meal_time'           => '-',
                        'notes'               => null,
                    ]);

                    $addedMedicines[] = $medicineName;
                }
            }

            // Buat pesan feedback
            if (!empty($duplicateMedicines)) {
                $duplicateList = implode(', ', $duplicateMedicines);
                $addedList = !empty($addedMedicines) ? implode(', ', $addedMedicines) : null;

                $message = "<p><i class='fa-solid fa-circle-xmark text-danger mr-1'></i> {$duplicateList} sudah ada dalam daftar pemberian obat.</p>";
                if ($addedList) {
                    $message .= "<p><i class='fa-solid fa-circle-check text-success mr-1'></i> {$addedList} berhasil ditambahkan dalam daftar pemberian obat.</p>";
                }

                return back()->with('warning', $message);
            }

            // Hapus old khusus untuk form ini
            if (session()->has('_old_input')) {
                session()->forget('_old_input');
            }

            return redirect("/immunization-data/{$id}/medicine/manage#pivot-table")
                ->with('success', 'Obat berhasil ditambahkan.');
        } catch (\Exception $e) {
            Log::error('Error: ' . $e->getMessage());

            return back()->with('error', 'Obat gagal ditambahkan. Silakan coba kembali.');
        }
    }

    public function updateMedicine(Request $request, $id)
    {
        $this->checkOfficerPosition();

        $rules = [
            'quantities' => 'required|array',
            'quantities.*' => 'required|numeric|min:1',

            'dosage_instructions' => 'required|array',
            'dosage_instructions.*' => 'nullable|string|max:100',

            'meal_time' => 'required|array',
            'meal_time.*' => 'required|string',

            'notes' => 'nullable|array',
            'notes.*' => 'nullable|string|max:100',
        ];

        $messages = [
            'quantities.*.required' => 'Jumlah wajib diisi.',
            'quantities.*.numeric' => 'Jumlah harus berupa angka.',
            'quantities.*.min' => 'Jumlah minimal 1.',

            'dosage_instructions.*.string' => 'Aturan pakai harus berupa teks.',
            'dosage_instructions.*.max' => 'Aturan pakai maksimal 100 karakter.',

            'meal_time.*.required' => 'Waktu makan wajib dipilih.',
        ];

        $data = $request->validate($rules, $messages);

        DB::beginTransaction();

        try {
            $immunization = Immunization::with('medicines')->findOrFail($id);

            // Ambil semua medicine yang berelasi (via pivot) untuk imunisasi ini
            foreach ($immunization->medicines as $medicine) {
                $medicineId = $medicine->id;

                $quantity = $data['quantities'][$medicineId] ?? null;
                $dosage = $data['dosage_instructions'][$medicineId] ?? null;
                $mealTime = $data['meal_time'][$medicineId] ?? '-';
                $notes = $data['notes'][$medicineId] ?? null;

                // Ambil nilai lama dari pivot
                $pivot = $medicine->pivot;
                $oldQty = $pivot?->quantity ?? 0;
                $newQty = (int) $quantity;

                // Update stok di tabel medicines jika ada perubahan jumlah
                if ($newQty !== $oldQty) {
                    DB::table('medicines')
                        ->where('id', $medicineId)
                        ->update([
                            'stock' => DB::raw("stock + ($oldQty - $newQty)")
                        ]);
                }

                // Update data pivot di tabel immunization_medicines
                DB::table('medicine_usages')
                    ->where('immunization_id', $id)
                    ->where('medicine_id', $medicineId)
                    ->update([
                        'quantity' => $newQty,
                        'dosage_instructions' => $dosage,
                        'meal_time' => $mealTime,
                        'notes' => $notes,
                    ]);
            }

            DB::commit();

            // Hapus old khusus untuk form ini
            if (session()->has('_old_input')) {
                session()->forget('_old_input');
            }

            return redirect("/immunization-data/{$id}/medicine/manage#pivot-table")->with('success', 'Obat berhasil disimpan.');
        } catch (\Exception $e) {
            DB::rollBack();

            Log::error('Error: ' . $e->getMessage()); // Check 'storage/logs/laravel.log'

            return back()->with('error', 'Obat gagal disimpan. Silakan coba kembali.');
        }
    }

    public function destroyMedicine(Request $request, $id)
    {
        $this->checkOfficerPosition();

        $medicineIds = $request->input('medicine_ids', []);

        if (empty($medicineIds)) {
            return redirect()->back()->with('error', 'Tidak ada obat yang dipilih untuk dihapus.');
        }

        $immunization = Immunization::with(['medicines'])->findOrFail($id);

        DB::beginTransaction();

        try {
            foreach ($medicineIds as $medicineId) {
                $pivot = $immunization->medicines->firstWhere('id', $medicineId)?->pivot;

                // Cek apakah ada quantity di pivot
                if ($pivot && !is_null($pivot->quantity)) {
                    // Kembalikan stok obat
                    DB::table('medicines')
                        ->where('id', $medicineId)
                        ->increment('stock', $pivot->quantity);
                }

                // Hapus relasi di pivot
                DB::table('medicine_usages')
                    ->where('immunization_id', $id)
                    ->where('medicine_id', $medicineId)
                    ->delete();
            }

            DB::commit();

            // Hapus old khusus untuk form ini
            if (session()->has('_old_input')) {
                session()->forget('_old_input');
            }

            return redirect("/immunization-data/{$id}/medicine/manage#pivot-table")->with('success', 'Obat berhasil dihapus.');
        } catch (\Exception $e) {
            DB::rollBack();

            Log::error('Error: ' . $e->getMessage()); // Check 'storage/logs/laravel.log'

            return back()->with('error', 'Obat gagal dihapus. Silakan coba kembali.');
        }
    }

    public function getImmunizationStatistics($year)
    {
        $monthlyStats = Immunization::selectRaw('MONTH(immunization_date) as month, COUNT(DISTINCT children_id) as total')
            ->whereYear('immunization_date', $year)
            ->groupByRaw('MONTH(immunization_date)')
            ->orderByRaw('MONTH(immunization_date)')
            ->pluck('total', 'month');

        $data = [];
        for ($i = 1; $i <= 12; $i++) {
            $data[] = $monthlyStats[$i] ?? 0;
        }

        return response()->json([
            'data' => $data,
            'total' => array_sum($data),
        ]);
    }
}
