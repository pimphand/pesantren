<?php

use Dedoc\Scramble\Http\Middleware\RestrictedDocsAccess;
use Dedoc\Scramble\Scramble;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/documentation-api', function () {
    return view('scramble::docs', [
        'spec' => file_get_contents("https://raw.githubusercontent.com/pimphand/pesantren/refs/heads/main/public/api.json?token=GHSAT0AAAAAAC6DQ2Z74MQF5ZCQTOYNG7SOZ6ZGIAQ"),
        'config' => Scramble::getGeneratorConfig('default'),
    ]);
})
    ->middleware(Scramble::getGeneratorConfig('default')->get('middleware', [RestrictedDocsAccess::class]));
