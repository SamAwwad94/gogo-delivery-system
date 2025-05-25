<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;

class ReactTestController extends Controller
{
    public function dashboard()
    {
        return Inertia::render('Dashboard', [
            'title' => 'React Dashboard Test',
            'message' => 'Your React + TypeScript setup is working correctly!'
        ])->rootView('react-test');
    }
}
