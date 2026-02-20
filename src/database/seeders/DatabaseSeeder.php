<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use BADDIServices\ClnkGO\Database\Seeders\UsersSeeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            UsersSeeder::class,
        ]);
    }
}
