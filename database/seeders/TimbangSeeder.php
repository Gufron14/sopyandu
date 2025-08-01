<?php

namespace Database\Seeders;

use App\Models\Weighing;
use Illuminate\Support\Carbon;
use Illuminate\Database\Seeder;

class TimbangSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $parentId = 26;
        $childId = 55;

        // Mulai dari Januari 2025, 13 bulan ke depan (Januari 2025 - Januari 2026)
        $startDate = Carbon::create(2025, 1, 15); // tanggal 15 sebagai contoh

        for ($i = 0; $i < 13; $i++) {
            $date = $startDate->copy()->addMonths($i);

            // Pastikan hanya tahun 2025 dan 2026
            if ($date->year < 2025 || $date->year > 2026) {
                continue;
            }

            // Cek duplikasi bulan di tahun yang sama
            $exists = Weighing::where('children_id', $childId)
                ->whereYear('weighing_date', $date->year)
                ->whereMonth('weighing_date', $date->month)
                ->exists();

            if ($exists) {
                continue;
            }

            Weighing::create([
                'children_id'        => $childId,
                'weighing_date'      => $date->toDateString(),
                'age_in_checks'      => $i . ' bulan',
                'weight'             => 2.5 + ($i * 0.5) + rand(0, 10) / 10,
                'height'             => 48 + ($i * 2) + rand(0, 10) / 10,
                'head_circumference' => 34 + ($i * 0.3) + rand(0, 10) / 10,
                'arm_circumference'  => 11 + ($i * 0.2) + rand(0, 10) / 10,
                'nutrition_status'   => collect(['Baik', 'Buruk', 'Kurang', 'Lebih'])->random(),
                'notes'              => null,
                'officer_id'         => null,
            ]);
        }
    }
}