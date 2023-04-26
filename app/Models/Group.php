<?php

namespace App\Models;

use App\Common\GlobalVariable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Http\Request;

class Group extends BaseModel
{
    use HasFactory;

    public function links(): HasMany
    {
        return $this->hasMany(Link::class);
    }
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    static function getInsertValidator(Request $request): array
    {
        return array_merge(
            [
                'group_id' =>[
                    'required',
                    'array'
                ]
            ],
            parent::getInsertValidator($request)
        );
    }

    function filterByRelation($model)
    {
        $global = app(GlobalVariable::class);
        $user_id = $global->currentUser->id;
        $user_role = $global->currentUser->role;
        if ($user_role === array_keys(User::ROLES)[0])
            return $model;
        return $model->where('user_id', $user_id);
    }
}
