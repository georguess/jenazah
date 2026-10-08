<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            UserSeeder::class,
            JenazahSeeder::class,
            LaporanKehilanganSeeder::class,
            DataAntemortemSeeder::class,
            DataPostmortemSeeder::class,
            KecocokanIdentifikasiSeeder::class,
        ]);
    }
}
