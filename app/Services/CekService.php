<?php

namespace App\Services;

use App\Models\SPTDetail;
use App\Repositories\Contracts\SPTGuestRepositoryInterface;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class CekService
{
    public function __construct(protected SPTGuestRepositoryInterface $guestRepository) {}

    public function verifikasi(string $key): array
    {
        $guest = $this->guestRepository->verify($key);

        if ($guest === null) {
            return [
                'success' => false,
                'data' => [
                    'header' => null,
                    'child' => [],
                    'message' => ['Kode tidak sesuai! SPT tidak ditemukan.'],
                    'type' => 'error',
                ],
            ];
        }

        $guest->tgl_berangkat = Carbon::parse($guest->tgl_berangkat)->format('d/m/Y');
        $guest->tgl_kembali = Carbon::parse($guest->tgl_kembali)->format('d/m/Y');
        $guest->status = $this->statusLabel($guest->status);

        $users = SPTDetail::join('pegawai as p', 'p.id', '=', 'spt_detail.pegawai_id')
            ->where('spt_id', (int) $guest->id)
            ->select('full_name', 'jabatan', DB::raw("p.pangkat || ' ' || p.golongan as pangkat_pegawai"), 'nip')
            ->orderBy('is_pelaksana', 'DESC')
            ->orderBy('nip')
            ->orderBy('full_name')
            ->get();

        $child = $users->map(function ($u) {
            return [
                'nama_pegawai' => $u->full_name,
                'nip' => $u->nip,
                'pangkat' => str_replace('- -', '-', $u->pangkat_pegawai),
                'jabatan' => $u->jabatan,
            ];
        })->values();

        return [
            'success' => true,
            'data' => [
                'header' => $guest,
                'child' => $child,
                'message' => ['SPT ditemukan!'],
                'type' => 'success',
            ],
        ];
    }

    protected function statusLabel(string $status): string
    {
        return match ($status) {
            'PROSES' => 'Dalam perjalanan dinas',
            'KEMBALI' => 'Sudah kembali dari perjalanan dinas',
            'KWITANSI' => 'Proses kwitansi',
            'SELESAI' => 'Selesai',
            default => '-',
        };
    }
}
