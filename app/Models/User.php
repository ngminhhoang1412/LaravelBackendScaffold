<?php

namespace App\Models;

use App\Common\Helper;
use Illuminate\Http\Request;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Spatie\Permission\Traits\HasRoles;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Validator;
use Spatie\Permission\Traits\HasPermissions;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;

/**
 * @method static insert(array $params)
 * @method static create(array $array)
 * @method static where(string $string, mixed $param)
 * @method static find($id)
 * @property mixed $id
 * @property string $table
 * @property mixed $worker
 * @property mixed $role
 */
class User extends Authenticatable
{
    const TABLE_NAME = 'users';

    use HasApiTokens, HasFactory, Notifiable;
    use HasRoles;
    use HasPermissions;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'otp',
        'last_sent',
        'confirm_email'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];
    const ABILITIES = [
        "admin",
        "creator",
        "leader",
        "insight",
        "hr",
        "finance",
        "user-manage"
    ];

    const ROLES = [
        "admin" => self::ABILITIES,
        "leader" => [],
        "accountant" => [],
        "hr" => [],
        "finance" => [],
        "guest" => []
    ];

    /**
     * @return HasMany
     */
    public function absenceRequests(): HasMany
    {
        return $this->hasMany(AbsenceRequest::class);
    }

    public function updateSalary(Request $request)
    {
        $validator = Validator::make(
            $request->all(),
            [
                'user_id' => [
                    'required',
                    'integer'
                ],
                'salary' => [
                    'required',
                    'integer'
                ]
            ]
        );

        if ($validator->fails()) {
            return Helper::getResponse('', 'Some values was not valid!');
        }

        $user_id = $request->get('user_id');
        $salary = $request->get('salary');
        try {
            if (Gate::allows('updateSalary')) {
                DB::table('users')
                    ->where('id', '=', $user_id)
                    ->update([
                        'salary' => $salary
                    ]);

                return Helper::getResponse(true);
            } else {
                return Helper::getResponse('', 'Unauthorized');
            }
        } catch (\Throwable $th) {
            return Helper::getResponse('');
        }
    }
}
