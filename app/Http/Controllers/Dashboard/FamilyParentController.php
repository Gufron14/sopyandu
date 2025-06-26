<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Api\LocationController;
use App\Http\Controllers\Controller;
use App\Models\FamilyParent;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

class FamilyParentController extends Controller
{
    // Fungsi untuk menghapus cache yang berkaitan dengan family_parent
    protected function clearFamilyParentCache()
    {
        $statuses = ['all', 'active', 'not-active'];

        foreach ($statuses as $status) {
            $key = 'family_parents_with_users_' . $status;
            Cache::forget($key);
        }
    }

public function index(Request $request)
{
    $selectedStatus = $request->get('status', 'all');
    $selectedPregnancyStatus = $request->get('pregnancy_status', 'all');
    $selectedMonth = $request->get('month', 'all');
    $selectedYear = $request->get('year', 'all');

    // Cache key dinamis berdasarkan semua filter
    $cacheKey = 'family_parents_with_users_' . $selectedStatus . '_pregnancy_' . $selectedPregnancyStatus . '_month_' . $selectedMonth . '_year_' . $selectedYear;

    $parents = Cache::remember($cacheKey, 300, function () use ($selectedStatus, $selectedPregnancyStatus, $selectedMonth, $selectedYear) {
        $query = FamilyParent::with(['users' => function ($query) use ($selectedStatus) {
            $query->where('role', 'family_parent');

            if ($selectedStatus === 'active') {
                $query->whereNotNull('verified_at');
            } elseif ($selectedStatus === 'not-active') {
                $query->whereNull('verified_at');
            }
        }])
        ->whereHas('users', function ($query) use ($selectedStatus) {
            $query->where('role', 'family_parent');

            if ($selectedStatus === 'active') {
                $query->whereNotNull('verified_at');
            } elseif ($selectedStatus === 'not-active') {
                $query->whereNull('verified_at');
            }
        });

        // Filter berdasarkan status kehamilan
        if ($selectedPregnancyStatus === 'pregnant') {
            $query->where('is_pregnant', 'Hamil');
        } elseif ($selectedPregnancyStatus === 'not-pregnant') {
            $query->where('is_pregnant', 'Tidak Hamil');
        }

        // Filter berdasarkan bulan terdaftar
        if ($selectedMonth !== 'all') {
            $query->whereMonth('created_at', $selectedMonth);
        }

        // Filter berdasarkan tahun terdaftar
        if ($selectedYear !== 'all') {
            $query->whereYear('created_at', $selectedYear);
        }

        // Sorting berdasarkan status
        if ($selectedStatus === 'not-active') {
            $query->orderBy('created_at', 'asc');
        } else {
            $query->orderBy('mother_fullname', 'asc');
        }

        return $query->get();
    });

    // Data untuk dropdown
    $availableYears = FamilyParent::selectRaw('YEAR(created_at) as year')
        ->distinct()
        ->orderByDesc('year')
        ->pluck('year');

    $months = [
        'all' => 'Semua Bulan',
        '1' => 'Januari', '2' => 'Februari', '3' => 'Maret', '4' => 'April',
        '5' => 'Mei', '6' => 'Juni', '7' => 'Juli', '8' => 'Agustus',
        '9' => 'September', '10' => 'Oktober', '11' => 'November', '12' => 'Desember'
    ];

    $years = ['all' => 'Semua Tahun'] + $availableYears->mapWithKeys(function ($year) {
        return [$year => $year];
    })->toArray();

    // Label dropdown status
    $statuses = [
        'all' => 'Semua',
        'active' => 'Aktif',
        'not-active' => 'Belum Verifikasi',
    ];

    // Label dropdown status kehamilan
    $pregnancyStatuses = [
        'all' => 'Semua',
        'pregnant' => 'Hamil',
        'not-pregnant' => 'Tidak Hamil',
    ];

    return view('dashboard.master-data.parent.index', compact(
        'parents', 
        'statuses', 
        'selectedStatus',
        'pregnancyStatuses',
        'selectedPregnancyStatus',
        'months',
        'selectedMonth',
        'years',
        'selectedYear'
    ));
}

public function printReport(Request $request)
{
    $selectedStatus = $request->get('status', 'all');
    $selectedPregnancyStatus = $request->get('pregnancy_status', 'all');
    $selectedMonth = $request->get('month', 'all');
    $selectedYear = $request->get('year', 'all');

    $query = FamilyParent::with(['users' => function ($query) use ($selectedStatus) {
        $query->where('role', 'family_parent');

        if ($selectedStatus === 'active') {
            $query->whereNotNull('verified_at');
        } elseif ($selectedStatus === 'not-active') {
            $query->whereNull('verified_at');
        }
    }])
    ->whereHas('users', function ($query) use ($selectedStatus) {
        $query->where('role', 'family_parent');

        if ($selectedStatus === 'active') {
            $query->whereNotNull('verified_at');
        } elseif ($selectedStatus === 'not-active') {
            $query->whereNull('verified_at');
        }
    });

    // Filter berdasarkan status kehamilan
    if ($selectedPregnancyStatus === 'pregnant') {
        $query->where('is_pregnant', 'Hamil');
    } elseif ($selectedPregnancyStatus === 'not-pregnant') {
        $query->where('is_pregnant', 'Tidak Hamil');
    }

    // Filter berdasarkan bulan terdaftar
    if ($selectedMonth !== 'all') {
        $query->whereMonth('created_at', $selectedMonth);
    }

    // Filter berdasarkan tahun terdaftar
    if ($selectedYear !== 'all') {
        $query->whereYear('created_at', $selectedYear);
    }

    $parents = $query->orderBy('mother_fullname', 'asc')->get();

    return view('dashboard.master-data.parent.print', compact(
        'parents',
        'selectedStatus',
        'selectedPregnancyStatus',
        'selectedMonth',
        'selectedYear'
    ));
}




