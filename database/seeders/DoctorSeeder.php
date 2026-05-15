<?php

namespace Database\Seeders;

use App\Models\Doctor;
use App\Models\User;
use Illuminate\Database\Seeder;

/**
 * DoctorSeeder
 *
 * Seeder ini mengisi tabel "doctors" berdasarkan user yang sudah
 * dibuat oleh UserSeeder dengan role = 'doctor'.
 *
 * PENTING: Jalankan UserSeeder DULU sebelum DoctorSeeder.
 * Urutan diatur di DatabaseSeeder.php.
 */
class DoctorSeeder extends Seeder
{
    public function run(): void
    {
        // Ambil semua user yang role-nya 'doctor'
        $doctorUsers = User::where('role', 'doctor')->get();

        foreach ($doctorUsers as $user) {
            // firstOrCreate: kalau dokter dengan user_id ini sudah ada → skip
            Doctor::firstOrCreate(
                ['user_id' => $user->id],
                // Kalau belum ada → buat dengan data dari factory
                Doctor::factory()->make(['user_id' => $user->id])->toArray()
            );
        }

        $this->command->info('✅ DoctorSeeder selesai: ' . $doctorUsers->count() . ' dokter dibuat.');
    }
}
