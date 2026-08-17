<?php

namespace App\Http\Requests\Api\V1;

class UserRequest extends BaseApiRequest
{
    public function rules(): array
    {
        $id = $this->route('id');

        $rules = [
            'role' => ['required', 'string'],
        ];

        if ($id) {
            $rules['password'] = ['nullable', 'string', 'min:6'];
        } else {
            $rules['email'] = ['required', 'email', 'unique:users,email'];
            $rules['password'] = ['required', 'string', 'min:6'];
        }

        return $rules;
    }
}
