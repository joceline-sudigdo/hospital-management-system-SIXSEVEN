<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

/**
 * DatabaseSeeder
 *
 * File ini adalah "induk" dari semua seeder.
 * Jalankan SEMUA seeder sekaligus dengan:
 *   php artisan db:seed
 *
 * URUTAN SANGAT PENTING karena ada relasi foreign key:
 *   User → Doctor/Patient → Schedule → Appointment → MedicalRecord
 *
 * Kalau urutannya salah → error "foreign key constraint fails"
 */
class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->command->info('🚀 Memulai seeding database...');

        $this->call([
            UserSeeder::class,        // 1️⃣ Buat users dulu (admin, dokter, pasien)
            DoctorSeeder::class,      // 2️⃣ Buat profil dokter (butuh user_id)
            PatientSeeder::class,     // 3️⃣ Buat profil pasien (butuh user_id)
            ScheduleSeeder::class,    // 4️⃣ Buat jadwal dokter (butuh doctor_id)
            AppointmentSeeder::class, // 5️⃣ Buat janji temu + rekam medis (butuh semua di atas)
        ]);

        $this->command->info('🎉 Semua seeder selesai!');
    }
}
