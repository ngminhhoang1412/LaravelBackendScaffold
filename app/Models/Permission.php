<?php

namespace App\Models;

use App\Common\Helper;
use App\Models\BaseModel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Validator;
use Spatie\Permission\Models\Role as ModelsRole;

class Permission extends BaseModel
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description'
    ];

    protected $updatable = [
        'description' => 'string',
        'is_active' => 'boolean'
    ];

    protected $guard = 'web';

    static function getInsertValidator(Request $request): array
    {
        return array_merge(
            [
                'name' => [
                    'required',
                    'string'
                ],
                'description' => [
                    'string'
                ]
            ],
            parent::getInsertValidator($request)
        );
    }

    static function getUpdateValidator(Request $request, string $id): array
    {
        return array_merge(
            [
                'name' => [
                    'string'
                ],
                'description' => [
                    'string'
                ]
            ],
            parent::getUpdateValidator($request, $id)
        );
    }

    /**
     * @return BelongsToMany
     */
    public function roles(): BelongsToMany
    {
        return $this->belongsToMany(Role::class);
    }

    public function assignPermissionToRole(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'role_id' => [
                'required',
                'integer'
            ],
            'permissions' => [
                'required',
                'array'
            ]
        ]);

        if ($validator->fails()) {
            return Helper::getResponse('','Some values not valid!');
        }

        $role_id = $request->get('role_id');
        $permissions = (array) $request->get('permissions');

        try {
            if (Gate::allows('assignPermissionToRole')) {
                $role = ModelsRole::findById($role_id,  $this->guard);
                $role->givePermissionTo($permissions);
                return Helper::getResponse(true);
            } else {
                return Helper::getResponse('');
            }
            // if ($global->currentUser->hasPermissionTo('admin')) {
            //     $role = ModelsRole::findById($role_id,  $this->guard);
            //     $role->givePermissionTo($permissions);
            // }
        } catch (\Exception $e) {
            return Helper::getResponse('');
        }
    }
}
