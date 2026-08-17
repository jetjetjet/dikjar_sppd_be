<?php

namespace App\Repositories\Contracts;

interface FileRepositoryInterface extends BaseRepositoryInterface
{
    public function saveFile(string $fileName, string $originalName, string $filePath, ?string $ext): int;

    public function getById(int $id): ?object;
}
