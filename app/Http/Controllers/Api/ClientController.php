<?php
namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Services\Api\ClientService;
use App\Models\Client;
use App\Http\Controllers\Controller;

class ClientController extends Controller
{
    public function __construct(ClientService $service)
    {
        $this->service = $service;
    }
}
