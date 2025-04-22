<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Student;
use App\Models\User;

class StudentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $santri = User::where('name', 'santri')->first();
        Student::create([
            'user_id' => $santri->id,
            'admission_number'  => random_int(1, 5),
            'national_admission_number' => random_int(5, 7),
            'address' => 'kalasan rt4 rw 30, kalasan, sleman, yogyakarta',
            'photo' => null,
            'level' => 'Madrasah Aliyah',
            'date_born' => date('2005-04-20'),
            'place_born' => 'Sleman',
            'gender' => 'L',
        ]);
    }
}
