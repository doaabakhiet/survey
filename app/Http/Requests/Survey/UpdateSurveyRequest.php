<?php

namespace App\Http\Requests\Survey;

use Illuminate\Foundation\Http\FormRequest;

class UpdateSurveyRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        $survey=$this->route('survey');
        if($this->user()->id !==$survey->user_id){
            return false;
        }
        return true;

    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\Rule|array|string>
     */
    public function rules(): array
    {
        return [
            'title'=>'required|string|max:1000',
            'user_id'=>'exists:users,id',
            'image'=>'nullable|string',
            'status'=>'required|boolean',
            'desription'=>'nullable|string',
            'expire_date'=>'nullable|date|after:tomorrow',
            'questions'=>'array'
        ];
    }
}