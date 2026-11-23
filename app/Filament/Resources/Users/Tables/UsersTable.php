<?php

namespace App\Filament\Resources\Users\Tables;

use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class UsersTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('perfil.nombre')
                    ->label('Perfil')
                    ->searchable(),
                TextColumn::make('nombres')
                    ->label('Nombres')
                    ->searchable(),
                TextColumn::make('correo')
                    ->label('Correo')
                    ->searchable(),
                TextColumn::make('contribuyente.razon_social')
                    ->label('Contribuyente')
                    ->searchable(),
                TextColumn::make('puntoEmision.punto_codigo')
                    ->label('Punto de emisión')
                    ->searchable(),
                TextColumn::make('estado')
                    ->label('Estado')
                    ->searchable(),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions([]);
    }
}
