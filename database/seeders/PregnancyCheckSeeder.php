<?php

namespace Database\Seeders;

use App\Models\FamilyParent;
use App\Models\PregnancyCheck;
use Carbon\Carbon;
use Faker\Factory as Faker;
use Illuminate\Database\Seeder;

class PregnancyCheckSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $faker = Faker::create('id_ID');

        // Membuat 50 data pemeriksaan ibu hamil
        for ($i = 0; $i < 50; $i++) {
            // Ambil ID ibu secara acak dari tabel FamilyParent
            $mother = FamilyParent::inRandomOrder()->first();

            // Ambil tanggal lahir ibu
            $mother_date_of_birth = Carbon::parse($mother->mother_date_of_birth);

            // Tanggal pemeriksaan secara acak
            $check_date = Carbon::parse($faker->dateTimeBetween('2023-01-01', '2025-12-31')->format('Y-m-d'));

            // Hitung usia ibu berdasarkan tanggal lahir dan tanggal pemeriksaan
            $age = $mother_date_of_birth->diff($check_date);

            // Format usia menjadi "X tahun, Y bulan, Z hari"
            $age_in_checks = "{$age->y} tahun, {$age->m} bulan, {$age->d} hari";

            // Usia kehamilan diacak antara 1 sampai 40 minggu
            $gestational_age = $faker->numberBetween(1, 40);

            PregnancyCheck::create([
                'parent_id' => $mother->id,  // ID ibu yang menjalani pemeriksaan
                'check_date' => $check_date->format('Y-m-d'),  // Tanggal pemeriksaan
                'age_in_checks' => $age_in_checks,  // Usia ibu saat pemeriksaan
                'gestational_age' => $gestational_age,  // Usia kehamilan dalam minggu
                'mother_weight' => $faker->randomFloat(2, 40, 100),  // Berat badan ibu (kg)
                'blood_pressure' => $faker->randomElement([
                    '110/70',
                    '120/80',
                    '130/85',
                    '140/90',
                    '150/95'
                ]),  // Tekanan darah ibu
                'pulse_rate' => $faker->numberBetween(60, 100),  // Denyut nadi ibu (bpm)
                'blood_sugar' => $faker->randomFloat(2, 70, 150),  // Gula darah sewaktu (mg/dL)
                'cholesterol' => $faker->randomFloat(2, 150, 300),  // Kadar kolesterol (mg/dL)
                'fundus_height' => $faker->randomFloat(1, 20, 40),  // Tinggi fundus rahim (cm)
                'fetal_heart_rate' => $faker->numberBetween(120, 160),  // Detak jantung janin (bpm)
                'fetal_presentation' => $faker->randomElement(['Kepala', 'Bokong', 'Lainnya']),  // Presentasi janin
                'edema' => $faker->randomElement(['Tidak', 'Ringan', 'Sedang', 'Berat']),  // Tingkat edema pada ibu
                'notes' => $faker->optional()->text,  // Catatan tambahan (opsional)
                'officer_id' => $faker->numberBetween(2, 15),  // ID petugas yang melakukan pemeriksaan
            ]);
        }
    }
}
