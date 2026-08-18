#!/bin/bash
cd /var/www

resources=("Amenity" "Guest" "Season" "Property" "Payment" "Booking" "User")

for resource in "${resources[@]}"; do
    php artisan make:filament-resource $resource --generate
done

php artisan make:filament-relation-manager PropertyResource rooms name
