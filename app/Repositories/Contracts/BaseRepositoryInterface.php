<?php

namespace App\Repositories\Contracts;

interface BaseRepositoryInterface
{
    public function all();

    public function get();

    public function findById($id);

    public function create(array $attributes);

    public function insert(array $attributes);

    public function update(array $attributes, $id=null);

    public function deleteById($id);

    public function delete();

    public function first();

    public function take($limit);

    public function with($relations);

    public function where($column, $value, $operator = '=');

    public function whereIn($column, $values);

    public function orderBy($column, $direction = 'asc');

    public function paginate($per_page);

    public function count();

    public function getModel();

    public function setModel($model);

    public function query();

    public function select($columns = ['*']);

    public function pluck($value, $key);

    public function createWithTranslations(array $data);

    public function updateWithTranslations(array $data, $id);

    public function findWithTranslations($id);

    public function convertDate($date);

    public static function convertDateFromTo($date, $oldFormat, $newFormat="Y-m-d", $delimiter='/');

    public function getDateFormat($date);

    public function getWithTranslations();

    public function paginateGetWithTranslations($perPage=10);

}

