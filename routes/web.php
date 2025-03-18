<?php

use App\Http\Middleware\RestrictedDocsAccess;
use Dedoc\Scramble\Scramble;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/documentation-api', function () {
    return view('scramble::docs', [
        'spec' => file_get_contents(base_path('public/api.json')),
        'config' => Scramble::getGeneratorConfig('default'),
    ]);
});
