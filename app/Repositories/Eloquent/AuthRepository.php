<?php

namespace App\Repositories\Eloquent;

use App\Models\User;
use App\Repositories\Contracts\AuthRepositoryInterface;

class AuthRepository extends BaseRepository implements AuthRepositoryInterface
{
    public function __construct(User $model)
    {
        parent::__construct($model);
    }

    public function findByEmail(string $email): ?object
    {
        return $this->model->newQuery()->where('email', $email)->first();
    }

    public function createToken(object $user, string $name, array $abilities): string
    {
        return $user->createToken($name, $abilities)->plainTextToken;
    }

    public function tokenAbilities(object $user): array
    {
        return $user->getAllPermissions()->pluck('name')->toArray();
    }

    public function isSuperAdmin(object $user): bool
    {
        return $user->hasRole('Super Admin');
    }

    public function deleteCurrentToken(object $user): void
    {
        $user->currentAccessToken()->delete();
    }
}
