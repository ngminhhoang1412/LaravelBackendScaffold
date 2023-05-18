<?php

namespace App\Http\Controllers;

use App\Models\Permission;
use Illuminate\Http\Request;

class PermissionController extends Controller
{
    public $model = Permission::class;

    public function assignPermissionToRole(Request $request)
    {
        $modelObj = $this->modelObj;
        return $modelObj->assignPermissionToRole($request);
    }
}
