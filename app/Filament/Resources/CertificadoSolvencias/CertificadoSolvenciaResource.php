<?php

namespace App\Filament\Resources\CertificadoSolvencias;

use App\Filament\Resources\CertificadoSolvencias\Pages\CreateCertificadoSolvencia;
use App\Filament\Resources\CertificadoSolvencias\Pages\ListCertificadoSolvencias;
use App\Filament\Resources\CertificadoSolvencias\Schemas\CertificadoSolvenciaForm;
use App\Filament\Resources\CertificadoSolvencias\Tables\CertificadoSolvenciasTable;
use App\Models\CertificadoSolvencia;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;

class CertificadoSolvenciaResource extends Resource
{
    protected static ?string $model = CertificadoSolvencia::class;

    protected static string|\UnitEnum|null $navigationGroup = 'Recaudaciones';

    protected static ?string $navigationLabel = 'Solvencias';

    protected static ?string $modelLabel = 'solvencia';

    protected static ?string $pluralModelLabel = 'solvencias';

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-document-check';

    public static function form(Schema $schema): Schema
    {
        return CertificadoSolvenciaForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return CertificadoSolvenciasTable::configure($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListCertificadoSolvencias::route('/'),
            'create' => CreateCertificadoSolvencia::route('/create'),
        ];
    }
}
