<?php

namespace App\Services;

use App\Repositories\Contracts\BiayaRepositoryInterface;
use App\Repositories\Contracts\SPTDetailRepositoryInterface;
use App\Repositories\Contracts\SPTLogRepositoryInterface;
use App\Repositories\Contracts\SPTRepositoryInterface;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class SPTService
{
    public function __construct(
        protected SPTRepositoryInterface $sptRepository,
        protected SPTDetailRepositoryInterface $sptDetailRepository,
        protected SPTLogRepositoryInterface $sptLogRepository,
        protected BiayaRepositoryInterface $biayaRepository,
        protected DocumentGeneratorService $documentGenerator,
    ) {}

    public function getGrid(?string $tahun)
    {
        $user = auth('sanctum')->user();
        $isAdmin = $user->tokenCan('is_admin');

        $rows = $this->sptRepository->getGrid($tahun, $user->id, $isAdmin);

        foreach ($rows as $row) {
            $row->tgl = $row->tgl_berangkat.' s/d '.$row->tgl_kembali;
            $row->spt_file = $row->spt_file_id !== null;
            $row->status = ucwords(strtolower($row->status));
            $row->keterangan = $this->lateKeterangan($row);
            $row->name = $this->pegawaiNames($row);
            $row->can_generate = ($user->tokenCan('spt_generate') || $isAdmin) ? 1 : 0;
            unset($row->details);
        }

        return $rows;
    }

    protected function lateKeterangan($spt): string
    {
        if ($spt->proceed_at !== null && $spt->finished_at === null) {
            // Carbon 3: diffInDays() default balikin float — dibulatkan (int cast).
            $days = (int) Carbon::parse($spt->tgl_kembali)->diffInDays(Carbon::now(), false);
            if ($days < 0) {
                return 'Telat '.abs($days).' hari';
            }
        }

        return '';
    }

    protected function pegawaiNames($spt): string
    {
        $names = optional($spt->details)->pluck('pegawai.full_name')->filter();

        return $names ? implode('_', $names->all()) : '';
    }

    public function getDetail(int $id)
    {
        $data = $this->sptRepository->findWithDetail($id);

        if ($data !== null) {
            $user = auth('sanctum')->user();
            $isAdmin = $user->tokenCan('is_admin');

            $data->spt_file = $data->spt_file_id !== null;
            $data->can_generate = ($user->tokenCan('spt_generate') || $isAdmin) ? 1 : 0;
            $data->can_edit = $this->canEdit($data) ? 1 : 0;
            $data->can_void = $this->canVoid($data) ? 1 : 0;
            $data->can_finish = (
                $data->completed_at === null
                && $data->proceed_at !== null
                && Carbon::parse($data->tgl_kembali)->lte(Carbon::today())
            ) ? 1 : 0;
        }

        return $data;
    }

    public function store(array $data): void
    {
        DB::transaction(function () use ($data) {
            $berangkat = new Carbon($data['tgl_berangkat']);
            $kembali = new Carbon($data['tgl_kembali']);
            // Carbon 3: diffInDays() default balikin float — dibulatkan (int cast)
            // supaya jumlah_hari yang tersimpan & tercetak di dokumen tetap bulat.
            $jumlahHari = (int) $berangkat->diffInDays($kembali) + 1;
            $tahun = Carbon::now()->format('Y');

            $noMax = $this->sptRepository->allocateNoIndex($tahun);

            $spt = $this->sptRepository->create([
                'no_index' => $noMax,
                'no_spt' => $data['no_spt'],
                'jenis_dinas' => $data['jenis_dinas'],
                'anggaran_id' => $data['anggaran_id'],
                'pttd_id' => $data['pttd_id'],
                'pptk_id' => $data['pptk_id'],
                'pelaksana_id' => $data['pelaksana_id'],
                'bendahara_id' => $data['bendahara_id'],
                'pengguna_anggaran_id' => $data['pengguna_anggaran_id'],
                'dasar_pelaksana' => $data['dasar_pelaksana'],
                'untuk' => $data['untuk'],
                'transportasi' => $data['transportasi'],
                'daerah_asal' => $data['daerah_asal'],
                'daerah_tujuan' => $data['daerah_tujuan'],
                'tgl_berangkat' => $data['tgl_berangkat'],
                'tgl_kembali' => $data['tgl_kembali'],
                'jumlah_hari' => $jumlahHari,
                'tgl_spt' => $data['tgl_spt'],
                'status' => 'KONSEP',
                'periode' => (int) date('Y'),
            ]);

            $this->sptDetailRepository->createDetail($spt->id, (int) $data['pelaksana_id'], true);

            foreach ((array) ($data['pegawai_id'] ?? []) as $pegawaiId) {
                $this->sptDetailRepository->createDetail($spt->id, (int) $pegawaiId, false);
            }

            $this->sptLogRepository->log(
                auth('sanctum')->id(),
                auth('sanctum')->user()->pegawai->full_name ?? null,
                $spt->id,
                'Simpan SPT'
            );
        });
    }

    public function update(int $id, array $data): void
    {
        $spt = $this->sptRepository->findOrFail($id);

        DB::transaction(function () use ($id, $data, $spt) {
            $berangkat = new Carbon($data['tgl_berangkat']);
            $kembali = new Carbon($data['tgl_kembali']);
            // Carbon 3: diffInDays() default balikin float — dibulatkan (int cast)
            // supaya jumlah_hari yang tersimpan & tercetak di dokumen tetap bulat.
            $jumlahHari = (int) $berangkat->diffInDays($kembali) + 1;

            $updateData = [
                'jenis_dinas' => $data['jenis_dinas'],
                'anggaran_id' => $data['anggaran_id'],
                'pttd_id' => $data['pttd_id'],
                'pptk_id' => $data['pptk_id'],
                'pelaksana_id' => $data['pelaksana_id'],
                'bendahara_id' => $data['bendahara_id'],
                'pengguna_anggaran_id' => $data['pengguna_anggaran_id'],
                'dasar_pelaksana' => $data['dasar_pelaksana'],
                'untuk' => $data['untuk'],
                'transportasi' => $data['transportasi'],
                'daerah_asal' => $data['daerah_asal'],
                'daerah_tujuan' => $data['daerah_tujuan'],
                'tgl_berangkat' => $data['tgl_berangkat'],
                'tgl_kembali' => $data['tgl_kembali'],
                'jumlah_hari' => $jumlahHari,
                'tgl_spt' => $data['tgl_spt'],
            ];

            // Nomor surat cuma boleh diubah selama SPT belum diproses.
            if ($spt->proceed_at === null && array_key_exists('no_spt', $data)) {
                $updateData['no_spt'] = $data['no_spt'];
            }

            $this->sptRepository->updateById($id, $updateData);

            $this->sptDetailRepository->syncPegawai(
                $id,
                array_map('intval', (array) ($data['pegawai_id'] ?? [])),
                (int) $data['pelaksana_id']
            );

            $this->sptLogRepository->log(
                auth('sanctum')->id(),
                auth('sanctum')->user()->pegawai->full_name ?? null,
                $id,
                'Ubah SPT'
            );
        });
    }

    public function proses(int $id): string
    {
        $spt = $this->sptRepository->findOrFail($id);
        $users = $this->sptDetailRepository->getUsersForProses($id);
        $loginId = auth('sanctum')->id();

        DB::transaction(function () use ($spt, $users, $id, $loginId) {
            $this->biayaRepository->deleteBySptId($id);
            foreach ($users as $user) {
                $this->biayaRepository->createForSpt($id, $spt->anggaran_id, (int) $user->pegawai_id);
            }
            $this->sptRepository->updateById($id, [
                'proceed_at' => now()->toDateTimeString(),
                'proceed_by' => $loginId,
                'status' => 'PROSES',
            ]);
        });

        $result = $this->documentGenerator->generateProses($spt, $users);

        $this->sptRepository->updateById($id, [
            'spt_file_id' => $result['file_id'],
            'spt_generated_at' => now()->toDateTimeString(),
            'spt_generated_by' => $loginId,
        ]);

        $this->sptLogRepository->log(
            $loginId,
            auth('sanctum')->user()->pegawai->full_name ?? null,
            $id,
            'Proses SPT'
        );

        return $result['path'];
    }

    public function finish(int $id): void
    {
        $this->sptRepository->updateById($id, [
            'completed_at' => now()->toDateTimeString(),
            'completed_by' => auth('sanctum')->id(),
            'status' => 'KEMBALI',
        ]);

        $this->sptLogRepository->log(
            auth('sanctum')->id(),
            auth('sanctum')->user()->pegawai->full_name ?? null,
            $id,
            'Selesai SPT'
        );
    }

    public function void(int $id, string $remark): void
    {
        $this->sptRepository->updateById($id, [
            'voided_at' => now()->toDateTimeString(),
            'voided_by' => auth('sanctum')->id() ?? 0,
            'void_remark' => $remark,
            'status' => 'VOID',
        ]);

        $this->sptLogRepository->log(
            auth('sanctum')->id(),
            auth('sanctum')->user()->pegawai->full_name ?? null,
            $id,
            'VOID SPT'
        );
    }

    public function delete(int $id): void
    {
        DB::transaction(function () use ($id) {
            $this->sptDetailRepository->deleteBySptId($id);
            $this->sptRepository->deleteById($id);
        });

        $this->sptLogRepository->log(
            auth('sanctum')->id(),
            auth('sanctum')->user()->pegawai->full_name ?? null,
            $id,
            'Hapus SPT'
        );
    }

    public function getSPTFile(int $id): ?string
    {
        $spt = $this->sptRepository->find($id);
        if ($spt === null || $spt->spt_file_id === null) {
            return null;
        }

        $file = $this->sptRepository->getFile($spt->spt_file_id);

        return $file ? ($file->file_path.'/'.$file->file_name) : null;
    }

    public function getSPTDocument(int $id): ?string
    {
        $file = $this->sptRepository->findWithFile($id);

        return $file ? ($file->file_path.'/'.$file->file_name) : null;
    }

    public function canEdit(object $spt): bool
    {
        $user = auth('sanctum')->user();
        $isAdmin = $user->tokenCan('is_admin');

        return ($spt->proceed_at === null || $isAdmin) && $spt->completed_at === null;
    }

    public function canVoid(object $spt): bool
    {
        $user = auth('sanctum')->user();
        $isAdmin = $user->tokenCan('is_admin');
        $canVoid = $user->tokenCan('spt-void');

        return ($canVoid || $isAdmin)
            && $spt->proceed_at !== null
            && $spt->finished_at === null
            && $spt->voided_at === null;
    }

}
