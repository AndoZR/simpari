<?php

namespace Database\Seeders;

use App\Models\Plotting;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class PlottingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 🔹 Buat plotting antara pemungut ↔ masyarakat
        Plotting::Create([
            'pemungut_id' => 3,
            'masyarakat_id' => 1,
        ]);

        Plotting::Create([
            'pemungut_id' => 3,
            'masyarakat_id' => 2,
        ]);
    }
}
