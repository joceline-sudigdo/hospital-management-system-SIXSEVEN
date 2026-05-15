<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * DoctorFactory
 *
 * Membuat data dummy untuk tabel "doctors".
 * Relasi: Doctor belongs to User (butuh user_id)
 */
class DoctorFactory extends Factory
{
    // Daftar spesialisasi dokter yang realistis
    private array $specializations = [
        'Dokter Umum',
        'Dokter Spesialis Anak',
        'Dokter Spesialis Jantung',
        'Dokter Spesialis Kulit',
        'Dokter Spesialis Penyakit Dalam',
        'Dokter Spesialis Bedah',
        'Dokter Spesialis Kandungan',
        'Dokter Spesialis Mata',
        'Dokter Spesialis THT',
        'Dokter Spesialis Saraf',
        'Dokter Spesialis Ortopedi',
        'Dokter Spesialis Urologi',
    ];

    public function definition(): array
    {
        $faker = \Faker\Factory::create('id_ID');

        // Format nomor HP Indonesia: +62 8xx-xxxx-xxxx
        // $faker->numerify('08##########') → '08' + 10 digit acak
        $phone = '+62' . $faker->numerify('8##########');

        return [
            // Kalau user_id tidak diberikan → buat User baru otomatis dengan role doctor
            // Ini contoh "relasi antar factory"
            'user_id'        => User::factory()->doctor(),

            // Pilih spesialisasi secara acak dari array di atas
            'specialization' => $faker->randomElement($this->specializations),

            'phone'          => $phone,

            // photo bisa null (tidak wajib upload)
            'photo'          => null,
        ];
    }
}
