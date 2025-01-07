<?php

namespace App\Http\Controllers;

use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\File;

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
     * Get resources
     *
     * @return JsonResponse
     */
    public function getConnectionErrors(): JsonResponse
    {
        $directory = storage_path('app/private/data');

        $files = File::files($directory);
        $mergedData = [];

        foreach ($files as $file) {
            if ($file->getExtension() === 'json') {
                $content = File::get($file->getPathname());
                $data = json_decode($content, true);

                if (is_array($data)) {
                    $mergedData = array_merge($mergedData, $data);
                }
            }
        }

        return response()->json($mergedData);
    }
}
