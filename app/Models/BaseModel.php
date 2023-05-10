<?php

namespace App\Models;

use App\Common\Constant;
use App\Common\GlobalVariable;
use Exception;
use Illuminate\Database\Query\Expression;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Pagination\Paginator;
use Illuminate\Validation\Rule;
use Mehradsadeghi\FilterQueryString\FilterQueryString;

/**
 * @method static insert(array $params)
 * @method static create(array $array)
 * @method static where(string $string, mixed $email)
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
    public $queryBy = 'id';
    public $showingRelations = [];
    protected $groupBy = [];
    protected $softDelete = True;

    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
        $this->filters = array_merge($this->filters, ['sort']);
        $this->groupBy = array_merge($this->groupBy);
        $this->hidden = array_merge($this->hidden, ['updated_at', 'created_at']);
    }

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
            // TODO: it's a bug here, if use withCount and select together, it won't work
            $model = $model->select($this->getAliasString());
        }
        $model = $this->filterByRelation($model);
        return $model
            ->simplePaginate($limit ?: BaseModel::CUSTOM_LIMIT)
            ->appends($request);
    }

    /**
     * @param $id
     * @return Builder|Model|object|null
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
            ->where(Constant::IS_ACTIVE, $id)
            ->select($this->getAliasString())
            ->first();
    }

    /**
     * @param Request $request
     * @return Builder|Builder[]|Collection|Model|null
     */
    public function storeWithCustomFormat(Request $request)
    {
        $keys = array_keys($this::getStoreValidator($request));
        $additionalFields = $this->getAdditionalStoreFields();
        $keys = array_merge($keys, array_keys($additionalFields));
        $insertArray = array_merge($request->toArray(), $additionalFields);
        $params = collect($keys)
            ->mapWithKeys(function ($item) use ($insertArray) {
                return [$item => $insertArray[$item]];
            })->toArray();
        $id = self::query()->insertGetId($params);
        return $this::query()->find($id);
    }

    /**
     * @param Request $request
     * @param $id
     * @return null
     */
    public function updateWithCustomFormat(Request $request, $id): ?Model
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
            return $this::update([
                Constant::IS_ACTIVE => 0
            ]);
        }
        return (bool) $this::destroy($id);
    }

    /**
     * @return Expression
     */
    public function getAliasString()
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
    static function getQueryValidator(): array
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
     */
    function filterByRelation($model)
    {
        return $model;
    }

    /**
     * @return array
     */
    protected function getAdditionalStoreFields(): array
    {
        return [];
    }
}
