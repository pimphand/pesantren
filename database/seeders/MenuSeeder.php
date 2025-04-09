<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Menu;
use App\Models\Permission;

class MenuSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $menus = [
            'Merchant',
            'Santri',
            'Transaction',
            'Mutasi',
            'Panel Configuration',
            'Menu',
        ];
        
        foreach ($menus as $key => $value) {
            $permission = Permission::where('name', 'ILIKE', '%read%')
                ->where('name', 'ILIKE', "%$value%")
                ->first();
                if($permission) {
                    $route = strtolower(str_replace(' ', '_', $value));
                    if($value == 'Merchant') {
                        $route = strtolower(str_replace(' ', '_', $value)).'_list';
                    }
            
                    Menu::create([
                        'permission_id' => $permission->id,
                        'name' => $value,
                        'url' => $route,
                        'menu_id' => ($value == 'Menu') ? 5 : null,
                        'order_menu' => $key + 1,
                        'icon' => null,
                    ]);
                } else {
                    $pemrissionId = Permission::insertGetId([
                        'name'  => strtolower(str_replace(' ', '_', $value)) . '-read',
                        'display_name'  => 'Read ' . $value,
                        'description'   => 'Read ' . $value,
                    ]);

                    Menu::create([
                        'permission_id' => $pemrissionId,
                        'name' => $value,
                        'url' => strtolower(str_replace(' ', '_', $value)),
                        'menu_id' => ($value == 'Menu') ? 5 : null,
                        'order_menu' => $key + 1,
                        'icon' => null,
                    ]);
                }
        }
        
    }
}
