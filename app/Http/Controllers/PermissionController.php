<?php

namespace App\Http\Controllers;

use App\Common\GlobalVariable;
use App\Models\Permission;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Spatie\Permission\Models\Role as ModelsRole;

class PermissionController extends Controller
{
    public $model = Permission::class;
    protected $guard = 'web';

    public function assignPermissionToRole(Request $request)
    {
        $role_id = $request->get('role_id');
        $permissions = (array) $request->get('permissions');

        DB::beginTransaction();
        try {
            if (Gate::allows('assignPermissionToRole')) {
                $role = ModelsRole::findById($role_id,  $this->guard);
                $role->givePermissionTo($permissions);
            }
            // if ($global->currentUser->hasPermissionTo('admin')) {
            //     $role = ModelsRole::findById($role_id,  $this->guard);
            //     $role->givePermissionTo($permissions);
            // }
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
        }
    }
}
