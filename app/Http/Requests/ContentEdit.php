<?php

namespace App\Http\Requests;


use Illuminate\Foundation\Http\FormRequest;


class ContentEdit extends FormRequest
{
    /**
     * Edit the word file in HTML editor
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
            'path' => 'required|string|bail',
        ];
    }
}
