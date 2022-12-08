<?php

/**
 *  app/Http/Requests/Admin/CategoryRequest.php
 *
 * Date-Time: 07.06.21
 * Time: 17:03
 * @author Insite LLC <hello@insite.international>
 */

namespace App\Http\Requests\Admin;

use App\Models\Language;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * Class CategoryRequest
 * @package App\Http\Requests\Admin
 */
class StaffRequest extends FormRequest
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
            config('translatable.fallback_locale') . '.name' => 'required',
            //'slug' => ['required', 'alpha_dash', Rule::unique('vacancies', 'slug')->ignore($this->vacancy)],
        ];
    }
}
