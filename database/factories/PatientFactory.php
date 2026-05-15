<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * PatientFactory
 *
 * Membuat data dummy untuk tabel "patients".
 * Menggunakan data Indonesia: nama kota, format HP +62, dll.
 */
class PatientFactory extends Factory
{
    // Daftar kota di Indonesia untuk alamat yang realistis
    private array $indonesianCities = [
        'Jakarta', 'Surabaya', 'Bandung', 'Medan', 'Bekasi',
        'Tangerang', 'Depok', 'Semarang', 'Palembang', 'Makassar',
        'Malang', 'Yogyakarta', 'Bogor', 'Pekanbaru', 'Batam',
    ];

    // Daftar nama jalan Indonesia
    private array $streetNames = [
        'Jl. Sudirman', 'Jl. Thamrin', 'Jl. Gatot Subroto',
        'Jl. Ahmad Yani', 'Jl. Diponegoro', 'Jl. Veteran',
        'Jl. Pahlawan', 'Jl. Merdeka', 'Jl. Imam Bonjol',
        'Jl. Hayam Wuruk', 'Jl. Gajah Mada', 'Jl. Pemuda',
    ];

    public function definition(): array
    {
        $faker = \Faker\Factory::create('id_ID');

        // Tanggal lahir acak antara 10 tahun lalu sampai 80 tahun lalu
        $dateOfBirth = $faker->dateTimeBetween('-80 years', '-10 years')->format('Y-m-d');

        // Nomor HP format +62
        $phone = '+62' . $faker->numerify('8##########');

        // Buat alamat lengkap: "Jl. Sudirman No. 12, RT 03/RW 05, Bandung"
        $street  = $faker->randomElement($this->streetNames);
        $no      = $faker->numberBetween(1, 150);
        $rt      = $faker->numberBetween(1, 10);
        $rw      = $faker->numberBetween(1, 15);
        $city    = $faker->randomElement($this->indonesianCities);
        $address = "{$street} No. {$no}, RT 0{$rt}/RW 0{$rw}, {$city}";

        return [
            'user_id'       => User::factory()->patient(),
            'date_of_birth' => $dateOfBirth,
            'address'       => $address,
            'phone'         => $phone,
            'photo'         => null,
        ];
    }
}
