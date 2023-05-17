<?php

namespace App\Models;

use Illuminate\Database\Query\Builder;
use Illuminate\Http\Request;
use App\Common\GlobalVariable;
use App\Common\Helper;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Validation\Rule;

class Group extends BaseModel
{
    use HasFactory;

    const INTERMEDIATE_TABLE = [
        'group_link'
    ];

    protected $filters = [
        'link'
    ];

    protected $fillable = [
        'description'
    ];

    protected $updatable = [
        'description' => 'string'
    ];

    /**
     * @return BelongsToMany
     */
    public function links(): BelongsToMany
    {
        return $this->belongsToMany(Link::class);
    }

    /**
     * @return BelongsTo
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * @param Request $request
     * @return array
     */
    static function getInsertValidator(Request $request): array
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

    /**
     * @param Request $request
     * @param string $id
     * @return array
     */
    static function getUpdateValidator(Request $request, string $id): array
    {
        return array_merge(
            [
                'description' => [
                    'required',
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
    function filterByRelation($model)
    {
        /** @var GlobalVariable $global */
        $global = app(GlobalVariable::class);
        $user_id = $global->currentUser->id;
        $user_role = $global->currentUser->role;
        if ($user_role === array_keys(User::ROLES)[0])
            return $model;
        return $model->where('user_id', $user_id);
    }

    /**
     * @return array
     */
    protected function getAdditionalUpdate()
    {
        /** @var GlobalVariable $global */
        $global = app(GlobalVariable::class);
        return [
            'user_id' => $global->currentUser->id
        ];
    }

    protected function getAdditionalInsert($request)
    {
        /** @var GlobalVariable $global */
        $global = app(GlobalVariable::class);
        return [
            'user_id' => $global->currentUser->id
        ];
    }

    public function getLinksFromGroup($id)
    {
        $link_id = array();
        DB::table(Group::INTERMEDIATE_TABLE[0])
            ->where('group_id', '=', $id)
            ->get('link_id')->map(function ($value) use (&$link_id) {
                $link_id[] = $value->link_id;
            });

        return Helper::getResponse(["link_id" => $link_id]);
    }
}
