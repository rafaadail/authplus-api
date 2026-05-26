<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Support\Arr;

class Request extends FormRequest
{
    /**
    * Get the error messages for the defined validation rules.*
    * @return array
    */
    protected function failedValidation(Validator $validator)
    {
        $message = "Erro de validação.";
        
        $message = implode(PHP_EOL, $validator->errors()->all());

        if(isset($validator->failed()['USUARIO']['App\Rules\ValidaLoginRule'])){
            $message = $validator->errors()->first('USUARIO');
        }

        throw new HttpResponseException(response()->json([
        "success" => false,
        "message" => $message,
        'errors' => $validator->errors()->all(),
        ], 422));
    }
}