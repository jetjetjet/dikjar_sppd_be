<?php
namespace App\Repositories;

// use App\Traits\PerPage;
// use App\Traits\Pagination;
use Illuminate\Database\Eloquent\Model;

class BaseRepository
{
//   use Perpage;
//   use Pagination;

  protected $model;

  public function __construct(Model $model)
  {
    $this->model = $model;
  }

  public function create(array $inputs): Model
  {
    return $this->model->create($inputs);
  }

  public function updateData(Model|string|int $id, array $inputs)
  {
    $model = $id instanceof Model ? $id : $this->model->findOrFail($id);
    $update = $model->update($inputs);

    if ($update) {
      return $model;
    }

    return $update;
  }

  public function find($id, $querySelect = '*', $relations = null)
  {
    if ($relations) {
      return $this->model->select($querySelect)->with($relations)->find($id);
    }
    return $this->model->select($querySelect)->findOrFail($id);
  }

  public function findBy($key, $querySelect = '*', $relations = null)
  {
    $data = $this->model->select($querySelect);

    if ($relations) {
      $data = $data->with($relations);
    }

    return $data->where($key)->firstOrFail();
  }

  public function delete($id)
  {
    $data = $this->model->findOrFail($id);

    return $data->delete();
  }
}