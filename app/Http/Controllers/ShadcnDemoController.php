<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\DataTables\ShadcnOrderDataTable;

class ShadcnDemoController extends Controller
{
    /**
     * Display a listing of the orders with ShadCN styling.
     *
     * @param ShadcnOrderDataTable $dataTable
     * @return \Illuminate\Http\Response
     */
    public function orders(ShadcnOrderDataTable $dataTable)
    {
        $pageTitle = 'Orders';
        $auth_user = authSession();
        $assets = ['datatable'];
        $button = '<a href="' . route('order.create') . '" class="shadcn-button shadcn-button-primary">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="mr-2"><path d="M5 12h14"></path><path d="M12 5v14"></path></svg>
                ' . __('message.add_form_title', ['form' => __('message.order')]) . '
            </a>';

        return $dataTable->render('layouts.shadcn-datatable', compact('pageTitle', 'button', 'auth_user', 'assets'));
    }
}
