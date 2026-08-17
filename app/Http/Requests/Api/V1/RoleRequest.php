<?php

namespace App\Http\Requests\Api\V1;

class RoleRequest extends BaseApiRequest
{
    public function rules(): array
    {
        $id = $this->route('id');

        return [
            'name' => ['required', 'string', $id ? 'unique:roles,name,'.$id : 'unique:roles,name'],
            'perms' => ['nullable', 'string'],
        ];
    }
}
