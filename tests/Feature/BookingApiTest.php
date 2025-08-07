<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\Booking;
use App\Models\BookingSlot;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BookingApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_unauthenticated_access_denied()
    {
        $response = $this->getJson('/api/bookings');
        $response->assertStatus(401);
    }

    public function test_create_booking_with_multiple_slots()
    {
        $user = User::factory()->create();
        $token = $user->api_token;

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->postJson('/api/bookings', [
            'slots' => [
                [
                    'start_time' => '2025-06-25 12:00:00',
                    'end_time' => '2025-06-25 13:00:00',
                ],
                [
                    'start_time' => '2025-06-25 13:30:00',
                    'end_time' => '2025-06-25 14:30:00',
                ]
            ]
        ]);

        $response->assertStatus(201);

        $this->assertCount(1, Booking::all());
        $this->assertCount(2, BookingSlot::all());
    }

    public function test_add_slot_with_conflict_fails()
    {
        $user = User::factory()->create();
        $token = $user->api_token;

        $booking = Booking::factory()->create([
            'user_id' => $user->id
        ]);

        BookingSlot::factory()->create([
            'booking_id' => $booking->id,
            'start_time' => '2025-06-25 12:00:00',
            'end_time' => '2025-06-25 13:00:00',
        ]);

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->postJson("/api/bookings/{$booking->id}/slots", [
            'start_time' => '2025-06-25 12:30:00',
            'end_time' => '2025-06-25 13:30:00',
        ]);

        $response->assertStatus(409)
            ->assertJsonStructure(['message']);
    }

    public function test_update_slot_successfully()
    {
        $user = User::factory()->create();
        $token = $user->api_token;

        $booking = Booking::factory()->create([
            'user_id' => $user->id
        ]);

        $slot = BookingSlot::factory()->create([
            'booking_id' => $booking->id,
            'start_time' => '2025-06-25 12:00:00',
            'end_time' => '2025-06-25 13:00:00',
        ]);

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->patchJson("/api/bookings/{$booking->id}/slots/{$slot->id}", [
            'start_time' => '2025-06-25 14:00:00',
            'end_time' => '2025-06-25 15:00:00',
        ]);

        $response->assertStatus(200);
        $this->assertEquals('2025-06-25 14:00:00', $slot->fresh()->start_time);
    }

    public function test_cannot_update_others_booking()
    {
        $user1 = User::factory()->create();
        $user2 = User::factory()->create();

        $booking = Booking::factory()->create(['user_id' => $user2->id]);

        $slot = BookingSlot::factory()->create([
            'booking_id' => $booking->id,
            'start_time' => '2025-06-25 14:00:00',
            'end_time' => '2025-06-25 15:00:00'
        ]);

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $user1->api_token,
        ])->patchJson("/api/bookings/{$booking->id}/slots/{$slot->id}", [
            'start_time' => '2025-06-25 16:00:00',
            'end_time' => '2025-06-25 17:00:00',
        ]);

        $response->assertStatus(403);
    }
}
