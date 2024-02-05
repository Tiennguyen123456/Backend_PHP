<?php
namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Services\Api\OrganizerService;
use App\Models\Organizer;
use App\Http\Controllers\Controller;

class OrganizerController extends Controller
{
    public function __construct(OrganizerService $service)
    {
        $this->service = $service;
    }
}
