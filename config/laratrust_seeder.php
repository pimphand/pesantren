<?php

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
            'users' => 'c,r,u,d',
            'roles' => 'c,r,u,d',
            'permissions' => 'c,r,u,d',
        ],
        'pengawas' => [

        ],
        'orang_tua' => [
            'santri' => 'c,r,u,d',
            'mutasi' => 'r',
            'pembayaran' => 'c,r',
        ],
        'santri' => [
            'transaksi' => 'c',
        ],
        'kepala_ponpes' => [

        ],
        'admin' => [
            'users' => 'c,r,u,d',
            'roles' => 'c,r,u,d',
            'permissions' => 'c,r,u,d',
        ],
        'merchant' => [
            'product' => 'c,r,u,d',
            'product_category' => 'c,r,u,d',
            'transaction' => 'c,r,u,d',
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
