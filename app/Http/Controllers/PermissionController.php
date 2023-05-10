<?php

namespace App\Http\Controllers;

use App\Common\Helper;
use App\Models\Permission;
use Illuminate\Http\Request;
use App\Common\GlobalVariable;
use Spatie\Permission\Models\Role as ModelsRole;

class PermissionController extends Controller
{
    public $model = Permission::class;

    public function assignPermissionToRole(Request $request)
    {
        $modelObj = $this->modelObj;
        return $modelObj->assignPermissionToRole($request);
    }
}
