<?php

namespace Database\Seeders;

use App\Models\Tarea;
use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        //Tarea::factory(10000)->create();

        $user = new User();
        $user->name = "Administrador";
        $user->email = "admin@gmail.com";
        $user->password = '$2y$10$Y8woSBY4JAUYYQwA9crX6.XKZP1kGlFlB0LR5IhxBFWLc0vcYiJZy'; //12345678
        $user->save();
    }
}
