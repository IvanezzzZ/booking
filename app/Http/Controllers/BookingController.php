<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Exceptions\TimeConflictException;
use App\Http\Requests\AddSlotRequestRequest;
use App\Http\Requests\BookingStoreRequest;
use App\Http\Requests\SlotUpdateRequest;
use App\Models\Booking;
use App\Models\BookingSlot;
use App\Services\BookingService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class BookingController extends Controller
{
    public function index(): JsonResponse
    {
        $user     = auth()->user();
        $bookings = $user->bookings()->with('slots')->get();

        return response()->json($bookings);
    }

    public function store(
        BookingStoreRequest $request,
        BookingService $bookingService
    ): JsonResponse {
        $data = $request->validated();
        $userID = $request->user()->id;

        try {
            $booking = $bookingService->store($data, $userID);
        } catch (TimeConflictException $exception) {
            return response()->json([
                'message' => $exception->getMessage()
            ], 409);
        }

        return response()->json($booking->load('slots'), 201);
    }

    public function updateSlot(
        SlotUpdateRequest $request,
        Booking $booking,
        BookingSlot $slot,
        BookingService $bookingService
    ): JsonResponse {
        $data = $request->validated();

        try {
            $slot = $bookingService->updateSlot($data, $slot);
        } catch (TimeConflictException $exception){
            return response()->json([
                'message' => $exception->getMessage()
            ], 409);
        }

        return response()->json($slot);
    }

    public function addSlot(
        AddSlotRequestRequest $request,
        Booking $booking,
        BookingService $bookingService
    ): JsonResponse {

        $data = $request->validated();

        try {
            $slot = $bookingService->addSlot($data, $booking);
        } catch (TimeConflictException $exception) {
            return response()->json([
                'message' => $exception->getMessage(),
            ], 409);
        }

        return response()->json($slot);
    }

    public function destroy(
        Booking $booking
    ): JsonResponse {

        $booking->delete();

        return response()->json([
            'message' => 'Booking deleted successfully'
        ]);
    }
}
