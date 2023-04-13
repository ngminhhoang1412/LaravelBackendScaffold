<?php

namespace App\Http\Controllers;

use App\Common\Helper;
use App\Models\Link;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class LinkController extends Controller
{
    public $model = Link::class;
}