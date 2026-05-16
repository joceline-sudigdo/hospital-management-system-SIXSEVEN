<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\File;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class FileController extends Controller
{
    /**
     * Upload file (foto profil atau dokumen medis)
     * POST /api/v1/files/upload
     */
    public function upload(Request $request)
    {
        $request->validate([
            'file'          => 'required|file|mimes:jpeg,png,pdf|max:5120', // max 5MB
            'fileable_type' => 'required|in:patient,doctor,medical_record',
            'fileable_id'   => 'required|integer',
        ]);

        $uploadedFile = $request->file('file');

        // Sanitasi nama file (cegah path traversal)
        $originalName = basename($uploadedFile->getClientOriginalName());
        $safeName     = Str::slug(pathinfo($originalName, PATHINFO_FILENAME))
                        . '_' . time()
                        . '.' . $uploadedFile->getClientOriginalExtension();

        // Simpan ke storage/app/private/medical-files/
        $path = $uploadedFile->storeAs('private/medical-files', $safeName);

        // Resolve model class dari fileable_type
        $modelMap = [
            'patient'        => \App\Models\Patient::class,
            'doctor'         => \App\Models\Doctor::class,
            'medical_record' => \App\Models\MedicalRecord::class,
        ];

        $file = File::create([
            'fileable_type' => $modelMap[$request->fileable_type],
            'fileable_id'   => $request->fileable_id,
            'file_path'     => $path,
            'original_name' => $originalName,
            'mime_type'     => $uploadedFile->getMimeType(),
            'size'          => $uploadedFile->getSize(),
            'uploaded_by'   => $request->user()->id,
        ]);

        return response()->json([
            'status'  => 'success',
            'message' => 'File berhasil diupload.',
            'data'    => $file,
        ], 201);
    }

    /**
     * Download / stream file
     * GET /api/v1/files/{id}
     */
    public function show(Request $request, $id)
    {
        $file = File::findOrFail($id);

        // Authorization: hanya uploader atau admin yang boleh download
        if ($request->user()->role !== 'admin' && $file->uploaded_by !== $request->user()->id) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Anda tidak punya akses ke file ini.',
            ], 403);
        }

        // Cek file fisik masih ada
        if (!Storage::exists($file->file_path)) {
            return response()->json([
                'status'  => 'error',
                'message' => 'File tidak ditemukan di server.',
            ], 404);
        }

        // Stream file langsung ke browser
        return Storage::response($file->file_path, $file->original_name, [
            'Content-Type' => $file->mime_type,
        ]);
    }

    /**
     * Hapus file (soft delete: tandai deleted_at, hapus fisik via job)
     * DELETE /api/v1/files/{id}
     */
    public function destroy(Request $request, $id)
    {
        $file = File::findOrFail($id);

        // Authorization: hanya admin atau uploader
        if ($request->user()->role !== 'admin' && $file->uploaded_by !== $request->user()->id) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Anda tidak punya akses menghapus file ini.',
            ], 403);
        }

        // Hapus fisik file dari storage
        if (Storage::exists($file->file_path)) {
            Storage::delete($file->file_path);
        }

        // Hapus record dari database
        $file->delete();

        return response()->json([
            'status'  => 'success',
            'message' => 'File berhasil dihapus.',
        ], 200);
    }
}