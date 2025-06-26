<?php

namespace Database\Seeders;

use App\Models\FamilyChildren;
use App\Models\Immunization;
use App\Models\Vaccine;
use Carbon\Carbon;
use Faker\Factory as Faker;
use Illuminate\Database\Seeder;

class ImmunizationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $faker = Faker::create('id_ID');

        // Membuat 50 data imunisasi
        for ($i = 0; $i < 50; $i++) {
            // Ambil ID anak secara acak
            $children = FamilyChildren::inRandomOrder()->first();

            // Ambil tanggal lahir anak
            $date_of_birth = Carbon::parse($children->date_of_birth);

            // Tanggal imunisasi secara acak
            $immunization_date = Carbon::parse($faker->dateTimeBetween('2023-01-01', '2025-12-31')->format('Y-m-d'));

            // Hitung usia berdasarkan tanggal lahir dan tanggal imunisasi
            $age = $date_of_birth->diff($immunization_date);

            // Format usia menjadi "X tahun, Y bulan, Z hari"
            $age_in_checks = "{$age->y} tahun, {$age->m} bulan, {$age->d} hari";

            // Ambil vaksin secara acak
            $vaccine = Vaccine::inRandomOrder()->first();

            Immunization::create([
                'children_id' => $children->id,  // ID anak yang menerima imunisasi
                'immunization_date' => $immunization_date->format('Y-m-d'),  // Tanggal imunisasi
                'age_in_checks' => $age_in_checks,  // Usia anak saat imunisasi
                'vaccine_id' => $vaccine->id,  // ID vaksin yang digunakan
                'vaccine_category' => $faker->randomElement(['-', 'Wajib', 'Tambahan', 'Khusus', 'Lainnya']),  // Kategori vaksin
                'side_effects' => $faker->optional()->text,  // Efek samping (opsional)
                'notes' => $faker->optional()->text,  // Catatan tambahan (opsional)
                'officer_id' => $faker->numberBetween(2, 15),  // ID petugas yang melakukan imunisasi
            ]);
        }
    }
}
