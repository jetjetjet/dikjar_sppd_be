<?php

namespace App\Repositories\Contracts;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

interface BaseRepositoryInterface
{
    public function all(array $columns = ['*']): Collection;

    public function find(int|string $id, array $columns = ['*']): ?Model;

    public function findOrFail(int|string $id, array $columns = ['*']): Model;

    public function create(array $data): Model;

    public function update(Model $model, array $data): Model;

    public function updateById(int|string $id, array $data): bool;

    public function delete(Model $model): bool;

    public function deleteById(int|string $id): bool;

    public function where(string $column, mixed $operator, mixed $value = null): static;
}
