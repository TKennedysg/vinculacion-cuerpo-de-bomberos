<?php

namespace App\Filament\Resources\PuntoEmisions;

use App\Filament\Resources\PuntoEmisions\Pages\CreatePuntoEmision;
use App\Filament\Resources\PuntoEmisions\Pages\EditPuntoEmision;
use App\Filament\Resources\PuntoEmisions\Pages\ListPuntoEmisions;
use App\Filament\Resources\PuntoEmisions\Schemas\PuntoEmisionForm;
use App\Filament\Resources\PuntoEmisions\Tables\PuntoEmisionsTable;
use App\Models\PuntoEmision;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class PuntoEmisionResource extends Resource
{
    protected static ?string $model = PuntoEmision::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    public static function form(Schema $schema): Schema
    {
        return PuntoEmisionForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return PuntoEmisionsTable::configure($table);
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
            'index' => ListPuntoEmisions::route('/'),
            'create' => CreatePuntoEmision::route('/create'),
            'edit' => EditPuntoEmision::route('/{record}/edit'),
        ];
    }
}
