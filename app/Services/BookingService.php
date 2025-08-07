<?php

declare(strict_types=1);

namespace App\Services;

use App\Exceptions\TimeConflictException;
use App\Models\Booking;
use App\Models\BookingSlot;
use DateTime;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

class BookingService
{
    /**
     * @throws TimeConflictException
     */
    public function store(
        array $data,
        int $userID
    ): Booking {
        DB::beginTransaction();

        $booking = Booking::query()
            ->create([
                'user_id' => $userID
            ]);

        foreach ($data['slots'] as $slot) {
            if ($this->hasConflict($slot['start_time'], $slot['end_time'])) {
                DB::rollBack();
                throw new TimeConflictException('Time slot conflicts with existing bookings.');
            }
            $booking->slots()->create($slot);
        }

        DB::commit();

        return $booking;
    }

    /**
     * @throws TimeConflictException
     */
    public function addSlot(
        array $data,
        Booking $booking
    ): Booking {
        if ($this->hasConflict($data['start_time'], $data['end_time'])) {
            throw new TimeConflictException('Time conflict with other bookings.');
        }

        return $booking->slots()->create($data);
    }

    /**
     * @throws TimeConflictException
     */
    public function updateSlot(
        array $data,
        BookingSlot $slot
    ): JsonResponse|BookingSlot {
        if ($this->hasConflict($data['start_time'], $data['end_time'], $slot->id)) {
            throw new TimeConflictException('Time conflict with other bookings.');
        }

        $slot->update($data);

        return $slot;
    }

    private function hasConflict(
        DateTime|string $start,
        DateTime|string $end,
        ?int $excludeSlotId = null
    ): bool {
        $query = BookingSlot::query()
            ->where(function ($query) use ($start, $end) {
                $query->whereBetween('start_time', [$start, $end])
                    ->orWhereBetween('end_time', [$start, $end])
                    ->orWhere(function ($query2) use ($start, $end) {
                        $query2->where('start_time', '<=', $start)->where('end_time', '>=', $end);
                    });
            });

        if ($excludeSlotId) {
            $query->where('id', '!=', $excludeSlotId);
        }

        return $query->exists();
    }
}
