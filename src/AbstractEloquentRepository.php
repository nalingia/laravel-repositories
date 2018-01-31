<?php

namespace Nalingia\Repositories;

use Illuminate\Database\Eloquent\Model;
use \StdClass;
use \InvalidArgumentException;

abstract class AbstractEloquentRepository {
  /**
   * Model instance.
   *
   * @var \Illuminate\Database\Eloquent\Model
   */
  protected $_model;

  /**
   * Return all instances of $model.
   *
   * @param array $with
   * @return mixed
   */
  public function all(array $with = []) {
    return $this->_model->with($with)->get();
  }

  /**
   * Returns the $model instance having the given $id.
   *
   * @param $id
   * @param array $with
   * @param bool $fail
   * @return \Illuminate\Database\Eloquent\Collection|Model|mixed
   */
  public function findById($id, array $with = [], $fail = true) {
    $q = $this->_model->with($with);
    return ($fail) ? $q->findOrFail($id) : $q->find($id);
  }

  /**
   * Returns the $model having $key equals to $value.
   *
   * @param $key
   * @param $value
   * @param array $with
   * @param string $comparator
   * @param bool $fail
   * @return \Illuminate\Database\Eloquent\Model|null|static
   */
  public function getFirstBy($key, $value, array $with = [], $comparator = '=', $fail = false) {
    $q = $this->_model->with($with)->where($key, $comparator, $value);
    return $fail ? $q->firstOrFail() : $q->first();
  }

  /**
   * Returns all $models having $key equals to $value.
   *
   * @param $key
   * @param $value
   * @param array $with
   * @param string $comparator
   * @return \Illuminate\Database\Eloquent\Collection|static[]
   */
  public function getManyBy($key, $value, array $with = [], $comparator = '=') {
    return $this->_model->with($with)->where($key, $comparator, $value)->get();
  }

  /**
   * Returns all $models matchings the $where clauses.
   *
   * @param $where
   * @param array $with
   * @param bool $fail
   * @return \Illuminate\Database\Eloquent\Model|null|static
   */
  public function getFirstWhere(array $where, $with = [], $fail = false) {
    $q = $this->_model->with($with)->where($where);
    return $fail ? $q->firstOrFail() : $q->first();
  }

  /**
   * Returns all $model satisfying the $where clauses.
   *
   * @param $where
   * @param array $with
   * @param array $columns
   * @return \Illuminate\Database\Eloquent\Collection|static[]
   */
  public function getAllWhere($where, $with = [], $columns = ['*']) {
    return $this->_model->with($with)->where($where)->get($columns);
  }

  /**
   * Get paginated models.
   *
   * @param int $page
   * @param int $limit
   * @param array $with
   * @return \StdClass object with $items and $totalCount for pagination.
   */
  public function getByPage($page = 1, $limit = 10, array $with = []) {
    $result = new StdClass;
    $result->page = $page;
    $result->limit = $limit;
    $result->totalItems = 0;
    $result->items = collect([]);

    $items = $this->_model->with($with)
      ->skip($limit * ($page - 1))
      ->take($limit)
      ->get();

    $result->totalItems = $this->_model->count();
    $result->items = $items;
    return $result;
  }

  /**
   * Creates a new $model instance.
   *
   * @param array $data
   * @return \Illuminate\Database\Eloquent\Model
   */
  public function create(array $data) {
    return $this->_model->create($data);
  }

  /**
   * Updates $model having given $id.
   *
   * @param \Illuminate\Database\Eloquent\Model $model
   * @param array $data
   * @return \Illuminate\Database\Eloquent\Model|bool
   * @throws \InvalidArgumentException
   */
  public function update($model, array $data) {
    $repositoryClass = get_class($this->_model);

    if (!($model instanceof $repositoryClass)) {
      throw new InvalidArgumentException("The model is not an instance of {$repositoryClass}.");
    }

    if ($model->update($data)) {
      return $model;
    }

    return false;
  }

  /**
   * Deletes $model having given $id.
   *
   * @param \Illuminate\Database\Eloquent\Model $model
   * @return bool
   * @throws \InvalidArgumentException
   * @throws \Exception
   */
  public function delete($model) {
    $repositoryClass = get_class($this->_model);

    if (!($model instanceof $repositoryClass)) {
      throw new InvalidArgumentException("The model is not an instance of {$repositoryClass}.");
    }

    return $model->delete();
  }

  /**
   * Truncates $model's table.
   *
   * @return void
   */
  public function truncate() {
    $this->_model->truncate();
  }
}