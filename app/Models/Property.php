<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Property extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'slug', 'description', 'address', 'city', 'postal_code', 'country', 'latitude', 'longitude', 'type', 'max_guests', 'bedrooms', 'bathrooms', 'base_price_per_night', 'cleaning_fee', 'check_in_time', 'check_out_time', 'house_rules', 'status'];

    protected function casts(): array
    {
        return ['type' => \App\Enums\PropertyType::class, 'status' => \App\Enums\PropertyStatus::class, 'latitude' => 'decimal:8', 'longitude' => 'decimal:8'];
    }


    public function rooms() { return $this->hasMany(Room::class); }
    public function bookings() { return $this->hasMany(Booking::class); }
    public function seasons() { return $this->hasMany(Season::class); }
    public function amenities() { return $this->belongsToMany(Amenity::class, 'property_amenities'); }

}