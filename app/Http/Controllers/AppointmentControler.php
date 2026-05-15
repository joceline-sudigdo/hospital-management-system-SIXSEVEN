<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Resources\AppointmentResource;
use App\Models\Appointment;
use App\Models\Schedule;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

/**
 * AppointmentController
 *
 * Mengelola janji temu (appointment) pasien dengan dokter.
 * Contoh pagination + complex JOIN untuk laporan.
 */
class AppointmentController extends Controller
{
    /**
     * GET /api/v1/appointments
     *
     * List appointment dengan filter berdasarkan role:
     *  - Admin    → bisa lihat semua
     *  - Dokter   → hanya appointment miliknya
     *  - Pasien   → hanya appointment miliknya
     *
     * Contoh JOIN Kompleks (SQL JOIN #3): appointment + dokter + pasien + jadwal
     */
    public function index(Request $request)
    {
        $user = Auth::user();

        // ── JOIN Kompleks: 4 tabel sekaligus ─────────────────────────────
        $query = Appointment::query()
            ->join('patients', 'appointments.patient_id', '=', 'patients.id')
            ->join('doctors', 'appointments.doctor_id', '=', 'doctors.id')
            ->join('schedules', 'appointments.schedule_id', '=', 'schedules.id')
            ->join('users as patient_users', 'patients.user_id', '=', 'patient_users.id')
            ->join('users as doctor_users', 'doctors.user_id', '=', 'doctor_users.id')
            ->select(
                'appointments.*',
                'patient_users.name as patient_name',
                'doctor_users.name  as doctor_name',
                'doctors.specialization',
                'schedules.start_time',
                'schedules.end_time',
                'schedules.day_of_week'
            );

        // ── Filter berdasarkan role ───────────────────────────────────────
        if ($user->role === 'doctor') {
            $query->where('doctors.user_id', $user->id);
        } elseif ($user->role === 'patient') {
            $query->where('patients.user_id', $user->id);
        }
        // role admin → tidak ada filter, lihat semua

        // ── Filter opsional ───────────────────────────────────────────────
        if ($request->filled('status')) {
            $query->where('appointments.status', $request->status);
        }

        if ($request->filled('date')) {
            $query->whereDate('appointments.appointment_date', $request->date);
        }

        // ── Urutkan dari yang terbaru ─────────────────────────────────────
        $query->orderBy('appointments.appointment_date', 'desc');

        // ── Pagination ────────────────────────────────────────────────────
        $perPage      = $request->input('per_page', 15);
        $appointments = $query->paginate($perPage);

        return response()->json([
            'status'  => 'success',
            'message' => 'Data appointment berhasil diambil',
            'data'    => AppointmentResource::collection($appointments),
            'meta'    => [
                'current_page'  => $appointments->currentPage(),
                'per_page'      => $appointments->perPage(),
                'total'         => $appointments->total(),
                'last_page'     => $appointments->lastPage(),
                'next_page_url' => $appointments->nextPageUrl(),
                'prev_page_url' => $appointments->previousPageUrl(),
            ],
        ]);
    }

    /**
     * POST /api/v1/appointments
     *
     * Buat janji temu baru (hanya pasien).
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'doctor_id'        => 'required|exists:doctors,id',
            'schedule_id'      => 'required|exists:schedules,id',
            'appointment_date' => 'required|date|after_or_equal:today',
            'complaint'        => 'required|string|max:1000',
        ]);

        // Cek jadwal milik dokter yang dipilih
        $schedule = Schedule::where('id', $validated['schedule_id'])
            ->where('doctor_id', $validated['doctor_id'])
            ->firstOrFail();

        // Ambil patient_id dari user yang sedang login
        $patient = Auth::user()->patient;

        if (!$patient) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Profil pasien tidak ditemukan',
            ], 404);
        }

        // Cek apakah sudah ada appointment di jadwal & tanggal yang sama
        $exists = Appointment::where('doctor_id', $validated['doctor_id'])
            ->where('schedule_id', $validated['schedule_id'])
            ->where('appointment_date', $validated['appointment_date'])
            ->whereIn('status', ['pending', 'confirmed'])
            ->exists();

        if ($exists) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Jadwal sudah penuh, pilih tanggal atau jadwal lain',
                'errors'  => ['schedule_id' => ['Jadwal tidak tersedia']],
            ], 422);
        }

        $appointment = Appointment::create([
            'patient_id'       => $patient->id,
            'doctor_id'        => $validated['doctor_id'],
            'schedule_id'      => $validated['schedule_id'],
            'appointment_date' => $validated['appointment_date'],
            'complaint'        => $validated['complaint'],
            'status'           => 'pending',
        ]);

        return response()->json([
            'status'  => 'success',
            'message' => 'Appointment berhasil dibuat',
            'data'    => new AppointmentResource($appointment->load(['doctor.user', 'patient.user', 'schedule'])),
        ], 201);
    }

    /**
     * GET /api/v1/appointments/{id}
     */
    public function show(int $id)
    {
        $appointment = Appointment::with([
            'patient.user',
            'doctor.user',
            'schedule',
            'medicalRecord',
        ])->findOrFail($id);

        return response()->json([
            'status'  => 'success',
            'message' => 'Detail appointment berhasil diambil',
            'data'    => new AppointmentResource($appointment),
        ]);
    }

    /**
     * PUT /api/v1/appointments/{id}
     *
     * Update status appointment (Admin atau Dokter).
     */
    public function update(Request $request, int $id)
    {
        $appointment = Appointment::findOrFail($id);

        $validated = $request->validate([
            'status' => 'required|in:pending,confirmed,completed,cancelled',
        ]);

        $appointment->update($validated);

        return response()->json([
            'status'  => 'success',
            'message' => 'Status appointment berhasil diperbarui',
            'data'    => new AppointmentResource($appointment),
        ]);
    }

    /**
     * DELETE /api/v1/appointments/{id}
     *
     * Batalkan appointment.
     */
    public function destroy(int $id)
    {
        $appointment = Appointment::findOrFail($id);

        // Hanya bisa batalkan kalau masih pending atau confirmed
        if (!in_array($appointment->status, ['pending', 'confirmed'])) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Appointment yang sudah selesai atau dibatalkan tidak bisa dihapus',
            ], 422);
        }

        $appointment->update(['status' => 'cancelled']);

        return response()->json([
            'status'  => 'success',
            'message' => 'Appointment berhasil dibatalkan',
        ]);
    }
}
