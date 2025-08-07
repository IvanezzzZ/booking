<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class BookingStoreRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'slots'              => 'required|array',
            'slots.*.start_time' => 'required|date',
            'slots.*.end_time'   => 'required|date|after:slots.*.start_time',
        ];
    }
}
