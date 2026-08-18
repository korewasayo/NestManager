<?php

namespace App\Filament\Resources;

use App\Filament\Resources\BookingResource\Pages;
use App\Filament\Resources\BookingResource\RelationManagers;
use App\Models\Booking;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Forms\Components\Wizard;
use Illuminate\Support\Str;

class BookingResource extends Resource
{
    protected static ?string $model = Booking::class;

    protected static ?string $navigationIcon = 'heroicon-o-calendar-days';
    
    protected static ?string $navigationGroup = 'Guests & Bookings';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Wizard::make([
                    Wizard\Step::make('Guest & Property')
                        ->schema([
                            Forms\Components\Select::make('property_id')
                                ->relationship('property', 'name')
                                ->required()
                                ->searchable(),
                            Forms\Components\Select::make('guest_id')
                                ->relationship('guest', 'first_name') // You might want to customize this to show full name
                                ->getOptionLabelFromRecordUsing(fn (\App\Models\Guest $record) => "{$record->first_name} {$record->last_name}")
                                ->required()
                                ->searchable()
                                ->createOptionForm([
                                    Forms\Components\TextInput::make('first_name')->required(),
                                    Forms\Components\TextInput::make('last_name')->required(),
                                    Forms\Components\TextInput::make('email')->email(),
                                ]),
                            Forms\Components\TextInput::make('reference_code')
                                ->default(fn () => strtoupper(Str::random(8)))
                                ->required(),
                            Forms\Components\Select::make('source')
                                ->options(\App\Enums\GuestSource::class)
                                ->required(),
                        ])->columns(2),
                        
                    Wizard\Step::make('Dates & Occupancy')
                        ->schema([
                            Forms\Components\DatePicker::make('check_in')
                                ->required()
                                ->live(),
                            Forms\Components\DatePicker::make('check_out')
                                ->required()
                                ->after('check_in')
                                ->live()
                                ->rules([
                                    function (Forms\Get $get) {
                                        return function (string $attribute, $value, \Closure $fail) use ($get) {
                                            $propertyId = $get('property_id');
                                            $checkIn = $get('check_in');
                                            $checkOut = $value;
                                            $bookingId = $get('id'); // for edit context
                                            
                                            if ($propertyId && $checkIn && $checkOut) {
                                                $overlap = \App\Models\Booking::where('property_id', $propertyId)
                                                    ->where('status', '!=', \App\Enums\BookingStatus::CANCELLED)
                                                    ->when($bookingId, fn ($q) => $q->where('id', '!=', $bookingId))
                                                    ->where(function ($query) use ($checkIn, $checkOut) {
                                                        $query->whereBetween('check_in', [$checkIn, $checkOut])
                                                            ->orWhereBetween('check_out', [$checkIn, $checkOut])
                                                            ->orWhere(function ($q) use ($checkIn, $checkOut) {
                                                                $q->where('check_in', '<=', $checkIn)
                                                                  ->where('check_out', '>=', $checkOut);
                                                            });
                                                    })->exists();
                                                
                                                if ($overlap) {
                                                    $fail('The selected dates are not available for this property.');
                                                }
                                            }
                                        };
                                    },
                                ]),
                            Forms\Components\TextInput::make('num_guests')
                                ->required()
                                ->numeric()
                                ->default(1),
                            Forms\Components\TextInput::make('num_adults')
                                ->required()
                                ->numeric()
                                ->default(1),
                            Forms\Components\TextInput::make('num_children')
                                ->required()
                                ->numeric()
                                ->default(0),
                        ])->columns(2),
                        
                    Wizard\Step::make('Pricing')
                        ->schema([
                            Forms\Components\TextInput::make('price_per_night')
                                ->required()
                                ->numeric()
                                ->prefix('€'),
                            Forms\Components\TextInput::make('cleaning_fee')
                                ->required()
                                ->numeric()
                                ->default(0)
                                ->prefix('€'),
                            Forms\Components\TextInput::make('extra_fees')
                                ->required()
                                ->numeric()
                                ->default(0)
                                ->prefix('€'),
                            Forms\Components\TextInput::make('discount')
                                ->required()
                                ->numeric()
                                ->default(0)
                                ->prefix('€'),
                            Forms\Components\TextInput::make('total_price')
                                ->required()
                                ->numeric()
                                ->prefix('€'),
                            Forms\Components\Select::make('status')
                                ->options(\App\Enums\BookingStatus::class)
                                ->required()
                                ->default(\App\Enums\BookingStatus::PENDING),
                        ])->columns(2),
                        
                    Wizard\Step::make('Notes')
                        ->schema([
                            Forms\Components\Textarea::make('guest_notes')
                                ->columnSpanFull(),
                            Forms\Components\Textarea::make('internal_notes')
                                ->columnSpanFull(),
                        ]),
                ])->columnSpanFull()
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('reference_code')
                    ->searchable(),
                Tables\Columns\TextColumn::make('property.name')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('guest.first_name')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('check_in')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('check_out')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('total_price')
                    ->money('EUR')
                    ->sortable(),
                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->searchable(),
                Tables\Columns\TextColumn::make('source')
                    ->badge()
                    ->searchable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListBookings::route('/'),
            'create' => Pages\CreateBooking::route('/create'),
            'edit' => Pages\EditBooking::route('/{record}/edit'),
        ];
    }
}
