<?php

namespace App\Http\Resources\Api\V1;

use Illuminate\Http\Resources\Json\JsonResource;

class SPTDetailResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'sppd_file_id' => $this->sppd_file_id,
            'pegawai_id' => $this->pegawai_id,
            'full_name' => $this->full_name,
            'nip' => $this->nip,
            'total_biaya' => (float) $this->total_biaya,
            'can_edit' => (bool) $this->can_edit,
        ];
    }
}
