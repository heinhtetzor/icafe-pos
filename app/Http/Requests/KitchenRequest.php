<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class KitchenRequest extends FormRequest
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
            case 'POST' :
                return [
                    'name' => 'required',
                    'username' => 'required',
                    'password' => 'required',
                    'menu_groups' => 'required'
                ];
            case 'PUT' :
                return [
                    'name' => 'required,'.$this->route('kitchen'),
                    'username' => 'required,username,'.$this->route('kitchen'),
                ];
            default: break;
        }
    }
}
