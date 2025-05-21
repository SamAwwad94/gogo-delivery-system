<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class TestController extends Controller
{
    /**
     * Display a test page
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('test');
    }
}
