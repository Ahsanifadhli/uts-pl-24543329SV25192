<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Catatan>
 */
class CatatanFactory extends Factory
{
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'nama_catatan' => fake()->sentence(3),
            'pemateri' => fake()->name(),
            'tempat' => fake()->city(),
            'tanggal' => fake()->date(),
            'isi_catatan' => fake()->paragraph(),
            'referensi' => [fake()->sentence(2), fake()->sentence(2)],
        ];
    }
}
