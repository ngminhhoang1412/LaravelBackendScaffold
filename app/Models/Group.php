<?php

namespace App\Models;

use Illuminate\Http\Request;
use App\Common\GlobalVariable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Group extends BaseModel
{
    use HasFactory;

    const INTERMEDIATE_TABLE =[
        'group_link'
    ];

    protected $fillable = [
        'description'
    ];

    protected $updatable = [
        'description' => 'string'
    ];
    public function links(): BelongsToMany
    {
        return $this->belongsToMany(Link::class);
    }
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
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
