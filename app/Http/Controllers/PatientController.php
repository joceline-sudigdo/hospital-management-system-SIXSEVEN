<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Resources\PatientResource;
use App\Models\Patient;
use Illuminate\Http\Request;

/**
 * PatientController
 *
 * Contoh implementasi Pagination di Laravel.
 *
 * Pagination = fitur untuk memotong data besar menjadi halaman-halaman.
 * Misal: 50 pasien dibagi jadi 5 halaman, masing-masing 10 pasien per halaman.
 * Tanpa pagination → loading lama, boros memori.
 */
class PatientController extends Controller
{
    /**
     * GET /api/v1/patients
     *
     * Menampilkan semua pasien dengan pagination.
     *
     * Query parameter yang bisa dipakai:
     *   ?per_page=10   → jumlah data per halaman (default: 10)
     *   ?page=2        → halaman ke berapa (default: 1)
     *   ?search=budi   → filter nama/email pasien
     */
    public function index(Request $request)
    {
        // ── Validasi query parameter ──────────────────────────────────────
        $request->validate([
            'per_page' => 'integer|min:1|max:100', // maksimal 100 per halaman
            'search'   => 'string|max:100',
        ]);

        // ── Query builder ─────────────────────────────────────────────────
        $query = Patient::query()
            // with() = eager loading → ambil relasi sekaligus (hindari N+1 problem)
            ->with(['user:id,name,email', 'appointments'])

            // ── Contoh LEFT JOIN (SQL JOIN #1) ────────────────────────────
            // LEFT JOIN: ambil semua pasien, termasuk yang belum punya appointment
            ->leftJoin('appointments', 'patients.id', '=', 'appointments.patient_id')
            ->leftJoin('users', 'patients.user_id', '=', 'users.id')
            ->select(
                'patients.*',
                'users.name as user_name',
                'users.email as user_email',
                // COUNT appointment per pasien
                \DB::raw('COUNT(appointments.id) as total_appointments')
            )
            ->groupBy('patients.id', 'users.name', 'users.email');

        // ── Filter pencarian ──────────────────────────────────────────────
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('users.name', 'LIKE', "%{$search}%")
                  ->orWhere('users.email', 'LIKE', "%{$search}%")
                  ->orWhere('patients.phone', 'LIKE', "%{$search}%");
            });
        }

        // ── Pagination ────────────────────────────────────────────────────
        // paginate() secara otomatis:
        //   - Menghitung total data
        //   - Memotong data sesuai halaman
        //   - Membuat link prev/next
        $perPage  = $request->input('per_page', 10); // default 10
        $patients = $query->paginate($perPage);

        // ── Response dengan format envelope yang konsisten ────────────────
        return response()->json([
            'status'  => 'success',
            'message' => 'Data pasien berhasil diambil',
            'data'    => PatientResource::collection($patients),

            // Meta = informasi pagination (WAJIB ada sesuai brief)
            'meta'    => [
                'current_page' => $patients->currentPage(),   // halaman sekarang
                'per_page'     => $patients->perPage(),        // item per halaman
                'total'        => $patients->total(),          // total semua data
                'last_page'    => $patients->lastPage(),       // halaman terakhir
                'next_page_url' => $patients->nextPageUrl(),   // URL halaman berikutnya
                'prev_page_url' => $patients->previousPageUrl(), // URL halaman sebelumnya
            ],
        ], 200);
    }

    /**
     * GET /api/v1/patients/{id}
     *
     * Detail satu pasien beserta semua appointment-nya.
     * Contoh INNER JOIN (SQL JOIN #2)
     */
    public function show(int $id)
    {
        // ── INNER JOIN: appointment + dokter + pasien ─────────────────────
        // INNER JOIN hanya mengembalikan data yang cocok di KEDUA tabel
        $patient = Patient::query()
            ->join('users', 'patients.user_id', '=', 'users.id')
            ->select('patients.*', 'users.name', 'users.email')
            ->where('patients.id', $id)
            ->firstOrFail(); // 404 kalau tidak ditemukan

        // Load relasi appointment beserta dokternya
        $patient->load([
            'appointments.doctor.user',
            'appointments.medicalRecord',
        ]);

        return response()->json([
            'status'  => 'success',
            'message' => 'Detail pasien berhasil diambil',
            'data'    => new PatientResource($patient),
        ], 200);
    }

    /**
     * PUT /api/v1/patients/{id}
     */
    public function update(Request $request, int $id)
    {
        $patient = Patient::findOrFail($id);

        $validated = $request->validate([
            'date_of_birth' => 'date',
            'address'       => 'string|max:500',
            'phone'         => 'string|max:20',
        ]);

        $patient->update($validated);

        return response()->json([
            'status'  => 'success',
            'message' => 'Data pasien berhasil diperbarui',
            'data'    => new PatientResource($patient),
        ], 200);
    }
}
