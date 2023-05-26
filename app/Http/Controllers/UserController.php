<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Common\Helper;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class UserController extends Controller
{
    public $model = User::class;

    public function updateSalary(Request $request, $id)
    {
        $modelObj = $this->modelObj;
        return $modelObj->updateSalary($request, $id);
    }

    public function updateUser(Request $request)
    {
        $modelObj = $this->modelObj;
        return $modelObj->updateUser($request);
    }
}
