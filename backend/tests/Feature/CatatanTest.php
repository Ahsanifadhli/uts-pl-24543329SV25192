<?php

namespace Tests\Feature;

use App\Models\Catatan;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CatatanTest extends TestCase
{
    use RefreshDatabase;

    private function contohData(): array
    {
        return [
            'nama_catatan' => 'Kajian Tafsir Al-Fatihah',
            'pemateri' => 'Ustadz Ahmad',
            'tempat' => 'Masjid Kampus UGM',
            'tanggal' => '2026-10-20',
            'isi_catatan' => 'Ringkasan pembahasan kajian.',
            'referensi' => ['Tafsir Ibnu Katsir', 'Tafsir As-Sa\'di'],
        ];
    }

    public function test_dapat_menampilkan_daftar_catatan(): void
    {
        $user = User::factory()->create();
        Catatan::factory()->count(3)->for($user)->create();

        $this->actingAs($user)
            ->getJson('/api/catatan')
            ->assertStatus(200)
            ->assertJsonCount(3);
    }

    public function test_dapat_menambah_catatan_mengembalikan_201(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)
            ->postJson('/api/catatan', $this->contohData());

        $response->assertStatus(201);
        $response->assertJsonFragment(['nama_catatan' => 'Kajian Tafsir Al-Fatihah']);
        $this->assertDatabaseHas('catatans', [
            'nama_catatan' => 'Kajian Tafsir Al-Fatihah',
            'user_id' => $user->id,
        ]);
    }

    public function test_validasi_gagal_mengembalikan_422(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)
            ->postJson('/api/catatan', [
                'nama_catatan' => '',
            ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['nama_catatan', 'pemateri', 'tempat', 'tanggal', 'isi_catatan']);
    }

    public function test_dapat_menampilkan_satu_catatan(): void
    {
        $user = User::factory()->create();
        $catatan = Catatan::factory()->for($user)->create();

        $this->actingAs($user)
            ->getJson("/api/catatan/{$catatan->id}")
            ->assertStatus(200)
            ->assertJsonFragment(['id' => $catatan->id]);
    }

    public function test_dapat_mengubah_catatan(): void
    {
        $user = User::factory()->create();
        $catatan = Catatan::factory()->for($user)->create();

        $data = $this->contohData();
        $data['nama_catatan'] = 'Judul Diperbarui';

        $this->actingAs($user)
            ->putJson("/api/catatan/{$catatan->id}", $data)
            ->assertStatus(200)
            ->assertJsonFragment(['nama_catatan' => 'Judul Diperbarui']);
    }

    public function test_dapat_menghapus_catatan(): void
    {
        $user = User::factory()->create();
        $catatan = Catatan::factory()->for($user)->create();

        $this->actingAs($user)
            ->deleteJson("/api/catatan/{$catatan->id}")
            ->assertStatus(200);

        $this->assertDatabaseMissing('catatans', ['id' => $catatan->id]);
    }
}
