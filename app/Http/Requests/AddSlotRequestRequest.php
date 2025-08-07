<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AddSlotRequestRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'start_time' => 'required|date',
            'end_time'   => 'required|date|after:start_time',
        ];
    }
}
