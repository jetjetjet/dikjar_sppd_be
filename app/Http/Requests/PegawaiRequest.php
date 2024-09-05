<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PegawaiRequest extends BaseRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        return [
			'nip' => ['nullable', 'unique:pegawai,nip'],
            'email' => 'required',
			'full_name' => 'required',
			'jabatan' => 'required',
			'pangkat' => 'nullable',
			'golongan' => 'nullable',
			'jenis_kelamin' => 'required',
			'status_pegawai' => 'required',
			'phone' => 'max:15',
            'pegawai_app' => 'required',
        ];
    }
}
