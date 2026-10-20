<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AuthTest extends TestCase
{
    use RefreshDatabase;

    public function test_login_berhasil_mengembalikan_token(): void
    {
        User::factory()->create([
            'email' => 'penuntut@spira.test',
            'password' => Hash::make('rahasia123'),
        ]);

        $response = $this->postJson('/api/login', [
            'email' => 'penuntut@spira.test',
            'password' => 'rahasia123',
        ]);

        $response->assertStatus(200);
        $response->assertJsonStructure(['message', 'user', 'token']);
    }

    public function test_login_dengan_kredensial_salah_gagal_validasi(): void
    {
        User::factory()->create([
            'email' => 'penuntut@spira.test',
            'password' => Hash::make('rahasia123'),
        ]);

        $response = $this->postJson('/api/login', [
            'email' => 'penuntut@spira.test',
            'password' => 'salah',
        ]);

        $response->assertStatus(422);
    }

    public function test_endpoint_terproteksi_tanpa_token_mengembalikan_401(): void
    {
        $response = $this->getJson('/api/catatan');

        $response->assertStatus(401);
    }

    public function test_logout_mencabut_token(): void
    {
        $user = User::factory()->create([
            'email' => 'penuntut@spira.test',
            'password' => Hash::make('rahasia123'),
        ]);

        $token = $this->postJson('/api/login', [
            'email' => 'penuntut@spira.test',
            'password' => 'rahasia123',
        ])->json('token');

        $this->withHeader('Authorization', "Bearer {$token}")
            ->postJson('/api/logout')
            ->assertStatus(200);

        $this->assertCount(0, $user->fresh()->tokens);
    }
}
