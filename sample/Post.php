<?php


use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Http\Request;

class Post extends \App\Models\BaseModel
{

    protected $fillable = [
        'description'
    ];

    protected $updatable = [
        'description' => 'string'
    ];

    protected $groupBy = [
        'user_id'
    ];

    /**
     * @return BelongsTo
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(\App\Models\User::class);
    }

    static function getStoreValidator(Request $request): array
    {
        return array_merge(
            [
                'description' => [
                    'required'
                ]
            ],
            parent::getStoreValidator($request)
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
            parent::getStoreValidator($request)
        );
    }

    function getAdditionalStoreFields(): array
    {
        return [
            'user_id' => 1
        ];
    }
}
