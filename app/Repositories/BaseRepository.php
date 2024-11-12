<?php

namespace App\Repositories;

use Carbon\carbon;
use App\Language;
use App\Repositories\Contracts\BaseRepositoryInterface;
use Illuminate\Database\Eloquent\Model;

class BaseRepository implements BaseRepositoryInterface
{

    /**
     * The repository model
     *
     * @var \Illuminate\Database\Eloquent\Model
     */
    protected $model;


    /**
     * The query builder
     *
     * @var \Illuminate\Database\Eloquent\Builder
     */
    protected $query;


    /**
     * Alias for the query limit
     *
     * @var int
     */
    protected $take;


    /**
     * Array of related models to eager load
     *
     * @var array
     */
    protected $with = [];


    /**
     * Array of one or more where clause parameters
     *
     * @var array
     */
    protected $wheres = [];

    /**
    *where null arrays
    * @var array
    **/
    protected $whereNull = [];
    /**
     * Array of one or more where in clause parameters
     *
     * @var array
     */
    protected $whereIns = [];


    /**
     * Array of one or more where in clause parameters
     *
     * @var array
     */
    protected $selects = [];


    /**
     * Array of one or more ORDER BY column/value pairs
     *
     * @var array
     */
    protected $orderBys = [];
    protected $groupBys = [];


    /** where not null **/
    protected $whereNotNull=[];


    /**
     * BaseRepository constructor.
     *
     * @param Model $model
     */
    public function __construct(Model $model)
    {
        $this->model = $model;
    }

    public function all(){
        $this->newQuery()->eagerLoad();
        $models = $this->query->get();
        $this->unsetClauses();
        return $models;
    }

    public function get(){
        $this->newQuery()->eagerLoad()->setClauses();
        $models = $this->query->get();
        $this->unsetClauses();
        return $models;
    }

    public function findById($id){
        $this->where('id', $id);
        $this->newQuery()->eagerLoad()->setClauses();
        return $this->query->first();
    }

    public function create(array $attributes){
        $this->unsetClauses();
        return $this->model->create($attributes);
    }

    public function insert(array $attributes){
        $this->unsetClauses();
        return $this->model->insert($attributes);
    }

    public function update(array $attributes, $id= null ){
        if($id != null){
             $item = $this->model->find($id);
             $item->update($attributes);
             return $item;
        }else{
            $this->newQuery()->eagerLoad()->setClauses();
            $models = $this->query->update($attributes);
            $this->unsetClauses();
            return $models;
        }
       
        
    }

    public function deleteById($id){
        $this->unsetClauses();
        return $this->model->destroy($id);
    }

    public function delete(){
        $this->newQuery()->setClauses();
        $result = $this->query->delete();
        $this->unsetClauses();
        return $result;
    }

    public function first(){
        $this->newQuery()->eagerLoad()->setClauses();
        $model = $this->query->first();
        $this->unsetClauses();
        return $model;
    }

    public function take($limit){
        $this->take = $limit;
        return $this;
    }

    public function with($relations){
        if (is_string($relations)){
            $this->with[] = $relations;
        }elseif(is_array($relations)){
            foreach ($relations as $relation) {
                $this->with[] = $relation;
            }
        }
        return $this;
    }

    public function where($column, $value, $operator = '='){
        $this->wheres[] = compact('column', 'value', 'operator');
        return $this;
    }

    public function whereNull($column){
        $this->whereNull[] = compact('column');
        return $this;
    }

    public function whereNotNull($column){
        $this->whereNotNull[] = compact('column');
        return $this;
    }

    public function whereIn($column, $values){
        $values = is_array($values) ? $values : array($values);
        $this->whereIns[] = compact('column', 'values');
        return $this;
    }

    public function orderBy($column, $direction = 'asc'){
        $this->orderBys[] = compact('column', 'direction');
        return $this;
    }
    public function groupBy($column){
        $this->groupBys[] = compact('column');
        return $this;
    }

    public function paginate($per_page){
        $this->newQuery()->eagerLoad()->setClauses();
        $models = $this->query->paginate($per_page);
        $this->unsetClauses();
        return $models;
    }

    public function count(){
        return $this->get()->count();
    }

    public function getModel(){
        return $this->model;
    }

    public function setModel($model){
        $this->model = $model;
        return $this;
    }

    public function query(){
        return $this->model->query();
    }

    public function select($columns = ['*']){
        $this->selects = is_array($columns) ? $columns : func_get_args();
        return $this;
    }

    public function pluck($value, $key){
        $this->newQuery()->eagerLoad()->setClauses();
        $arr = $this->query->pluck($value, $key)->toArray();
        $this->unsetClauses();
        return $arr;
    }

