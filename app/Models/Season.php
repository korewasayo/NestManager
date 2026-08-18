<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Season extends Model
{
    use HasFactory;

    protected $fillable = ['property_id', 'name', 'start_date', 'end_date', 'price_per_night', 'min_nights'];

    protected function casts(): array
    {
        return ['start_date' => 'date', 'end_date' => 'date'];
    }


    public function property() { return $this->belongsTo(Property::class); }

}