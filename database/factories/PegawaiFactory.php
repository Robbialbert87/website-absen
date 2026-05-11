<?php

namespace Database\Factories;

use App\Models\Pegawai, Ruangan, Shift;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Pegawai>
 */
class PegawaiFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
              'nip' => $this->faker->unique()->numerify('#########'),
            'nama' => $this->faker->name,
            'ruangan_id' => Ruangan::all()->random()->id,
            'jabatan' => $this->faker->jobTitle,
            'kategori_kerja' => $this->faker->randomElement(['non_shift', 'shift']),
            'shift_id' => Shift::all()->random()->id,
            'status_aktif' => $this->faker->boolean,
        ];
    }
}
