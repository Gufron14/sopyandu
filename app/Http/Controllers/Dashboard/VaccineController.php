<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\Vaccine;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class VaccineController extends Controller
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

        // Ambil tanggal hari ini
        $now = Carbon::now();

        // Hitung tanggal 3 bulan yang lalu dari sekarang
        $threeMonthsAgo = $now->copy()->subMonths(3);

        // Ambil vaksin yang:
        // - Belum kedaluwarsa (expiry_date >= sekarang), atau
        // - Kedaluwarsa tapi belum lebih dari 3 bulan (expiry_date antara 3 bulan lalu dan sekarang)
        $vaccines = Vaccine::where(function ($query) use ($now, $threeMonthsAgo) {
            $query->where('expiry_date', '>=', $now) // Belum kedaluwarsa
                ->orWhereBetween('expiry_date', [$threeMonthsAgo, $now]); // Kedaluwarsa kurang dari 3 bulan
        })->orderBy('expiry_date', 'asc')->get();

        return view('dashboard.supply.vaccine.index', compact('vaccines'));
    }

    public function create()
    {
        $this->checkOfficerPosition();

        return view('dashboard.supply.vaccine.create');
    }

    public function store(Request $request)
    {
        $this->checkOfficerPosition();

        $rules = [
            'vaccine_name' => 'required',
            'unit' => 'required',
            'stock' => 'required|numeric',
            'entry_date' => 'required|date',
            'expiry_date' => 'required|date',
            'notes' => 'required|string|max:100',
        ];

        $messages = [
            'vaccine_name.required' => 'Nama vaksin wajib diisi.',
            'unit.required' => 'Unit vaksin wajib diisi.',
            'stock.required' => 'Stok vaksin wajib diisi.',
            'stock.numeric' => 'Stok vaksin harus berupa angka.',
            'entry_date.required' => 'Tanggal masuk wajib diisi.',
            'entry_date.date' => 'Tanggal masuk harus berupa tanggal yang valid.',
            'expiry_date.required' => 'Tanggal kedaluwarsa wajib diisi.',
            'expiry_date.date' => 'Tanggal kedaluwarsa harus berupa tanggal yang valid.',
            'notes.required' => 'Keterangan wajib diisi.',
            'notes.max' => 'Keterangan maksimal 100 karakter.',
        ];

        $data = $request->validate($rules, $messages);

        $data['entry_date'] = Carbon::parse($data['entry_date'])->format('Y-m-d');
        $data['expiry_date'] = Carbon::parse($data['expiry_date'])->format('Y-m-d');

        try {
            Vaccine::create([
                'vaccine_name' => $data['vaccine_name'],
                'unit' => $data['unit'],
                'stock' => $data['stock'],
                'entry_date' => $data['entry_date'],
                'expiry_date' => $data['expiry_date'],
                'notes' => $data['notes'],
            ]);

            return redirect(url('/vaccine-data'))->with('success', "Data berhasil ditambahkan.");
        } catch (\Exception $e) {
            Log::error('Error: ' . $e->getMessage()); // Check 'storage/logs/laravel.log'

            return back()->with('error', 'Data gagal ditambahkan. Silakan coba kembali.');
        }
    }

    public function edit($id)
    {
        $this->checkOfficerPosition();

        $vaccine = Vaccine::findOrFail($id);

        return view('dashboard.supply.vaccine.edit', compact('vaccine'));
    }

    public function update(Request $request, $id)
    {
        $this->checkOfficerPosition();

        $vaccine = Vaccine::findOrFail($id);

        $rules = [
            'vaccine_name' => 'required',
            'unit' => 'required',
            'stock' => 'required|numeric',
            'entry_date' => 'required|date',
            'expiry_date' => 'required|date',
            'notes' => 'required|string|max:100',
        ];

        $messages = [
            'vaccine_name.required' => 'Nama vaksin wajib diisi.',
            'unit.required' => 'Unit vaksin wajib diisi.',
            'stock.required' => 'Stok vaksin wajib diisi.',
            'stock.numeric' => 'Stok vaksin harus berupa angka.',
            'entry_date.required' => 'Tanggal masuk wajib diisi.',
            'entry_date.date' => 'Tanggal masuk harus berupa tanggal yang valid.',
            'expiry_date.required' => 'Tanggal kedaluwarsa wajib diisi.',
            'expiry_date.date' => 'Tanggal kedaluwarsa harus berupa tanggal yang valid.',
            'notes.required' => 'Keterangan wajib diisi.',
            'notes.max' => 'Keterangan maksimal 100 karakter.',
        ];

        $data = $request->validate($rules, $messages);

        $data['entry_date'] = Carbon::parse($data['entry_date'])->format('Y-m-d');
        $data['expiry_date'] = Carbon::parse($data['expiry_date'])->format('Y-m-d');

        try {
            $vaccine->update([
                'vaccine_name' => $data['vaccine_name'],
                'unit' => $data['unit'],
                'stock' => $data['stock'],
                'entry_date' => $data['entry_date'],
                'expiry_date' => $data['expiry_date'],
                'notes' => $data['notes'],
            ]);

            return redirect(url('/vaccine-data'))->with('success', 'Data berhasil diperbarui.');
        } catch (\Exception $e) {
            Log::error('Error: ' . $e->getMessage()); // Check 'storage/logs/laravel.log'

            return back()->with('error', 'Data gagal diperbarui. Silakan coba kembali.');
        }
    }

    public function destroy($id)
    {
        $this->checkOfficerPosition();

        $vaccine = Vaccine::findOrFail($id);

        $vaccine->delete();

        return redirect(url('/vaccine-data'))->with('success', 'Data berhasil dihapus.');
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
        $query = Vaccine::query();

        if ($print_criteria === 'Tanggal Masuk') {
            $query->whereBetween('entry_date', [$early_period, $final_period])
                ->orderBy('entry_date', 'asc');
        } elseif ($print_criteria === 'Tanggal Kedaluwarsa') {
            $query->whereBetween('expiry_date', [$early_period, $final_period])
                ->orderBy('expiry_date', 'asc');
        }

        // Eksekusi query
        $vaccines = $query->get();

        // Return view
        return view('dashboard.supply.vaccine.stock-report', compact('vaccines', 'early_period', 'final_period', 'print_criteria'));
    }
}
