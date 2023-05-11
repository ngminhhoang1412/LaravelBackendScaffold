<?php


namespace App\sample;

use App\Models\BaseModel;
use App\Models\User;
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

    protected $groupBy = [
        'user_id'
    ];

    /**
     * @return BelongsTo
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
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

    function getAdditionalStoreFields(Request $request): array
    {
        return [
            'user_id' => 1
        ];
    }
}
