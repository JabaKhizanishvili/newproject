<?php

namespace App\Http\Request;

use Illuminate\Foundation\Http\FormRequest;

class LoginFrontRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'loginEmail' => 'required|email',
            'loginPassword' => 'required'
        ];
    }
}
