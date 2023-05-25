<?php

namespace App\Models;

use App\Models\BaseModel;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Http\Request;

class AbsenceType extends BaseModel
{
    use HasFactory;

    protected $fillable = [
        'code',
        'description'
    ];

    protected $updatable = [
        'code' => 'string',
        'description' => 'string'
    ];

    const INTERMEDIATE_TABLES = [
        'absence_types_user',
        'check_in',
        'holidays'
    ];

    const ABSENCE_TYPES = [
        'W' => [
            'description' => 'Working day - Ngày làm việc',
            'default_amount' => 0
        ],
        'W/2' => [
            'description' => 'Working half day - Làm việc nửa ngày',
            'default_amount' => 0
        ],
        'AL' => [
            'description' => 'Annual Leave - Nghỉ phép',
            'default_amount' => 0
        ],
        'PH' => [
            'description' => 'Public Holiday - Nghỉ lễ',
            'default_amount' => 0
        ],
        'WL' => [
            'description' => 'Wedding Leave - Nghỉ cưới',
            'default_amount' => 0
        ],
        'CL' => [
            'description' => 'Compensation Leave - Nghỉ bù',
            'default_amount' => 0
        ],
        'UL' => [
            'description' => 'Unpaid Leave - Nghỉ không lương',
            'default_amount' => 0
        ],
        'SL' => [
            'description' => 'Sick Leave - Nghỉ ốm',
            'default_amount' => 0
        ]
    ];

    static function getStoreValidator(Request $request): array
    {
        return array_merge(
            [
                'code' => [
                    'string',
                    'required'
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
                'code' => [
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
    public function users() : BelongsToMany
    {
        return $this->belongsToMany(User::class);
    }

    /**
     * @return HasMany
     */
    public function absenceRequests(): HasMany
    {
        return $this->hasMany(AbsenceRequest::class);
    }
}
