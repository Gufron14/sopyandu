<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Api\LocationController;
use App\Http\Controllers\Controller;
use App\Models\Elderly;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class ElderlyController extends Controller
{
    // Fungsi untuk menghapus cache yang berkaitan dengan elderlies
    protected function clearElderlyCache()
    {
        $keys = [
            'elderlies',
        ];

        foreach ($keys as $key) {
            Cache::forget($key);
        }
    }

    public function index()
    {
        $cacheKey = 'elderlies';

        $elderlies = Cache::remember($cacheKey, 300, function () {
            return Elderly::orderBy('fullname', 'asc')->get();
        });

        return view('dashboard.master-data.elderly.index', compact('elderlies'));
    }

    public function show($id)
    {
        $elderly = Elderly::findOrFail($id);

        $province = $city = $subdistrict = $village = 'N/A';

        if ($elderly->province) {
            $provinces = LocationController::getProvincesStatic();
            $province = collect($provinces)->firstWhere('id', $elderly->province)['name'] ?? 'N/A';
        }

        if ($elderly->city) {
            $cities = LocationController::getCitiesStatic($elderly->province);
            $city = collect($cities)->firstWhere('id', $elderly->city)['name'] ?? 'N/A';
        }

        if ($elderly->subdistrict) {
            $districts = LocationController::getDistrictsStatic($elderly->city);
            $subdistrict = collect($districts)->firstWhere('id', $elderly->subdistrict)['name'] ?? 'N/A';
        }

        if ($elderly->village) {
            $villages = LocationController::getVillagesStatic($elderly->subdistrict);
            $village = collect($villages)->firstWhere('id', $elderly->village)['name'] ?? 'N/A';
        }

        return view('dashboard.master-data.elderly.show', compact('elderly', 'province', 'city', 'subdistrict', 'village'));
    }

    public function create()
    {
        return view('dashboard.master-data.elderly.create');
    }

    public function store(Request $request)
    {
        $rules = [
            'nik' => 'required|numeric|unique:elderlies,nik',
            'fullname' => 'required',
            'birth_place' => 'required',
            'date_of_birth' => 'required|date',
            'blood_type' => 'required',
            'gender' => 'required',
            'address' => 'required',
            'province' => 'required',
            'city' => 'required',
            'subdistrict' => 'required',
            'village' => 'required',
            'hamlet' => 'required',
        ];

        $messages = [
            'nik.required' => 'NIK wajib diisi.',
            'nik.numeric' => 'NIK harus berupa angka.',
            'nik.unique' => 'NIK sudah terdaftar.',
            'fullname.required' => 'Nama lengkap wajib diisi.',
            'birth_place.required' => 'Tempat lahir wajib diisi.',
            'date_of_birth.required' => 'Tanggal lahir wajib diisi.',
            'date_of_birth.date' => 'Tanggal lahir harus berupa tanggal yang valid.',
            'blood_type.required' => 'Golongan darah wajib dipilih.',
            'gender.required' => 'Jenis kelamin wajib dipilih.',
            'address.required' => 'Alamat wajib diisi.',
            'province.required' => 'Provinsi wajib dipilih.',
            'city.required' => 'Kota/Kabupaten wajib dipilih.',
            'subdistrict.required' => 'Kecamatan wajib dipilih.',
            'village.required' => 'Kelurahan/Desa wajib dipilih.',
            'hamlet.required' => 'Lingkungan/Dusun wajib diisi.',
        ];

        $data = $request->validate($rules, $messages);

        $data['date_of_birth'] = Carbon::parse($data['date_of_birth'])->format('Y-m-d');
        $data['hamlet'] = strtoupper($data['hamlet']);

        try {
            Elderly::create([
                'nik' => $data['nik'],
                'fullname' => $data['fullname'],
                'birth_place' => $data['birth_place'],
                'date_of_birth' => $data['date_of_birth'],
                'blood_type' => $data['blood_type'],
                'gender' => $data['gender'],
                'address' => $data['address'],
                'province' => $data['province'],
                'city' => $data['city'],
                'subdistrict' => $data['subdistrict'],
                'village' => $data['village'],
                'hamlet' => $data['hamlet'],
            ]);

            // Hapus chace
            $this->clearElderlyCache();

            return redirect(url('/elderly-data'))->with('success', "Data berhasil ditambahkan.");
        } catch (\Exception $e) {
            Log::error('Error: ' . $e->getMessage()); // Check 'storage/logs/laravel.log'

            return back()->with('error', 'Data gagal ditambahkan. Silakan coba kembali.');
        }
    }

    public function edit($id)
    {
        $elderly = Elderly::findOrFail($id);

        return view('dashboard.master-data.elderly.edit', compact('elderly'));
    }

    public function update(Request $request, $id)
    {
        $elderly = Elderly::findOrFail($id);

        $rules = [
            'nik' => $elderly->nik === $request->input('nik') ? 'required|numeric' : 'required|numeric|unique:elderlies,nik,' . $elderly->id,
            'fullname' => 'required',
            'birth_place' => 'required',
            'date_of_birth' => 'required|date',
            'blood_type' => 'required',
            'gender' => 'required',
            'address' => 'required',
            'province' => 'required',
            'city' => 'required',
            'subdistrict' => 'required',
            'village' => 'required',
            'hamlet' => 'required',
        ];

        $messages = [
            'nik.required' => 'NIK wajib diisi.',
            'nik.numeric' => 'NIK harus berupa angka.',
            'nik.unique' => 'NIK sudah terdaftar.',
            'fullname.required' => 'Nama lengkap wajib diisi.',
            'birth_place.required' => 'Tempat lahir wajib diisi.',
            'date_of_birth.required' => 'Tanggal lahir wajib diisi.',
            'date_of_birth.date' => 'Tanggal lahir harus berupa tanggal yang valid.',
            'blood_type.required' => 'Golongan darah wajib dipilih.',
            'gender.required' => 'Jenis kelamin wajib dipilih.',
            'address.required' => 'Alamat wajib diisi.',
            'province.required' => 'Provinsi wajib dipilih.',
            'city.required' => 'Kota/Kabupaten wajib dipilih.',
            'subdistrict.required' => 'Kecamatan wajib dipilih.',
            'village.required' => 'Kelurahan/Desa wajib dipilih.',
            'hamlet.required' => 'Lingkungan/Dusun wajib diisi.',
        ];

        $data = $request->validate($rules, $messages);

        $data['date_of_birth'] = Carbon::parse($data['date_of_birth'])->format('Y-m-d');
        $data['hamlet'] = strtoupper($data['hamlet']);

        try {
            $elderly->update([
                'nik' => $data['nik'],
                'fullname' => $data['fullname'],
                'birth_place' => $data['birth_place'],
                'date_of_birth' => $data['date_of_birth'],
                'blood_type' => $data['blood_type'],
                'gender' => $data['gender'],
                'address' => $data['address'],
                'province' => $data['province'],
                'city' => $data['city'],
                'subdistrict' => $data['subdistrict'],
                'village' => $data['village'],
                'hamlet' => $data['hamlet'],
            ]);

            // Hapus chace
            $this->clearElderlyCache();

            return redirect(url('/elderly-data'))->with('success', 'Data berhasil diperbarui.');
        } catch (\Exception $e) {
            Log::error('Error: ' . $e->getMessage()); // Check 'storage/logs/laravel.log'

            return back()->with('error', 'Data gagal diperbarui. Silakan coba kembali.');
        }
    }

    public function destroy($id)
    {
        $elderly = Elderly::findOrFail($id);

        $elderly->delete();

        // Hapus chace
        $this->clearElderlyCache();

        return redirect(url('/elderly-data'))->with('success', 'Data berhasil dihapus.');
    }
}
