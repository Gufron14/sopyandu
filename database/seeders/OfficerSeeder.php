<?php

namespace Database\Seeders;

use App\Models\Officer;
use App\Models\User;
use Faker\Factory as Faker;
use Illuminate\Database\Seeder;

class OfficerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $faker = Faker::create('id_ID');

        // Buat data untuk Lurah dan Admin
        $officers = [
            ['id' => 1, 'position' => 'Lurah', 'role' => 'village_head', 'username' => 'lurah'],
            ['id' => 2, 'position' => 'Admin', 'role' => 'admin', 'username' => 'admin'],
        ];

        foreach ($officers as $data) {
            // Set nama sesuai jenis kelamin
            $gender = $faker->randomElement(['L', 'P']);
            $fullname = $gender === 'L' ? $faker->name('male') : $faker->name('female');

            // Buat data untuk Lurah dengan ID 1 dan Admin dengan ID 2 sesuai data diatas
            $officer = Officer::create([
                'id' => $data['id'],
                'nik' => $faker->unique()->numerify('################'),
                'fullname' => $fullname,
                'birth_place' => $faker->city,
                'date_of_birth' => $faker->date(),
                'gender' => $gender,
                'position' => $data['position'],
                'address' => $faker->address,
                'last_education' => $faker->randomElement(['SMA', 'D3', 'S1', 'S2']),
            ]);

            // Buat akun untuk Lurah dan Admin
            User::create([
                'username' => $data['username'],
                'password' => bcrypt('123'),
                'phone_number' => '+62' . $faker->numerify('8##########'),
                'role' => $data['role'],
                'officer_id' => $officer->id,
                'parent_id' => null,
                'verified_at' => now(),
                'remember_token' => null,
            ]);
        }
    }
}
