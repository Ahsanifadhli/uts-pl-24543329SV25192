<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Catatan;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CatatanController extends Controller
{
    /**
     * Menampilkan seluruh catatan milik pengguna yang sedang login.
     */
    public function index(Request $request): JsonResponse
    {
        $catatan = $request->user()->catatan()->latest()->get();

        return response()->json($catatan);
    }

    /**
     * Menyimpan catatan baru.
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $this->validateData($request);

        $catatan = $request->user()->catatan()->create($validated);

        return response()->json($catatan, 201);
    }

    /**
     * Menampilkan satu catatan.
     */
    public function show(Request $request, Catatan $catatan): JsonResponse
    {
        $this->authorizeOwner($request, $catatan);

        return response()->json($catatan);
    }

    /**
     * Memperbarui catatan.
     */
    public function update(Request $request, Catatan $catatan): JsonResponse
    {
        $this->authorizeOwner($request, $catatan);

        $validated = $this->validateData($request);

        $catatan->update($validated);

        return response()->json($catatan);
    }

    /**
     * Menghapus catatan.
     */
    public function destroy(Request $request, Catatan $catatan): JsonResponse
    {
        $this->authorizeOwner($request, $catatan);

        $catatan->delete();

        return response()->json(['message' => 'Catatan berhasil dihapus.']);
    }

    /**
     * Validasi data catatan. Gagal validasi menghasilkan respons 422.
     */
    private function validateData(Request $request): array
    {
        return $request->validate([
            'nama_catatan' => 'required|string|max:255',
            'pemateri' => 'required|string|max:255',
            'tempat' => 'required|string|max:255',
            'tanggal' => 'required|date',
            'isi_catatan' => 'required|string',
            'referensi' => 'nullable|array',
            'referensi.*' => 'string|max:255',
        ]);
    }

    /**
     * Memastikan catatan dimiliki oleh pengguna yang sedang login.
     */
    private function authorizeOwner(Request $request, Catatan $catatan): void
    {
        abort_unless($catatan->user_id === $request->user()->id, 403, 'Akses ditolak.');
    }
}
