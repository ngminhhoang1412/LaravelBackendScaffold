<?php

namespace App\Http\Controllers;

use App\Models\Role;
use App\Common\Helper;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role as ModelsRole;

class RoleController extends Controller
{
    public $model = Role::class;

    public function assignRoleToUser(Request $request)
    {
        $modelObj = $this->modelObj;
        return $modelObj->assignRoleToUser($request);
    }
}
