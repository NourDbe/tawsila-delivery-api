<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class OrderListRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Query parameters used by order list endpoints.
     *
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'per_page' => [
                'nullable',
                'integer',
                'min:1',
                'max:100',
            ],

            'minutes' => [
                'nullable',
                'integer',
                'min:1',
                'max:1440',
            ],
        ];
    }
}
