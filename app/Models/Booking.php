<?php

declare(strict_types=1);

namespace App\Models;

use App\Policies\BookingPolicy;
use Illuminate\Database\Eloquent\Attributes\UsePolicy;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[UsePolicy(BookingPolicy::class)]
class Booking extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id'
    ];

    public function slots(): HasMany
    {
        return $this->hasMany(BookingSlot::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
