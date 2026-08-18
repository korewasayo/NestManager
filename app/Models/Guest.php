<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Guest extends Model
{
    use HasFactory;

    protected $fillable = ['first_name', 'last_name', 'email', 'phone', 'phone_secondary', 'date_of_birth', 'nationality', 'id_document_type', 'id_document_number', 'address', 'city', 'country', 'postal_code', 'notes', 'source'];

    protected function casts(): array
    {
        return ['source' => \App\Enums\GuestSource::class, 'date_of_birth' => 'date'];
    }


    public function bookings() { return $this->hasMany(Booking::class); }

}