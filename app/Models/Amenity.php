<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Amenity extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'icon', 'category'];

    protected function casts(): array
    {
        return ['category' => \App\Enums\AmenityCategory::class];
    }


    public function properties() { return $this->belongsToMany(Property::class, 'property_amenities'); }

}