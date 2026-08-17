<?php

namespace App\Services;

use App\Repositories\Contracts\AuthRepositoryInterface;
use Illuminate\Support\Facades\Hash;

class AuthService
{
    public function __construct(protected AuthRepositoryInterface $authRepository) {}

    public function login(string $email, string $password): array
    {
        $user = $this->authRepository->findByEmail($email);

        if ($user === null || ! Hash::check($password, $user->password)) {
            throw new \Exception('Email atau Password Salah');
        }

        $user->last_login = now();
        $user->save();

        $perm = $this->authRepository->tokenAbilities($user);
        if ($this->authRepository->isSuperAdmin($user)) {
            $perm[] = 'is_admin';
        }

        $token = $this->authRepository->createToken($user, $email, $perm);

        $pegawai = $user->pegawai;
        if ($pegawai === null) {
            throw new \Exception('Data pegawai untuk akun ini tidak ditemukan.');
        }

        return [
            'token' => $token,
            'pegawai_id' => $pegawai->id,
            'email' => $pegawai->email,
            'full_name' => $pegawai->full_name,
            'nip' => $pegawai->nip,
            'path_foto' => $pegawai->path_foto ?: '/storage/profile/user.png',
            'perms' => $perm,
        ];
    }

    public function logout(): void
    {
        $user = auth('sanctum')->user();
        if ($user) {
            $this->authRepository->deleteCurrentToken($user);
        }
    }
}
