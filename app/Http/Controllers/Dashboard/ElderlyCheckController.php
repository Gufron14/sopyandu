<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Api\LocationController;
use App\Http\Controllers\Controller;
use App\Models\Elderly;
use App\Models\ElderlyCheck;
use App\Models\Medicine;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ElderlyCheckController extends Controller
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

    // Fungsi untuk menghapus cache yang berkaitan dengan elderly check
    protected function clearElderlyCheckCache()
    {
        $currentYear = now()->year;

        // Ambil semua tahun yang tersedia dari database (DESC untuk cari tahun terbaru)
        $availableYears = ElderlyCheck::selectRaw('YEAR(check_date) as year')
            ->distinct()
            ->orderByDesc('year')
            ->pluck('year');

        // Tentukan tahun yang dipilih
        $selectedYear = $availableYears->contains($currentYear)
            ? $currentYear
            : $availableYears->first(); // Ambil tahun terbaru jika tahun saat ini tidak ada

        // Hapus cache berdasarkan tahun yang dipilih
        Cache::forget("elderly_check_{$selectedYear}");
    }

    public function index()
    {
        $cacheKey = 'elderly_check';
        $availableYears = collect();
        $selectedYear = null;

        // Ambil semua tahun unik dari data pemeriksaan lansia (DESC agar terbaru di atas)
        $availableYears = ElderlyCheck::selectRaw('YEAR(check_date) as year')
            ->distinct()
            ->orderByDesc('year')
            ->pluck('year');

        $currentYear = now()->year;
        $requestedYear = request('year');

        // Tentukan tahun yang akan digunakan
        $selectedYear = $requestedYear ?? ($availableYears->contains($currentYear) ? $currentYear : $availableYears->first());

        // Ambil data berdasarkan tahun terpilih dengan cache
        $elderly_checks = Cache::remember("{$cacheKey}_{$selectedYear}", 300, function () use ($selectedYear) {
            return ElderlyCheck::with(['elderlies', 'officers'])
                ->whereYear('check_date', $selectedYear)
                ->orderBy('check_date', 'desc')
                ->get();
        });

        return view('dashboard.service.elderly-check.index', compact('elderly_checks', 'availableYears', 'selectedYear'));
    }


    public function show($id)
    {
        $this->checkOfficerPosition();

        $elderlyCheck = ElderlyCheck::with(['elderlies', 'officers', 'medicines'])->findOrFail($id);

        $province = $city = $subdistrict = $village = 'N/A';

        if ($elderlyCheck->elderlies->province) {
            $provinces = LocationController::getProvincesStatic();
            $province = collect($provinces)->firstWhere('id', $elderlyCheck->elderlies->province)['name'] ?? 'N/A';
        }

        if ($elderlyCheck->elderlies->city) {
            $cities = LocationController::getCitiesStatic($elderlyCheck->elderlies->province);
            $city = collect($cities)->firstWhere('id', $elderlyCheck->elderlies->city)['name'] ?? 'N/A';
        }

        if ($elderlyCheck->elderlies->subdistrict) {
            $districts = LocationController::getDistrictsStatic($elderlyCheck->elderlies->city);
            $subdistrict = collect($districts)->firstWhere('id', $elderlyCheck->elderlies->subdistrict)['name'] ?? 'N/A';
        }

        if ($elderlyCheck->elderlies->village) {
            $villages = LocationController::getVillagesStatic($elderlyCheck->elderlies->subdistrict);
            $village = collect($villages)->firstWhere('id', $elderlyCheck->elderlies->village)['name'] ?? 'N/A';
        }

        return view('dashboard.service.elderly-check.show', compact('elderlyCheck', 'province', 'city', 'subdistrict', 'village'));
    }

    public function create()
    {
        $this->checkOfficerPosition();

        $elderlies = Elderly::orderBy('fullname', 'asc')->get();

        return view('dashboard.service.elderly-check.create', compact('elderlies'));
    }

    public function store(Request $request)
    {
        $this->checkOfficerPosition();

        $rules = [
            'elderly_id'         => 'required|exists:elderlies,id',
            'check_date'         => 'required|date',
            'age_in_checks'      => 'required|string',
            'body_weight'        => 'required|numeric',
            'blood_pressure'     => 'required|string|regex:/^\d{2,3}\/\d{2,3}$/',
            'pulse_rate'         => 'required|integer',
            'blood_sugar'        => 'nullable|numeric',
            'cholesterol'        => 'nullable|numeric',
            'uric_acid'          => 'nullable|numeric',
            'mobility_status'    => 'required|in:Mandiri,Bantuan Alat,Dibantu Orang Lain',
            'cognitive_status'   => 'required|in:Normal,Penurunan Ringan,Demensia',
            'nutritional_status' => 'required|in:Baik,Kurang,Lebih',
            'notes'              => 'nullable|string|max:255',
            'officer_id'         => 'required',
        ];

        $messages = [
            'elderly_id.required'         => 'Nama lansia wajib dipilih.',
            'elderly_id.exists'           => 'Lansia yang dipilih tidak ditemukan.',
            'check_date.required'         => 'Tanggal pemeriksaan wajib diisi.',
            'check_date.date'             => 'Tanggal pemeriksaan harus berupa tanggal yang valid.',
            'age_in_checks.required'      => 'Usia saat pemeriksaan wajib diisi.',
            'age_in_checks.string'        => 'Usia saat pemeriksaan harus berupa teks, contoh: "65 tahun, 3 bulan, 12 hari".',
            'body_weight.required'        => 'Berat badan lansia wajib diisi.',
            'body_weight.numeric'         => 'Berat badan harus berupa angka valid (kg).',
            'blood_pressure.required'     => 'Tekanan darah wajib diisi.',
            'blood_pressure.string'       => 'Tekanan darah harus berupa teks.',
            'blood_pressure.regex'        => 'Format tekanan darah tidak valid. Gunakan format seperti 120/80.',
            'pulse_rate.required'         => 'Denyut nadi wajib diisi.',
            'pulse_rate.integer'          => 'Denyut nadi harus berupa angka (bpm).',
            'blood_sugar.numeric'         => 'Gula darah harus berupa angka valid (mg/dL).',
            'cholesterol.numeric'         => 'Kolesterol harus berupa angka valid (mg/dL).',
            'uric_acid.numeric'           => 'Asam urat harus berupa angka valid (mg/dL).',
            'mobility_status.required'    => 'Status mobilitas wajib dipilih',
            'mobility_status.in'          => 'Status mobilitas tidak valid. Pilih antara: Mandiri, Bantuan Alat, atau Dibantu Orang Lain.',
            'cognitive_status.required'   => 'Status kognitif wajib dipilih.',
            'cognitive_status.in'         => 'Status kognitif tidak valid. Pilih antara: Normal, Penurunan Ringan, atau Demensia.',
            'nutritional_status.required' => 'Status gizi wajib dipilih.',
            'nutritional_status.in'       => 'Status gizi tidak valid. Pilih antara: Baik, Kurang, atau Lebih.',
            'notes.max'                   => 'Keterangan maksimal 255 karakter.',
            'officer_id.required'         => 'Petugas wajib diisi.',
        ];

        $data = $request->validate($rules, $messages);

        $data['check_date'] = Carbon::parse($data['check_date'])->format('Y-m-d');

        try {
            ElderlyCheck::create([
                'elderly_id'         => $data['elderly_id'],
                'check_date'         => $data['check_date'],
                'age_in_checks'      => $data['age_in_checks'],
                'body_weight'        => $data['body_weight'],
                'blood_pressure'     => $data['blood_pressure'],
                'pulse_rate'         => $data['pulse_rate'],
                'blood_sugar'        => $data['blood_sugar'],
                'cholesterol'        => $data['cholesterol'],
                'uric_acid'          => $data['uric_acid'],
                'mobility_status'    => $data['mobility_status'],
                'cognitive_status'   => $data['cognitive_status'],
                'nutritional_status' => $data['nutritional_status'] ?? null,
                'notes'              => $data['notes'],
                'officer_id'         => $data['officer_id'],
            ]);

            // Hapus chace
            $this->clearElderlyCheckCache();

            return redirect(url('/elderly-check-data'))->with('success', "Data berhasil ditambahkan.");
        } catch (\Exception $e) {
            Log::error('Error: ' . $e->getMessage()); // Check 'storage/logs/laravel.log'

            return back()->with('error', 'Data gagal ditambahkan. Silakan coba kembali.');
        }
    }

    public function edit($id)
    {
        $this->checkOfficerPosition();

        $elderlyCheck = ElderlyCheck::findOrFail($id);

        $elderlies = Elderly::orderBy('fullname', 'asc')->get();

        return view('dashboard.service.elderly-check.edit', compact('elderlyCheck', 'elderlies'));
    }

    public function update(Request $request, $id)
    {
        $this->checkOfficerPosition();

        $elderly = ElderlyCheck::findOrFail($id);

        $rules = [
            'elderly_id'         => 'required|exists:elderlies,id',
            'check_date'         => 'required|date',
            'age_in_checks'      => 'required|string',
            'body_weight'        => 'required|numeric',
            'blood_pressure'     => 'required|string|regex:/^\d{2,3}\/\d{2,3}$/',
            'pulse_rate'         => 'required|integer',
            'blood_sugar'        => 'nullable|numeric',
            'cholesterol'        => 'nullable|numeric',
            'uric_acid'          => 'nullable|numeric',
            'mobility_status'    => 'required|in:Mandiri,Bantuan Alat,Dibantu Orang Lain',
            'cognitive_status'   => 'required|in:Normal,Penurunan Ringan,Demensia',
            'nutritional_status' => 'required|in:Baik,Kurang,Lebih',
            'notes'              => 'nullable|string|max:255',
            'officer_id'         => 'required',
        ];

        $messages = [
            'elderly_id.required'         => 'Nama lansia wajib dipilih.',
            'elderly_id.exists'           => 'Lansia yang dipilih tidak ditemukan.',
            'check_date.required'         => 'Tanggal pemeriksaan wajib diisi.',
            'check_date.date'             => 'Tanggal pemeriksaan harus berupa tanggal yang valid.',
            'age_in_checks.required'      => 'Usia saat pemeriksaan wajib diisi.',
            'age_in_checks.string'        => 'Usia saat pemeriksaan harus berupa teks, contoh: "65 tahun, 3 bulan, 12 hari".',
            'body_weight.required'        => 'Berat badan lansia wajib diisi.',
            'body_weight.numeric'         => 'Berat badan harus berupa angka valid (kg).',
            'blood_pressure.required'     => 'Tekanan darah wajib diisi.',
            'blood_pressure.string'       => 'Tekanan darah harus berupa teks.',
            'blood_pressure.regex'        => 'Format tekanan darah tidak valid. Gunakan format seperti 120/80.',
            'pulse_rate.required'         => 'Denyut nadi wajib diisi.',
            'pulse_rate.integer'          => 'Denyut nadi harus berupa angka (bpm).',
            'blood_sugar.numeric'         => 'Gula darah harus berupa angka valid (mg/dL).',
            'cholesterol.numeric'         => 'Kolesterol harus berupa angka valid (mg/dL).',
            'uric_acid.numeric'           => 'Asam urat harus berupa angka valid (mg/dL).',
            'mobility_status.required'    => 'Status mobilitas wajib dipilih',
            'mobility_status.in'          => 'Status mobilitas tidak valid. Pilih antara: Mandiri, Bantuan Alat, atau Dibantu Orang Lain.',
            'cognitive_status.required'   => 'Status kognitif wajib dipilih.',
            'cognitive_status.in'         => 'Status kognitif tidak valid. Pilih antara: Normal, Penurunan Ringan, atau Demensia.',
            'nutritional_status.required' => 'Status gizi wajib dipilih.',
            'nutritional_status.in'       => 'Status gizi tidak valid. Pilih antara: Baik, Kurang, atau Lebih.',
            'notes.max'                   => 'Keterangan maksimal 255 karakter.',
            'officer_id.required'         => 'Petugas wajib diisi.',
        ];

        $data = $request->validate($rules, $messages);

        $data['check_date'] = Carbon::parse($data['check_date'])->format('Y-m-d');

        try {
            $elderly->update([
                'elderly_id'         => $data['elderly_id'],
                'check_date'         => $data['check_date'],
                'age_in_checks'      => $data['age_in_checks'],
                'body_weight'        => $data['body_weight'],
                'blood_pressure'     => $data['blood_pressure'],
                'pulse_rate'         => $data['pulse_rate'],
                'blood_sugar'        => $data['blood_sugar'],
                'cholesterol'        => $data['cholesterol'],
                'uric_acid'          => $data['uric_acid'],
                'mobility_status'    => $data['mobility_status'],
                'cognitive_status'   => $data['cognitive_status'],
                'nutritional_status' => $data['nutritional_status'],
                'notes'              => $data['notes'],
                'officer_id'         => $data['officer_id'],
            ]);

            // Hapus chace
            $this->clearElderlyCheckCache();

            return redirect(url('/elderly-check-data'))->with('success', "Data berhasil diperbarui.");
        } catch (\Exception $e) {
            Log::error('Error: ' . $e->getMessage()); // Check 'storage/logs/laravel.log'

            return back()->with('error', 'Data gagal diperbarui. Silakan coba kembali.');
        }
    }

    public function destroy($id)
    {
        $this->checkOfficerPosition();

        $elderly = ElderlyCheck::findOrFail($id);

        $elderly->delete();

        // Hapus chace
        $this->clearElderlyCheckCache();

        return redirect(url('/elderly-check-data'))->with('success', 'Data berhasil dihapus.');
    }

    public function printReport(Request $request)
    {
        // Validasi input
        $rules = [
            'early_period' => 'required|date',
            'final_period' => 'required|date|after_or_equal:early_period',
        ];

        $messages = [
            'early_period.required' => 'Periode awal wajib diisi.',
            'final_period.required' => 'Periode akhir wajib diisi.',
            'final_period.after_or_equal' => 'Periode akhir harus sama atau setelah periode awal.',
        ];

        $data = $request->validate($rules, $messages);

        // Ambil data dari request
        $early_period = $data['early_period'];
        $final_period = $data['final_period'];

        // Query data
        $query = ElderlyCheck::with(['elderlies', 'officers'])
            ->whereBetween('check_date', [$early_period, $final_period]);

        $elderly_checks = $query->orderBy('check_date', 'asc')->get();

        // Hapus cache (jika ada)
        $this->clearElderlyCheckCache();

        // Return view
        return view('dashboard.service.elderly-check.report', compact('elderly_checks', 'early_period', 'final_period'));
    }

    public function manageMedicine($id)
    {
        $this->checkOfficerPosition();

        $elderlyCheck = ElderlyCheck::with(['elderlies', 'officers', 'medicines'])->findOrFail($id);

        $medicines = Medicine::where('expiry_date', '>=', Carbon::now()->addMonths(3))
            ->orderBy('expiry_date', 'asc')
            ->get();

        $province = $city = $subdistrict = $village = 'N/A';

        if ($elderlyCheck->elderlies->province) {
            $provinces = LocationController::getProvincesStatic();
            $province = collect($provinces)->firstWhere('id', $elderlyCheck->elderlies->province)['name'] ?? 'N/A';
        }

        if ($elderlyCheck->elderlies->city) {
            $cities = LocationController::getCitiesStatic($elderlyCheck->elderlies->province);
            $city = collect($cities)->firstWhere('id', $elderlyCheck->elderlies->city)['name'] ?? 'N/A';
        }

        if ($elderlyCheck->elderlies->subdistrict) {
            $districts = LocationController::getDistrictsStatic($elderlyCheck->elderlies->city);
            $subdistrict = collect($districts)->firstWhere('id', $elderlyCheck->elderlies->subdistrict)['name'] ?? 'N/A';
        }

        if ($elderlyCheck->elderlies->village) {
            $villages = LocationController::getVillagesStatic($elderlyCheck->elderlies->subdistrict);
            $village = collect($villages)->firstWhere('id', $elderlyCheck->elderlies->village)['name'] ?? 'N/A';
        }

        return view('dashboard.service.elderly-check.manage-medicine', compact('elderlyCheck', 'medicines', 'province', 'city', 'subdistrict', 'village'));
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
            // Ambil semua medicine_id yang sudah ada untuk elderly_check_id ini
            $existingMedicineIds = DB::table('medicine_usages')
                ->where('elderly_check_id', $id)
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
                        'immunization_id'     => null,
                        'pregnancy_check_id'  => null,
                        'elderly_check_id'    => $id,
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

            return redirect("/elderly-check-data/{$id}/medicine/manage#pivot-table")
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
            $elderly = ElderlyCheck::with('medicines')->findOrFail($id);

            // Ambil semua medicine yang berelasi (via pivot) untuk imunisasi ini
            foreach ($elderly->medicines as $medicine) {
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

                // Update data pivot
                DB::table('medicine_usages')
                    ->where('elderly_check_id', $id)
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

            return redirect("/elderly-check-data/{$id}/medicine/manage#pivot-table")->with('success', 'Obat berhasil disimpan.');
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

        $elderly = ElderlyCheck::with(['medicines'])->findOrFail($id);

        DB::beginTransaction();

        try {
            foreach ($medicineIds as $medicineId) {
                $pivot = $elderly->medicines->firstWhere('id', $medicineId)?->pivot;

                // Cek apakah ada quantity di pivot
                if ($pivot && !is_null($pivot->quantity)) {
                    // Kembalikan stok obat
                    DB::table('medicines')
                        ->where('id', $medicineId)
                        ->increment('stock', $pivot->quantity);
                }

                // Hapus relasi di pivot
                DB::table('medicine_usages')
                    ->where('elderly_check_id', $id)
                    ->where('medicine_id', $medicineId)
                    ->delete();
            }

            DB::commit();

            // Hapus old khusus untuk form ini
            if (session()->has('_old_input')) {
                session()->forget('_old_input');
            }

            return redirect("/elderly-check-data/{$id}/medicine/manage#pivot-table")->with('success', 'Obat berhasil dihapus.');
        } catch (\Exception $e) {
            DB::rollBack();

            Log::error('Error: ' . $e->getMessage()); // Check 'storage/logs/laravel.log'

            return back()->with('error', 'Obat gagal dihapus. Silakan coba kembali.');
        }
    }

    public function getElderlyCheckStatistics($year)
    {
        $monthlyStats = ElderlyCheck::selectRaw('MONTH(check_date) as month, COUNT(DISTINCT elderly_id) as total')
            ->whereYear('check_date', $year)
            ->groupByRaw('MONTH(check_date)')
            ->orderByRaw('MONTH(check_date)')
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
