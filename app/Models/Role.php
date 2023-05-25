<?php

namespace App\Models;

use App\Common\Helper;
use App\Models\BaseModel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Validator;

class Role extends BaseModel
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

    static function getStoreValidator(Request $request): array
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
            parent::getStoreValidator($request)
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
    public function permissions(): BelongsToMany
    {
        return $this->belongsToMany(Permission::class);
    }

    public function assignRoleToUser(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => [
                'required',
                'integer'
            ],
            'role' => [
                'required',
                'string'
            ]
        ]);

        if ($validator->fails()) {
            return Helper::getResponse('', 'Some values not valid!');
        }

        $user_id = $request->get('user_id');
        $role = $request->get('role');

        try {
            if (Gate::allows('assignRoleToUser')) {
                $user = User::find($user_id);
                $user->assignRole($role);
                return Helper::getResponse(true);
            } else {
                return Helper::getResponse('');
            }
            // if ($global->currentUser->hasPermissionTo('admin')) {
            //     $user = User::find($user_id);
            //     $user->assignRole($role);
            // }
        } catch (\Exception $e) {
            return Helper::getResponse('');
        }
    }
}
