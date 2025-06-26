<?php

namespace Database\Seeders;

use App\Models\FamilyChildren;
use App\Models\Weighing;
use Carbon\Carbon;
use Faker\Factory as Faker;
use Illuminate\Database\Seeder;

class WeighingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $faker = Faker::create('id_ID');

        for ($i = 0; $i < 50; $i++) {
            // Ambil ID anak secara acak
            $children = FamilyChildren::inRandomOrder()->first();

            // Ambil tanggal lahir anak
            $date_of_birth = Carbon::parse($children->date_of_birth);

            // Tanggal penimbangan secara acak
            $weighing_date = Carbon::parse($faker->dateTimeBetween('2023-01-01', '2025-12-31')->format('Y-m-d'));

            // Hitung usia berdasarkan tanggal lahir dan tanggal penimbangan
            $age = $date_of_birth->diff($weighing_date);

            // Format usia menjadi "X tahun, Y bulan, Z hari"
            $age_in_checks = "{$age->y} tahun, {$age->m} bulan, {$age->d} hari";

            Weighing::create([
                'children_id' => $children->id,  // ID anak yang diambil dari database
                'weighing_date' => $weighing_date->format('Y-m-d'),  // Tanggal penimbangan
                'age_in_checks' => $age_in_checks,  // Usia anak saat penimbangan
                'weight' => $faker->randomFloat(2, 5, 30),  // Berat badan (kg) antara 5 dan 30 kg
                'height' => $faker->randomFloat(2, 50, 120),  // Tinggi badan (cm) antara 50 dan 120 cm
                'head_circumference' => $faker->randomFloat(2, 40, 60),  // Lingkar kepala (cm) antara 40 dan 60 cm
                'arm_circumference' => $faker->randomFloat(2, 15, 30),  // Lingkar lengan (cm) antara 15 dan 30 cm
                'nutrition_status' => $faker->randomElement(['Baik', 'Buruk', 'Kurang', 'Lebih']),  // Status gizi
                'notes' => $faker->optional()->text,  // Catatan tambahan (opsional)
                'officer_id' => $faker->numberBetween(2, 15),  // ID petugas (anggap ada 15 petugas)
            ]);
        }
    }
}
