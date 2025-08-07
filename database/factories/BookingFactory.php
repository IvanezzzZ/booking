<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class BookingFactory extends Factory
{
    public function definition(): array
    {
        return [
            'user_id' => User::all()->random(),
        ];
    }
}
