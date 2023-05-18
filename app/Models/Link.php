<?php

namespace App\Models;

use App\Common\GlobalVariable;
use App\Common\Helper;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class Link extends BaseModel
{
    use HasFactory;

    /**
     * @var mixed
     */
    protected $fillable = [
        'amount'
    ];

    protected $filters = [
        'between',
        'user_id'
    ];

    protected $groupBy = [
        'link'
    ];

    protected $alias = [
        // 'SUM(amount)' => 'total_amount'
    ];

    protected $updatable = [
        'name' => 'string',
        'link' => 'string'
    ];

    /**
     * @return BelongsTo
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * @return BelongsToMany
     */
    public function groups(): BelongsToMany
    {
        return $this->belongsToMany(Group::class);
    }

    /**
     * @return HasMany
     */
    public function log(): HasMany
    {
        return $this->hasMany(Log::class);
    }

    /**
     * @param Request $request
     * @return array
     */
    static function getInsertValidator(Request $request): array
    {
        return array_merge(
            [
                'link' => [
                    'required'
                ],
                'name' => [
                    'string'
                ]
            ],
            parent::getInsertValidator($request)
        );
    }

    /**
     * @param Request $request
     * @param string $id
     * @return array
     */
    static function getUpdateValidator(Request $request, string $id): array
    {
        return array_merge(
            [
                'link' => [
                    'required'
                ],
                'name' => [
                    'string'
                ]
            ],
            parent::getUpdateValidator($request, $id)
        );
    }

    /**
     * @param $model
     * @return mixed
     */
    function filterByRelation($model){
        /** @var GlobalVariable $global */
        $global = app(GlobalVariable::class);
        $user_id = $global->currentUser->id;
        $user_role = $global->currentUser->role;

        if ($user_role === array_keys(User::ROLES)[0])
            return $model;
        return $model->where('user_id', $user_id);
    }

    /**
     * @param $request
     * @return array
     */
    protected function getAdditionalInsert($request){
        /** @var GlobalVariable $global */
        $global = app(GlobalVariable::class);
        $user_id = $global->currentUser->id;
        return [
            'short_link' => Str::random(7),
            'user_id' => $user_id
        ];
    }
}
