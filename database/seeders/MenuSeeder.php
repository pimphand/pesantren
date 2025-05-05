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
            'Kesiswaan',
            'Payment',
            'Transaction',
            'Mutasi',
            'Panel_Configuration',
            'Menu',
            'Santri',
            'Orang_Tua',
        ];
        
        foreach ($menus as $key => $value) {
            // Display name untuk user-friendly name
            $displayName = str_replace('_', ' ', $value);
            $displayName = ucwords($displayName);
        
            // Normalisasi nama permission
            $permissionName = strtolower(str_replace(' ', '_', $displayName)) . '-read';
        
            // Cari atau buat permission
            $permission = Permission::firstOrCreate(
                ['name' => $permissionName],
                [
                    'display_name' => "Read {$displayName}",
                    'description' => "Read {$displayName}",
                ]
            );
        
            // Default route URL
            $route = strtolower(str_replace(' ', '_', $displayName));
        
            // Spesial untuk merchant
            if ($displayName === 'Merchant') {
                $route .= '_list';
            }
        
            // Atur parent menu jika perlu
            $menuId = null;
            if ($displayName === 'Menu') {
                $parent = Menu::where('name', 'Panel Configuration')->first();
                $menuId = $parent?->id;
            }
            if (in_array($displayName, ['Santri', 'Orang Tua'])) {
                $parent = Menu::where('name', 'Kesiswaan')->first();
                $menuId = $parent?->id;
            }
        
            // Hindari duplikasi menu
            $existingMenu = Menu::where('name', $displayName)->first();
            if (!$existingMenu) {
                Menu::create([
                    'permission_id' => $permission->id,
                    'name' => $displayName,
                    'url' => $route,
                    'menu_id' => $menuId,
                    'order_menu' => $key + 1,
                    'icon' => null,
                ]);
            }
        }
        
    }
}
