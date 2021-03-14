<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class WaiterReqeust extends FormRequest
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
                    'name' => 'required|unique:waiters',
                    'username' => 'required|unique:waiters',
                ];
            case 'PUT' :
                return [
                    'name' => 'required|unique:waiters,name,'.$this->route('waiter'),
                    'username' => 'required|unique:waiters,username,'.$this->route('waiter'),
                ];
            default: break;
        }
    }
}
