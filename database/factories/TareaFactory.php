<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class TareaFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'descripcion' => $this->faker->sentence,
            'prioridad' => $this->faker->randomElement(['Alta', 'Media', 'Baja']),
            'fecha_limite' => $this->faker->dateTimeBetween('now', '+1 month')->format('Y-m-d'),
            'responsable' => null, // Cambiar según tus necesidades
            'estado' => $this->faker->randomElement(['Pendiente', 'En progreso', 'Completada', 'En revisión', 'Aplazada', 'Bloqueada', 'Cancelada', 'Rechazada']),
        ];
    }
}
