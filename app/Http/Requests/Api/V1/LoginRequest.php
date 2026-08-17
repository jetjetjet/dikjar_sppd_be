<?php

namespace App\Http\Requests\Api\V1;

class LoginRequest extends BaseApiRequest
{
    public function rules(): array
    {
        return [
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
        ];
    }
}