    public function show($id)
    {
        $parent = FamilyParent::findOrFail($id);
        $user = $parent->users()->first();

        $province = $city = $subdistrict = $village = 'N/A';

        if ($parent->province) {
            $provinces = LocationController::getProvincesStatic();
            $province = collect($provinces)->firstWhere('id', $parent->province)['name'] ?? 'N/A';
        }

        if ($parent->city) {
            $cities = LocationController::getCitiesStatic($parent->province);
            $city = collect($cities)->firstWhere('id', $parent->city)['name'] ?? 'N/A';
        }

        if ($parent->subdistrict) {
            $districts = LocationController::getDistrictsStatic($parent->city);
            $subdistrict = collect($districts)->firstWhere('id', $parent->subdistrict)['name'] ?? 'N/A';
        }

        if ($parent->village) {
            $villages = LocationController::getVillagesStatic($parent->subdistrict);
            $village = collect($villages)->firstWhere('id', $parent->village)['name'] ?? 'N/A';
        }

        return view('dashboard.master-data.parent.show', compact('parent', 'user', 'province', 'city', 'subdistrict', 'village'));
    }

    public function create()
    {
        $isAdmin = Auth::user() && Auth::user()->role === 'admin';

        if (!$isAdmin) {
            abort(403, 'Unauthorized');
        }

        return view('dashboard.master-data.parent.create');
    }

