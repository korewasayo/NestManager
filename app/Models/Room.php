<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Room extends Model
{
    use HasFactory;

    protected $fillable = ['property_id', 'name', 'type', 'max_occupancy', 'price_override', 'description', 'is_active'];

    protected function casts(): array
    {
        return ['type' => \App\Enums\RoomType::class, 'is_active' => 'boolean'];
    }


    public function property() { return $this->belongsTo(Property::class); }
    public function bookings() { return $this->belongsToMany(Booking::class, 'booking_rooms'); }

}