<?php

namespace Database\Seeders;

use App\Models\Doctor;
use App\Models\Schedule;
use Illuminate\Database\Seeder;

/**
 * ScheduleSeeder
 *
 * Setiap dokter mendapat 3 jadwal praktek per minggu secara acak.
 * Menggunakan ScheduleFactory untuk data realistis.
 */
class ScheduleSeeder extends Seeder
{
    public function run(): void
{
    $doctors = Doctor::all();
    $total   = 0;

    $dayNames = ['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday'];

    foreach ($doctors as $doctor) {
        // Pilih 3 hari acak dari 7 hari
        $days = collect($dayNames)->shuffle()->take(3);

        foreach ($days as $day) {
            Schedule::firstOrCreate(
                ['doctor_id' => $doctor->id, 'day_of_week' => $day],
                Schedule::factory()->make([
                    'doctor_id'   => $doctor->id,
                    'day_of_week' => $day,
                ])->toArray()
            );
            $total++;
        }
    }

    $this->command->info("✅ ScheduleSeeder selesai: {$total} jadwal dibuat.");
}
}
