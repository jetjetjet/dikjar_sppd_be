<?php

namespace App\Http\Requests\Api\V1;

class VoidSPTRequest extends BaseApiRequest
{
    public function rules(): array
    {
        return [
            'void_remark' => ['required', 'string'],
        ];
    }
}
