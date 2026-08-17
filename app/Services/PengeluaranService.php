<?php

namespace App\Services;

use App\Repositories\Contracts\BiayaRepositoryInterface;
use App\Repositories\Contracts\PengeluaranRepositoryInterface;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class PengeluaranService
{
    public function __construct(
        protected PengeluaranRepositoryInterface $pengeluaranRepository,
        protected BiayaRepositoryInterface $biayaRepository,
        protected BiayaService $biayaService,
        protected FileStorageService $fileStorageService,
    ) {}

    public function show(int $id): ?object
    {
        return $this->pengeluaranRepository->find($id);
    }

    public function store(array $data)
    {
        try {
            DB::transaction(function () use ($data) {
                $this->pengeluaranRepository->createPengeluaran([
                    'biaya_id' => $data['biaya_id'],
                    'pegawai_id' => $data['pegawai_id'],
                    'tgl' => $data['tgl'],
                    'kategori' => $data['kategori'],
                    'catatan' => $data['catatan'] ?? null,
                    'nominal' => $data['nominal'],
                    'satuan' => $data['satuan'],
                    'jml' => $data['jml'],
                    'jml_hari' => $data['jml_hari'] ?? null,
                    'total' => $data['total'],
                ]);

                $this->biayaService->recalculate((int) $data['biaya_id']);
            });

            return $this->biayaRepository->find((int) $data['biaya_id'])->total_biaya;
        } catch (\Exception $e) {
            Log::channel('spderr')->info('pengeluaran_save: '.json_encode($e->getMessage()));

            throw $e;
        }
    }

    public function update(int $id, int $pegawaiId, int $biayaId, array $data)
    {
        try {
            DB::transaction(function () use ($id, $pegawaiId, $biayaId, $data) {
                $pengeluaran = $this->pengeluaranRepository->findScoped($id, $pegawaiId, $biayaId);

                $this->pengeluaranRepository->update($pengeluaran, [
                    'tgl' => $data['tgl'],
                    'kategori' => $data['kategori'],
                    'catatan' => $data['catatan'] ?? null,
                    'nominal' => $data['nominal'],
                    'satuan' => $data['satuan'],
                    'jml' => $data['jml'],
                    'jml_hari' => $data['jml_hari'] ?? null,
                    'total' => $data['total'],
                ]);

                $this->biayaService->recalculate($biayaId);
            });

            return $this->biayaRepository->find($biayaId)->total_biaya;
        } catch (\Exception $e) {
            Log::channel('spderr')->info('pengeluaran_update: '.json_encode($e->getMessage()));

            throw $e;
        }
    }

    public function destroy(int $id, int $biayaId, int $pegawaiId)
    {
        try {
            DB::transaction(function () use ($id, $biayaId, $pegawaiId) {
                $pengeluaran = $this->pengeluaranRepository->findScoped($id, $pegawaiId, $biayaId);
                $this->pengeluaranRepository->delete($pengeluaran);

                $this->biayaService->recalculate($biayaId);
            });

            return $this->biayaRepository->find($biayaId)->total_biaya;
        } catch (\Exception $e) {
            Log::channel('spderr')->info('pengeluaran_delete: '.json_encode($e->getMessage()));

            throw $e;
        }
    }

    public function uploadFile(int $id): void
    {
        $pengeluaran = $this->pengeluaranRepository->findOrFail($id);

        $file = request()->file('file');
        $fileId = $file ? $this->fileStorageService->storeUploadedFile($file, 'struk') : null;

        if ($fileId) {
            $this->pengeluaranRepository->updateById($id, ['file_id' => $fileId]);
        }
    }
}
