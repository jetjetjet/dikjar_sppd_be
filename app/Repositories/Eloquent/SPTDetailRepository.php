<?php

namespace App\Repositories\Eloquent;

use App\Models\SPTDetail;
use App\Repositories\Contracts\SPTDetailRepositoryInterface;
use Illuminate\Support\Facades\DB;

class SPTDetailRepository extends BaseRepository implements SPTDetailRepositoryInterface
{
    public function __construct(SPTDetail $model)
    {
        parent::__construct($model);
    }

    public function createDetail(int $sptId, int $pegawaiId, bool $isPelaksana = false): void
    {
        $this->model->newQuery()->create([
            'spt_id' => $sptId,
            'pegawai_id' => $pegawaiId,
            'is_pelaksana' => $isPelaksana ? '1' : '0',
        ]);
    }

    public function getBySptId(int $sptId): object
    {
        return $this->model->newQuery()
            ->join('pegawai as p', 'p.id', '=', 'spt_detail.pegawai_id')
            ->where('spt_detail.spt_id', $sptId)
            ->select('spt_detail.id', 'spt_detail.sppd_file_id', 'p.id as pegawai_id', 'p.full_name', 'p.nip')
            ->orderBy('is_pelaksana', 'DESC')
            ->orderBy('nip')
            ->orderBy('full_name')
            ->get();
    }

    public function getPegawaiIds(int $sptId, bool $onlyNonPelaksana = false): object
    {
        $q = $this->model->newQuery()->where('spt_id', $sptId);
        if ($onlyNonPelaksana) {
            $q->where('is_pelaksana', '0');
        }

        return $q->pluck('pegawai_id');
    }

    public function syncPegawai(int $sptId, array $pegawaiIds, ?int $pelaksanaId): void
    {
        if ($pelaksanaId !== null && ! in_array($pelaksanaId, $pegawaiIds, true)) {
            $pegawaiIds[] = $pelaksanaId;
        }

        $this->model->newQuery()
            ->where('spt_id', $sptId)
            ->whereNotIn('pegawai_id', $pegawaiIds)
            ->delete();

        $existingIds = $this->model->newQuery()
            ->where('spt_id', $sptId)
            ->pluck('pegawai_id')
            ->map(fn ($id) => (int) $id)
            ->all();

        foreach (array_diff($pegawaiIds, $existingIds) as $pegawaiId) {
            $isPelaksana = $pelaksanaId !== null && (int) $pegawaiId === $pelaksanaId;
            $this->createDetail($sptId, (int) $pegawaiId, $isPelaksana);
        }
    }

    public function getUsersForProses(int $sptId): object
    {
        $rows = DB::table('spt_detail')
            ->join('pegawai as p', 'p.id', '=', 'spt_detail.pegawai_id')
            ->where('spt_detail.spt_id', $sptId)
            ->orderBy('spt_detail.id')
            ->select(
                'p.full_name as nama_pegawai',
                'p.jabatan as jabatan_pegawai',
                'p.pangkat',
                'p.golongan',
                'spt_detail.id',
                'p.id as pegawai_id',
                'p.nip as nip_pegawai'
            )
            ->get();

        return $rows->values()->map(function ($row, $index) {
            $row->index_no = $index + 1;
            $row->golongan_pegawai = trim($row->pangkat.' '.$row->golongan);
            unset($row->pangkat, $row->golongan);

            return $row;
        });
    }

    public function hasPelaksana(int $sptId): bool
    {
        return $this->model->newQuery()
            ->where('spt_id', $sptId)
            ->where('is_pelaksana', '1')
            ->exists();
    }

    public function getFirstBySptAndPegawai(int $sptId, int $pegawaiId): ?object
    {
        return $this->model->newQuery()
            ->where('spt_id', $sptId)
            ->where('pegawai_id', $pegawaiId)
            ->first();
    }

    public function updateFile(int $sptId, int $pegawaiId, int $fileId): void
    {
        $this->model->newQuery()
            ->where('spt_id', $sptId)
            ->where('pegawai_id', $pegawaiId)
            ->update([
                'sppd_file_id' => $fileId,
                'sppd_generated_at' => DB::raw('now()'),
                'sppd_generated_by' => auth('sanctum')->id(),
            ]);
    }

    public function deleteBySptId(int $sptId): void
    {
        $this->model->newQuery()->where('spt_id', $sptId)->delete();
    }

    public function updateSettledBySptId(int $sptId, string $settledBy): void
    {
        $this->model->newQuery()
            ->where('spt_id', $sptId)
            ->update([
                'settled_at' => now()->toDateTimeString(),
                'settled_by' => $settledBy,
            ]);
    }
}
