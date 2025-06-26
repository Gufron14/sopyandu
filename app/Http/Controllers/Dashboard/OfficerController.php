<?php

namespace App\Http\Controllers\Dashboard;

use Carbon\Carbon;
use App\Models\User;
use App\Models\Officer;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Cache;

class OfficerController extends Controller
{
    // Fungsi untuk menghapus cache berdasarkan semua kombinasi role
    protected function clearOfficerCache()
    {
        $roles = ['admin', 'midwife', 'officer', 'village_head'];

        $roleCombinations = $this->getRoleCombinations($roles);

        foreach ($roleCombinations as $combo) {
            sort($combo);
            $key = 'officers_by_role_' . implode('_', $combo);
            Cache::forget($key);
        }
    }

    // Fungsi helper untuk menghasilkan semua kombinasi peran
    protected function getRoleCombinations($roles)
    {
        $results = [];
        $total = pow(2, count($roles));

        for ($i = 1; $i < $total; $i++) {
            $subset = [];
            for ($j = 0; $j < count($roles); $j++) {
                if ($i & (1 << $j)) {
                    $subset[] = $roles[$j];
                }
            }
            $results[] = $subset;
        }

        return $results;
    }

    // Fungsi umum untuk mengambil data officer berdasarkan peran
    protected function getOfficersByRole($roles)
    {
        sort($roles); // Supaya konsisten dengan cache key
        $cacheKey = 'officers_by_role_' . implode('_', $roles);

        return Cache::remember($cacheKey, 300, function () use ($roles) {
            return Officer::with(['users']) // Eager load relasi 'users'
                ->whereHas('users', function ($query) use ($roles) {
                    $query->whereIn('role', $roles);
                })
                ->orderBy('fullname', 'asc')
                ->get();
        });
    }

    public function index()
    {
        $all_data = $this->getOfficersByRole(['admin', 'midwife', 'officer', 'village_head']);
        return view('dashboard.master-data.officer.index', compact('all_data'));
    }

    public function adminData()
    {
        $admins = $this->getOfficersByRole(['admin']);
        return view('dashboard.master-data.officer.admin-data', compact('admins'));
    }

    public function midwifeData()
    {
        $midwives = $this->getOfficersByRole(['midwife']);
        return view('dashboard.master-data.officer.midwife-data', compact('midwives'));
    }

public function officerData()
{
    $officers = $this->getOfficersByRole(['officer', 'village_head']);

    // Filter hanya untuk Tenaga Medis Puskesmas
    $officers = $officers->filter(function ($officer) {
        return $officer->position === 'Tenaga Medis Puskesmas/Desa';
    });

    return view('dashboard.master-data.officer.officer-data', compact('officers'));
}


    public function show($id)
    {
        $officer = Officer::findOrFail($id);
        $user = $officer->users()->first();

        return view('dashboard.master-data.officer.show', compact('officer', 'user'));
    }

    public function create()
    {
        return view('dashboard.master-data.officer.create');
    }

