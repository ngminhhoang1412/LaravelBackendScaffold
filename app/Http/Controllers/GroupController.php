<?php

namespace App\Http\Controllers;

use App\Models\Group;

class GroupController extends Controller
{
    public $model = Group::class;

    public function getLinksFromGroup($id)
    {
        $modelObj = $this->modelObj;
        return $modelObj->getLinksFromGroup($id);
    }
}
