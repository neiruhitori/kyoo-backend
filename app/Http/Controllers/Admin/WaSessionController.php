<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\WhatsAppService;

class WaSessionController extends Controller
{
    private WhatsAppService $waService;

    public function __construct(WhatsAppService $waService)
    {
        $this->waService = $waService;
    }

    public function index()
    {
        return view('admin.waSession.index');
    }

    public function getQr()
    {
        return $this->waService->authenticate();
    }

    public function getProfile()
    {
        return $this->waService->me();
    }
}