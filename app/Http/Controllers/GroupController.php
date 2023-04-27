<?php

namespace App\Http\Controllers;

use Exception;
use App\Models\Link;
use App\Models\User;
use App\Models\Group;
use App\Common\Helper;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Common\GlobalVariable;
use Illuminate\Support\Facades\DB;

class GroupController extends Controller
{
    public $model = Group::class;

}
