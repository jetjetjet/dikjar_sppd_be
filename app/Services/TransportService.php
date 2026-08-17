<?php

namespace App\Services;

use App\Repositories\Contracts\BiayaRepositoryInterface;
use App\Repositories\Contracts\TransportRepositoryInterface;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class TransportService
{
    public function __construct(
        protected TransportRepositoryInterface $transportRepository,
        protected BiayaRepositoryInterface $biayaRepository,
        protected BiayaService $biayaService,
        protected FileStorageService $fileStorageService,
    ) {}

    public function show(int $id): ?object
    {
        return $this->transportRepository->find($id);
    }

    public function store(array $data)
    {
        try {
            $transport = DB::transaction(function () use ($data) {
                $transport = $this->transportRepository->createTransport([
                    'pegawai_id' => $data['pegawai_id'],
                    'biaya_id' => $data['biaya_id'],
                    'jenis_transport' => $data['jenis_transport'],
                    'catatan' => $data['catatan'] ?? null,
                    'perjalanan' => $data['perjalanan'],
                    'agen' => $data['agen'],
                    'no_tiket' => $data['no_tiket'],
                    'kode_booking' => $data['kode_booking'] ?? null,
                    'no_penerbangan' => $data['no_penerbangan'] ?? null,
                    'tgl' => $data['tgl'],
                    'total_bayar' => $data['total_bayar'] ?? 0,
                ]);

                $this->biayaService->recalculate((int) $data['biaya_id']);

                return $transport;
            });

            $biaya = $this->biayaRepository->find($transport->biaya_id);

            return $biaya->total_biaya;
        } catch (\Exception $e) {
            Log::channel('spderr')->info('transport_save_err: '.json_encode($e->getMessage()));

            throw $e;
        }
    }

    public function update(int $id, int $pegawaiId, int $biayaId, array $data)
    {
        try {
            $biaya = DB::transaction(function () use ($id, $pegawaiId, $biayaId, $data) {
                $transport = $this->transportRepository->findScoped($id, $pegawaiId, $biayaId);

                $this->transportRepository->update($transport, [
                    'jenis_transport' => $data['jenis_transport'],
                    'catatan' => $data['catatan'] ?? null,
                    'perjalanan' => $data['perjalanan'],
                    'agen' => $data['agen'],
                    'no_tiket' => $data['no_tiket'],
                    'kode_booking' => $data['kode_booking'] ?? null,
                    'no_penerbangan' => $data['no_penerbangan'] ?? null,
                    'tgl' => $data['tgl'],
                    'total_bayar' => $data['total_bayar'] ?? 0,
                ]);

                $this->biayaService->recalculate($biayaId);

                return $this->biayaRepository->find($biayaId);
            });

            return $biaya->total_biaya;
        } catch (\Exception $e) {
            Log::channel('spderr')->info('transport_update_err: '.json_encode($e->getMessage()));

            throw $e;
        }
    }

    public function destroy(int $id, int $biayaId, int $pegawaiId)
    {
        try {
            $total = DB::transaction(function () use ($id, $biayaId, $pegawaiId) {
                $transport = $this->transportRepository->findScoped($id, $pegawaiId, $biayaId);
                $this->transportRepository->delete($transport);

                $this->biayaService->recalculate($biayaId);

                return $this->biayaRepository->find($biayaId)->total_biaya;
            });

            return $total;
        } catch (\Exception $e) {
            Log::channel('spderr')->info('transport_delete: '.json_encode($e->getMessage()));

            throw $e;
        }
    }

    public function uploadFile(int $id): void
    {
        $transport = $this->transportRepository->findOrFail($id);

        $file = request()->file('file');
        $fileId = $file ? $this->fileStorageService->storeUploadedFile($file, 'struk') : null;

        if ($fileId) {
            $this->transportRepository->updateById($id, ['file_id' => $fileId]);
        }
    }
}
