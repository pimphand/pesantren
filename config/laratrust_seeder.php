<?php
const CRUD = 'c,r,u,d';
return [
    /**
     * Control if the seeder should create a user per role while seeding the data.
     */
    'create_users' => false,

    /**
     * Control if all the laratrust tables should be truncated before running the seeder.
     */
    'truncate_tables' => true,

    'roles_structure' => [
        'developer' => [
            'users' => CRUD,
            'roles' => CRUD,
            'permissions' => CRUD,
            'santri' => CRUD,
            'payment' => CRUD,
            'merchant' => CRUD,
            'panel_configuration' => 'r',
            'menu'  => 'c,r,u',
        ],
        'pengawas' => [

        ],
        'orang_tua' => [
            'santri' => CRUD,
            'mutasi' => 'r,p',
            'pembayaran' => 'c,r',
        ],
        'santri' => [
            'transaksi' => 'c',
        ],
        'kepala_ponpes' => [
            'pembayaran' => 'r,re',
        ],
        'admin' => [
            'users' => CRUD,
            'roles' => CRUD,
            'permissions' => CRUD,
            'panel_configuration' => 'r',
            'menu'  => 'c,r,u',
        ],
        'merchant' => [
            'product' => CRUD,
            'product_category' => CRUD,
            'transaction' => 'c,r,u,d,p',
            'merchant' => 'r,u',
        ],
    ],

    'permissions_map' => [
        'c' => 'create',
        'r' => 'read',
        'u' => 'update',
        'd' => 'delete',
        'a' => 'approve',
        're' => 'reject',
        'p' => 'print',
        'e' => 'export',
        'i' => 'import',
    ],
];