    public function store(Request $request)
    {
        $isAdmin = Auth::user() && Auth::user()->role === 'admin';

        if (!$isAdmin) {
            abort(403, 'Unauthorized');
        }

        $rules = [
            'nik' => 'required|numeric|unique:family_parents,nik',
            'mother_fullname' => 'required',
            'mother_birth_place' => 'required',
            'mother_date_of_birth' => 'required|date',
            'mother_blood_type' => 'required',
            'father_fullname' => 'required',
            'father_nik' => 'required',
            'father_birth_place' => 'required',
            'father_date_of_birth' => 'required|date',
            'father_blood_type' => 'required',
            'is_pregnant' => 'required',
            'number_of_children' => 'required|numeric',
            'address' => 'required',
            'province' => 'required',
            'city' => 'required',
            'subdistrict' => 'required',
            'village' => 'required',
            'hamlet' => 'required',
            'username' => 'required|unique:users,username',
            'phone_number' => [
                'required',
                'unique:users,phone_number',
                // Validasi tambahan
                function ($attribute, $value, $fail) {
                    if (!(substr($value, 0, 4) === '+628' || substr($value, 0, 2) === '08')) {
                        $fail('Nomor HP/WA tidak valid.');
                    }
                },
            ],
        ];

        $messages = [
            'nik.required' => 'NIK wajib diisi.',
            'nik.numeric' => 'NIK harus berupa angka.',
            'nik.unique' => 'NIK sudah terdaftar.',
            'mother_fullname.required' => 'Nama lengkap ibu wajib diisi.',
            'mother_birth_place.required' => 'Tempat lahir ibu wajib diisi.',
            'mother_date_of_birth.required' => 'Tanggal lahir ibu wajib diisi.',
            'mother_date_of_birth.date' => 'Tanggal lahir ibu harus berupa tanggal yang valid',
            'mother_blood_type.required' => 'Golongan darah ibu wajib dipilih.',
            'father_nik.required' => 'NIK ayah wajib diisi.',
            'father_fullname.required' => 'Nama lengkap ayah wajib diisi.',
            'father_birth_place.required' => 'Tempat lahir ayah wajib diisi.',
            'father_date_of_birth.required' => 'Tanggal lahir ayah wajib diisi.',
            'father_date_of_birth.date' => 'Tanggal lahir ayah harus berupa tanggal yang valid',
            'father_blood_type.required' => 'Golongan darah ayah wajib dipilih.',
            'is_pregnant.required' => 'Status kehamilan wajib dipilih.',
            'number_of_children.required' => 'Jumlah anak wajib diisi.',
            'number_of_children.numeric' => 'Jumlah anak harus berupa angka.',
            'address.required' => 'Alamat wajib diisi.',
            'province.required' => 'Provinsi wajib dipilih.',
            'city.required' => 'Kota/Kabupaten wajib dipilih.',
            'subdistrict.required' => 'Kecamatan wajib dipilih.',
            'village.required' => 'Kelurahan/Desa wajib dipilih.',
            'hamlet.required' => 'Lingkungan/Dusun wajib diisi.',
            'username.required' => 'Nama pengguna wajib diisi.',
            'username.unique' => 'Nama pengguna sudah digunakan.',
            'phone_number.required' => 'Nomor HP/WA wajib diisi.',
            'phone_number.unique' => 'Nomor HP/WA sudah terdaftar.',
        ];

        // Konversi nomor '08' ke '+628' (sebelum validasi)
        if (substr($request->input('phone_number'), 0, 2) === '08') {
            $request->merge([
                'phone_number' => '+62' . substr($request->input('phone_number'), 1),
            ]);
        }

        $data = $request->validate($rules, $messages);

        $data['mother_date_of_birth'] = Carbon::parse($data['mother_date_of_birth'])->format('Y-m-d');
        $data['father_date_of_birth'] = Carbon::parse($data['father_date_of_birth'])->format('Y-m-d');
        $data['hamlet'] = strtoupper($data['hamlet']);

        try {
            $parent = FamilyParent::create([
                'nik' => $data['nik'],
                'mother_fullname' => $data['mother_fullname'],
                'mother_birth_place' => $data['mother_birth_place'],
                'mother_date_of_birth' => $data['mother_date_of_birth'],
                'mother_blood_type' => $data['mother_blood_type'],
                'father_fullname' => $data['father_fullname'],
                'father_nik' => $data['father_nik'],
                'father_date_of_birth' => $data['father_date_of_birth'],
                'father_blood_type' => $data['father_blood_type'],
                'is_pregnant' => $data['is_pregnant'],
                'number_of_children' => $data['number_of_children'],
                'address' => $data['address'],
                'province' => $data['province'],
                'city' => $data['city'],
                'subdistrict' => $data['subdistrict'],
                'village' => $data['village'],
                'hamlet' => $data['hamlet'],
            ]);

            User::create([
                'username' => $data['nik'],
                'password' => bcrypt($data['nik']),
                'phone_number' => $data['phone_number'],
                'role' => 'family_parent',
                'parent_id' => $parent->id,
                'verified_at' => now(),
            ]);

            // Hapus chace
            $this->clearFamilyParentCache();

            return redirect(url('/parent-data'))->with('success', "Data berhasil ditambahkan. Nama pengguna dan kata sandi menggunakan NIK: {$data['nik']}");
        } catch (\Exception $e) {
            Log::error('Error: ' . $e->getMessage()); // Check 'storage/logs/laravel.log'

            if (isset($parent)) {
                $parent->delete();
            }
            if (isset($user)) {
                $user->delete();
            }

            return back()->with('error', 'Data gagal ditambahkan. Silakan coba kembali.');
        }
    }

