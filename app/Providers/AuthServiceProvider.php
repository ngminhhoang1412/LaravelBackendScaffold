<?php

namespace App\Providers;

use App\Common\GlobalVariable;
use App\Models\User;
use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Spatie\Permission\Traits\HasPermissions;
use Spatie\Permission\Traits\HasRoles;

class AuthServiceProvider extends ServiceProvider
{
    use HasRoles;
    use HasPermissions;
    /**
     * The policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        // 'App\Models\Model' => 'App\Policies\ModelPolicy',
    ];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerPolicies();
        
        $global = app(GlobalVariable::class);
        Gate::define('assignRoleToUser', function ($user) use ($global) {
            $user = $global->currentUser;
            // The user need to HAVE THE ROLE in intermediate table
            if ($user->hasPermissionTo(User::ABILITIES[0])) {
                return true;
            }
            return false;
        });

        Gate::define('assignPermissionToRole', function ($user) use ($global) {
            $user = $global->currentUser;
            // The user need to HAVE THE ROLE in intermediate table
            if ($user->hasPermissionTo(User::ABILITIES[0])) {
                return true;
            }
            return false;
        });
    }
}
