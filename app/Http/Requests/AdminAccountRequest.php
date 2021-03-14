<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AdminAccountRequest extends FormRequest
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
                    'username' => 'required|unique:admin_accounts',
                    'password' => 'required'
                ];
            case 'PUT':
                return [                    
                    'username' => 'required|unique:admin_accounts,username,' . $this->route('admin_account'),
                    'password' => 'required'
                ];
            default: break;
        }
    }
}
