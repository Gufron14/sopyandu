<?php

namespace Database\Seeders;

use App\Http\Controllers\Api\LocationController;
use App\Models\FamilyChildren;
use App\Models\FamilyParent;
use App\Models\Officer;
use App\Models\User;
use Faker\Factory as Faker;
use Illuminate\Database\Seeder;

class ParentChildrenSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $faker = Faker::create('id_ID');

        // Buat data untuk petugas lainnya mulai dari ID 3
        for ($i = 3; $i <= 20; $i++) {
            $nik = $faker->unique()->numerify('################');

            // Daftar jabatan dan peran yang sesuai
            $positionRoleMap = [
                'Admin' => 'admin',
                'Bidan' => 'midwife',
                'Tenaga Medis Puskesmas/Desa' => 'officer',
                'Kepala Lingkungan' => 'officer',
                'Kader' => 'officer',
            ];

            // Pilih jabatan secara acak
            $position = $faker->randomElement(array_keys($positionRoleMap));

            // Tentukan gender untuk Bidan sebagai 'P' (Perempuan)
            if ($position === 'Bidan') {
                $gender = 'P';
                $fullname = $faker->name('female');
            } else {
                // Set nama sesuai jenis kelamin acak selain Bidan
                $gender = $faker->randomElement(['L', 'P']);
                $fullname = $gender === 'L' ? $faker->name('male') : $faker->name('female');
            }

            // Tentukan peran berdasarkan jabatan yang dipilih
            $role = $positionRoleMap[$position];

            // Buat data petugas
            $officer = Officer::create([
                'nik' => $nik,
                'fullname' => $fullname,
                'birth_place' => $faker->city,
                'date_of_birth' => $faker->date(),
                'gender' => $gender,
                'position' => $position,
                'address' => $faker->address,
                'last_education' => $faker->randomElement(['SMA', 'D3', 'S1', 'S2']),
            ]);

            // Buat akun untuk petugas
            User::create([
                'username' => $nik,
                'password' => bcrypt('123'),
                'phone_number' => '+62' . $faker->numerify('8##########'),
                'role' => $role,
                'officer_id' => $officer->id,
                'parent_id' => null,
                'verified_at' => now(),
                'remember_token' => null,
            ]);
        }

        // Buat data untuk orang tua dan anak-anak
        for ($i = 1; $i <= 25; $i++) {
            $number_of_children = $faker->numberBetween(1, 3);

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

            // Buat data orang tua
            $parent = FamilyParent::create([
                'nik' => $faker->unique()->numerify('################'),
                'mother_fullname' => $faker->name('female'),
                'mother_birth_place' => $faker->city,
                'mother_date_of_birth' => $faker->dateTimeBetween('1990-01-01', '2000-12-31')->format('Y-m-d'),
                'mother_blood_type' => $faker->randomElement(['A', 'B', 'O', 'AB']),
                'father_fullname' => $faker->name('male'),
                'father_birth_place' => $faker->city,
                'father_date_of_birth' => $faker->dateTimeBetween('1990-01-01', '2000-12-31')->format('Y-m-d'),
                'father_blood_type' => $faker->randomElement(['A', 'B', 'O', 'AB']),
                'is_pregnant' => $faker->randomElement(['Tidak Hamil', 'Hamil']),
                'number_of_children' => $number_of_children,
                'address' => $faker->streetAddress,
                'province' => $province['id'],
                'city' => $city['id'],
                'subdistrict' => $district['id'],
                'village' => $village['id'],
                'hamlet' => strtoupper($village['name']),
            ]);

            // Buat User untuk orang tua
            User::create([
                'username' => $parent->nik,
                'password' => bcrypt('123'),
                'phone_number' => '+62' . $faker->numerify('8##########'),
                'role' => 'family_parent',
                'officer_id' => null,
                'parent_id' => $parent->id,
                'verified_at' => $faker->randomElement([null, now()]),
                'remember_token' => null,
            ]);

            // Buat data anak-anak (Pastikan terkait dengan orang tua yang baru dibuat)
            for ($j = 0; $j < $number_of_children; $j++) {
                // Set nama sesuai jenis kelamin
                $gender = $faker->randomElement(['L', 'P']);
                $fullname = $gender === 'L' ? $faker->name('male') : $faker->name('female');

                FamilyChildren::create([
                    'nik' => $faker->unique()->numerify('################'),
                    'fullname' => $fullname,
                    'birth_place' => $faker->city,
                    'date_of_birth' => $faker->dateTimeBetween('2023-01-01', '2024-12-31')->format('Y-m-d'),
                    'gender' => $gender,
                    'blood_type' => $faker->randomElement(['A', 'B', 'O', 'AB']),
                    'parent_id' => $parent->id,
                ]);
            }
        }
    }
}
