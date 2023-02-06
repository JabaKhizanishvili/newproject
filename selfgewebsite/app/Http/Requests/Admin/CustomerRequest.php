<?php
/**
 *  app/Http/Requests/Admin/ProductRequest.php
 *
 * Date-Time: 10.06.21
 * Time: 15:07
 * @author Insite LLC <hello@insite.international>
 */

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * Class ProductRequest
 * @package App\Http\Requests\Admin
 */
class CustomerRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        // Check if method is get,fields are nullable.
        if ($this->method() === 'GET') {
            return [];
        }

        return [
            'email' => ['required','email', Rule::unique('customers', 'email')->ignore($this->customer)],
            //'password' => ['required'],



        ];
    }
}
