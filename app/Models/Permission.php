<?php

namespace App\Models;

use App\Models\BaseModel;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Http\Request;

class Permission extends BaseModel
{
    use HasFactory;

    protected $fillable = [
        'name'
    ];

    protected $updatable = [
        'description' => 'string',
        'created_by' => 'string',
        'updated_by' => 'string',
        'is_active' => 'boolean'
    ];

    static function getInsertValidator(Request $request): array
    {
        return array_merge(
            [
                'name' => [
                    'required',
                    'string'
                ]
            ],
            parent::getInsertValidator($request)
        );
    }

    /**
     * @return BelongsToMany
     */
    public function roles(): BelongsToMany
    {
        return $this->belongsToMany(Role::class);
    }
}
