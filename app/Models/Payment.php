<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    use HasFactory;

    protected $fillable = ['booking_id', 'amount', 'method', 'status', 'payment_date', 'notes'];

    protected function casts(): array
    {
        return ['method' => \App\Enums\PaymentMethod::class, 'status' => \App\Enums\PaymentStatus::class, 'payment_date' => 'date'];
    }


    public function booking() { return $this->belongsTo(Booking::class); }

}