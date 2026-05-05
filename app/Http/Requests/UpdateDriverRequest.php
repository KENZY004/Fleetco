<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateDriverRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'phone_number' => 'nullable|string|max:20',
            'license_number' => 'nullable|string|max:50',
            'user_id' => 'nullable|exists:users,id|unique:drivers,user_id,' . $this->route('driver')->id,
            'vehicle_id' => 'nullable|exists:vehicles,id',
        ];
    }
}
