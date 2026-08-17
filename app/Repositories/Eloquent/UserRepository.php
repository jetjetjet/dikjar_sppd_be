<?php

namespace App\Repositories\Eloquent;

use App\Models\User;
use App\Repositories\Contracts\UserRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;

class UserRepository extends BaseRepository implements UserRepositoryInterface
{
    public function __construct(User $model)
    {
        parent::__construct($model);
    }

    public function grid(): Collection
    {
        return $this->model->newQuery()
            ->join('pegawai as p', 'p.email', '=', 'users.email')
            ->orderBy('p.created_at', 'DESC')
            ->select('users.id', 'p.email', 'p.full_name', 'p.jabatan')
            ->get();
    }

    public function findByEmail(string $email): ?object
    {
        return $this->model->newQuery()->where('email', $email)->first();
    }

    public function findUserRole(int $id): ?string
    {
        $user = $this->model->find($id);

        return $user ? $user->getRoleNames()[0] ?? null : null;
    }
}
