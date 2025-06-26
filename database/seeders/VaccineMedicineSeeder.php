<?php

namespace Database\Seeders;

use App\Models\Medicine;
use App\Models\Vaccine;
use Faker\Factory as Faker;
use Illuminate\Database\Seeder;

class VaccineMedicineSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $faker = Faker::create('id_ID');

        // Mapping nama vaksin ke jenis vaksin
        $vaccineTypes = [
            'BCG' => 'Wajib',
            'Hepatitis B' => 'Wajib',
            'Polio' => 'Wajib',
            'DTP' => 'Wajib',
            'Hib' => 'Tambahan',
            'Campak' => 'Wajib',
            'MR' => 'Khusus',
        ];

        // Buat data untuk vaksin dan obat
        for ($i = 0; $i < 15; $i++) {
            $vaccineName = $faker->randomElement(array_keys($vaccineTypes));
            $vaccineType = $vaccineTypes[$vaccineName];

            Vaccine::create([
                'vaccine_name' => $vaccineName,
                'vaccine_type' => $vaccineType, // Menambahkan kolom jenis vaksin
                'unit' => $faker->randomElement(['vial', 'dosis']),
                'stock' => $faker->numberBetween(10, 100),
                'entry_date' => $faker->dateTimeBetween('2023-01-01', '2024-12-31')->format('Y-m-d'),
                'expiry_date' => $faker->dateTimeBetween('2025-01-01', '2026-12-31')->format('Y-m-d'),
                'notes' => $faker->sentence(),
            ]);

            // Obat
            $medicineName = $faker->randomElement([
                'Paracetamol', 'Ambroxol', 'Cetirizine', 'Ferrous Sulfate',
                'Cefadroxil', 'Vitamin A', 'Vitamin B12', 'Amoxicillin',
                'Ibuprofen', 'Omeprazole'
            ]);

            // Menentukan kategori dan unit berdasarkan nama obat
            if (strpos($medicineName, 'Vitamin') !== false) {
                $medicineType = 'Suplemen';
                $medicineUnit = $faker->randomElement(['pcs', 'tablet', 'strip']);
            } elseif (in_array($medicineName, ['Cefadroxil', 'Amoxicillin', 'Ibuprofen', 'Omeprazole'])) {
                $medicineType = 'Antibiotik';
                $medicineUnit = $faker->randomElement(['tablet', 'capsule', 'strip']);
            } elseif (in_array($medicineName, ['Paracetamol', 'Ambroxol', 'Cetirizine', 'Ferrous Sulfate'])) {
                $medicineType = 'Lainnya';
                $medicineUnit = $faker->randomElement(['tablet', 'capsule', 'pcs']);
            } else {
                $medicineType = 'Lainnya';
                $medicineUnit = $faker->randomElement(['tablet', 'capsule', 'pcs']);
            }

            // Tambahkan ke database
            Medicine::create([
                'medicine_name' => $medicineName,
                'type' => $medicineType,
                'unit' => $medicineUnit,
                'stock' => $faker->numberBetween(10, 100),
                'entry_date' => $faker->dateTimeBetween('2023-01-01', '2024-12-31')->format('Y-m-d'),
                'expiry_date' => $faker->dateTimeBetween('2025-01-01', '2026-12-31')->format('Y-m-d'),
                'notes' => $faker->sentence(),
            ]);
        }
    }
}
