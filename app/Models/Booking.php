<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Booking extends Model
{
    use HasFactory;

    protected $fillable = ['reference_code', 'property_id', 'guest_id', 'created_by', 'check_in', 'check_out', 'num_guests', 'num_adults', 'num_children', 'price_per_night', 'cleaning_fee', 'extra_fees', 'discount', 'total_price', 'status', 'source', 'guest_notes', 'internal_notes'];

    protected function casts(): array
    {
        return ['status' => \App\Enums\BookingStatus::class, 'source' => \App\Enums\GuestSource::class, 'check_in' => 'date', 'check_out' => 'date'];
    }


    public function property() { return $this->belongsTo(Property::class); }
    public function guest() { return $this->belongsTo(Guest::class); }
    public function creator() { return $this->belongsTo(User::class, 'created_by'); }
    public function rooms() { return $this->belongsToMany(Room::class, 'booking_rooms'); }
    public function payments() { return $this->hasMany(Payment::class); }

}