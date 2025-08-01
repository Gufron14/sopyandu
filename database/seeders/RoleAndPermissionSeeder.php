<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RoleAndPermissionSeeder extends Seeder
{
    public function run()
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // =======================
        // Define all permissions
        // =======================
        $permissions = [
            // Umum
            'dashboard.show',

            // Data Orang Tua
            'data-orang-tua.show',
            'data-orang-tua.create',
            'data-orang-tua.update',
            'data-orang-tua.delete',

            // Data Balita
            'data-balita.show',
            'data-balita.create',
            'data-balita.update',
            'data-balita.delete',

            // Data Kader dan Bidan
            'data-kader-bidan.show',
            'data-kader-bidan.create',
            'data-kader-bidan.update',
            'data-kader-bidan.delete',

            // Pendaftaran
            'pendaftaran.show',
            'pendaftaran.create',
            'pendaftaran.update',
            'pendaftaran.delete',

            // Artikel KIA
            'artikel-kia.show',
            'artikel-kia.create',
            'artikel-kia.update',
            'artikel-kia.delete',

            // Jadwal Posyandu
            'jadwal-posyandu.show',
            'jadwal-posyandu.create',
            'jadwal-posyandu.update',
            'jadwal-posyandu.delete',

            // Imunisasi Balita
            'imunisasi-balita.show',
            'imunisasi-balita.create',
            'imunisasi-balita.update',
            'imunisasi-balita.delete',

            // Penimbangan Balita
            'penimbangan-balita.show',
            'penimbangan-balita.create',
            'penimbangan-balita.update',
            'penimbangan-balita.delete',

            // Pemeriksaan Kehamilan
            'pemeriksaan-kehamilan.show',
            'pemeriksaan-kehamilan.create',
            'pemeriksaan-kehamilan.update',
            'pemeriksaan-kehamilan.delete',

            // Kelola Data Penimbangan
            'kelola-penimbangan.create',
            'kelola-penimbangan.update',
            'kelola-penimbangan.delete',

            // Kelola Pemeriksaan
            'kelola-pemeriksaan.show',
            'kelola-pemeriksaan.create',
            'kelola-pemeriksaan.update',
            'kelola-pemeriksaan.delete',

            // Vaksin
            'vaksin.show',
            'vaksin.create',
            'vaksin.update',
            'vaksin.delete',
        ];

        // Create all permissions
        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        // =======================
        // Create Roles
        // =======================
        $roleKader = Role::firstOrCreate(['name' => 'Kader']);
        $roleBidan = Role::firstOrCreate(['name' => 'Bidan']);
        $roleIbu = Role::firstOrCreate(['name' => 'Ibu']);

        // =======================
        // Assign Permissions to Kader
        // =======================
        $roleKader->syncPermissions([
            'dashboard.show',
            'data-orang-tua.show', 'data-orang-tua.create', 'data-orang-tua.update', 'data-orang-tua.delete',
            'data-balita.show', 'data-balita.create', 'data-balita.update', 'data-balita.delete',
            'data-kader-bidan.show', 'data-kader-bidan.create', 'data-kader-bidan.update', 'data-kader-bidan.delete',
            'pendaftaran.show', 'pendaftaran.create', 'pendaftaran.update', 'pendaftaran.delete',
            'artikel-kia.show', 'artikel-kia.create', 'artikel-kia.update', 'artikel-kia.delete',
            'jadwal-posyandu.show', 'jadwal-posyandu.create', 'jadwal-posyandu.update', 'jadwal-posyandu.delete',
            'imunisasi-balita.show',
            'penimbangan-balita.show', 'penimbangan-balita.create', 'penimbangan-balita.update', 'penimbangan-balita.delete',
            'pemeriksaan-kehamilan.show',
            'kelola-penimbangan.create', 'kelola-penimbangan.update', 'kelola-penimbangan.delete',
            'kelola-pemeriksaan.show',
            'vaksin.show',
        ]);

        // =======================
        // Assign Permissions to Bidan
        // =======================
        $roleBidan->syncPermissions([
            'dashboard.show',
            'data-orang-tua.show',
            'data-balita.show',
            'data-kader-bidan.show',
            'pendaftaran.show',
            'artikel-kia.show',
            'jadwal-posyandu.show',
            'imunisasi-balita.show', 'imunisasi-balita.create', 'imunisasi-balita.update', 'imunisasi-balita.delete',
            'penimbangan-balita.show',
            'pemeriksaan-kehamilan.show', 'pemeriksaan-kehamilan.create', 'pemeriksaan-kehamilan.update', 'pemeriksaan-kehamilan.delete',
            'kelola-pemeriksaan.show', 'kelola-pemeriksaan.create', 'kelola-pemeriksaan.update', 'kelola-pemeriksaan.delete',
            'vaksin.show', 'vaksin.create', 'vaksin.update', 'vaksin.delete',
        ]);

        // =======================
        // Assign Permissions to Ibu
        // =======================
        $roleIbu->syncPermissions([
            'dashboard.show',
            'data-balita.show', 'data-balita.create', 'data-balita.update', 'data-balita.delete',
            'artikel-kia.show',
            'jadwal-posyandu.show',
            'imunisasi-balita.show',
            'penimbangan-balita.show',
            'pemeriksaan-kehamilan.show',
        ]);
    }
}
