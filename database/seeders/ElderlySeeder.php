<?php

namespace Database\Seeders;

use App\Http\Controllers\Api\LocationController;
use App\Models\Elderly;
use Faker\Factory as Faker;
use Illuminate\Database\Seeder;

class ElderlySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $faker = Faker::create('id_ID');

        // Buat data untuk orang tua lanjut usia
        for ($i = 1; $i <= 30; $i++) {
            // Set nama sesuai jenis kelamin
            $gender = $faker->randomElement(['L', 'P']);
            $fullname = $gender === 'L' ? $faker->name('male') : $faker->name('female');

            // Random data dari API
            $provinces = LocationController::getProvincesStatic();
            $province = collect($provinces)->random();

            $cities = LocationController::getCitiesStatic($province['id']);
            $city = collect($cities)->random();

            $districts = LocationController::getDistrictsStatic($city['id']);
            $district = collect($districts)->random();

            $villages = LocationController::getVillagesStatic($district['id']);
            $village = collect($villages)->random();

            // Buat data orang tua lajut usia
            Elderly::create([
                'nik' => $faker->unique()->numerify('################'),
                'fullname' => $fullname,
                'birth_place' => $faker->city,
                'date_of_birth' => $faker->dateTimeBetween('1960-01-01', '1970-12-31')->format('Y-m-d'),
                'gender' => $gender,
                'blood_type' => $faker->randomElement(['A', 'B', 'O', 'AB']),
                'address' => $faker->streetAddress,
                'province' => $province['id'],
                'city' => $city['id'],
                'subdistrict' => $district['id'],
                'village' => $village['id'],
                'hamlet' => strtoupper($village['name']),
            ]);
        }
    }
}
