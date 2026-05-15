<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;

/**
 * UserFactory
 *
 * Factory = "cetakan" untuk membuat data dummy secara otomatis.
 * Faker adalah library yang men-generate data palsu tapi realistis.
 *
 * Cara pakai:
 *   User::factory()->create()           → buat 1 user
 *   User::factory()->count(10)->create() → buat 10 user sekaligus
 *   User::factory()->doctor()->create()  → buat 1 user dengan role dokter
 */
class UserFactory extends Factory
{
    /**
     * Locale id_ID = data Faker akan pakai format Indonesia
     * (nama, kota, nomor telepon Indonesia)
     */
    protected $faker;

    public function definition(): array
    {
        // Paksa locale Indonesia
        $faker = \Faker\Factory::create('id_ID');

        return [
            // $faker->name('id_ID') → nama Indonesia seperti "Budi Santoso"
            'name'              => $faker->name(),

            // $faker->unique() → pastikan email tidak ada yang sama
            'email'             => $faker->unique()->safeEmail(),

            'password'          => Hash::make('password'), // semua password = "password"
            'role'              => 'patient',               // default role
            'email_verified_at' => now(),                   // anggap sudah verifikasi email
        ];
    }

    // ── State: role = 'doctor' ─────────────────────────────────────────────
    // State = variasi dari factory default
    // Pakai: User::factory()->doctor()->create()
    public function doctor(): static
    {
        return $this->state(fn (array $attributes) => [
            'role' => 'doctor',
        ]);
    }

    // ── State: role = 'patient' ────────────────────────────────────────────
    public function patient(): static
    {
        return $this->state(fn (array $attributes) => [
            'role' => 'patient',
        ]);
    }

    // ── State: email belum diverifikasi ───────────────────────────────────
    public function unverified(): static
    {
        return $this->state(fn (array $attributes) => [
            'email_verified_at' => null,
        ]);
    }
}
