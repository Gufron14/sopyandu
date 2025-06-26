<?php

namespace Database\Seeders;

use App\Models\Elderly;
use App\Models\ElderlyCheck;
use Carbon\Carbon;
use Faker\Factory as Faker;
use Illuminate\Database\Seeder;

class ElderlyCheckSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $faker = Faker::create('id_ID');

        // Membuat 50 data pemeriksaan lansia
        for ($i = 0; $i < 50; $i++) {
            // Ambil ID lansia secara acak dari tabel Elderlies
            $elderly = Elderly::inRandomOrder()->first();

            // Ambil tanggal lahir lansia
            $date_of_birth = Carbon::parse($elderly->date_of_birth);

            // Tanggal pemeriksaan secara acak
            $check_date = Carbon::parse($faker->dateTimeBetween('2023-01-01', '2025-12-31')->format('Y-m-d'));

            // Hitung usia lansia berdasarkan tanggal lahir dan tanggal pemeriksaan
            $age = $date_of_birth->diff($check_date);

            // Format usia menjadi "X tahun, Y bulan, Z hari"
            $age_in_checks = "{$age->y} tahun, {$age->m} bulan, {$age->d} hari";

            // Data lainnya diacak sesuai dengan format yang diperlukan
            ElderlyCheck::create([
                'elderly_id' => $elderly->id,  // ID lansia yang menjalani pemeriksaan
                'check_date' => $check_date->format('Y-m-d'),  // Tanggal pemeriksaan
                'age_in_checks' => $age_in_checks,  // Usia lansia saat pemeriksaan
                'body_weight' => $faker->randomFloat(2, 40, 80),  // Berat badan lansia (kg)
                'blood_pressure' => $faker->randomElement([
                    '110/70',
                    '120/80',
                    '130/85',
                    '140/90',
                    '150/95'
                ]),  // Tekanan darah lansia
                'pulse_rate' => $faker->numberBetween(60, 100),  // Denyut nadi (bpm)
                'blood_sugar' => $faker->randomFloat(2, 70, 180),  // Gula darah sewaktu (mg/dL)
                'cholesterol' => $faker->randomFloat(2, 150, 300),  // Kadar kolesterol (mg/dL)
                'uric_acid' => $faker->randomFloat(2, 3, 10),  // Asam urat (mg/dL)
                'mobility_status' => $faker->randomElement(['Mandiri', 'Bantuan Alat', 'Dibantu Orang Lain']),  // Status mobilitas lansia
                'cognitive_status' => $faker->randomElement(['Normal', 'Penurunan Ringan', 'Demensia']),  // Status kognitif lansia
                'nutritional_status' => $faker->randomElement(['Baik', 'Kurang', 'Lebih']),  // Status gizi lansia
                'notes' => $faker->optional()->text,  // Catatan tambahan (opsional)
                'officer_id' => $faker->numberBetween(2, 15),  // ID petugas yang melakukan pemeriksaan
            ]);
        }
    }
}
