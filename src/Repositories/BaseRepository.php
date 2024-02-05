<?php

namespace Laravel\Infrastructure\Repositories;

use Illuminate\Database\Eloquent\Model;

abstract class BaseRepository
{

    protected Model $model;

    public function __construct()
    {
    }

    //    abstract public function create(array $payload);
    //
    //    abstract public function update();
    //
    //    abstract public function get(array $where);
    //
    //    abstract public function delete();
    //
    //    abstract public function getById($id);

    // public function create(array $payload)
    // {
    //     $result = $this->model::create($payload);
    //     return $result;
    // }

    // public function update($where, $payload)
    // {
    //     $result = $this->model::where($where);
    // }

    // public function get($where)
    // {
    //     # code...
    // }
}
