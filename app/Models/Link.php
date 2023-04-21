<?php

namespace App\Models;

use App\Common\GlobalVariable;
use App\Common\Helper;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
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
    /**
     * @return BelongsTo
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
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
                    'required',
                    Rule::unique(Link::retrieveTableName(), 'link')
                        ->where(function ($query) {
                            return $query->where('user_id', auth()->user()->id);
                        })
                ]
            ],
            parent::getInsertValidator($request)
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
}
