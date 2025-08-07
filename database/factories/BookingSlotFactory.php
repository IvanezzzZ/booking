<?php

namespace Database\Factories;

use App\Models\Booking;
use Illuminate\Database\Eloquent\Factories\Factory;

class BookingSlotFactory extends Factory
{
    public function definition(): array
    {
        $startTime = $this->faker->dateTimeBetween('-10 days', '+10 days');
        $endTime = (clone $startTime)->modify("+60 minutes");

        return [
            'booking_id' => Booking::factory()->create(),
            'start_time' => $startTime,
            'end_time'   => $endTime,
        ];
    }
}
