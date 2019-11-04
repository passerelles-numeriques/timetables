<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\AdminService;

class AdminController extends Controller
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
     * Function for the authentification
     *
     * @param Request $request
     * @return boolean
     */
    public function isAdmin(Request $request)
    {
        $user = $request->input('user');
        $password = $request->input('password');
        return response()->json(AdminService::isAdmin($user, $password));
    }
}
