<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Http\Request;

class Post extends BaseModel
{

    protected $fillable = [
        'description'
    ];

    protected $updatable = [
        'description' => 'string'
    ];

    /**
     * @return BelongsTo
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    static function getInsertValidator(Request $request): array
    {
        return array_merge(
            [
                'description' => [
                    'required'
                ]
            ],
            parent::getInsertValidator($request)
        );
    }

    static function getUpdateValidator(Request $request, string $id): array
    {
        return array_merge(
            [
                'description' => [
                    'required',
                    'string'
                ]
            ],
            parent::getInsertValidator($request)
        );
    }
}
