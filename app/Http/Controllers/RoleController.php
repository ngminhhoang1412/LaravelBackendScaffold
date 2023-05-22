<?php

namespace App\Http\Controllers;

use App\Models\Role;
use Illuminate\Http\Request;

class RoleController extends Controller
{
    public $model = Role::class;

    public function assignRoleToUser(Request $request)
    {
        $modelObj = $this->modelObj;
        return $modelObj->assignRoleToUser($request);
    }
}
