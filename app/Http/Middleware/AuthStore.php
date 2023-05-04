<?php

namespace App\Http\Middleware;

use Closure;
use App\Models\User;
use Illuminate\Http\Request;
use App\Common\GlobalVariable;
use Illuminate\Http\RedirectResponse;

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
