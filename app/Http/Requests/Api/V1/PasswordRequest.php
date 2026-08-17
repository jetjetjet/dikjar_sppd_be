<?php

namespace App\Http\Requests\Api\V1;

class PasswordRequest extends BaseApiRequest
{
    public function rules(): array
    {
        return [
            'password' => ['required', 'string', 'min:6'],
        ];
    }
}
