<?php

namespace App\Http\Requests\Business;

use Illuminate\Foundation\Http\FormRequest;

class StoreBusinessRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true; // Middleware handles this, but we could add more logic here.
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'municipality_id'       => 'required|numeric|exists:municipalities,id',
            'business_type_id'      => 'required|numeric|exists:business_types,id',
            'name'                  => 'required|string|max:255',
            'neighborhood_id'       => 'nullable|numeric|exists:neighborhoods,id',
            'district_id'           => 'nullable|numeric|exists:districts,id',
            'sectors'               => 'nullable|string', // Validated in controller via explode, but we can do better later
            'description'           => 'nullable|string',
            'address'               => 'nullable|string',
            'lat'                   => 'nullable|numeric',
            'lng'                   => 'nullable|numeric',
            'investment'            => 'nullable|numeric',
            'rental'                => 'nullable|numeric',
            'size'                  => 'nullable|numeric',
            // Add more as needed based on BusinessController@store
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'municipality_id.required'  => 'El ID del Municipio es requerido',
            'municipality_id.exists'    => 'El ID del Municipio no existe en la BD',
            'business_type_id.required' => 'El ID del Tipo de Negocio es requerido',
            'name.required'             => 'El Nombre del Negocio es requerido',
        ];
    }
}
