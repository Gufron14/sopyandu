<?php

namespace Database\Seeders;

use App\Models\EventSchedule;
use Carbon\Carbon;
use Faker\Factory as Faker;
use Illuminate\Database\Seeder;

class EventScheduleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $faker = Faker::create('id_ID');

        // Daftar kegiatan posyandu
        $activities = [
            'Pemberian Vitamin A',
            'Pemberian Obat Cacing',
            'Pemberian Imunisasi BCG',
            'Pemberian Imunisasi DTP',
            'Pemberian Imunisasi Polio',
            'Pemberian Imunisasi Campak',
            'Pemberian Imunisasi Hepatitis B',
            'Pemeriksaan Kesehatan Balita',
            'Penyuluhan Gizi',
            'Pemberian Makanan Tambahan',
            'Konsultasi Gizi',
            'Pemberian Vitamin C',
            'Penyuluhan Kesehatan Ibu dan Anak',
            'Pemeriksaan Kesehatan Ibu Hamil',
            'Penyuluhan Kesehatan Reproduksi',
            'Pemeriksaan Tumbuh Kembang Anak',
            'Pemberian Vaksin MR (Measles Rubella)',
            'Pemeriksaan Kesehatan Lansia',
            'Penyuluhan tentang Kebersihan Lingkungan',
            'Penyuluhan tentang Penyakit Menular'
        ];

        // 5 acara untuk hari kemarin untuk preview tampilan dashboard
        $yesterday = Carbon::yesterday()->format('Y-m-d');
        for ($i = 0; $i < 5; $i++) {
            EventSchedule::create([
                'officer_id' => $faker->numberBetween(2, 15),
                'title' => $faker->randomElement($activities),
                'event_date' => $yesterday,
                'start_time' => '07:30:00',
                'end_time' => '12:00:00',
                'event_location' => $faker->city,
                'description' => $faker->paragraph(),
            ]);
        }

        // 10 acara untuk hari ini untuk preview tampilan dashboard
        $today = Carbon::today()->format('Y-m-d');
        for ($i = 0; $i < 10; $i++) {
            EventSchedule::create([
                'officer_id' => $faker->numberBetween(2, 15),
                'title' => $faker->randomElement($activities),
                'event_date' => $today,
                'start_time' => '07:30:00',
                'end_time' => '12:00:00',
                'event_location' => $faker->city,
                'description' => $faker->paragraph(),
            ]);
        }

        // 5 acara untuk besok pagi untuk preview tampilan dashboard
        $tomorrowMorning = Carbon::tomorrow()->startOfDay()->format('Y-m-d');
        for ($i = 0; $i < 5; $i++) {
            EventSchedule::create([
                'officer_id' => $faker->numberBetween(2, 15),
                'title' => $faker->randomElement($activities),
                'event_date' => $tomorrowMorning,
                'start_time' => '07:30:00',
                'end_time' => '12:00:00',
                'event_location' => $faker->city,
                'description' => $faker->paragraph(),
            ]);
        }

        // Membuat 50 data jadwal acara
        for ($i = 0; $i < 50; $i++) {
            EventSchedule::create([
                'officer_id' => $faker->numberBetween(2, 15),  // ID petugas antara 2 hingga 15
                'title' => $faker->randomElement($activities),  // Nama kegiatan posyandu yang relevan
                'event_date' =>  $faker->dateTimeBetween('2023-01-01', '2025-12-31')->format('Y-m-d'),  // Tanggal acara antara 2023 hingga 2025
                'start_time' => $faker->time('H:i:s', '07:30:00'),  // Waktu mulai tetap 07:30:00
                'end_time' => $faker->time('H:i:s', '12:00:00'),  // Waktu selesai tetap 12:00:00
                'event_location' => $faker->city,  // Lokasi acara
                'description' => $faker->paragraph(),  // Deskripsi acara
            ]);
        }
    }
}
