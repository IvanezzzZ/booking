<?php

namespace App\Policies;

use App\Models\Booking;
use App\Models\User;

class BookingPolicy
{
    public function updateSlot(User $user, Booking $booking): bool
    {
        return $user->id === $booking->user_id;
    }

    public function destroy(User $user, Booking $booking): bool
    {
        return $user->id === $booking->user_id;
    }

    public function addSlot(User $user, Booking $booking): bool
    {
        return $user->id === $booking->user_id;
    }
}
