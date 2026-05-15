<?php

namespace Database\Factories;

use App\Models\Appointment;
use App\Models\Doctor;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * MedicalRecordFactory
 *
 * Membuat data rekam medis yang realistis.
 * Relasi: MedicalRecord belongs to Appointment & Doctor.
 */
class MedicalRecordFactory extends Factory
{
    private array $diagnoses = [
        'Infeksi Saluran Pernapasan Atas (ISPA)',
        'Hipertensi Grade I',
        'Diabetes Mellitus Tipe 2',
        'Gastritis Akut',
        'Dermatitis Atopik',
        'Migrain',
        'Anemia Defisiensi Besi',
        'Infeksi Saluran Kemih (ISK)',
        'Faringitis Akut',
        'Otitis Media Akut',
        'Bronkitis Akut',
        'Konjungtivitis Bakteri',
        'Vertigo Paroksismal Jinak',
        'Artritis Gout',
        'Dispepsia Fungsional',
    ];

    private array $prescriptions = [
        'Paracetamol 500mg 3x1, Amoxicillin 500mg 3x1, Vitamin C 500mg 1x1',
        'Amlodipine 5mg 1x1 (pagi), Captopril 12.5mg 2x1',
        'Metformin 500mg 2x1 (sesudah makan), Glibenklamid 5mg 1x1 (pagi)',
        'Omeprazole 20mg 2x1 (sebelum makan), Antasida 3x1 (sesudah makan)',
        'Cetirizine 10mg 1x1 (malam), Hydrocortisone cream 2x sehari (oles)',
        'Ibuprofen 400mg 3x1 (sesudah makan), Domperidone 10mg 3x1',
        'Amoxicillin 500mg 3x1, Bromhexine 8mg 3x1, Paracetamol 500mg 3x1',
        'Ciprofloxacin 500mg 2x1, Paracetamol 500mg 3x1 (bila demam)',
        'Betahistine 24mg 2x1, Dimenhydrinate 50mg bila perlu',
        'Allopurinol 100mg 1x1, Colchicine 0.5mg 2x1 (bila nyeri)',
    ];

    private array $notes = [
        'Pasien disarankan istirahat cukup, minum air putih minimal 2 liter/hari.',
        'Kontrol kembali 2 minggu lagi. Pantau tekanan darah secara rutin.',
        'Dianjurkan diet rendah gula, olahraga ringan 30 menit setiap hari.',
        'Hindari makanan pedas dan berminyak. Makan teratur, jangan telat.',
        'Jaga kebersihan kulit, hindari sabun yang mengandung pewangi.',
        'Kurangi kafein dan stress. Tidur cukup minimal 7-8 jam per malam.',
        'Konsumsi makanan kaya zat besi: bayam, daging merah, kacang-kacangan.',
        'Minum air putih banyak. Jaga kebersihan area kewanitaan.',
        'Hindari rokok dan asap rokok. Konsumsi obat sesuai anjuran.',
        'Batasi konsumsi purin: jeroan, seafood. Perbanyak minum air putih.',
    ];

    public function definition(): array
    {
        $faker = \Faker\Factory::create('id_ID');

        return [
            'appointment_id' => Appointment::factory()->completed(),
            'doctor_id'      => Doctor::factory(),
            'diagnosis'      => $faker->randomElement($this->diagnoses),
            'prescription'   => $faker->randomElement($this->prescriptions),
            'notes'          => $faker->randomElement($this->notes),
        ];
    }
}
