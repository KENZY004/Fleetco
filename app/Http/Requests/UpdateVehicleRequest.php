<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateVehicleRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'license_plate' => 'required|string|max:50|unique:vehicles,license_plate,' . $this->route('vehicle')->id,
            'status' => 'required|in:active,idle,maintenance,offline',
            'current_driver_id' => 'nullable|exists:drivers,id',
        ];
    }
}