    protected function newQuery(){
        $this->query = $this->model->newQuery();
        return $this;
    }

    protected function eagerLoad(){
        foreach($this->with as $relation){
            $this->query->with($relation);
        }
        return $this;
    }

    protected function setClauses(){
        foreach($this->wheres as $where){
            $this->query->where($where['column'], $where['operator'], $where['value']);
        }

        foreach($this->whereNull as $where){
            $this->query->whereNull($where['column']);
        }
         foreach($this->whereNotNull as $where){
            $this->query->whereNotNull($where['column']);
        }

        foreach($this->whereIns as $whereIn){
            $this->query->whereIn($whereIn['column'], $whereIn['values']);
        }

        foreach($this->orderBys as $orders){
            $this->query->orderBy($orders['column'], $orders['direction']);
        }
        foreach($this->groupBys as $groups){
            $this->query->groupBy($groups['column']);
        }
        if(count($this->selects) > 0)
            $this->query->select($this->selects);

        if(isset($this->take) && !is_null($this->take)){
            $this->query->take($this->take);
        }
        return $this;
    }

    protected function unsetClauses(){
        $this->wheres   = [];
        $this->whereIns = [];
        $this->orderBys  = [];
        $this->groupBys  = [];
        $this->selects  = [];
        $this->whereNull = [];
        $this->take     = null;
        return $this;
    }

    // common user defined methods

    public function createWithTranslations(array $data){
        if(\Auth::guard('api')->check())
        {
             $data['created_by'] = \Auth::guard('api')->user()->id;
             $data['modified_by'] = \Auth::guard('api')->user()->id;

        }
        $item = $this->create($data);
        $locales = Language::pluck('name')->toArray();
        foreach ($locales as $locale) {
            foreach($item->translatedAttributes as $attribute){
              $item->translateOrNew($locale)->$attribute = isset($data[$locale][$attribute]) ? $data[$locale][$attribute] : '';
            }
        }
        $item->save();

        return $item;
    }

    public function updateWithTranslations(array $data, $id){
        if(\Auth::guard('api')->check()) {
            $data['modified_by'] = \Auth::guard('api')->user()->id;
        }
        $item = $this->update($data, $id);
        $locales = Language::pluck('name')->toArray();
        foreach ($locales as $locale) {
            foreach($item->translatedAttributes as $attribute){
              $item->translateOrNew($locale)->$attribute = isset($data[$locale][$attribute]) ? $data[$locale][$attribute] : '';
            }
        }
        $item->save();
        return $item;
    }

    public function findWithTranslations($id){
    
        $item = $this->findById($id);
        if($item){
            foreach ($item->translations->toArray() as $transArr) {
            $item[$transArr['locale']] = array_intersect_key($transArr, array_flip($item->translatedAttributes));
           }
        }
        
        return $item;
    }


    public function convertDate($date){
        return carbon::parse($date)->format('Y-m-d');
    }

    public function getDateFormat($date){
        return carbon::parse($date)->format('d/m/Y');
    }

    public static function convertDateFromTo($date, $oldFormat, $newFormat= "Y-m-d", $fromDelimiter= '/', $toDelimiter= '-'){

        $dateArray = explode($fromDelimiter, $date);

        if(count($dateArray) != 3){
            return false;
        }else{
             
            $oldFormat = array_flip(explode($fromDelimiter, $oldFormat));
            $newFormat = explode($toDelimiter, $newFormat);
            
            $output = $dateArray[$oldFormat[$newFormat[0]]]  . $toDelimiter . $dateArray[$oldFormat[$newFormat[1]]]  . $toDelimiter. $dateArray[$oldFormat[$newFormat[2]]] ;
        }
        return $output;
    }


    public function getWithTranslations($academic_year = false){
        $this->newQuery()->eagerLoad()->setClauses();
        $models = $this->query->get();
        $this->unsetClauses();
        if($academic_year){
           $models->transform(function ($model){
            return array_merge([
                'id'   => $model->id,
                'is_active' => $model->is_active
            ], $model->getTranslationLocales());
        }); 
       }else{
        $models->transform(function ($model){
            return array_merge([
                'id'   => $model->id
            ], $model->getTranslationLocales());
        });
       }
        
        return $models;
    }

    public function paginateGetWithTranslations($perPage=10){
        $this->newQuery()->eagerLoad()->setClauses();
        $models = $this->query->paginate($perPage);
        $this->unsetClauses();
        $models->transform(function ($model){
            return array_merge([
                'id'   => $model->id,
            ], $model->getTranslationLocales());
        });
        return $models;
    }
}

