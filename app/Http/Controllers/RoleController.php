<?php

namespace App\Http\Controllers;

use App\Common\GlobalVariable;
use App\Common\Helper;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Spatie\Permission\Models\Role as ModelsRole;

class RoleController extends Controller
{
    public $model = Role::class;

    public function assignRoleToUser(Request $request)
    {
        $user_id = $request->get('user_id');
        $role = $request->get('roles');

        DB::beginTransaction();
        try {
            if (Gate::allows('assignRoleToUser')) {
                $user = User::find($user_id);
                $user->assignRole($role);
            }
            // if ($global->currentUser->hasPermissionTo('admin')) {
            //     $user = User::find($user_id);
            //     $user->assignRole($role);
            // }
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
        }
    }
}
