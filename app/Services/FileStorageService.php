<?php

namespace App\Services;

use App\Repositories\Contracts\FileRepositoryInterface;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use RuntimeException;

class FileStorageService
{
    public function __construct(protected FileRepositoryInterface $fileRepository) {}

    /**
     * Save an uploaded file to the configured storage disk (public or s3)
     * and persist its metadata in the files table.
     *
     * @return int file id
     */
    public function storeUploadedFile(UploadedFile|string $file, string $subFolder, ?string $disk = null): int
    {
        $disk = $disk ?? config('filesystems.default', 'public');

        $originalName = pathinfo($file instanceof UploadedFile ? $file->getClientOriginalName() : $file, PATHINFO_FILENAME);
        $mediaName = $file instanceof UploadedFile ? $file->getClientOriginalName() : basename($file);
        $ext = $file instanceof UploadedFile ? $file->getClientOriginalExtension() : pathinfo($file, PATHINFO_EXTENSION);
        $newName = time().'_'.$mediaName;
        $path = $subFolder.'/'.$newName;

        if ($file instanceof UploadedFile) {
            Storage::disk($disk)->putFileAs($subFolder, $file, $newName);
        } else {
            Storage::disk($disk)->put($path, file_get_contents($file));
        }

        return $this->fileRepository->saveFile($newName, $originalName, '/storage/'.$subFolder, $ext);
    }

    /**
     * Store an uploaded file and return its public path string
     * (file_path + file_name), preserving the legacy "/path/name" contract.
     */
    public function storePath(UploadedFile|string $file, string $subFolder, ?string $disk = null): string
    {
        $fileId = $this->storeUploadedFile($file, $subFolder, $disk);
        $record = $this->fileRepository->getById($fileId);

        return $record->file_path.'/'.$record->file_name;
    }

    public function url(?int $fileId): ?string
    {
        if ($fileId === null) {
            return null;
        }

        $file = $this->fileRepository->getById($fileId);

        return $file ? ($file->file_path.'/'.$file->file_name) : null;
    }

    protected function assertDiskSupported(string $disk): void
    {
        if (! in_array($disk, [config('filesystems.default_public', 'public'), config('filesystems.default_cloud', 's3')], true)) {
            throw new RuntimeException("Disk [{$disk}] is not supported.");
        }
    }
}
