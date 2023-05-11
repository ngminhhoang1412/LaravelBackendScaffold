<?php

namespace App\Models;

use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;
use Spatie\Permission\Traits\HasPermissions;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;

/**
 * @method static insert(array $params)
 * @method static create(array $array)
 * @method static where(string $string, mixed $email)
 * @method static find($id)
 * @property mixed $id
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
        "admin" => [
            self::ABILITIES[0],
            self::ABILITIES[1],
            self::ABILITIES[2],
            self::ABILITIES[3],
            self::ABILITIES[4],
            self::ABILITIES[5],
            self::ABILITIES[6],
        ],
        "leader" => [],
        "accountant" => [],
        "hr" => [],
        "finance" => [],
        "guest" => []
    ];
}
