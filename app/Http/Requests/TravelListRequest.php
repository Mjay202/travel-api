<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class TravelListRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(response()->json($validator->errors(), 422));
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            //
            // 'is_public' => 'boolean',
            'name' => ['required', 'unique:travels'],
            'description' => ['required'],
            'number_of_days' => ['integer', 'required'],
        ];
    }

    public function messages(): array
    {
        return [
            'is_public' => 'boolean are only accepeted',
            'name' => 'name is required and must be unique',
            'description' => 'description is required',
            'number_of_days' => 'number of days must be an integer and it is required',
        ];
    }
}
