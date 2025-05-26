<?php

namespace App\Http\Controllers;

use App\Services\BusinessLogicService;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    protected $businessLogicService;

    public function __construct(BusinessLogicService $businessLogicService)
    {
        $this->businessLogicService = $businessLogicService;
    }

    public function index()
    {
        return $this->businessLogicService->getIndexData();
    }

    // Other controller methods can be refactored in a similar way
}
