<?php

namespace App\Http\Controllers;

use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use App\Models\NetworkConnection;

class HomeController extends Controller
{

    /**
     * Display home page
     *
     * @return View
     */
    public function index (): View
    {
        return view( 'welcome' );
    }

    /**
     * Retrieve connections with status 0 (unreachable nodes)
     *
     * @return JsonResponse
     */
    public function getConnectionErrors(): JsonResponse
    {
        $unreachableConnections = NetworkConnection::where('status', 0)->get();

        return response()->json($unreachableConnections);
    }

}
