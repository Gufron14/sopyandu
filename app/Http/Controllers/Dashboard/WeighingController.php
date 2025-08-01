<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\FamilyChildren;
use App\Models\FamilyParent;
use App\Models\Weighing;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class WeighingController extends Controller
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

    // Fungsi untuk menghapus cache yang berkaitan dengan weighing
    protected function clearWeighingCache()
    {
        $currentYear = now()->year;

        // Ambil semua tahun yang tersedia dari database (DESC untuk cari tahun terbaru)
        $availableYears = Weighing::selectRaw('YEAR(weighing_date) as year')->distinct()->orderByDesc('year')->pluck('year');

        // Tentukan tahun yang dipilih
        $selectedYear = $availableYears->contains($currentYear) ? $currentYear : $availableYears->first(); // Ambil tahun terbaru jika tahun saat ini tidak ada

        // Hapus cache berdasarkan tahun yang dipilih
        Cache::forget("weighing_children_{$selectedYear}");
    }

    public function index()
    {
        $user = Auth::user();
        $isParent = $user && $user->role === 'family_parent';
        $cacheKey = 'weighing_children';

        $weighings = [];
        $availableYears = collect();
        $selectedYear = null;

        if ($isParent) {
            // Ambil data parent berdasarkan parent_id dari user
            $parent = FamilyParent::find($user->parent_id);

            if ($parent) {
                // Ambil semua anak milik parent tersebut
                $children = $parent->familyChildren()->orderBy('fullname', 'asc')->get();

                // Ambil data penimbangan untuk anak-anak parent
                $weighings = Weighing::with(['familyChildren', 'officers'])
                    ->whereIn('children_id', $children->pluck('id'))
                    ->orderBy('weighing_date', 'desc')
                    ->get();
            }
        } else {
            // Ambil semua tahun unik dari data weighing (DESC agar tahun terbaru di atas)
            $availableYears = Weighing::selectRaw('YEAR(weighing_date) as year')->distinct()->orderByDesc('year')->pluck('year');

            $currentYear = now()->year;
            $requestedYear = request('year'); // Tangkap input dari dropdown (jika ada)

            // Tentukan tahun yang akan digunakan untuk filter
            $selectedYear = $requestedYear ?? ($availableYears->contains($currentYear) ? $currentYear : $availableYears->first());

            // Ambil data sesuai tahun yang dipilih
            $weighings = Cache::remember("{$cacheKey}_{$selectedYear}", 300, function () use ($selectedYear) {
                return Weighing::with(['familyChildren', 'officers'])
                    ->whereYear('weighing_date', $selectedYear)
                    ->orderBy('weighing_date', 'desc')
                    ->get();
            });
        }

        return view('dashboard.service.weighing.index', compact('weighings', 'availableYears', 'selectedYear'));
    }

    public function show($id)
    {
        $weighing = Weighing::with(['familyChildren.familyParents', 'officers'])->findOrFail($id);

        return view('dashboard.service.weighing.show', compact('weighing'));
    }

    public function create()
    {
        $this->checkOfficerPosition();

        $children = FamilyChildren::with('familyParents')->select('id', 'nik', 'fullname', 'gender', 'birth_place', 'date_of_birth', 'parent_id')->orderBy('fullname', 'asc')->get();

        return view('dashboard.service.weighing.create', compact('children'));
    }

    public function store(Request $request)
    {
        $this->checkOfficerPosition();

        // Log the incoming request data for debugging
        Log::info('Incoming request data:', $request->all());

        $rules = [
            'children_id' => 'required',
            'weighing_date' => 'required|date',
            'age_in_checks' => 'required',
            'weight' => 'required|numeric|min:0',
            'height' => 'required|numeric|min:0',
            'head_circumference' => 'required|numeric|min:0',
            'arm_circumference' => 'required|numeric|min:0',
            'nutrition_status' => 'required|in:Baik,Buruk,Kurang,Lebih',
            'notes' => 'nullable|string|max:255',
            'officer_id' => 'required',
        ];

        $messages = [
            'children_id.required' => 'Nama anak wajib dipilih.',
            'weighing_date.required' => 'Tanggal penimbangan wajib diisi.',
            'weighing_date.date' => 'Tanggal penimbangan harus berupa tanggal yang valid',
            'age_in_checks.required' => 'Usia saat penimbangan wajib diisi.',
            'weight.required' => 'Berat badan wajib diisi.',
            'weight.numeric' => 'Berat badan harus berupa angka valid (kg).',
            'weight.min' => 'Berat badan tidak boleh kurang dari 0.',
            'height.required' => 'Tinggi badan wajib diisi.',
            'height.numeric' => 'Tinggi badan harus berupa angka valid (cm).',
            'height.min' => 'Tinggi badan tidak boleh kurang dari 0.',
            'head_circumference.required' => 'Ukuran lingkar kepala wajib diisi.',
            'head_circumference.numeric' => 'Ukuran lingkar kepala harus berupa angka valid (cm).',
            'head_circumference.min' => 'Ukuran lingkar kepala tidak boleh kurang dari 0.',
            'arm_circumference.required' => 'Ukuran lingkar lengan wajib diisi.',
            'arm_circumference.numeric' => 'Ukuran lingkar lengan harus berupa angka valid (cm).',
            'arm_circumference.min' => 'Ukuran lingkar lengan tidak boleh kurang dari 0.',
            'nutrition_status.required' => 'Status gizi wajib diisi.',
            'nutrition_status.in' => 'Status gizi tidak valid.',
            'notes.max' => 'Keterangan maksimal 255 karakter.',
            'officer_id.required' => 'Petugas wajib diisi.',
        ];

        try {
            // Check for existing record first
            $existingWeighing = Weighing::where('children_id', $request->children_id)->where('weighing_date', $request->weighing_date)->first();

            if ($existingWeighing) {
                $indonesianDateFormat = Carbon::parse($request->weighing_date)->locale('id')->isoFormat('D MMMM YYYY');
                Log::info('Duplicate weighing record detected:', [
                    'children_id' => $request->children_id,
                    'weighing_date' => $request->weighing_date,
                ]);
                return back()
                    ->withErrors(['children_id' => "Data penimbangan untuk anak ini pada tanggal {$indonesianDateFormat} sudah ada."])
                    ->withInput();
            }

            // Validate the request data
            $data = $request->validate($rules, $messages);

            // Format the date
            $data['weighing_date'] = Carbon::parse($data['weighing_date'])->format('Y-m-d');

            // Log the validated data
            Log::info('Validated data:', $data);

            // Create weighing record
            $weighing = Weighing::create($data);

            // Log successful creation
            Log::info('Weighing record created successfully:', ['id' => $weighing->id]);

            // Clear cache
            $this->clearWeighingCache();

            return redirect(url('/weighing-data'))->with('success', 'Data berhasil ditambahkan.');
        } catch (\Illuminate\Validation\ValidationException $e) {
            Log::error('Validation error:', ['errors' => $e->errors()]);
            return back()->withErrors($e->errors())->withInput();
        } catch (\Exception $e) {
            Log::error('Error creating weighing record:', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            return back()->with('error', 'Data gagal ditambahkan. Silakan coba kembali.')->withInput();
        }
    }

    public function edit($id)
    {
        $this->checkOfficerPosition();

        $weighing = Weighing::findOrFail($id);

        $children = FamilyChildren::with('familyParents')->select('id', 'nik', 'fullname', 'gender', 'birth_place', 'date_of_birth', 'parent_id')->orderBy('fullname', 'asc')->get();

        return view('dashboard.service.weighing.edit', compact('weighing', 'children'));
    }

    public function update(Request $request, $id)
    {
        $this->checkOfficerPosition();

        $weighing = Weighing::findOrFail($id);

        $rules = [
            'children_id' => 'required',
            'weighing_date' => 'required|date',
            'age_in_checks' => 'required',
            'weight' => 'required|numeric|min:0',
            'height' => 'required|numeric|min:0',
            'head_circumference' => 'required|numeric|min:0',
            'arm_circumference' => 'required|numeric|min:0',
            'notes' => 'nullable|string|max:255',
            'officer_id' => 'required',
        ];

        $messages = [
            'children_id.required' => 'Nama anak wajib dipilih.',
            'weighing_date.required' => 'Tanggal penimbangan wajib diisi.',
            'weighing_date.date' => 'Tanggal penimbangan harus berupa tanggal yang valid',
            'age_in_checks.required' => 'Usia saat penimbangan wajib diisi.',
            'weight.required' => 'Berat badan wajib diisi.',
            'weight.numeric' => 'Berat badan harus berupa angka valid (kg).',
            'weight.min' => 'Berat badan tidak boleh kurang dari 0.',
            'height.required' => 'Tinggi badan wajib diisi.',
            'height.numeric' => 'Tinggi badan harus berupa angka valid (cm).',
            'height.min' => 'Tinggi badan tidak boleh kurang dari 0.',
            'head_circumference.required' => 'Ukuran lingkar kepala wajib diisi.',
            'head_circumference.numeric' => 'Ukuran lingkar kepala harus berupa angka valid (cm).',
            'head_circumference.min' => 'Ukuran lingkar kepala tidak boleh kurang dari 0.',
            'arm_circumference.required' => 'Ukuran lingkar lengan wajib diisi.',
            'arm_circumference.numeric' => 'Ukuran lingkar lengan harus berupa angka valid (cm).',
            'arm_circumference.min' => 'Ukuran lingkar lengan tidak boleh kurang dari 0.',
            'notes.max' => 'Keterangan maksimal 255 karakter.',
            'officer_id.required' => 'Petugas wajib diisi.',
        ];

        $existingWeighing = Weighing::where('children_id', $request->children_id)->where('weighing_date', $request->weighing_date)->where('id', '!=', $id)->first();

        if ($existingWeighing) {
            $indonesianDateFormat = Carbon::parse($request->weighing_date)->locale('id')->isoFormat('D MMMM YYYY');
            return back()
                ->withErrors(['children_id' => "Data penimbangan untuk anak ini pada tanggal {$indonesianDateFormat} sudah ada."])
                ->withInput();
        }

        $data = $request->validate($rules, $messages);
        $data['weighing_date'] = Carbon::parse($data['weighing_date'])->format('Y-m-d');

        try {
            // Update weighing record first
            $weighing->update([
                'children_id' => $data['children_id'],
                'weighing_date' => $data['weighing_date'],
                'age_in_checks' => $data['age_in_checks'],
                'weight' => $data['weight'],
                'height' => $data['height'],
                'head_circumference' => $data['head_circumference'],
                'arm_circumference' => $data['arm_circumference'],
                'notes' => $data['notes'],
                'officer_id' => $data['officer_id'],
            ]);

            // Refresh the model to ensure we have the latest data
            $weighing->refresh();

            // Calculate all anthropometric measurements using WHO standards
            $measurements = $weighing->calculateAllMeasurements();

            // Determine overall nutrition status based on WHO guidelines
            $nutritionStatuses = array_filter([$measurements['bb_u']['status'] ?? null, $measurements['tb_u']['status'] ?? null, $measurements['bb_tb']['status'] ?? null, $measurements['imt_u']['status'] ?? null]);

            // Determine overall status prioritizing the worst condition
            $overallStatus = 'Baik'; // Default status
            if (in_array('Buruk', $nutritionStatuses)) {
                $overallStatus = 'Buruk';
            } elseif (in_array('Kurang', $nutritionStatuses)) {
                $overallStatus = 'Kurang';
            } elseif (in_array('Lebih', $nutritionStatuses)) {
                $overallStatus = 'Lebih';
            }

            // Update the weighing record with the calculated nutrition status
            $weighing->update([
                'nutrition_status' => $overallStatus,
            ]);

            // Clear cache
            $this->clearWeighingCache();

            return redirect(url('/weighing-data'))->with('success', 'Data berhasil diperbarui.');
        } catch (\Exception $e) {
            Log::error('Error: ' . $e->getMessage());
            return back()->with('error', 'Data gagal diperbarui. Silakan coba kembali.');
        }
    }

    public function destroy($id)
    {
        $this->checkOfficerPosition();

        $weighing = Weighing::findOrFail($id);

        $weighing->delete();

        // Hapus chace
        $this->clearWeighingCache();

        return redirect(url('/weighing-data'))->with('success', 'Data berhasil dihapus.');
    }

    public function printReport(Request $request)
    {
        // Validasi input
        $rules = [
            'early_period' => 'required|date',
            'final_period' => 'required|date|after_or_equal:early_period',
            'nutrition_status' => 'required|string|in:Baik,Buruk,Kurang,Lebih,Semua',
        ];

        $messages = [
            'early_period.required' => 'Periode awal wajib diisi.',
            'final_period.required' => 'Periode akhir wajib diisi.',
            'final_period.after_or_equal' => 'Periode akhir harus sama atau setelah periode awal.',
            'nutrition_status.required' => 'Status gizi wajib dipilih.',
        ];

        $data = $request->validate($rules, $messages);

        // Ambil data dari request
        $early_period = $data['early_period'];
        $final_period = $data['final_period'];
        $nutrition_status = $data['nutrition_status'];

        // Query data penimbangan
        $query = Weighing::with(['familyChildren', 'officers'])->whereBetween('weighing_date', [$early_period, $final_period]);

        if ($nutrition_status !== 'Semua') {
            $query->where('nutrition_status', $nutrition_status);
        }

        $weighings = $query->orderBy('weighing_date', 'asc')->get();

        // Hapus cache (jika ada)
        $this->clearWeighingCache();

        // Return view
        return view('dashboard.service.weighing.report', compact('weighings', 'early_period', 'final_period', 'nutrition_status'));
    }

    public function getWeighingStatistics($year)
    {
        $monthlyStats = Weighing::selectRaw('MONTH(weighing_date) as month, COUNT(DISTINCT children_id) as total')->whereYear('weighing_date', $year)->groupByRaw('MONTH(weighing_date)')->orderByRaw('MONTH(weighing_date)')->pluck('total', 'month');

        $data = [];
        for ($i = 1; $i <= 12; $i++) {
            $data[] = $monthlyStats[$i] ?? 0;
        }

        return response()->json([
            'data' => $data,
            'total' => array_sum($data),
        ]);
    }

    public function getNutritionStatusStatistics($year)
    {
        $statuses = ['Baik', 'Buruk', 'Kurang', 'Lebih'];

        // Ambil data penimbangan per bulan per status
        $monthlyStats = Weighing::selectRaw('MONTH(weighing_date) as month, nutrition_status, COUNT(DISTINCT children_id) as total')->whereYear('weighing_date', $year)->groupByRaw('MONTH(weighing_date), nutrition_status')->get();

        // Inisialisasi struktur data bulanan
        $data = [];
        for ($i = 1; $i <= 12; $i++) {
            $data[$i] = array_fill_keys($statuses, 0);
        }

        // Masukkan data bulanan dari query ke array
        foreach ($monthlyStats as $stat) {
            $month = (int) $stat->month;
            $status = $stat->nutrition_status;
            $total = $stat->total;

            if (isset($data[$month][$status])) {
                $data[$month][$status] = $total;
            }
        }

        // Hitung total anak unik per status sepanjang tahun
        $totalPerStatus = Weighing::selectRaw('nutrition_status, COUNT(DISTINCT children_id) as total')
            ->whereYear('weighing_date', $year)
            ->groupBy('nutrition_status')
            ->pluck('total', 'nutrition_status') // Hasilnya dalam bentuk associative array: ['Baik' => 12, 'Buruk' => 4, ...]
            ->toArray();

        // Pastikan semua status memiliki nilai meskipun 0
        foreach ($statuses as $status) {
            if (!isset($totalPerStatus[$status])) {
                $totalPerStatus[$status] = 0;
            }
        }

        return response()->json([
            'data' => $data,
            'total' => $totalPerStatus,
        ]);
    }

    public function getNutritionStatusParentChildrenStatistics($year = null)
    {
        $user = Auth::user();

        if (!$user || $user->role !== 'family_parent') {
            return response()->json(
                [
                    'data' => [],
                    'message' => 'Unauthorized',
                ],
                403,
            );
        }

        $parent = FamilyParent::find($user->parent_id);

        if (!$parent) {
            return response()->json(
                [
                    'data' => [],
                    'message' => 'Parent not found',
                ],
                404,
            );
        }

        $children = FamilyChildren::where('parent_id', $parent->id)->get();

        $result = [];

        foreach ($children as $child) {
            $query = Weighing::where('children_id', $child->id);

            // Filter berdasarkan tahun jika ada
            if ($year) {
                $query->whereYear('weighing_date', $year);
            }

            // Urutkan berdasarkan tanggal
            $weighings = $query->orderBy('weighing_date')->get();

            foreach ($weighings as $weighing) {
                $result[] = [
                    'children_id' => $child->id,
                    'fullname' => $child->fullname,
                    'weighing_date' => $weighing->weighing_date,
                    'age_in_checks' => $weighing->age_in_checks,
                    'nutrition_status' => $weighing->nutrition_status,
                ];
            }
        }

        return response()->json([
            'data' => $result,
        ]);
    }
}
