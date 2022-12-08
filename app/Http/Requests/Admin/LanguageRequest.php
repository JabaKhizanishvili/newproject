<?php
/**
 *  app/Http/Requests/Admin/LanguageRequest.php
 *
 * Date-Time: 04.06.21
 * Time: 10:22
 * @author Insite LLC <hello@insite.international>
 */

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Class LanguageRequest
 * @package App\Http\Requests\Admin
 */
class LanguageRequest extends FormRequest
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
        $isRequired = $this->method() === 'GET' ? 'nullable' : 'required';

        return [
            'title' => $isRequired . '|string|max:255',
            'locale' => $isRequired . '|max:2|min:2',
        ];
    }
}
