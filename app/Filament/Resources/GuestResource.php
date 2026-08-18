<?php

namespace App\Filament\Resources;

use App\Filament\Resources\GuestResource\Pages;
use App\Filament\Resources\GuestResource\RelationManagers;
use App\Models\Guest;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class GuestResource extends Resource
{
    protected static ?string $model = Guest::class;

    protected static ?string $navigationIcon = 'heroicon-o-users';
    
    protected static ?string $navigationGroup = 'Guests & Bookings';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('first_name')
                    ->required(),
                Forms\Components\TextInput::make('last_name')
                    ->required(),
                Forms\Components\TextInput::make('email')
                    ->email(),
                Forms\Components\TextInput::make('phone')
                    ->tel(),
                Forms\Components\TextInput::make('phone_secondary')
                    ->tel(),
                Forms\Components\DatePicker::make('date_of_birth'),
                Forms\Components\TextInput::make('nationality'),
                Forms\Components\TextInput::make('id_document_type'),
                Forms\Components\TextInput::make('id_document_number'),
                Forms\Components\Textarea::make('address')
                    ->columnSpanFull(),
                Forms\Components\TextInput::make('city'),
                Forms\Components\TextInput::make('country'),
                Forms\Components\TextInput::make('postal_code'),
                Forms\Components\Textarea::make('notes')
                    ->columnSpanFull(),
                Forms\Components\Select::make('source')
                    ->options(\App\Enums\GuestSource::class),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('first_name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('last_name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('email')
                    ->searchable(),
                Tables\Columns\TextColumn::make('phone')
                    ->searchable(),
                Tables\Columns\TextColumn::make('phone_secondary')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('date_of_birth')
                    ->date()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('nationality')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('id_document_type')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('id_document_number')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('city')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('country')
                    ->searchable(),
                Tables\Columns\TextColumn::make('postal_code')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
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
                Tables\Filters\SelectFilter::make('source')
                    ->options(\App\Enums\GuestSource::class),
                Tables\Filters\SelectFilter::make('country')
                    ->options(fn () => Guest::query()->distinct()->pluck('country', 'country')->filter()->toArray())
                    ->searchable(),
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
            'index' => Pages\ListGuests::route('/'),
            'create' => Pages\CreateGuest::route('/create'),
            'edit' => Pages\EditGuest::route('/{record}/edit'),
        ];
    }
}
