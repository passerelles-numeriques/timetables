<?php

namespace App\Http\Controllers;

use App\Services\DataFileManager;

class AppController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Return the last time each data file was modified
     * As a JSON object
     *
     * @return mixed list of modification times
     */
    public function getModificationTimes(){
        try {
            $times = DataFileManager::getLastModifiedTime();
            return response()->json($times);
        } catch (\Exception $e) {
            return response($e->getMessage(), 500);
        }
    }
}
