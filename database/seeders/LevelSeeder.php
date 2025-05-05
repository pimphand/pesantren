<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class LevelSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $levels = [
            'Madrasah Ibtidaiyah',
            'Madrasah Tsanawiyah',
            'Madrasah Aliyah',
            'Perguruan Tinggi',
        ];
        foreach ($levels as $level) {
            \App\Models\Level::create([
                'id' => Str::uuid(),
                'name' => $level,
            ]);
        }
    }
}
