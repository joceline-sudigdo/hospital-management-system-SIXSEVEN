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

        // Kalau belum ada dokter/pasien → hentikan dengan pesan error
        if ($doctors->isEmpty() || $patients->isEmpty()) {
            $this->command->error('❌ Jalankan UserSeeder, DoctorSeeder, PatientSeeder dulu!');
            return;
        }

        $appointmentCount  = 0;
        $medicalRecordCount = 0;

        for ($i = 0; $i < 200; $i++) {
            // Pilih dokter & pasien secara acak
            $doctor  = $doctors->random();
            $patient = $patients->random();

            // Dokter harus punya jadwal
            if ($doctor->schedules->isEmpty()) {
                continue;
            }

            $schedule = $doctor->schedules->random();

            // Buat appointment dengan AppointmentFactory
            $appointment = Appointment::factory()->create([
                'doctor_id'   => $doctor->id,
                'patient_id'  => $patient->id,
                'schedule_id' => $schedule->id,
            ]);

            $appointmentCount++;

            // ── Buat rekam medis untuk appointment 'completed' ──────────
            // Target 100 rekam medis → buat selama belum mencapai 100
            if ($appointment->status === 'completed' && $medicalRecordCount < 100) {
                MedicalRecord::factory()->create([
                    'appointment_id' => $appointment->id,
                    'doctor_id'      => $doctor->id,
                ]);
                $medicalRecordCount++;
            }
        }

        $this->command->info("✅ AppointmentSeeder selesai: {$appointmentCount} appointments, {$medicalRecordCount} rekam medis.");
    }
}
