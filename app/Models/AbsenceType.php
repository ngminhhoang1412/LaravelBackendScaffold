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

    const DEFAULT_AMOUNT = 0;

    const INTERMEDIATE_TABLES = [
        'absence_types_user',
        'check_in',
        'holidays'
    ];

    const ABSENCE_TYPES = [
        'W' => 'Working day - Ngày làm việc',
        'W/2' => 'Working half day - Làm việc nửa ngày',
        'AL' => 'Annual Leave - Nghỉ phép',
        'PH' => 'Public Holiday - Nghỉ lễ',
        'WL' => 'Wedding Leave - Nghỉ cưới',
        'CL' => 'Compensation Leave - Nghỉ bù',
        'UL' => 'Unpaid Leave - Nghỉ không lương',
        'SL' => 'Sick Leave - Nghỉ ốm'
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
