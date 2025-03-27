<?php

namespace App\Http\Controllers;

use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Foundation\Application;

class DashboardController extends Controller
{
    public function index(): Factory|Application|View
    {
        return view('welcome');
    }

    public function pin(): Factory|Application|View
    {
        return view('auth.pin');
    }
}
