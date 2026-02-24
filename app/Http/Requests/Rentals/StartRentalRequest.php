<?php

declare(strict_types=1);

namespace App\Http\Requests\Rentals;

use Illuminate\Foundation\Http\FormRequest;

class StartRentalRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, list<string>>
     */
    public function rules(): array
    {
        return [
            'pickup_point_id' => ['required', 'integer', 'exists:pickup_points,id'],
            'power_bank_id' => ['required', 'integer', 'exists:power_banks,id'],
        ];
    }
}