    public function edit($id)
    {
        $villageHeadAndNeighborhoodHead = (Auth::user() && Auth::user()->officers->position === 'Lurah') || Auth::user()->officers->position === 'Kepala Lingkungan';

        // Periksa apakah posisi pejabat adalah 'Lurah' atau 'Kepala Lingkungan'
        if ($villageHeadAndNeighborhoodHead) {
            abort(403, 'Unauthorized');
        }

        $parent = FamilyParent::findOrFail($id);
        $user = $parent->users()->first();

        return view('dashboard.master-data.parent.edit', compact('parent', 'user'));
    }

    public function update(Request $request, $id)
    {
        $parent = FamilyParent::findOrFail($id);
        $user = $parent->users()->first();

        $rules = [
            'nik' => $parent->nik === $request->input('nik') ? 'required|numeric' : 'required|numeric|unique:family_parents,nik,' . $parent->id,
            'mother_fullname' => 'required',
            'mother_birth_place' => 'required',
            'mother_date_of_birth' => 'required|date',
            'mother_blood_type' => 'required',
            'father_nik' => 'required',
            'father_fullname' => 'required',
            'father_birth_place' => 'required',
            'father_date_of_birth' => 'required|date',
            'father_blood_type' => 'required',
            'is_pregnant' => 'required',
            'number_of_children' => 'required|numeric',
            'address' => 'required',
            'province' => 'required',
            'city' => 'required',
            'subdistrict' => 'required',
            'village' => 'required',
            'hamlet' => 'required',
            'username' => $user->username === $request->input('username') ? 'required' : 'required|unique:users,username,' . $user->id,
            'phone_number' => array_filter([
                'required',
                $user->phone_number !== $request->input('phone_number') ? 'unique:users,phone_number,' . $user->id : null,
                // Validasi tambahan
                function ($attribute, $value, $fail) {
                    if (!(substr($value, 0, 4) === '+628' || substr($value, 0, 2) === '08')) {
                        $fail('Nomor HP/WA tidak valid.');
                    }
                },
            ]),
            'status' => 'required',
        ];

        $messages = [
            'nik.required' => 'NIK wajib diisi.',
            'nik.numeric' => 'NIK harus berupa angka.',
            'nik.unique' => 'NIK sudah terdaftar.',
            'mother_fullname.required' => 'Nama lengkap ibu wajib diisi.',
            'mother_birth_place.required' => 'Tempat lahir ibu wajib diisi.',
            'mother_date_of_birth.required' => 'Tanggal lahir ibu wajib diisi.',
            'mother_date_of_birth.date' => 'Tanggal lahir ibu harus berupa tanggal yang valid.',
            'mother_blood_type.required' => 'Golongan darah ibu wajib dipilih.',
            'father_nik.required' => 'NIK ayah wajib diisi.',
            'father_fullname.required' => 'Nama lengkap ayah wajib diisi.',
            'father_birth_place.required' => 'Tempat lahir ayah wajib diisi.',
            'father_date_of_birth.required' => 'Tanggal lahir ayah wajib diisi.',
            'father_date_of_birth.date' => 'Tanggal lahir ayah harus berupa tanggal yang valid.',
            'father_blood_type.required' => 'Golongan darah ayah wajib dipilih.',
            'is_pregnant.required' => 'Status kehamilan wajib dipilih.',
            'number_of_children.required' => 'Jumlah anak wajib diisi.',
            'number_of_children.numeric' => 'Jumlah anak harus berupa angka.',
            'address.required' => 'Alamat wajib diisi.',
            'province.required' => 'Provinsi wajib dipilih.',
            'city.required' => 'Kota/Kabupaten wajib dipilih.',
            'subdistrict.required' => 'Kecamatan wajib dipilih.',
            'village.required' => 'Kelurahan/Desa wajib dipilih.',
            'hamlet.required' => 'Lingkungan/Dusun wajib diisi.',
            'username.required' => 'Nama pengguna wajib diisi.',
            'username.unique' => 'Nama pengguna sudah digunakan.',
            'phone_number.required' => 'Nomor HP/WA wajib diisi.',
            'phone_number.unique' => 'Nomor HP/WA sudah terdaftar.',
            'status' => 'Status akun wajib dipilih.',
        ];

        // Konversi nomor '08' ke '+628' (sebelum validasi)
        if (substr($request->input('phone_number'), 0, 2) === '08') {
            $request->merge([
                'phone_number' => '+62' . substr($request->input('phone_number'), 1),
            ]);
        }

        $data = $request->validate($rules, $messages);

        $data['mother_date_of_birth'] = Carbon::parse($data['mother_date_of_birth'])->format('Y-m-d');
        $data['father_date_of_birth'] = Carbon::parse($data['father_date_of_birth'])->format('Y-m-d');
        $data['hamlet'] = strtoupper($data['hamlet']);

        try {
            $parent->update([
                'nik' => $data['nik'],
                'mother_fullname' => $data['mother_fullname'],
                'mother_birth_place' => $data['mother_birth_place'],
                'mother_date_of_birth' => $data['mother_date_of_birth'],
                'mother_blood_type' => $data['mother_blood_type'],
                'father_nik' => $data['father_nik'],
                'father_fullname' => $data['father_fullname'],
                'father_birth_place' => $data['father_birth_place'],
                'father_date_of_birth' => $data['father_date_of_birth'],
                'father_blood_type' => $data['father_blood_type'],
                'is_pregnant' => $data['is_pregnant'],
                'number_of_children' => $data['number_of_children'],
                'address' => $data['address'],
                'province' => $data['province'],
                'city' => $data['city'],
                'subdistrict' => $data['subdistrict'],
                'village' => $data['village'],
                'hamlet' => $data['hamlet'],
            ]);

            if ($data['status'] === 'Aktif') {
                $verified_at = now();
            } else {
                $verified_at = null;
            }

            $user->update([
                'username' => $data['username'],
                'phone_number' => $data['phone_number'],
                'role' => 'family_parent',
                'verified_at' => $verified_at,
            ]);

            // Hapus chace
            $this->clearFamilyParentCache();

            return redirect(url('/parent-data'))->with('success', 'Data berhasil diperbarui.');
        } catch (\Exception $e) {
            Log::error('Error: ' . $e->getMessage()); // Check 'storage/logs/laravel.log'

            return back()->with('error', 'Data gagal diperbarui. Silakan coba kembali.');
        }
    }

