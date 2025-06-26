<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\SiteIdentity;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class SiteIdentityController extends Controller
{
    public function index()
    {
        return view('dashboard.site-identity.index');
    }

    public function update(Request $request)
    {
        $site = SiteIdentity::first();

        $rules = [
            'village_name' => 'required',
            'phone_number' => [
                'required',
                function ($attribute, $value, $fail) {
                    if (!(substr($value, 0, 4) === '+628' || substr($value, 0, 2) === '08')) {
                        $fail('Nomor HP/WA tidak valid.');
                    }
                }
            ],
            'wa_group_url' => 'required|url',
            'officer_wa_group_url' => 'required|url',
        ];

        $messages = [
            'village_name.required' => 'Kelurahan/Desa wajib diisi.',
            'phone_number.required' => 'Nomor HP/WA wajib diisi.',
            'wa_group_url.required' => 'Whatsapp Grup URL wajib diisi.',
            'wa_group_url.url' => 'Format Whatsapp Grup URL tidak valid.',
            'officer_wa_group_url.required' => 'Whatsapp Grup URL (Petugas) wajib diisi.',
            'officer_wa_group_url.url' => 'Format Whatsapp Grup URL (Petugas) tidak valid.',
        ];

        // Jalankan validasi
        $data = $request->validate($rules, $messages);

        // Konversi nomor '08' ke '+628' (setelah validasi)
        if (substr($data['phone_number'], 0, 2) === '08') {
            $data['phone_number'] = '+62' . substr($data['phone_number'], 1);
        }

        try {
            $site->update([
                'village_name' => $data['village_name'],
                'phone_number' => $data['phone_number'],
                'wa_group_url' => $data['wa_group_url'],
                'officer_wa_group_url' => $data['officer_wa_group_url'],
                'updated_at' => now(),
            ]);

            return redirect(url('/site-identity'))->with('success', 'Identitas situs berhasil diperbarui.');
        } catch (\Exception $e) {
            Log::error('Error: ' . $e->getMessage()); // Check 'storage/logs/laravel.log'

            return back()->with('error', 'Identitas situs gagal diperbarui. Silakan coba kembali.');
        }
    }
}
