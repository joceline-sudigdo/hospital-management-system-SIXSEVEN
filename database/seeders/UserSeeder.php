<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

/**
 * UserSeeder
 *
 * Seeder = script untuk mengisi database dengan data awal.
 * Jalankan dengan: php artisan db:seed --class=UserSeeder
 *
 * firstOrCreate() = "buat kalau belum ada, skip kalau sudah ada"
 * → aman dijalankan berkali-kali (idempotent)
 */
class UserSeeder extends Seeder
{
    public function run(): void
    {
        // ── 1. Buat akun Admin tetap (hardcoded, bukan Faker) ──────────────
        User::firstOrCreate(
            ['email' => 'admin@kliniksehat.id'],  // cari berdasarkan email ini
            [
                'name'              => 'Administrator',
                'password'          => Hash::make('password'),
                'role'              => 'admin',
                'email_verified_at' => now(),
            ]
        );

        // ── 2. Buat 10 akun Dokter pakai Factory ──────────────────────────
        // User::factory() → pakai DoctorUserFactory (lihat UserFactory.php)
        // ->doctor()      → gunakan state "doctor" di factory
        // ->count(10)     → buat 10 data sekaligus
        // ->create()      → simpan ke database
        User::factory()
            ->doctor()
            ->count(10)
            ->create();

        // ── 3. Buat 50 akun Pasien pakai Factory ──────────────────────────
        User::factory()
            ->patient()
            ->count(50)
            ->create();

        $this->command->info('✅ UserSeeder selesai: 1 admin, 10 dokter, 50 pasien.');
    }
}
