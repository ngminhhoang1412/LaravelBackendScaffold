<?php

namespace App\Http\Middleware;

use App\Common\GlobalVariable;
use App\Models\User;
use Closure;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class AuthStore
{
    /**
     * Handle an incoming request.
     *
     * @param Request $request
     * @param Closure(Request): (Response|RedirectResponse) $next
     * @return Response|RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        /** @var GlobalVariable $global */
        $global = app(GlobalVariable::class);
        $global->currentUser = User::find($request->user()->id);
        return $next($request);
    }
}
