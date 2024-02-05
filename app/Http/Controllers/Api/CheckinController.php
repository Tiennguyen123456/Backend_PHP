<?php
namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Services\Api\CheckinService;
use App\Models\Checkin;
use App\Http\Controllers\Controller;

class CheckinController extends Controller
{
    public function __construct(CheckinService $service)
    {
        $this->service = $service;
    }
}
