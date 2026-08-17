<?php

namespace App\Http\Requests\Api\V1;

class ProfileRequest extends BaseApiRequest
{
    public function rules(): array
    {
        return [
            'email' => ['nullable', 'email'],
            'full_name' => ['nullable', 'string'],
            'jenis_kelamin' => ['nullable', 'string'],
            'address' => ['nullable', 'string'],
            'phone' => ['nullable', 'string', 'max:15'],
            'password' => ['nullable', 'string', 'min:6'],
        ];
    }
}
