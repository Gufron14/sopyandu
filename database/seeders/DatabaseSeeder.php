<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    // Default
    // /**
    //  * Seed the application's database.
    //  */
    // public function run(): void
    // {
    //     // User::factory(10)->create();

    //     User::factory()->create([
    //         'name' => 'Test User',
    //         'email' => 'test@example.com',
    //     ]);
    // }

    // Custom
    public function run(): void
    {
        $this->call(SiteIdentitySeeder::class);
        $this->call(OfficerSeeder::class);
        $this->call(ParentChildrenSeeder::class);
        $this->call(ElderlySeeder::class);
        $this->call(VaccineMedicineSeeder::class);
        $this->call(EventScheduleSeeder::class);
        $this->call(ImmunizationSeeder::class);
        $this->call(WeighingSeeder::class);
        $this->call(PregnancyCheckSeeder::class);
        $this->call(ElderlyCheckSeeder::class);
    }
}