    public function store(Request $request)
    {
        $rules = [
            'nik' => 'nullable',
            'fullname' => 'required',
            'birth_place' => 'required',
            'date_of_birth' => 'required|date',
            'gender' => 'required',
            'position' => 'required',
            'address' => 'required',
            'last_education' => 'required',
            'username' => 'nullable',
            'phone_number' =>
            'nullable',
            // 'unique:users,phone_number',
            // Validasi tambahan
            // function ($attribute, $value, $fail) {
            //     if (!(substr($value, 0, 4) === '+628' || substr($value, 0, 2) === '08')) {
            //         $fail('Nomor HP/WA tidak valid.');
            //     }
            // }
        ];

        $messages = [
            'nik.required' => 'NIK wajib diisi.',
            'nik.numeric' => 'NIK harus berupa angka.',
            'nik.unique' => 'NIK sudah terdaftar.',
            'fullname.required' => 'Nama lengkap wajib diisi.',
            'birth_place.required' => 'Tempat lahir wajib diisi.',
            'date_of_birth.required' => 'Tanggal lahir wajib diisi.',
            'date_of_birth.date' => 'Tanggal lahir harus berupa tanggal yang valid.',
            'gender.required' => 'Jenis kelamin wajib dipilih.',
            'position.required' => 'Jabatan wajib dipilih.',
            'address.required' => 'Alamat wajib diisi.',
            'last_education.required' => 'Pendidikan terkahir wajib diisi.',
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
        $datarandom = Str::random(8);

        $data['date_of_birth'] = Carbon::parse($data['date_of_birth'])->format('Y-m-d');

        try {
            $officer = Officer::create([
                'nik' => $datarandom, // Gunakan NIK acak sebagai default
                'fullname' => $data['fullname'],
                'birth_place' => $data['birth_place'],
                'date_of_birth' => $data['date_of_birth'],
                'gender' => $data['gender'],
                'position' => $data['position'],
                'address' => $data['address'],
                'last_education' => $data['last_education'],
            ]);

            switch ($officer->position) {
                case 'Admin':
                    $role = 'admin';
                    $path = url('/admin/officer-data');
                    break;
                case 'Bidan':
                    $role = 'midwife';
                    $path = url('/midwife/officer-data');
                    break;
                default:
                    $role = 'officer';
                    $path = url('/officer/officer-data');
                    break;
            }

            User::create([
                'username' => $datarandom,
                'password' => bcrypt($datarandom),
                'phone_number' => $data['phone_number'],
                'role' => $role,
                'officer_id' => $officer->id,
                'verified_at' => now(),
            ]);

            // Hapus chace
            $this->clearOfficerCache();

            return redirect($path)->with('success', "Data berhasil ditambahkan. Nama pengguna dan kata sandi menggunakan NIK: {$data['nik']}");
        } catch (\Exception $e) {
            Log::error('Error: ' . $e->getMessage()); // Check 'storage/logs/laravel.log'

            if (isset($officer)) $officer->delete();
            if (isset($user)) $user->delete();

            return back()->with('error', 'Data gagal ditambahkan. Silakan coba kembali.');
        }
    }

    public function edit($id)
    {
        $officer = Officer::findOrFail($id);
        $user = $officer->users()->first();

        return view('dashboard.master-data.officer.edit', compact('officer', 'user'));
    }

    public function update(Request $request, $id)
    {
        $officer = Officer::findOrFail($id);
        $user = $officer->users()->first();

        $rules = [
            'nik' => $officer->nik === $request->input('nik') ? 'nullable' : 'nullable' . $officer->id,
            'fullname' => 'required',
            'birth_place' => 'required',
            'date_of_birth' => 'required|date',
            'gender' => 'required',
            'position' => 'required',
            'address' => 'required',
            'last_education' => 'required',
            'username' => $user->username === $request->input('username') ? 'nullable' : 'nullable' . $user->id,
            'phone_number' => array_filter([
                'nullable',
                $user->phone_number !== $request->input('phone_number') ? 'nullable,' . $user->id : null,
                // Validasi tambahan
                function ($attribute, $value, $fail) {
                    if (!(substr($value, 0, 4) === '+628' || substr($value, 0, 2) === '08')) {
                        $fail('Nomor HP/WA tidak valid.');
                    }
                }
            ]),
            'status' => 'required',
        ];

        $messages = [
            'nik.required' => 'NIK wajib diisi.',
            'nik.numeric' => 'NIK harus berupa angka.',
            'nik.unique' => 'NIK sudah terdaftar.',
            'fullname.required' => 'Nama lengkap wajib diisi.',
            'birth_place.required' => 'Tempat lahir wajib diisi.',
            'date_of_birth.required' => 'Tanggal lahir wajib diisi.',
            'date_of_birth.date' => 'Tanggal lahir harus berupa tanggal yang valid.',
            'gender.required' => 'Jenis kelamin wajib dipilih.',
            'position.required' => 'Jabatan wajib dipilih.',
            'address.required' => 'Alamat wajib diisi.',
            'last_education.required' => 'Pendidikan terkahir wajib diisi.',
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

        $data['date_of_birth'] = Carbon::parse($data['date_of_birth'])->format('Y-m-d');

        try {
            $officer->update([
                'nik' => $data['nik'],
                'fullname' => $data['fullname'],
                'birth_place' => $data['birth_place'],
                'date_of_birth' => $data['date_of_birth'],
                'gender' => $data['gender'],
                'position' => $data['position'],
                'address' => $data['address'],
                'last_education' => $data['last_education'],
            ]);

            if ($data['status'] === 'Aktif') {
                $verified_at = now();
            } else {
                $verified_at = null;
            }

            switch ($officer->position) {
                case 'Admin':
                    $role = 'admin';
                    $path = url('/admin/officer-data');
                    break;
                case 'Bidan':
                    $role = 'midwife';
                    $path = url('/midwife/officer-data');
                    break;
                default:
                    $role = 'officer';
                    $path = url('/officer/officer-data');
                    break;
            }

            $user->update([
                'username' => $data['username'],
                'phone_number' => $data['phone_number'],
                'role' => $role,
                'verified_at' => $verified_at,
            ]);

            // Hapus chace
            $this->clearOfficerCache();

            return redirect($path)->with('success', 'Data berhasil diperbarui.');
        } catch (\Exception $e) {
            Log::error('Error: ' . $e->getMessage()); // Check 'storage/logs/laravel.log'

            return back()->with('error', 'Data gagal diperbarui. Silakan coba kembali.');
        }
    }

    public function destroy($id)
    {
        $officer = Officer::findOrFail($id);

        $officer->users()->delete();

        switch ($officer->position) {
            case 'Admin':
                $path = url('/admin/officer-data');
                break;
            case 'Bidan':
                $path = url('/midwife/officer-data');
                break;
            default:
                $path = url('/officer/officer-data');
                break;
        }

        $officer->delete();

        // Hapus chace
        $this->clearOfficerCache();

        return redirect($path)->with('success', 'Data berhasil dihapus.');
    }

    public function resetAccount($id)
    {
        $officer = Officer::findOrFail($id);

        $officer->users()->each(function ($user) use ($officer) {
            $user->update([
                'username' => $officer->nik,
                'password' => bcrypt($officer->nik),
            ]);
        });

        $whatsapp_urls = $officer->users->map(function ($user) use ($officer) {
            $message = "Hai {$officer->fullname}, akun Anda sudah direset. Silakan masuk ke SIDU dengan nama pengguna dan kata sandi menggunakan NIK: {$officer->nik}. Terima kasih dan Salam Sehat!";
            $message_urlencode = urlencode($message);
            return "https://wa.me/{$user->phone_number}?text={$message_urlencode}";
        });

        $whatsapp_url = $whatsapp_urls->first();

        switch ($officer->position) {
            case 'Admin':
                $path = url('/admin/officer-data');
                break;
            case 'Bidan':
                $path = url('/midwife/officer-data');
                break;
            default:
                $path = url('/officer/officer-data');
                break;
        }

        // Hapus chace
        $this->clearOfficerCache();

        return redirect($path)
            ->with('success', 'Akun berhasil direset')
            ->with('whatsapp_url', $whatsapp_url);
    }

    public function editProfile()
    {
        $user = Auth::user();
        $officer = Officer::whereHas('users', function ($query) use ($user) {
            $query->where('id', $user->id);
        })->firstOrFail();

        return view('dashboard.master-data.officer.profile', compact('officer', 'user'));
    }

    public function updateProfile(Request $request)
    {
        $user = Auth::user();
        $officer = Officer::whereHas('users', function ($query) use ($user) {
            $query->where('id', $user->id);
        })->firstOrFail();

        $user = $officer->users()->first();

        $rules = [
            'nik' => $officer->nik === $request->input('nik') ? 'required|numeric' : 'required|numeric|unique:officers,nik,' . $officer->id,
            'fullname' => 'required',
            'birth_place' => 'required',
            'date_of_birth' => 'required|date',
            'gender' => 'required',
            'address' => 'required',
            'last_education' => 'required',
            'username' => $user->username === $request->input('username') ? 'nullable' : 'nullable|unique:users,username,' . $user->id,
            'phone_number' => array_filter([
                'nullable',
                $user->phone_number !== $request->input('phone_number') ? 'unique:users,phone_number,' . $user->id : null,
                // Validasi tambahan
                function ($attribute, $value, $fail) {
                    if (!(substr($value, 0, 4) === '+628' || substr($value, 0, 2) === '08')) {
                        $fail('Nomor HP/WA tidak valid.');
                    }
                }
            ]),
            'password_old' => 'nullable',
            'password' => 'nullable|confirmed|min:8',
            'password_confirmation' => 'nullable|same:password|min:8',
        ];

        $messages = [
            'nik.required' => 'NIK wajib diisi.',
            'nik.numeric' => 'NIK harus berupa angka.',
            'nik.unique' => 'NIK sudah terdaftar.',
            'fullname.required' => 'Nama lengkap wajib diisi.',
            'birth_place.required' => 'Tempat lahir wajib diisi.',
            'date_of_birth.required' => 'Tanggal lahir wajib diisi.',
            'date_of_birth.date' => 'Tanggal lahir harus berupa tanggal yang valid.',
            'gender.required' => 'Jenis kelamin wajib dipilih.',
            'address.required' => 'Alamat wajib diisi.',
            'last_education.required' => 'Pendidikan terakhir wajib diisi.',
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
                return redirect()->back()->withErrors(['password_old' => 'Kata sandi lama salah.']);
            }

            $user->password = Hash::make($data['password']);
        }

        $data['date_of_birth'] = Carbon::parse($data['date_of_birth'])->format('Y-m-d');

        try {
            $officer->update([
                'nik' => $data['nik'],
                'fullname' => $data['fullname'],
                'birth_place' => $data['birth_place'],
                'date_of_birth' => $data['date_of_birth'],
                'gender' => $data['gender'],
                'address' => $data['address'],
                'last_education' => $data['last_education'],
            ]);

            $user->update([
                'username' => $data['username'],
                'phone_number' => $data['phone_number'],
            ]);

            if (isset($user->password)) {
                $user->save();
            }

            // Hapus chace
            $this->clearOfficerCache();

            return redirect(url('/officer-profile'))->with('success', 'Profil berhasil diperbarui.');
        } catch (\Exception $e) {
            Log::error('Error: ' . $e->getMessage()); // Check 'storage/logs/laravel.log'

            return back()->with('error', 'Profil gagal diperbarui. Silakan coba kembali.');
        }
    }
}
