<?php

namespace Database\Seeders;

use App\Models\Appointment;
use App\Models\MedicalRecord;
use Illuminate\Database\Seeder;


class MedicalRecordSeeder extends Seeder
{
    public function run(): void
{
    $completedAppointments = Appointment::where('status', 'completed')
        ->whereDoesntHave('medicalRecord')
        ->with('doctor')
        ->get(); // ← hapus ->limit(100), ambil semua yang completed

    if ($completedAppointments->isEmpty()) {
        $this->command->warn('⚠️  Tidak ada appointment completed yang belum punya rekam medis.');
        return;
    }

    $count = 0;

    foreach ($completedAppointments as $appointment) {
        MedicalRecord::firstOrCreate(
            ['appointment_id' => $appointment->id],
            MedicalRecord::factory()->make([
                'appointment_id' => $appointment->id,
                'doctor_id'      => $appointment->doctor_id,
            ])->toArray()
        );
        $count++;
    }

    // Kalau masih kurang dari 100, paksa buat sisanya
    if ($count < 100) {
        $kurang = 100 - $count;
        $this->command->warn("⚠️  Hanya {$count} rekam medis, membuat {$kurang} tambahan...");

        for ($i = 0; $i < $kurang; $i++) {
            MedicalRecord::factory()->create();
        }
        $count = 100;
    }

    $this->command->info("✅ MedicalRecordSeeder selesai: {$count} rekam medis dibuat.");
}
}
