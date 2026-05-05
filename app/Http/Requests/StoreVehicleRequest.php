<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreVehicleRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'license_plate' => 'required|string|max:50|unique:vehicles,license_plate',
            'status' => 'required|in:active,idle,maintenance,offline',
        ];
    }
}
