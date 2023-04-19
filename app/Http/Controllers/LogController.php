<?php
namespace App\Http\Controllers;

use App\Common\Helper;
use App\Models\Log;
use App\Models\User;
use Exception;
use App\Common\GlobalVariable;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Redirector;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class LogController extends Controller
{
    public $model = Log::class;
}