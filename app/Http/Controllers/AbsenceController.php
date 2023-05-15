<?php

namespace App\Http\Controllers;

use App\Common\Helper;
use App\Models\AbsenceType;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class AbsenceController extends Controller
{
    public $model = AbsenceType::class;
}
