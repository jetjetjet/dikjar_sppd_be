<?php

namespace App\Repositories\Eloquent;

use App\Repositories\Contracts\BaseRepositoryInterface;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

abstract class BaseRepository implements BaseRepositoryInterface
{
    public function __construct(protected Model $model) {}

    public function all(array $columns = ['*']): Collection
    {
        return $this->model->newQuery()->get($columns);
    }

    public function find(int|string $id, array $columns = ['*']): ?Model
    {
        return $this->model->newQuery()->find($id, $columns);
    }

    public function findOrFail(int|string $id, array $columns = ['*']): Model
    {
        return $this->model->newQuery()->findOrFail($id, $columns);
    }

    public function create(array $data): Model
    {
        return $this->model->newQuery()->create($data);
    }

    public function update(Model $model, array $data): Model
    {
        $model->update($data);

        return $model;
    }

    public function updateById(int|string $id, array $data): bool
    {
        return $this->model->newQuery()->whereKey($id)->update($data);
    }

    public function delete(Model $model): bool
    {
        return (bool) $model->delete();
    }

    public function deleteById(int|string $id): bool
    {
        return (bool) $this->model->newQuery()->findOrFail($id)->delete();
    }

    public function where(string $column, mixed $operator, mixed $value = null): static
    {
        $this->model = $this->model->newQuery()->where($column, $operator, $value);

        return $this;
    }

    protected function query(): Builder
    {
        return $this->model->newQuery();
    }
}
