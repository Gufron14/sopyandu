<?php

namespace App\Http\Controllers\Dashboard;

use Carbon\Carbon;
use App\Models\Vaccine;
use App\Models\Medicine;
use App\Models\FamilyParent;
use Illuminate\Http\Request;
use App\Models\PregnancyCheck;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use App\Http\Controllers\Api\LocationController;

class PregnancyCheckController extends Controller
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

    // Fungsi untuk menghapus cache yang berkaitan dengan prregnancy check
    protected function clearPregnancyCheckCache()
    {
        $currentYear = now()->year;

        // Ambil semua tahun yang tersedia dari database (DESC untuk cari tahun terbaru)
        $availableYears = PregnancyCheck::selectRaw('YEAR(check_date) as year')->distinct()->orderByDesc('year')->pluck('year');

        // Tentukan tahun yang dipilih
        $selectedYear = $availableYears->contains($currentYear) ? $currentYear : $availableYears->first(); // Ambil tahun terbaru jika tahun saat ini tidak ada

        // Hapus cache berdasarkan tahun yang dipilih
        Cache::forget("pregnancy_check_{$selectedYear}");
    }

    public function index()
    {
        $user = Auth::user();
        $isParent = $user && $user->role === 'family_parent';
        $cacheKey = 'pregnancy_check';

        $pregnancy_checks = [];
        $availableYears = collect();
        $selectedYear = null;

        if ($isParent) {
            // Ambil data parent berdasarkan parent_id dari user
            $parent = FamilyParent::find($user->parent_id);

            if ($parent) {
                // Ambil data pemeriksaan hanya untuk parent yang login
                $pregnancy_checks = PregnancyCheck::with(['familyParents', 'officers'])
                    ->where('parent_id', $parent->id)
                    ->orderBy('check_date', 'desc')
                    ->get();
            }
        } else {
            // Ambil semua tahun unik dari data pemeriksaan (DESC agar terbaru di atas)
            $availableYears = PregnancyCheck::selectRaw('YEAR(check_date) as year')->distinct()->orderByDesc('year')->pluck('year');

            $currentYear = now()->year;
            $requestedYear = request('year');

            // Tentukan tahun yang akan digunakan
            $selectedYear = $requestedYear ?? ($availableYears->contains($currentYear) ? $currentYear : $availableYears->first());

            // Ambil data sesuai tahun
            $pregnancy_checks = Cache::remember("{$cacheKey}_{$selectedYear}", 300, function () use ($selectedYear) {
                return PregnancyCheck::with(['familyParents', 'officers'])
                    ->whereYear('check_date', $selectedYear)
                    ->orderBy('check_date', 'desc')
                    ->get();
            });
        }

        return view('dashboard.service.pregnancy-check.index', compact('pregnancy_checks', 'availableYears', 'selectedYear'));
    }

    public function show($id)
    {
        $this->checkOfficerPosition();

        $pregnancy = PregnancyCheck::with(['familyParents', 'officers', 'medicines'])->findOrFail($id);

        $province = $city = $subdistrict = $village = 'N/A';

        if ($pregnancy->familyParents->province) {
            $provinces = LocationController::getProvincesStatic();
            $province = collect($provinces)->firstWhere('id', $pregnancy->familyParents->province)['name'] ?? 'N/A';
        }

        if ($pregnancy->familyParents->city) {
            $cities = LocationController::getCitiesStatic($pregnancy->familyParents->province);
            $city = collect($cities)->firstWhere('id', $pregnancy->familyParents->city)['name'] ?? 'N/A';
        }

        if ($pregnancy->familyParents->subdistrict) {
            $districts = LocationController::getDistrictsStatic($pregnancy->familyParents->city);
            $subdistrict = collect($districts)->firstWhere('id', $pregnancy->familyParents->subdistrict)['name'] ?? 'N/A';
        }

        if ($pregnancy->familyParents->village) {
            $villages = LocationController::getVillagesStatic($pregnancy->familyParents->subdistrict);
            $village = collect($villages)->firstWhere('id', $pregnancy->familyParents->village)['name'] ?? 'N/A';
        }

        return view('dashboard.service.pregnancy-check.show', compact('pregnancy', 'province', 'city', 'subdistrict', 'village'));
    }

    public function create()
    {
        $this->checkOfficerPosition();

        $parents = FamilyParent::orderBy('mother_fullname', 'asc')->get();

        return view('dashboard.service.pregnancy-check.create', compact('parents'));
    }

    public function store(Request $request)
    {
        $this->checkOfficerPosition();

        $rules = [
            'parent_id' => 'required',
            'check_date' => 'required|date',
            'age_in_checks' => 'required',
            'gestational_age' => 'required|integer',
            'mother_weight' => 'required|numeric',
            'mother_height' => 'required|numeric',
            'blood_pressure' => 'nullable|string|regex:/^\d{2,3}\/\d{2,3}$/',
            'pulse_rate' => 'nullable|integer', // Denyut nadi
            'blood_sugar' => 'nullable|numeric',
            'cholesterol' => 'nullable|numeric',
            'fundus_height' => 'nullable|numeric',
            'fetal_heart_rate' => 'nullable|integer',
            'fetal_presentation' => 'nullable|in:Kepala,Bokong,Lainnya', // Presentasi Janin
            'edema' => 'nullable|in:Tidak,Ringan,Sedang,Berat',
            'status_vaksin' => 'required|in:Sudah,Sekarang,Tidak',
            'jenis_vaksin' => 'nullable|in:Wajib,Tambahan,Khusus',
            'vaccine_id' => 'nullable|exists:vaccines,id',
            'notes' => 'nullable|string|max:255',
            'officer_id' => 'required',
        ];

        $messages = [
            'parent_id.required' => 'Nama ibu wajib dipilih.',
            'check_date.required' => 'Tanggal pemeriksaan wajib diisi.',
            'check_date.date' => 'Tanggal pemeriksaan harus berupa tanggal yang valid',
            'age_in_checks.required' => 'Usia saat pemeriksaan wajib diisi.',
            'gestational_age.required' => 'Usia kehamilan wajib diisi.',
            'gestational_age.integer' => 'Usia kehamilan harus berupa angka (minggu).',
            'mother_weight.required' => 'Berat badan wajib diisi.',
            'mother_weight.numeric' => 'Berat badan harus berupa angka valid (kg).',
            'mother_height.required' => 'Tinggi badan wajib diisi.',
            'mother_height.numeric' => 'Tinggi badan harus berupa angka valid (cm).',
            'blood_pressure.required' => 'Tekanan darah wajib diisi.',
            'blood_pressure.regex' => 'Format tekanan darah tidak valid. Gunakan format seperti 120/80.',
            'pulse_rate.required' => 'Denyut nadi wajib diisi.',
            'pulse_rate.integer' => 'Denyut nadi harus berupa angka (bpm).',
            'blood_sugar.numeric' => 'Gula darah harus berupa angka valid (mg/dL).',
            'cholesterol.numeric' => 'Kolesterol harus berupa angka valid (mg/dL).',
            'fundus_height.numeric' => 'Tinggi fundus harus berupa angka (cm).',
            'fetal_heart_rate.integer' => 'Detak jantung janin harus berupa angka (bpm).',
            'fetal_presentation.required' => 'Presentasi janin wajib dipilih.',
            'fetal_presentation.in' => 'Pilihan presentasi janin tidak valid. Pilih antara: Kepala, Bokong, atau Lainnya.',
            'edema.required' => 'Tingkat edema wajib dipilih.',
            'edema.in' => 'Pilihan edema tidak valid. Pilih antara: Tidak, Ringan, Sedang, atau Berat.',
            'status_vaksin.required' => 'Status vaksin wajib dipilih.',
            'status_vaksin.in' => 'Status vaksin tidak valid.',
            'jenis_vaksin.in' => 'Jenis vaksin tidak valid.',
            'vaccine_id.exists' => 'Vaksin yang dipilih tidak valid.',
            'notes.max' => 'Keterangan maksimal 255 karakter.',
            'officer_id.required' => 'Petugas wajib diisi.',
        ];

        // Validasi tambahan untuk vaksin
        if ($request->status_vaksin === 'Sekarang') {
            $rules['jenis_vaksin'] = 'required|in:Wajib,Tambahan,Khusus';
            $rules['vaccine_id'] = 'required|exists:vaccines,id';
            $messages['jenis_vaksin.required'] = 'Jenis vaksin wajib dipilih jika status vaksin "Sekarang".';
            $messages['vaccine_id.required'] = 'Nama vaksin wajib dipilih jika status vaksin "Sekarang".';
        }

        $data = $request->validate($rules, $messages);

        $data['check_date'] = Carbon::parse($data['check_date'])->format('Y-m-d');

        // Calculate BMI and BMI status
        $heightInMeters = $data['mother_height'] / 100;
        $bmi = $data['mother_weight'] / ($heightInMeters * $heightInMeters);
        $data['bmi'] = round($bmi, 2);

        // Determine BMI status
        if ($bmi < 18.5) {
            $data['bmi_status'] = 'Kurang Berat Badan';
        } elseif ($bmi >= 18.5 && $bmi < 25) {
            $data['bmi_status'] = 'Normal';
        } elseif ($bmi >= 25 && $bmi < 30) {
            $data['bmi_status'] = 'Kelebihan Berat Badan';
        } elseif ($bmi >= 30 && $bmi < 40) {
            $data['bmi_status'] = 'Obesitas';
        } elseif ($bmi >= 40 && $bmi < 50) {
            $data['bmi_status'] = 'Obesitas Parah';
        } else {
            $data['bmi_status'] = 'Obesitas Ekstrem';
        }

        // Set vaccine fields to null if status is not "Sekarang"
        if ($data['status_vaksin'] !== 'Sekarang') {
            $data['jenis_vaksin'] = null;
            $data['vaccine_id'] = null;
        }

        try {
            PregnancyCheck::create($data);

            // Kurangi stok vaksin jika status "Sekarang" dan ada vaksin_id
            if ($data['status_vaksin'] === 'Sekarang' && $data['vaccine_id']) {
                Vaccine::where('id', $data['vaccine_id'])->decrement('stock', 1);
            }

            $this->clearPregnancyCheckCache();

            return redirect(url('/pregnancy-check-data'))->with('success', 'Data berhasil ditambahkan.');
        } catch (\Exception $e) {
            Log::error('Error: ' . $e->getMessage());

            return back()->with('error', 'Data gagal ditambahkan. Silakan coba kembali.');
        }
    }

    // Add method to get vaccines by type
    public function getVaccinesByType($type)
    {
        $vaccines = Vaccine::where('vaccine_type', $type)->where('stock', '>', 0)->orderBy('vaccine_name', 'asc')->get();

        return response()->json($vaccines);
    }

    public function edit($id)
    {
        $this->checkOfficerPosition();

        $pregnancy = PregnancyCheck::findOrFail($id);
        $parents = FamilyParent::orderBy('mother_fullname', 'asc')->get();

        // Ambil data vaksin untuk kebutuhan edit
        $vaccines = Vaccine::orderBy('vaccine_name', 'asc')->get();

        return view('dashboard.service.pregnancy-check.edit', compact('pregnancy', 'parents', 'vaccines'));
    }

    public function update(Request $request, $id)
    {
        $this->checkOfficerPosition();

        $pregnancy = PregnancyCheck::findOrFail($id);

        $rules = [
            'parent_id' => 'required|exists:family_parents,id',
            'check_date' => 'required|date',
            'age_in_checks' => 'required',
            'gestational_age' => 'required|integer',
            'mother_weight' => 'required|numeric',
            'mother_height' => 'required|numeric',
            'blood_pressure' => 'nullable|string|regex:/^\d{2,3}\/\d{2,3}$/',
            'pulse_rate' => 'nullable|integer',
            'blood_sugar' => 'nullable|numeric',
            'cholesterol' => 'nullable|numeric',
            'fundus_height' => 'nullable|numeric',
            'fetal_heart_rate' => 'nullable|integer',
            'fetal_presentation' => 'nullable|in:Kepala,Bokong,Lainnya',
            'edema' => 'nullable|in:Tidak,Ringan,Sedang,Berat',
            'status_vaksin' => 'required|in:Sudah,Sekarang,Tidak',
            'jenis_vaksin' => 'nullable|in:Wajib,Tambahan,Khusus',
            'vaccine_id' => 'nullable|exists:vaccines,id',
            'notes' => 'nullable|string|max:255',
            'officer_id' => 'required',
        ];

        $messages = [
            'parent_id.required' => 'Nama ibu wajib dipilih.',
            'parent_id.exists' => 'Data ibu tidak valid.',
            'check_date.required' => 'Tanggal pemeriksaan wajib diisi.',
            'check_date.date' => 'Tanggal pemeriksaan harus berupa tanggal yang valid',
            'age_in_checks.required' => 'Usia saat pemeriksaan wajib diisi.',
            'gestational_age.required' => 'Usia kehamilan wajib diisi.',
            'gestational_age.integer' => 'Usia kehamilan harus berupa angka (minggu).',
            'mother_weight.required' => 'Berat badan wajib diisi.',
            'mother_weight.numeric' => 'Berat badan harus berupa angka valid (kg).',
            'mother_height.required' => 'Tinggi badan wajib diisi.',
            'mother_height.numeric' => 'Tinggi badan harus berupa angka valid (cm).',
            'blood_pressure.regex' => 'Format tekanan darah tidak valid. Gunakan format seperti 120/80.',
            'pulse_rate.integer' => 'Denyut nadi harus berupa angka (bpm).',
            'blood_sugar.numeric' => 'Gula darah harus berupa angka valid (mg/dL).',
            'cholesterol.numeric' => 'Kolesterol harus berupa angka valid (mg/dL).',
            'fundus_height.numeric' => 'Tinggi fundus harus berupa angka (cm).',
            'fetal_heart_rate.integer' => 'Detak jantung janin harus berupa angka (bpm).',
            'fetal_presentation.in' => 'Pilihan presentasi janin tidak valid.',
            'edema.in' => 'Pilihan edema tidak valid.',
            'status_vaksin.required' => 'Status vaksin wajib dipilih.',
            'status_vaksin.in' => 'Status vaksin tidak valid.',
            'jenis_vaksin.in' => 'Jenis vaksin tidak valid.',
            'vaccine_id.exists' => 'Vaksin yang dipilih tidak valid.',
            'notes.max' => 'Keterangan maksimal 255 karakter.',
            'officer_id.required' => 'Petugas wajib diisi.',
        ];

        // Validasi tambahan untuk vaksin
        if ($request->status_vaksin === 'Sekarang') {
            $rules['jenis_vaksin'] = 'required|in:Wajib,Tambahan,Khusus';
            $rules['vaccine_id'] = 'required|exists:vaccines,id';
            $messages['jenis_vaksin.required'] = 'Jenis vaksin wajib dipilih jika status vaksin "Sekarang".';
            $messages['vaccine_id.required'] = 'Nama vaksin wajib dipilih jika status vaksin "Sekarang".';
        }

        $data = $request->validate($rules, $messages);

        // Set vaccine fields to null if status is not "Sekarang"
        if ($data['status_vaksin'] !== 'Sekarang') {
            $data['jenis_vaksin'] = null;
            $data['vaccine_id'] = null;
        }

        $data['check_date'] = Carbon::parse($data['check_date'])->format('Y-m-d');

        // Calculate BMI and BMI status
        $heightInMeters = $data['mother_height'] / 100;
        $bmi = $data['mother_weight'] / ($heightInMeters * $heightInMeters);
        $data['bmi'] = round($bmi, 2);

        // Determine BMI status
        if ($bmi < 18.5) {
            $data['bmi_status'] = 'Kurang Berat Badan';
        } elseif ($bmi >= 18.5 && $bmi < 25) {
            $data['bmi_status'] = 'Normal';
        } elseif ($bmi >= 25 && $bmi < 30) {
            $data['bmi_status'] = 'Kelebihan Berat Badan';
        } elseif ($bmi >= 30 && $bmi < 40) {
            $data['bmi_status'] = 'Obesitas';
        } elseif ($bmi >= 40 && $bmi < 50) {
            $data['bmi_status'] = 'Obesitas Parah';
        } else {
            $data['bmi_status'] = 'Obesitas Ekstrem';
        }

        try {
            // Simpan data lama untuk cek perubahan vaksin
            $oldVaccineId = $pregnancy->vaccine_id;
            $oldStatusVaksin = $pregnancy->status_vaksin;

            // Update data
            $pregnancy->update($data);

            // Handle vaccine stock changes
            if ($data['status_vaksin'] === 'Sekarang' && $data['vaccine_id'] && ($oldStatusVaksin !== 'Sekarang' || $oldVaccineId != $data['vaccine_id'])) {
                Vaccine::where('id', $data['vaccine_id'])->decrement('stock', 1);

                // Kembalikan stok vaksin lama jika berbeda
                if ($oldStatusVaksin === 'Sekarang' && $oldVaccineId && $oldVaccineId != $data['vaccine_id']) {
                    Vaccine::where('id', $oldVaccineId)->increment('stock', 1);
                }
            }

            // Kembalikan stok jika status berubah dari "Sekarang" ke selainnya
            if ($oldStatusVaksin === 'Sekarang' && $data['status_vaksin'] !== 'Sekarang' && $oldVaccineId) {
                Vaccine::where('id', $oldVaccineId)->increment('stock', 1);
            }

            $this->clearPregnancyCheckCache();

            return redirect(url('/pregnancy-check-data'))->with('success', 'Data berhasil diperbarui.');
        } catch (\Exception $e) {
            Log::error('Error updating pregnancy check: ' . $e->getMessage());
            Log::error('Request data: ' . json_encode($request->all()));
            Log::error('Stack trace: ' . $e->getTraceAsString());

            // Untuk debugging, tampilkan error detail (hapus setelah selesai debug)
            return back()
                ->withInput()
                ->with('error', 'Data gagal diperbarui: ' . $e->getMessage());
        }
    }

    public function destroy($id)
    {
        $this->checkOfficerPosition();

        $pregnancy = PregnancyCheck::findOrFail($id);

        $pregnancy->delete();

        // Hapus chace
        $this->clearPregnancyCheckCache();

        return redirect(url('/pregnancy-check-data'))->with('success', 'Data berhasil dihapus.');
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
        $query = PregnancyCheck::with(['familyParents', 'officers'])->whereBetween('check_date', [$early_period, $final_period]);

        $pregnancy_checks = $query->orderBy('check_date', 'asc')->get();

        // Hapus cache (jika ada)
        $this->clearPregnancyCheckCache();

        // Return view
        return view('dashboard.service.pregnancy-check.report', compact('pregnancy_checks', 'early_period', 'final_period'));
    }

    public function manageMedicine($id)
    {
        $this->checkOfficerPosition();

        $pregnancy = PregnancyCheck::with(['familyParents', 'officers', 'medicines'])->findOrFail($id);

        $medicines = Medicine::where('expiry_date', '>=', Carbon::now()->addMonths(3))
            ->orderBy('expiry_date', 'asc')
            ->get();

        $province = $city = $subdistrict = $village = 'N/A';

        if ($pregnancy->familyParents->province) {
            $provinces = LocationController::getProvincesStatic();
            $province = collect($provinces)->firstWhere('id', $pregnancy->familyParents->province)['name'] ?? 'N/A';
        }

        if ($pregnancy->familyParents->city) {
            $cities = LocationController::getCitiesStatic($pregnancy->familyParents->province);
            $city = collect($cities)->firstWhere('id', $pregnancy->familyParents->city)['name'] ?? 'N/A';
        }

        if ($pregnancy->familyParents->subdistrict) {
            $districts = LocationController::getDistrictsStatic($pregnancy->familyParents->city);
            $subdistrict = collect($districts)->firstWhere('id', $pregnancy->familyParents->subdistrict)['name'] ?? 'N/A';
        }

        if ($pregnancy->familyParents->village) {
            $villages = LocationController::getVillagesStatic($pregnancy->familyParents->subdistrict);
            $village = collect($villages)->firstWhere('id', $pregnancy->familyParents->village)['name'] ?? 'N/A';
        }

        return view('dashboard.service.pregnancy-check.manage-medicine', compact('pregnancy', 'medicines', 'province', 'city', 'subdistrict', 'village'));
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
            // Ambil semua medicine_id yang sudah ada untuk pregnancy_check_id ini
            $existingMedicineIds = DB::table('medicine_usages')->where('pregnancy_check_id', $id)->pluck('medicine_id')->toArray();

            // Array untuk nama obat duplikat dan yang berhasil ditambahkan
            $duplicateMedicines = [];
            $addedMedicines = [];

            foreach ($medicineIds as $medicineId) {
                // Cek apakah sudah ada
                $exists = in_array($medicineId, $existingMedicineIds);

                $medicineName = DB::table('medicines')->where('id', $medicineId)->value('medicine_name');

                if ($exists) {
                    $duplicateMedicines[] = $medicineName;
                } else {
                    // Simpan data
                    DB::table('medicine_usages')->insert([
                        'immunization_id' => null,
                        'pregnancy_check_id' => $id,
                        'elderly_check_id' => null,
                        'medicine_id' => $medicineId,
                        'quantity' => null,
                        'dosage_instructions' => null,
                        'meal_time' => '-',
                        'notes' => null,
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

            return redirect("/pregnancy-check-data/{$id}/medicine/manage#pivot-table")->with('success', 'Obat berhasil ditambahkan.');
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
            $pregnancy = PregnancyCheck::with('medicines')->findOrFail($id);

            // Ambil semua medicine yang berelasi (via pivot) untuk imunisasi ini
            foreach ($pregnancy->medicines as $medicine) {
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
                            'stock' => DB::raw("stock + ($oldQty - $newQty)"),
                        ]);
                }

                // Update data pivot
                DB::table('medicine_usages')
                    ->where('pregnancy_check_id', $id)
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

            return redirect("/pregnancy-check-data/{$id}/medicine/manage#pivot-table")->with('success', 'Obat berhasil disimpan.');
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

        $pregnancy = PregnancyCheck::with(['medicines'])->findOrFail($id);

        DB::beginTransaction();

        try {
            foreach ($medicineIds as $medicineId) {
                $pivot = $pregnancy->medicines->firstWhere('id', $medicineId)?->pivot;

                // Cek apakah ada quantity di pivot
                if ($pivot && !is_null($pivot->quantity)) {
                    // Kembalikan stok obat
                    DB::table('medicines')->where('id', $medicineId)->increment('stock', $pivot->quantity);
                }

                // Hapus relasi di pivot
                DB::table('medicine_usages')->where('pregnancy_check_id', $id)->where('medicine_id', $medicineId)->delete();
            }

            DB::commit();

            // Hapus old khusus untuk form ini
            if (session()->has('_old_input')) {
                session()->forget('_old_input');
            }

            return redirect("/pregnancy-check-data/{$id}/medicine/manage#pivot-table")->with('success', 'Obat berhasil dihapus.');
        } catch (\Exception $e) {
            DB::rollBack();

            Log::error('Error: ' . $e->getMessage()); // Check 'storage/logs/laravel.log'

            return back()->with('error', 'Obat gagal dihapus. Silakan coba kembali.');
        }
    }

    public function getPregnancyCheckStatistics($year)
    {
        $user = Auth::user();
        $isParent = $user && $user->role === 'family_parent';

        if ($isParent) {
            // Ambil data parent berdasarkan parent_id dari user
            $parent = FamilyParent::find($user->parent_id);

            if ($parent) {
                // Ambil data pemeriksaan hanya untuk parent yang login
                $monthlyStats = PregnancyCheck::selectRaw('MONTH(check_date) as month, COUNT(DISTINCT parent_id) as total')->whereYear('check_date', $year)->where('parent_id', $parent->id)->groupByRaw('MONTH(check_date)')->orderByRaw('MONTH(check_date)')->pluck('total', 'month');
            } else {
                $monthlyStats = collect(); // Jika parent tidak ditemukan
            }
        } else {
            // Ambil semua data pemeriksaan
            $monthlyStats = PregnancyCheck::selectRaw('MONTH(check_date) as month, COUNT(DISTINCT parent_id) as total')->whereYear('check_date', $year)->groupByRaw('MONTH(check_date)')->orderByRaw('MONTH(check_date)')->pluck('total', 'month');
        }

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
