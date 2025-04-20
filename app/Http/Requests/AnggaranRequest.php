<?php

namespace App\Http\Requests;

use Illuminate\Validation\Rule;

class AnggaranRequest extends BaseRequest
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
     * @return array
     */
    public function rules()
    {
		$year = date('Y');
		$year1 = $year;
        $bln = date('m');
        if ($bln > 11 || $bln < 2) {
            $year1 += 1;
        }
        $method = $this->method();
        if ($method === 'POST') {
            return [
                'kode_rekening' => 'required',
                'nama_rekening' => ['required',
                    Rule::unique('anggaran')->where(function ($query) {
                        $query->where('kode_rekening', $this->kode_rekening)
                        ->where('periode', $this->periode)
                        ->whereNull('deleted_at')
                        ->where('nama_rekening', $this->nama_rekening);
                    })
                ],
                'periode' => 'required|numeric|min:'.$year.'|max:'.$year1,
                'pagu' => 'required|numeric|min:500000|max:999999999999',
                'pptk_id' => 'required',
                'bidang' => 'required',
                'uraian' => 'sometimes',
                'bendahara_id' => 'required',
                'pengguna_id' => 'required'
            ];
        } else if ($method === 'PUT') {
            return [
                'kode_rekening' => 'required',
                'nama_rekening' => ['required',
                    Rule::unique('anggaran')->where(function ($query) {
                        $query->where('kode_rekening', $this->kode_rekening)
                        ->where('nama_rekening', $this->nama_rekening)
                        ->where('periode', $this->periode)
                        ->whereNull('deleted_at')
                        ->where('id', '!=', $this->id);
                    })
                ],
                'periode' => 'required|numeric|min:' . $year . '|max:' . $year1,
                'pagu' => 'required|numeric|min:500000|max:999999999999',
                'pptk_id' => 'required',
                'bidang' => 'required',
                'uraian' => 'sometimes',
                'bendahara_id' => 'required',
                'pengguna_id' => 'required'
            ];
        }
    }

    public function messages() {
        return [
           'nama_rekening.unique' => 'Kode dan Nama Rekening sudah ada!',
        ];
    }
}
