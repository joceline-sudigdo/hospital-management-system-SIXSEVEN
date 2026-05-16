<?php

namespace Database\Seeders;

use App\Models\Appointment;
use App\Models\Doctor;
use App\Models\MedicalRecord;
use App\Models\Patient;
use App\Models\Schedule;
use Illuminate\Database\Seeder;

/**
 * AppointmentSeeder
 *
 * Target: 200 appointments + 100 rekam medis.
 *
 * Alur logika:
 *  1. Ambil semua dokter & pasien yang sudah ada
 *  2. Loop 200 kali → tiap iterasi buat 1 appointment
 *  3. Untuk appointment yang statusnya 'completed' → buat rekam medisnya
 */
class AppointmentSeeder extends Seeder
{
    public function run(): void
{
    $doctors  = Doctor::with('schedules')->get();
    $patients = Patient::all();

    if ($doctors->isEmpty() || $patients->isEmpty()) {
        $this->command->error('❌ Jalankan UserSeeder, DoctorSeeder, PatientSeeder dulu!');
        return;
    }

    $faker = \Faker\Factory::create('id_ID');

    // Status pool untuk sisa appointment (setelah 100 completed)
    $statusPool = ['pending', 'pending', 'confirmed', 'confirmed', 'cancelled'];

    for ($i = 0; $i < 200; $i++) {
        $doctor  = $doctors->random();
        $patient = $patients->random();

        if ($doctor->schedules->isEmpty()) continue;

        $schedule = $doctor->schedules->random();

        // 100 pertama → PAKSA completed, sisanya → acak
        $status = $i < 100 ? 'completed' : $faker->randomElement($statusPool);

        Appointment::factory()->create([
            'doctor_id'   => $doctor->id,
            'patient_id'  => $patient->id,
            'schedule_id' => $schedule->id,
            'status'      => $status,
        ]);
    }

    $this->command->info("✅ AppointmentSeeder selesai: 200 appointments (100 completed, 100 lainnya acak).");
}
}
