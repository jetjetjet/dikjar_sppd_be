<?php

namespace App\Http\Resources\Api\V1;

use Illuminate\Http\Resources\Json\JsonResource;

class SPTResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'no_spt' => $this->no_spt,
            'periode' => $this->periode,
            'jenis_dinas' => $this->jenis_dinas,
            'status' => $this->status,
            'tgl' => isset($this->tgl) ? $this->tgl : ($this->tgl_berangkat.':'.$this->tgl_kembali),
            'daerah_asal' => $this->daerah_asal,
            'daerah_tujuan' => $this->daerah_tujuan,
            'untuk' => $this->untuk,
            'spt_file' => (bool) ($this->spt_file ?? ($this->spt_file_id !== null)),
            'settled_at' => $this->settled_at,
            'proceed_at' => $this->proceed_at,
            'name' => $this->name ?? null,
            'keterangan' => $this->keterangan ?? null,
            'can_generate' => $this->can_generate ?? 0,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
