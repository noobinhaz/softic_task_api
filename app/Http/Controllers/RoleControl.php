<?php

namespace App\Http\Controllers;

use App\Enums\Role;
use Illuminate\Http\Request;

class RoleControl extends Controller
{
    //
    public function index(){
        return response()->json([
            'isSuccess' => true,
            'message'   => '',
            'data'      => Role::getRoles
        ], 200);
    }
}
