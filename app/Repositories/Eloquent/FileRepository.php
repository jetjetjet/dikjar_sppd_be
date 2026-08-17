<?php

namespace App\Repositories\Eloquent;

use App\Models\File;
use App\Repositories\Contracts\FileRepositoryInterface;

class FileRepository extends BaseRepository implements FileRepositoryInterface
{
    public function __construct(File $model)
    {
        parent::__construct($model);
    }

    public function saveFile(string $fileName, string $originalName, string $filePath, ?string $ext): int
    {
        $file = $this->model->newQuery()->create([
            'file_name' => $fileName,
            'original_name' => $originalName,
            'file_path' => $filePath,
            'ext' => $ext,
        ]);

        return (int) $file->id;
    }

    public function getById(int $id): ?object
    {
        return $this->model->newQuery()->where('id', $id)->first();
    }
}