    public function destroy($id)
    {
        $parent = FamilyParent::findOrFail($id);

        $parent->familyChildren()->delete();

        $parent->users()->delete();

        $parent->delete();

        // Hapus chace
        $this->clearFamilyParentCache();

        return redirect(url('/parent-data'))->with('success', 'Data berhasil dihapus.');
    }

    public function verifyAccount($id)
    {
        $parent = FamilyParent::findOrFail($id);

        $parent->users()->each(function ($user) use ($parent) {
            $user->update([
                'verified_at' => now(),
            ]);
        });

        $whatsapp_urls = $parent->users->map(function ($user) use ($parent) {
            $message = "Hai {$parent->mother_fullname}, akun Anda sudah diverifikasi. Silakan masuk ke SIDU dengan nama pengguna dan kata sandi yang telah Anda buat. Terima kasih dan Salam Sehat!";
            $message_urlencode = urlencode($message);
            return "https://wa.me/{$user->phone_number}?text={$message_urlencode}";
        });

        $whatsapp_url = $whatsapp_urls->first();

        // Hapus chace
        $this->clearFamilyParentCache();

        return redirect(url('/parent-data'))->with('success', 'Akun berhasil diverifikasi')->with('whatsapp_url', $whatsapp_url);
    }

    public function resetAccount($id)
    {
        $parent = FamilyParent::findOrFail($id);

        $parent->users()->each(function ($user) use ($parent) {
            $user->update([
                'username' => $parent->nik,
                'password' => bcrypt($parent->nik),
            ]);
        });

        $whatsapp_urls = $parent->users->map(function ($user) use ($parent) {
            $message = "Hai {$parent->mother_fullname}, akun Anda sudah direset. Silakan masuk ke SIDU dengan nama pengguna dan kata sandi menggunakan NIK: {$parent->nik}. Terima kasih dan Salam Sehat!";
            $message_urlencode = urlencode($message);
            return "https://wa.me/{$user->phone_number}?text={$message_urlencode}";
        });

        $whatsapp_url = $whatsapp_urls->first();

        // Hapus chace
        $this->clearFamilyParentCache();

        return redirect(url('/parent-data'))->with('success', 'Akun berhasil direset')->with('whatsapp_url', $whatsapp_url);
    }

