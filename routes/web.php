<?php

use Dedoc\Scramble\Http\Middleware\RestrictedDocsAccess;
use Dedoc\Scramble\Scramble;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/documentation-api', function () {
    return view('scramble::docs', [
        'spec' => file_get_contents(base_path('api.json')),
        'config' => Scramble::getGeneratorConfig('default'),
    ]);
})
    ->middleware(Scramble::getGeneratorConfig('default')->get('middleware', [RestrictedDocsAccess::class]));
