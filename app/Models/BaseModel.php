<?php

namespace App\Models;

use App\Common\GlobalVariable;
use Exception;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Pagination\Paginator;
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

    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
        $this->filters = array_merge($this->filters, ['sort']);
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
        $request = $request->only($this->filters);
        $model = with(new static)::select();
        if ($relations) {
            $model = $model->with($relations);
        }
        if ($relationsCount) {
            $model = $model->withCount($relationsCount);
        }
        $model = $model->filter();
        if (!$relationsCount) {
            // TODO: it's a bug here, if use withCount and select together, it won't work
            $model = $model->select($this->getAliasArray());
        }
        $model = $this->filterByRelation($model);
        return $model
            ->simplePaginate($limit ?: BaseModel::CUSTOM_LIMIT)
            ->appends($request);
    }

    /**
     * @param Request $request
     * @return mixed
     */
    public function insertWithCustomFormat(Request $request)
    {
        // TODO: need reformat here
        $keys = array_keys($this::getInsertValidator($request));
        $additionalFields = $this->getInsertFields();
        $keys = array_merge($keys, array_keys($additionalFields));
        $insertArray = array_merge($request->toArray(), $additionalFields);
        $params = collect($keys)
            ->mapWithKeys(function ($item) use ($insertArray) {
                return [$item => $insertArray[$item]];
            })->toArray();
        return $this::insert($params);
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
        return (bool) $this::destroy($id);
    }

    /**
     * @return array
     */
    public function getAliasArray(): array
    {
        $result = ['*'];
        foreach ($this->alias as $key => $value) {
            $result[] = $key . ' as ' . $value;
        }
        return $result;
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
    static function getInsertValidator(Request $request): array
    {
        return [];
    }

    /**
     * @param Request $request
     * @param string $id
     * @return array
     */
    static function getUpdateValidator(Request $request, string $id): array
    {
        return [];
    }

    /**
     * @param $model
     */
    function filterByRelation($model)
    {
        return $model;
    }

    /**
     * @param $id
     * @return mixed
     * By default, no permission should be granted to any record
     * This function get the user id that links to current Model's record,
     * which will determine if this user can access the requesting record or not
     */
    function getUserId($id)
    {
        return null;
    }

    /**
     * @param $id
     * @return bool
     */
    public function checkPermission($id): bool
    {
        $result = false;
        try {
            $userId = $this->getUserId($id);
            if ($userId) {
                /** @var GlobalVariable $global */
                $global = app(GlobalVariable::class);
                $result = $userId == $global->currentUser->{'id'};
            }
        } catch (Exception $e) {
        }
        return $result;
    }

    /**
     * @return array
     */
    protected function getInsertFields(): array
    {
        return [];
    }
}
