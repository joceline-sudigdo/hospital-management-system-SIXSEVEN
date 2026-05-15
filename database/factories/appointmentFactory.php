<?php

namespace Database\Factories;

use App\Models\Doctor;
use App\Models\Patient;
use App\Models\Schedule;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * AppointmentFactory
 *
 * Ini contoh "relasi antar factory" yang diminta di brief:
 * AppointmentFactory menggunakan DoctorFactory dan PatientFactory.
 *
 * Status appointment mengikuti alur nyata:
 *   pending → confirmed → completed / cancelled
 */
class AppointmentFactory extends Factory
{
    // Keluhan pasien yang realistis dalam bahasa Indonesia
    private array $complaints = [
        'Demam sudah 3 hari tidak turun',
        'Sakit kepala dan pusing berputar',
        'Batuk berdahak lebih dari 2 minggu',
        'Nyeri dada saat beraktivitas',
        'Gatal-gatal di seluruh badan',
        'Mual dan muntah setelah makan',
        'Sesak napas saat naik tangga',
        'Nyeri lutut saat berjalan',
        'Penglihatan kabur mendadak',
        'Telinga berdenging terus-menerus',
        'Sakit perut bagian kanan bawah',
        'Susah tidur lebih dari seminggu',
        'Lemas dan lesu tanpa sebab jelas',
        'Bengkak pada kaki dan pergelangan',
        'Bintik merah muncul di kulit',
    ];

    public function definition(): array
    {
        $faker = \Faker\Factory::create('id_ID');

        // Tanggal appointment: antara 30 hari lalu sampai 30 hari ke depan
        $appointmentDate = $faker->dateTimeBetween('-30 days', '+30 days')->format('Y-m-d');

        // Status appointment dengan distribusi yang realistis
        // 20% pending, 30% confirmed, 40% completed, 10% cancelled
        $status = $faker->randomElement([
            'pending', 'pending',
            'confirmed', 'confirmed', 'confirmed',
            'completed', 'completed', 'completed', 'completed',
            'cancelled',
        ]);

        return [
            // Relasi ke model lain — kalau tidak diisi, factory akan buat data baru
            'patient_id'       => Patient::factory(),
            'doctor_id'        => Doctor::factory(),
            'schedule_id'      => Schedule::factory(),

            'appointment_date' => $appointmentDate,
            'status'           => $status,
            'complaint'        => $faker->randomElement($this->complaints),
        ];
    }

    // ── State: status = pending ────────────────────────────────────────────
    public function pending(): static
    {
        return $this->state(fn () => ['status' => 'pending']);
    }

    // ── State: status = confirmed ──────────────────────────────────────────
    public function confirmed(): static
    {
        return $this->state(fn () => ['status' => 'confirmed']);
    }

    // ── State: status = completed ──────────────────────────────────────────
    public function completed(): static
    {
        return $this->state(fn () => ['status' => 'completed']);
    }

    // ── State: status = cancelled ──────────────────────────────────────────
    public function cancelled(): static
    {
        return $this->state(fn () => ['status' => 'cancelled']);
    }
}
