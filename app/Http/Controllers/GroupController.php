<?php

namespace App\Http\Controllers;

use App\Models\Group;
use Illuminate\Http\Request;

class GroupController extends Controller
{
    public $model = Group::class;

    public function getLinksFromGroup(Request $request,$id)
    {
        $modelObj = $this->modelObj;
        return $modelObj->getLinksFromGroup($request, $id);
    }
}
