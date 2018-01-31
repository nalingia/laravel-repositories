<?php

namespace Nalingia\Repositories;

interface AbstractEloquentRepositoryContract {

  /**
   * Return all instances of $model.
   *
   * @param array $with
   * @return mixed
   */
  public function all(array $with = []);

  /**
   * Returns the $model instance having the given $id.
   *
   * @param $id
   * @param array $with
   * @param bool $fail
   * @return \Illuminate\Database\Eloquent\Collection|Model|mixed
   */
  public function findById($id, array $with = [], $fail = true);

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
  public function getFirstBy($key, $value, array $with = [], $comparator = '=', $fail = false);

  /**
   * Returns all $models having $key equals to $value.
   *
   * @param $key
   * @param $value
   * @param array $with
   * @param string $comparator
   * @return \Illuminate\Database\Eloquent\Collection|static[]
   */
  public function getManyBy($key, $value, array $with = [], $comparator = '=');

  /**
   * Returns all $models matchings the $where clauses.
   *
   * @param $where
   * @param array $with
   * @param bool $fail
   * @return \Illuminate\Database\Eloquent\Model|null|static
   */
  public function getFirstWhere(array $where, $with = [], $fail = false);

  /**
   * Returns all $model satisfying the $where clauses.
   *
   * @param $where
   * @param array $with
   * @param array $columns
   * @return \Illuminate\Database\Eloquent\Collection|static[]
   */
  public function getAllWhere($where, $with = [], $columns = ['*']);

  /**
   * Get paginated models.
   *
   * @param int $page
   * @param int $limit
   * @param array $with
   * @return \StdClass object with $items and $totalCount for pagination.
   */
  public function getByPage($page = 1, $limit = 10, array $with = []);

  /**
   * Creates a new $model instance.
   *
   * @param array $data
   * @return \Illuminate\Database\Eloquent\Model
   */
  public function create(array $data);

  /**
   * Updates $model having given $id.
   *
   * @param \Illuminate\Database\Eloquent\Model $model
   * @param array $data
   * @return \Illuminate\Database\Eloquent\Model|bool
   * @throws \InvalidArgumentException
   */
  public function update($model, array $data);

  /**
   * Deletes $model having given $id.
   *
   * @param \Illuminate\Database\Eloquent\Model $model
   * @return bool
   * @throws \InvalidArgumentException
   * @throws \Exception
   */
  public function delete($model);

  /**
   * Truncates $model's table.
   *
   * @return void
   */
  public function truncate();
}