<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class NewOrdersShadcnController extends Controller
{
    /**
     * Display the New Orders ShadCN page.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $pageTitle = 'New Orders ShadCN';
        $auth_user = authSession();
        
        return view('new-orders-shadcn.index', compact('pageTitle', 'auth_user'));
    }
}
