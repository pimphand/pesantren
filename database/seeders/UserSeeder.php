<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = [
            'developer',
            'admin',
            'kepala_ponpes',
            'pengawas',
            'santri',
            'orang_tua',
            'merchant',
        ];

        foreach ($users as $user) {
            $create = User::create([
                'name' => $user,
                'username' => $user,
                'email' => $user.'@gmail.com',
                'password' => bcrypt('password'),
            ]);
            if ($user === 'santri') {
                $create->balance = 1000000;
                $create->save();
            }

            $create->addRole($user);
        }
    }
}
