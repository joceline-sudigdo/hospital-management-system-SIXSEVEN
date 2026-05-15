<?php

namespace Database\Seeders;

use App\Models\Patient;
use App\Models\User;
use Illuminate\Database\Seeder;

/**
 * PatientSeeder
 *
 * Mengisi tabel "patients" dari user yang role-nya 'patient'.
 * Logika sama dengan DoctorSeeder.
 */
class PatientSeeder extends Seeder
{
    public function run(): void
    {
        $patientUsers = User::where('role', 'patient')->get();

        foreach ($patientUsers as $user) {
            Patient::firstOrCreate(
                ['user_id' => $user->id],
                Patient::factory()->make(['user_id' => $user->id])->toArray()
            );
        }

        $this->command->info('✅ PatientSeeder selesai: ' . $patientUsers->count() . ' pasien dibuat.');
    }
}