    public function editProfile()
    {
        $user = Auth::user();
        $parent = FamilyParent::whereHas('users', function ($query) use ($user) {
            $query->where('id', $user->id);
        })->firstOrFail();

        return view('dashboard.master-data.parent.profile', compact('parent', 'user'));
    }

    public function updateProfile(Request $request)
    {
        $user = Auth::user();
        $parent = FamilyParent::whereHas('users', function ($query) use ($user) {
            $query->where('id', $user->id);
        })->firstOrFail();

        $user = $parent->users()->first();

        $rules = [
            'nik' => $parent->nik === $request->input('nik') ? 'required|numeric' : 'required|numeric|unique:family_parents,nik,' . $parent->id,
            'mother_fullname' => 'required',
            'mother_birth_place' => 'required',
            'mother_date_of_birth' => 'required|date',
            'mother_blood_type' => 'required',
            'father_fullname' => 'required',
            'father_nik' => 'required',
            'father_birth_place' => 'required',
            'father_date_of_birth' => 'required|date',
            'father_blood_type' => 'required',
            'is_pregnant' => 'required',
            'number_of_children' => 'required|numeric',
            'address' => 'required',
            'province' => 'required',
            'city' => 'required',
            'subdistrict' => 'required',
            'village' => 'required',
            'hamlet' => 'required',
            'username' => $user->username === $request->input('username') ? 'required' : 'required|unique:users,username,' . $user->id,
            'phone_number' => array_filter([
                'required',
                $user->phone_number !== $request->input('phone_number') ? 'unique:users,phone_number,' . $user->id : null,
                // Validasi tambahan
                function ($attribute, $value, $fail) {
                    if (!(substr($value, 0, 4) === '+628' || substr($value, 0, 2) === '08')) {
                        $fail('Nomor HP/WA tidak valid.');
                    }
                },
            ]),
            'password_old' => 'nullable',
            'password' => 'nullable|confirmed|min:8',
            'password_confirmation' => 'nullable|same:password|min:8',
        ];

        $messages = [
            'nik.required' => 'NIK wajib diisi.',
            'nik.numeric' => 'NIK harus berupa angka.',
            'nik.unique' => 'NIK sudah terdaftar.',
            'mother_fullname.required' => 'Nama lengkap ibu wajib diisi.',
            'mother_birth_place.required' => 'Tempat lahir ibu wajib diisi.',
            'mother_date_of_birth.required' => 'Tanggal lahir ibu wajib diisi.',
            'mother_date_of_birth.date' => 'Tanggal lahir ibu harus berupa tanggal yang valid.',
            'mother_blood_type.required' => 'Golongan darah ibu wajib dipilih.',
            'father_fullname.required' => 'Nama lengkap ayah wajib diisi.',
            'father_nik.required' => 'NIK ayah wajib diisi.',
            'father_birth_place.required' => 'Tempat lahir ayah wajib diisi.',
            'father_date_of_birth.required' => 'Tanggal lahir ayah wajib diisi.',
            'father_date_of_birth.date' => 'Tanggal lahir ayah harus berupa tanggal yang valid.',
            'father_blood_type.required' => 'Golongan darah ayah wajib dipilih.',
            'is_pregnant.required' => 'Status kehamilan wajib dipilih.',
            'number_of_children.required' => 'Jumlah anak wajib diisi.',
            'number_of_children.numeric' => 'Jumlah anak harus berupa angka.',
            'address.required' => 'Alamat wajib diisi.',
            'province.required' => 'Provinsi wajib dipilih.',
            'city.required' => 'Kota/Kabupaten wajib dipilih.',
            'subdistrict.required' => 'Kecamatan wajib dipilih.',
            'village.required' => 'Kelurahan/Desa wajib dipilih.',
            'hamlet.required' => 'Lingkungan/Dusun wajib diisi.',
            'username.required' => 'Nama pengguna wajib diisi.',
            'username.unique' => 'Nama pengguna sudah digunakan.',
            'phone_number.required' => 'Nomor HP/WA wajib diisi.',
            'phone_number.unique' => 'Nomor HP/WA sudah terdaftar.',
            'password.min' => 'Kata sandi baru minimal 8 karakter.',
            'password.confirmed' => 'Konfirmasi kata sandi baru tidak sesuai.',
            'password_confirmation.same' => 'Konfirmasi kata sandi baru tidak sesuai.',
            'password_confirmation.min' => 'Konfirmasi kata sandi baru minimal 8 karakter.',
        ];

        // Konversi nomor '08' ke '+628' (sebelum validasi)
        if (substr($request->input('phone_number'), 0, 2) === '08') {
            $request->merge([
                'phone_number' => '+62' . substr($request->input('phone_number'), 1),
            ]);
        }

        $data = $request->validate($rules, $messages);

        if ($request->filled('password_old') && $request->filled('password') && $request->filled('password_confirmation')) {
            if (!Hash::check($request->password_old, $user->password)) {
                return redirect()
                    ->back()
                    ->withErrors(['password_old' => 'Kata sandi lama salah.']);
            }

            $user->password = Hash::make($data['password']);
        }

        $data['mother_date_of_birth'] = Carbon::parse($data['mother_date_of_birth'])->format('Y-m-d');
        $data['father_date_of_birth'] = Carbon::parse($data['father_date_of_birth'])->format('Y-m-d');
        $data['hamlet'] = strtoupper($data['hamlet']);

        try {
            $parent->update([
                'nik' => $data['nik'],
                'mother_fullname' => $data['mother_fullname'],
                'mother_birth_place' => $data['mother_birth_place'],
                'mother_date_of_birth' => $data['mother_date_of_birth'],
                'mother_blood_type' => $data['mother_blood_type'],
                'father_fullname' => $data['father_fullname'],
                'father_nik' => $data['father_nik'],
                'father_birth_place' => $data['father_birth_place'],
                'father_date_of_birth' => $data['father_date_of_birth'],
                'father_blood_type' => $data['father_blood_type'],
                'is_pregnant' => $data['is_pregnant'],
                'number_of_children' => $data['number_of_children'],
                'address' => $data['address'],
                'province' => $data['province'],
                'city' => $data['city'],
                'subdistrict' => $data['subdistrict'],
                'village' => $data['village'],
                'hamlet' => $data['hamlet'],
            ]);

            $user->update([
                'username' => $data['username'],
                'phone_number' => $data['phone_number'],
            ]);

            if (isset($user->password)) {
                $user->save();
            }

            // Hapus chace
            $this->clearFamilyParentCache();

            return redirect(url('/parent-profile'))->with('success', 'Profil berhasil diperbarui.');
        } catch (\Exception $e) {
            Log::error('Error: ' . $e->getMessage()); // Check 'storage/logs/laravel.log'

            return back()->with('error', 'Profil gagal diperbarui. Silakan coba kembali.');
        }
    }

    public function getUnverifiedParents()
    {
        $isAdmin = Auth::user() && Auth::user()->role === 'admin';

        if (!$isAdmin) {
            abort(403, 'Unauthorized');
        }

        $allCount = FamilyParent::whereHas('users', function ($query) {
            $query->where('role', 'family_parent')->whereNull('verified_at');
        })->count();

        $latestParents = FamilyParent::whereHas('users', function ($query) {
            $query->where('role', 'family_parent')->whereNull('verified_at');
        })
            ->orderBy('created_at', 'desc')
            ->take(3)
            ->get();

        $data = $latestParents->map(function ($parent) {
            return [
                'name' => $parent->mother_fullname ?? '-',
                'time' => Carbon::parse($parent->created_at)->locale('id')->diffForHumans(),
            ];
        });

        return response()->json([
            'count' => $allCount,
            'data' => $data,
        ]);
    }
}
