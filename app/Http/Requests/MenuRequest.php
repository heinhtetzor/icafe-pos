<?php

namespace App\Http\Requests;
use Illuminate\Http\Request;

use Illuminate\Foundation\Http\FormRequest;

class MenuRequest extends FormRequest
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
        switch ($this->method()) {
            case 'POST':
                return [
                    'name' => 'required|unique:menus',
                    'price' => 'required',
                    'menu_group_id' => 'required',
                ];
                case 'PUT':
                    return [
                        'name' => 'required|unique:menus,name,' . $this->route('menu'),
                        'price' => 'required',
                        'menu_group_id' => 'required',
                    ];
            default : break;
        }
    }
}
