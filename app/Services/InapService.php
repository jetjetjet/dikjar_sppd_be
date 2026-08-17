<?php

namespace App\Services;

use App\Repositories\Contracts\BiayaRepositoryInterface;
use App\Repositories\Contracts\InapRepositoryInterface;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class InapService
{
    public function __construct(
        protected InapRepositoryInterface $inapRepository,
        protected BiayaRepositoryInterface $biayaRepository,
        protected BiayaService $biayaService,
        protected FileStorageService $fileStorageService,
    ) {}

    public function show(int $id): ?object
    {
        return $this->inapRepository->find($id);
    }

    public function store(array $data)
    {
        try {
            DB::transaction(function () use ($data) {
                $this->inapRepository->createInap([
                    'pegawai_id' => $data['pegawai_id'],
                    'biaya_id' => $data['biaya_id'],
                    'hotel' => $data['hotel'],
                    'room' => $data['room'],
                    'harga' => $data['harga'] ?? 0,
                    'tgl_checkin' => $data['tgl_checkin'],
                    'tgl_checkout' => $data['tgl_checkout'] ?? null,
                    'total_bayar' => $data['total_bayar'] ?? 0,
                    'jml_hari' => $data['jml_hari'] ?? null,
                    'catatan' => $data['catatan'] ?? null,
                ]);

                $this->biayaService->recalculate((int) $data['biaya_id']);
            });

            $biaya = $this->biayaRepository->find((int) $data['biaya_id']);

            return $biaya->total_biaya;
        } catch (\Exception $e) {
            Log::channel('spderr')->info('inap_save: '.json_encode($e->getMessage()));

            throw $e;
        }
    }

    public function update(int $id, int $pegawaiId, int $biayaId, array $data)
    {
        try {
            DB::transaction(function () use ($id, $pegawaiId, $biayaId, $data) {
                $inap = $this->inapRepository->findScoped($id, $pegawaiId, $biayaId);

                $this->inapRepository->update($inap, [
                    'hotel' => $data['hotel'],
                    'room' => $data['room'],
                    'harga' => $data['harga'] ?? 0,
                    'tgl_checkin' => $data['tgl_checkin'],
                    'tgl_checkout' => $data['tgl_checkout'] ?? null,
                    'total_bayar' => $data['total_bayar'] ?? 0,
                    'jml_hari' => $data['jml_hari'] ?? null,
                    'catatan' => $data['catatan'] ?? null,
                ]);

                $this->biayaService->recalculate($biayaId);
            });

            return $this->biayaRepository->find($biayaId)->total_biaya;
        } catch (\Exception $e) {
            Log::channel('spderr')->info('inap_update: '.json_encode($e->getMessage()));

            throw $e;
        }
    }

    public function destroy(int $id, int $biayaId, int $pegawaiId)
    {
        try {
            DB::transaction(function () use ($id, $biayaId, $pegawaiId) {
                $inap = $this->inapRepository->findScoped($id, $pegawaiId, $biayaId);
                $this->inapRepository->delete($inap);

                $this->biayaService->recalculate($biayaId);
            });

            return $this->biayaRepository->find($biayaId)->total_biaya;
        } catch (\Exception $e) {
            Log::channel('spderr')->info('transport_delete: '.json_encode($e->getMessage()));

            throw $e;
        }
    }

    public function uploadFile(int $id): void
    {
        $inap = $this->inapRepository->findOrFail($id);

        $file = request()->file('file');
        $fileId = $file ? $this->fileStorageService->storeUploadedFile($file, 'struk') : null;

        if ($fileId) {
            $this->inapRepository->updateById($id, ['file_id' => $fileId]);
        }
    }
}
