<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

/**
 * DatabaseSeeder — Master Seeder
 *
 * Jalankan semua seeder sekaligus:
 *   php artisan db:seed
 *
 * Fresh start (hapus semua data lalu seed ulang):
 *   php artisan migrate:fresh --seed
 *
 * ⚠️  URUTAN INI PENTING — jangan diubah!
 *
 *   users ──► doctors ──► schedules ──┐
 *         └──► patients ──────────────┤
 *                                     ▼
 *                               appointments
 *                                     │
 *                                     ▼
 *                              medical_records
 */
class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->command->info('');
        $this->command->info('╔══════════════════════════════════════════╗');
        $this->command->info('║   🏥 Hospital Management System Seeder   ║');
        $this->command->info('╚══════════════════════════════════════════╝');
        $this->command->info('');

        $this->call([
            UserSeeder::class,          // Step 1: users (admin, 10 dokter, 50 pasien)
            DoctorSeeder::class,        // Step 2: profil dokter -> butuh user_id
            PatientSeeder::class,       // Step 3: profil pasien -> butuh user_id
            ScheduleSeeder::class,      // Step 4: jadwal dokter -> butuh doctor_id
            AppointmentSeeder::class,   // Step 5: janji temu   -> butuh semua di atas
            MedicalRecordSeeder::class, // Step 6: rekam medis  -> butuh appointment_id
        ]);

        $this->command->info('');
        $this->command->info('Seeding selesai! Summary:');
        $this->command->info('   - 1 Admin, 10 Dokter, 50 Pasien');
        $this->command->info('   - ±30 Jadwal praktek');
        $this->command->info('   - 200 Appointments');
        $this->command->info('   - 100 Rekam Medis');
        $this->command->info('');
        $this->command->info('Login Admin: admin@kliniksehat.id / password');
    }
}
