<?php

namespace App\Models;

use App\Models\BaseModel;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Http\Request;

class AbsenceRequest extends BaseModel
{
    use HasFactory;

    protected $fillable = [
        'reason',
        'absence_type_id'
    ];

    protected $updatable = [
        'reason' => 'string',
        'absence_type_id' => 'integer'
    ];

    const REQUEST_STATUS = [
        'Pending',
        'Accepted',
        'Denied'
    ];

    static function getStoreValidator(Request $request): array
    {
        return array_merge(
            [
                'reason' => [
                    'required',
                    'string'
                ],
                'absence_type_id' => [
                    'required',
                    'integer'
                ]
            ],
            parent::getStoreValidator($request)
        );
    }

    static function getUpdateValidator(Request $request, string $id): array
    {
        return array_merge(
            [
                'reason' => [
                    'string'
                ],
                'absence_type_id' => [
                    'integer'
                ]
            ],
            parent::getUpdateValidator($request, $id)
        );
    }

    /**
     * @return BelongsTo
     */
    public function absenceType(): BelongsTo
    {
        return $this->belongsTo(AbsenceType::class);
    }

    /**
     * @return BelongsTo
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
