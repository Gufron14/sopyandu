<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\ElderlyCheck;
use App\Models\Immunization;
use App\Models\Medicine;
use App\Models\PregnancyCheck;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class MedicineController extends Controller
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

    public function index()
    {
        $this->checkOfficerPosition();

        // Ambil tanggal sekarang
        $now = Carbon::now();

        // Ambil tanggal 6 bulan yang lalu dari sekarang
        $threeMonthsAgo = $now->copy()->subMonths(3);

        // Ambil semua data obat yang:
        // - Belum kedaluwarsa (expiry_date >= sekarang), ATAU
        // - Sudah kedaluwarsa tapi belum lebih dari 3 bulan (expiry_date antara 3 bulan yang lalu sampai sekarang)
        $medicines = Medicine::where(function ($query) use ($now, $threeMonthsAgo) {
            $query->where('expiry_date', '>=', $now) // Belum kedaluwarsa
                ->orWhereBetween('expiry_date', [$threeMonthsAgo, $now]); // Kedaluwarsa tapi masih dalam 3 bulan terakhir
        })->orderBy('expiry_date', 'asc')->get();

        return view('dashboard.supply.medicine.index', compact('medicines'));
    }

    public function create()
    {
        $this->checkOfficerPosition();

        return view('dashboard.supply.medicine.create');
    }

    public function store(Request $request)
    {
        $this->checkOfficerPosition();

        $rules = [
            'medicine_name' => 'required',
            'type' => 'required',
            'unit' => 'required',
            'stock' => 'required|numeric',
            'entry_date' => 'required|date',
            'expiry_date' => 'required|date',
            'notes' => 'required|string|max:100',
        ];

        $messages = [
            'medicine_name.required' => 'Nama obat wajib diisi.',
            'type.required' => 'Jenis obat wajib dipilih.',
            'unit.required' => 'Unit obat wajib diisi.',
            'stock.required' => 'Stok obat wajib diisi.',
            'stock.numeric' => 'Stok obat harus berupa angka.',
            'entry_date.required' => 'Tanggal masuk wajib diisi.',
            'entry_date.date' => 'Tanggal masuk harus berupa tanggal yang valid',
            'expiry_date.required' => 'Tanggal kedaluwarsa wajib diisi.',
            'expiry_date.date' => 'Tanggal kedaluwarsa harus berupa tanggal yang valid.',
            'notes.required' => 'Keterangan wajib diisi.',
            'notes.max' => 'Keterangan maksimal 100 karakter.',
        ];

        $data = $request->validate($rules, $messages);

        $data['entry_date'] = Carbon::parse($data['entry_date'])->format('Y-m-d');
        $data['expiry_date'] = Carbon::parse($data['expiry_date'])->format('Y-m-d');

        try {
            Medicine::create([
                'medicine_name' => $data['medicine_name'],
                'type' => $data['type'],
                'unit' => $data['unit'],
                'stock' => $data['stock'],
                'entry_date' => $data['entry_date'],
                'expiry_date' => $data['expiry_date'],
                'notes' => $data['notes'],
            ]);

            return redirect(url('/medicine-data'))->with('success', "Data berhasil ditambahkan.");
        } catch (\Exception $e) {
            Log::error('Error: ' . $e->getMessage()); // Check 'storage/logs/laravel.log'

            return back()->with('error', 'Data gagal ditambahkan. Silakan coba kembali.');
        }
    }

    public function edit($id)
    {
        $this->checkOfficerPosition();

        $medicine = Medicine::findOrFail($id);

        return view('dashboard.supply.medicine.edit', compact('medicine'));
    }

    public function update(Request $request, $id)
    {
        $this->checkOfficerPosition();

        $medicine = Medicine::findOrFail($id);

        $rules = [
            'medicine_name' => 'required',
            'type' => 'required',
            'unit' => 'required',
            'stock' => 'required|numeric',
            'entry_date' => 'required|date',
            'expiry_date' => 'required|date',
            'notes' => 'required|string|max:100',
        ];

        $messages = [
            'medicine_name.required' => 'Nama obat wajib diisi.',
            'type.required' => 'Jenis obat wajib dipilih.',
            'unit.required' => 'Unit obat wajib diisi.',
            'stock.required' => 'Stok obat wajib diisi.',
            'stock.numeric' => 'Stok obat harus berupa angka.',
            'entry_date.required' => 'Tanggal masuk wajib diisi.',
            'entry_date.date' => 'Tanggal masuk harus berupa tanggal yang valid',
            'expiry_date.required' => 'Tanggal kedaluwarsa wajib diisi.',
            'expiry_date.date' => 'Tanggal kedaluwarsa harus berupa tanggal yang valid.',
            'notes.required' => 'Keterangan wajib diisi.',
            'notes.max' => 'Keterangan maksimal 100 karakter.',
        ];

        $data = $request->validate($rules, $messages);

        $data['entry_date'] = Carbon::parse($data['entry_date'])->format('Y-m-d');
        $data['expiry_date'] = Carbon::parse($data['expiry_date'])->format('Y-m-d');

        try {
            $medicine->update([
                'medicine_name' => $data['medicine_name'],
                'type' => $data['type'],
                'unit' => $data['unit'],
                'stock' => $data['stock'],
                'entry_date' => $data['entry_date'],
                'expiry_date' => $data['expiry_date'],
                'notes' => $data['notes'],
            ]);

            return redirect(url('/medicine-data'))->with('success', 'Data berhasil diperbarui.');
        } catch (\Exception $e) {
            Log::error('Error: ' . $e->getMessage()); // Check 'storage/logs/laravel.log'

            return back()->with('error', 'Data gagal diperbarui. Silakan coba kembali.');
        }
    }

    public function destroy($id)
    {
        $this->checkOfficerPosition();

        $medicine = Medicine::findOrFail($id);

        $medicine->delete();

        return redirect(url('/medicine-data'))->with('success', 'Data berhasil dihapus.');
    }

    public function historyMedicine()
    {
        $this->checkOfficerPosition();

        // Ambil data langsung menggunakan relasi Eloquent tanpa membuat model terpisah, karena hanya digunakan satu kali
        $medicines = DB::table('medicine_usages')
            ->leftJoin('immunizations', 'medicine_usages.immunization_id', '=', 'immunizations.id')
            ->leftJoin('pregnancy_checks', 'medicine_usages.pregnancy_check_id', '=', 'pregnancy_checks.id')
            ->leftJoin('elderly_checks', 'medicine_usages.elderly_check_id', '=', 'elderly_checks.id')
            ->leftJoin('medicines', 'medicine_usages.medicine_id', '=', 'medicines.id')
            ->select(
                'medicine_usages.*',
                'medicines.medicine_name as medicine_name',
                'medicines.unit as medicine_unit',
                'immunizations.immunization_date',
                'pregnancy_checks.check_date as pregnancy_check_date',
                'elderly_checks.check_date as elderly_check_date',
                'immunizations.children_id',  // ID anak untuk imunisasi
                'pregnancy_checks.parent_id',  // ID ibu untuk pemeriksaan ibu hamil
                'elderly_checks.elderly_id'  // ID lansia untuk pemeriksaan lansia
            )
            ->orderBy('medicine_usages.created_at', 'desc')
            ->get();

        // Proses dan grouping
        $grouped = $medicines->map(function ($item) {
            // Tentukan tanggal
            $item->date = $item->immunization_date ?? $item->pregnancy_check_date ?? $item->elderly_check_date;

            // Tentukan jenis & ID layanan dan nama yang sesuai
            if ($item->immunization_id) {
                $item->type = 'Imunisasi';
                $item->related_id = $item->immunization_id;
                $item->patient_name = Immunization::find($item->immunization_id)->familyChildren->fullname;  // Nama anak untuk imunisasi
            } elseif ($item->pregnancy_check_id) {
                $item->type = 'Pemeriksaan Ibu Hamil';
                $item->related_id = $item->pregnancy_check_id;
                $item->patient_name = PregnancyCheck::find($item->pregnancy_check_id)->familyParents->mother_fullname;  // Nama ibu untuk pemeriksaan ibu hamil
            } elseif ($item->elderly_check_id) {
                $item->type = 'Pemeriksaan Lansia';
                $item->related_id = $item->elderly_check_id;
                $item->patient_name = ElderlyCheck::find($item->elderly_check_id)->elderlies->fullname;  // Nama lansia untuk pemeriksaan lansia
            }

            // Format info obat
            $item->medicine_info = "{$item->medicine_name} ({$item->quantity} {$item->medicine_unit})";

            return $item;
        })
            ->groupBy(function ($item) {
                // Grouping berdasarkan tanggal, jenis layanan, dan ID layanan
                return $item->date . '-' . $item->type . '-' . $item->related_id;
            })
            ->map(function ($group) {
                $first = $group->first();
                return (object)[
                    'date' => $first->date,
                    'type' => $first->type,
                    'patient_name' => $first->patient_name,
                    'related_id' => $first->related_id,
                    'medicines' => $group->pluck('medicine_info')->implode(', ')
                ];
            })
            ->values();

        return view('dashboard.supply.medicine.history', [
            'groupedMedicines' => $grouped
        ]);
    }

    public function printReport(Request $request)
    {
        $this->checkOfficerPosition();

        $rules = [
            'early_period' => 'required|date',
            'final_period' => 'required|date|after_or_equal:early_period',
            'service_name' => 'required|string|in:Imunisasi,Pemeriksaan Ibu Hamil,Pemeriksaan Lansia,Semua',
        ];

        $messages = [
            'early_period.required' => 'Periode awal wajib diisi.',
            'final_period.required' => 'Periode akhir wajib diisi.',
            'final_period.after_or_equal' => 'Periode akhir harus sama atau setelah periode awal.',
            'service_name.required' => 'Nama layanan wajib dipilih.',
        ];

        $data = $request->validate($rules, $messages);

        $early_period = $data['early_period'];
        $final_period = $data['final_period'];
        $service_name = $data['service_name'];

        // Ambil data dan filter berdasarkan periode & jenis layanan
        $medicines = DB::table('medicine_usages')
            ->leftJoin('immunizations', 'medicine_usages.immunization_id', '=', 'immunizations.id')
            ->leftJoin('pregnancy_checks', 'medicine_usages.pregnancy_check_id', '=', 'pregnancy_checks.id')
            ->leftJoin('elderly_checks', 'medicine_usages.elderly_check_id', '=', 'elderly_checks.id')
            ->leftJoin('medicines', 'medicine_usages.medicine_id', '=', 'medicines.id')
            ->select(
                'medicine_usages.*',
                'medicines.medicine_name as medicine_name',
                'medicines.unit as medicine_unit',
                'immunizations.immunization_date',
                'pregnancy_checks.check_date as pregnancy_check_date',
                'elderly_checks.check_date as elderly_check_date',
                'immunizations.children_id',
                'pregnancy_checks.parent_id',
                'elderly_checks.elderly_id'
            )
            ->when($service_name !== 'Semua', function ($query) use ($service_name) {
                if ($service_name === 'Imunisasi') {
                    $query->whereNotNull('medicine_usages.immunization_id');
                } elseif ($service_name === 'Pemeriksaan Ibu Hamil') {
                    $query->whereNotNull('medicine_usages.pregnancy_check_id');
                } elseif ($service_name === 'Pemeriksaan Lansia') {
                    $query->whereNotNull('medicine_usages.elderly_check_id');
                }
            })
            ->where(function ($query) use ($early_period, $final_period) {
                $query->whereBetween('immunizations.immunization_date', [$early_period, $final_period])
                    ->orWhereBetween('pregnancy_checks.check_date', [$early_period, $final_period])
                    ->orWhereBetween('elderly_checks.check_date', [$early_period, $final_period]);
            })
            ->orderBy('medicine_usages.created_at', 'desc')
            ->get();

        // Proses dan grouping
        $grouped = $medicines->map(function ($item) {
            $item->date = $item->immunization_date ?? $item->pregnancy_check_date ?? $item->elderly_check_date;

            if ($item->immunization_id) {
                $item->type = 'Imunisasi';
                $item->related_id = $item->immunization_id;
                $item->patient_name = optional(Immunization::find($item->immunization_id)->familyChildren)->fullname ?? '-';
            } elseif ($item->pregnancy_check_id) {
                $item->type = 'Pemeriksaan Ibu Hamil';
                $item->related_id = $item->pregnancy_check_id;
                $item->patient_name = optional(PregnancyCheck::find($item->pregnancy_check_id)->familyParents)->mother_fullname ?? '-';
            } elseif ($item->elderly_check_id) {
                $item->type = 'Pemeriksaan Lansia';
                $item->related_id = $item->elderly_check_id;
                $item->patient_name = optional(ElderlyCheck::find($item->elderly_check_id)->elderlies)->fullname ?? '-';
            }

            $item->medicine_info = "{$item->medicine_name} ({$item->quantity} {$item->medicine_unit})";

            return $item;
        })
            ->groupBy(function ($item) {
                return $item->date . '-' . $item->type . '-' . $item->related_id;
            })
            ->map(function ($group) {
                $first = $group->first();
                return (object)[
                    'date' => $first->date,
                    'type' => $first->type,
                    'patient_name' => $first->patient_name,
                    'related_id' => $first->related_id,
                    'medicines' => $group->pluck('medicine_info')->implode(', ')
                ];
            })
            ->values();

        return view('dashboard.supply.medicine.report', [
            'groupedMedicines' => $grouped,
            'early_period' => $early_period,
            'final_period' => $final_period,
            'service_name' => $service_name,
        ]);
    }

    public function printStockReport(Request $request)
    {
        // Validasi dasar
        $rules = [
            'print_criteria' => 'required|string|in:Tanggal Masuk,Tanggal Kedaluwarsa,Semua',
        ];

        // Jika kriteria bukan "Semua", tambahkan validasi periode
        if ($request->print_criteria !== 'Semua') {
            $rules['early_period'] = 'required|date';
            $rules['final_period'] = 'required|date|after_or_equal:early_period';
        }

        $messages = [
            'early_period.required' => 'Periode awal wajib diisi.',
            'final_period.required' => 'Periode akhir wajib diisi.',
            'final_period.after_or_equal' => 'Periode akhir harus sama atau setelah periode awal.',
            'print_criteria.required' => 'Kriteria cetak wajib dipilih.',
        ];

        $validated = $request->validate($rules, $messages);

        // Ambil nilai input
        $print_criteria = $validated['print_criteria'];
        $early_period = $validated['early_period'] ?? null;
        $final_period = $validated['final_period'] ?? null;

        // Bangun query
        $query = Medicine::query();

        if ($print_criteria === 'Tanggal Masuk') {
            $query->whereBetween('entry_date', [$early_period, $final_period])
                ->orderBy('entry_date', 'asc');
        } elseif ($print_criteria === 'Tanggal Kedaluwarsa') {
            $query->whereBetween('expiry_date', [$early_period, $final_period])
                ->orderBy('expiry_date', 'asc');
        }

        // Eksekusi query
        $medicines = $query->get();

        // Return view
        return view('dashboard.supply.medicine.stock-report', compact('medicines', 'early_period', 'final_period', 'print_criteria'));
    }
}
