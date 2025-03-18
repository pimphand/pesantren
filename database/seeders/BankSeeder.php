<?php

namespace Database\Seeders;

use App\Models\Bank;
use Illuminate\Database\Seeder;

class BankSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Bank::create([
            'name' => 'Bank BCA',
            'logo' => 'bca.png',
            'account_number' => '1234567890',
            'account_name' => 'PT. Pondok Pesantren',
        ]);
    }
}
