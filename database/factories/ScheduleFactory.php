<?php

namespace Database\Factories;

use App\Models\Doctor;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * ScheduleFactory
 *
 * Membuat jadwal praktek dokter.
 * day_of_week: 0 = Senin, 1 = Selasa, ..., 6 = Minggu
 */
class ScheduleFactory extends Factory
{
    // Jam mulai praktek yang masuk akal
    private array $startTimes = [
        '07:00', '08:00', '09:00', '13:00', '14:00', '16:00', '18:00',
    ];

    public function definition(): array
    {
        $faker = \Faker\Factory::create('id_ID');

        // Pilih jam mulai acak
        $startTime = $faker->randomElement($this->startTimes);

        // Jam selesai = jam mulai + 3 atau 4 jam
        $startCarbon  = \Carbon\Carbon::createFromFormat('H:i', $startTime);
        $durationHours = $faker->numberBetween(3, 4);
        $endTime      = $startCarbon->addHours($durationHours)->format('H:i');

        return [
            'doctor_id'   => Doctor::factory(),    // buat dokter baru kalau tidak diberikan
            'day_of_week' => $faker->numberBetween(0, 6), // 0=Senin, 6=Minggu
            'start_time'  => $startTime,
            'end_time'    => $endTime,
        ];
    }
}
