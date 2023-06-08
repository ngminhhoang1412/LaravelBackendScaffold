<?php

namespace App\Models;

use App\Common\Constant;
use Exception;
use Illuminate\Database\Query\Expression;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;
use App\Common\GlobalVariable;
use Illuminate\Pagination\Paginator;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Validation\Rule;
use Mehradsadeghi\FilterQueryString\FilterQueryString;

/**
 * @method static insert(array $params)
 * @method static create(array $array)
 * @method static select()
 * @method static find($id)
 * @method static where(string $string, string $id)
 */
class BaseModel extends Model
{
    use HasFactory;
    use FilterQueryString;

    const CUSTOM_LIMIT = 10;

    protected $filters = [];
    protected $fillable = [];
    protected $hidden = [];
    protected $alias = [];
    protected $updatable = [];
    /**
     NOTE: $updatable should looks like this, either bool or anything else
     protected $updatable = [
        'a' => 'string',
        'b' => 'bool',
        'c' => 'int',
    ];
     */
    public $queryBy = 'id';
    public $showingRelations = [];
    protected $groupBy = [];
    protected $softDelete = True;

    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
        $this->filters = array_merge($this->filters, ['sort']);
        $this->groupBy = array_merge($this->groupBy);
        $this->hidden = $this->getHiddenField();
    }

    /**
     * @return mixed
     */
    public static function retrieveTableName()
    {
        return with(new static)->getTable();
    }

    /**
     * @param Request $request
     * @return mixed|Paginator
     */
    public function queryWithCustomFormat(Request $request)
    {
        $limit = $request->get('limit');
        $relations = $request->{'relation'};
        $relationsCount = $request->{'relationCount'};
        $groupBy = $this->groupBy;
        $preFilteredRequest = $request;
        $request = $request->only($this->filters);
        $model = with(new static)::select();
        if ($relations) {
            $model = $model->with($relations);
        }
        if ($relationsCount) {
            $model = $model->withCount($relationsCount);
        }
        if ($groupBy) {
            DB::statement("SET SESSION sql_mode=(SELECT REPLACE(@@sql_mode,'ONLY_FULL_GROUP_BY',''))");
            $model = $model->groupBy($groupBy);
        }
        $model = $model->where(Constant::IS_ACTIVE, 1);
        $model = $model->filter();
        if (!$relationsCount) {
            // DB::statement("SET SESSION sql_mode = 'STRICT_ALL_TABLES'");
            // TODO: it's a bug here, if use withCount and select together, it won't work
            $model = $model->select($this->getAliasString());
        }
        $model = $this->filterByRelation($model, $preFilteredRequest);
        return $model
            ->paginate($limit ?: BaseModel::CUSTOM_LIMIT)
            ->appends($request);
    }

    /**
     * @param $id
     * @return Model|object|null
     */
    public function showWithCustomFormat($id)
    {
        return $this::query()
            ->with($this->showingRelations)
            ->where(function (Builder $query) use ($id) {
                $query
                    ->where($this->queryBy, $id)
                    ->orWhere('id', $id);
            })
            ->where(Constant::IS_ACTIVE, 1)
            ->select($this->getAliasString())
            ->first();
    }

    /**
     * @param Request $request
     * @return Collection|Model|null
     */
    public function storeWithCustomFormat(Request $request)
    {
        $keys = array_keys($this::getStoreValidator($request));
        $additionalFields = $this->getAdditionalStoreFields($request);
        $params = array_merge(
            collect($request->all())->only($keys)->toArray(),
            $additionalFields
        );
        $id = self::query()->insertGetId($params);
        return $this::query()->find($id);
    }

    /**
     * @param Request $request
     * @param $id
     * @return Model|null
     */
    public function updateWithCustomFormat(Request $request, $id)
    {
        try {
            /** @var Model $model */
            $model = with(new static)::find($id);
            if ($model) {
                foreach (collect($request->all())->only(array_keys($this->updatable)) as $key => $item) {
                    if ($this->updatable[$key] == 'bool') {
                        $item = (bool) $item;
                    } elseif ($this->updatable[$key] == 'int') {
                        $item = (int) $item;
                    }
                    $model->{$key} = $item;
                }
                /** @var GlobalVariable $global */
                $global = app(GlobalVariable::class);
                $model->{Constant::UPDATED_BY} = $global->currentUser->id;
                $model->save();
            }
        } catch (Exception $e) {
            $model = null;
        }
        return $model;
    }

    /**
     * @param $id
     * @return bool
     */
    public function destroyWithCustomFormat($id): bool
    {
        if ($this->softDelete) {
            /** @var GlobalVariable $global */
            $global = app(GlobalVariable::class);
            return $this::where('id', $id)->update([
                Constant::IS_ACTIVE => 0,
                Constant::UPDATED_BY => $global->currentUser->id
            ]);
        }
        return (bool) $this::destroy($id);
    }

    /**
     * @return Expression
     */
    public function getAliasString(): Expression
    {
        $result = '*';
        foreach ($this->alias as $key => $value) {
            $result = $result . ',' . $key . ' as ' . $value;
        }
        return DB::raw($result);
    }

    /**
     * @return array
     */
    static function getQueryValidator(Request $request): array
    {
        return [
            'limit' => [
                'numeric',
                'gte:0'
            ]
        ];
    }

    /**
     * @param Request $request
     * @return array
     */
    static function getStoreValidator(Request $request): array
    {
        return [];
    }

    /**
     * @param Request $request
     * @param string $id
     * @return array
     * Only allow update on active records
     */
    static function getUpdateValidator(Request $request, string $id): array
    {
        return [
            Rule::exists(self::retrieveTableName())
                ->where(function (Builder $query) use ($id) {
                return $query
                    ->where('id', $id)
                    ->where(Constant::IS_ACTIVE, 1);
            })
        ];
    }

    /**
     * @param $model
     * @param Request $request
     * @return mixed
     */
    function filterByRelation($model,Request $request)
    {
        return $model;
    }

    /**
     * @param Request $request
     * @return array
     */
    protected function getAdditionalStoreFields(Request $request): array
    {
        /** @var GlobalVariable $global */
        $global = app(GlobalVariable::class);
        return [
            Constant::CREATED_BY => $global->currentUser->id,
            Constant::UPDATED_BY => $global->currentUser->id
        ];
    }

    /**
     * @return array
     */
    protected function getHiddenField()
    {
        return [
            Constant::CREATED_BY,
            Constant::UPDATED_BY,
            Constant::CREATED_AT,
            Constant::UPDATED_AT,
            Constant::IS_ACTIVE
        ];

    }
}
