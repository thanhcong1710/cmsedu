<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class DiscountCodesRequest extends FormRequest
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
        return [
            //
            'code' => 'required|max:255',
            'name' => 'required',
            'percent' => 'required|min:0|max:100',
            'start_date' => 'required',
            'end_date' => 'required'
        ];
    }

    public function messages()
    {
        return [
            'name.required' => 'Tên mã chiết khấu không được để trống',
            'code.required' => 'Mã của mã chiết khấu không được để trống',
            'code.max' => 'Mã của mã chiết khấu không dài quá 255 ký tự',
            'percent.required' => 'Tỷ lệ chiết khấu không được để trống',
            'percent.min' => 'Tỷ lệ chiết khấu không được âm',
            'percent.max' => 'Tỷ lệ chiêt khấu không được lớn hơn 100%',
            'start_date.required' => "Ngày bắt đầu không được để trống",
            'end_date.required' => "Ngày kết thúc không được để trống"
        ];
    }
}
