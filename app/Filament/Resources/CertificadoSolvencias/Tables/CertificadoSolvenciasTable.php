<?php

namespace App\Filament\Resources\CertificadoSolvencias\Tables;

use App\Models\CertificadoSolvencia;
use App\Models\PuntoEmision;
use App\Services\AnulacionService;
use App\Services\CertificadoSolvenciaPdfService;
use Filament\Actions\Action;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class CertificadoSolvenciasTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(fn (Builder $query): Builder => $query->with([
                'comprobante.puntoEmision',
                'comprobante.contribuyente',
                'comprobante.usuario',
                'tipoTramite',
            ]))
            ->columns([
                TextColumn::make('comprobante.secuencial')
                    ->label('Número')
                    ->formatStateUsing(fn (CertificadoSolvencia $record): string => self::numero($record))
                    ->searchable(query: fn (Builder $query, string $search): Builder => $query
                        ->whereHas('comprobante', fn (Builder $comprobante) => $comprobante
                            ->where('secuencial', 'like', "%{$search}%"))),
                TextColumn::make('comprobante.fecha_emision')
                    ->label('Fecha')
                    ->date('d/m/Y'),
                TextColumn::make('comprobante.contribuyente.razon_social')
                    ->label('Contribuyente')
                    ->searchable(query: fn (Builder $query, string $search): Builder => $query
                        ->whereHas('comprobante.contribuyente', fn (Builder $contribuyente) => $contribuyente
                            ->where('razon_social', 'like', "%{$search}%"))),
                TextColumn::make('comprobante.contribuyente.identificacion')
                    ->label('Identificación')
                    ->searchable(query: fn (Builder $query, string $search): Builder => $query
                        ->whereHas('comprobante.contribuyente', fn (Builder $contribuyente) => $contribuyente
                            ->where('identificacion', 'like', "%{$search}%"))),
                TextColumn::make('tipoTramite.nombre')
                    ->label('Trámite'),
                TextColumn::make('valor')
                    ->label('Valor')
                    ->formatStateUsing(fn ($state): string => '$'.number_format((float) $state, 2)),
                TextColumn::make('comprobante.usuario.nombres')
                    ->label('Usuario'),
                TextColumn::make('comprobante.estado')
                    ->label('Estado')
                    ->badge()
                    ->color(fn (?string $state): string => $state === 'A' ? 'success' : 'danger'),
            ])
            ->filters([
                SelectFilter::make('tipo_tramite_id')
                    ->label('Tipo de trámite')
                    ->relationship('tipoTramite', 'nombre'),
                SelectFilter::make('punto_emision_id')
                    ->label('Serie / punto de emisión')
                    ->options(fn (): array => PuntoEmision::query()
                        ->get()
                        ->mapWithKeys(fn (PuntoEmision $puntoEmision): array => [
                            $puntoEmision->getKey() => "{$puntoEmision->establecimiento_codigo}-{$puntoEmision->punto_codigo}",
                        ])
                        ->all())
                    ->query(fn (Builder $query, array $data): Builder => filled($data['value'] ?? null)
                        ? $query->whereHas('comprobante', fn (Builder $comprobante) => $comprobante
                            ->where('punto_emision_id', $data['value']))
                        : $query),
                SelectFilter::make('estado')
                    ->label('Estado')
                    ->options(['A' => 'Activo', 'X' => 'Anulado'])
                    ->query(fn (Builder $query, array $data): Builder => filled($data['value'] ?? null)
                        ? $query->whereHas('comprobante', fn (Builder $comprobante) => $comprobante
                            ->where('estado', $data['value']))
                        : $query),
                Filter::make('secuencial')
                    ->label('Secuencial')
                    ->form([
                        TextInput::make('secuencial')
                            ->label('Secuencial'),
                    ])
                    ->query(fn (Builder $query, array $data): Builder => filled($data['secuencial'] ?? null)
                        ? $query->whereHas('comprobante', fn (Builder $comprobante) => $comprobante
                            ->where('secuencial', 'like', '%'.$data['secuencial'].'%'))
                        : $query),
                Filter::make('contribuyente')
                    ->label('Contribuyente')
                    ->form([
                        TextInput::make('contribuyente')
                            ->label('Nombre o razón social'),
                    ])
                    ->query(fn (Builder $query, array $data): Builder => filled($data['contribuyente'] ?? null)
                        ? $query->whereHas('comprobante.contribuyente', fn (Builder $contribuyente) => $contribuyente
                            ->where('razon_social', 'like', '%'.$data['contribuyente'].'%'))
                        : $query),
                Filter::make('identificacion')
                    ->label('Identificación')
                    ->form([
                        TextInput::make('identificacion')
                            ->label('Identificación'),
                    ])
                    ->query(fn (Builder $query, array $data): Builder => filled($data['identificacion'] ?? null)
                        ? $query->whereHas('comprobante.contribuyente', fn (Builder $contribuyente) => $contribuyente
                            ->where('identificacion', 'like', '%'.$data['identificacion'].'%'))
                        : $query),
                Filter::make('fecha')
                    ->label('Rango de fechas')
                    ->form([
                        DatePicker::make('desde')->label('Desde'),
                        DatePicker::make('hasta')->label('Hasta'),
                    ])
                    ->query(fn (Builder $query, array $data): Builder => $query
                        ->when($data['desde'] ?? null, fn (Builder $consulta, $fecha) => $consulta
                            ->whereHas('comprobante', fn (Builder $comprobante) => $comprobante
                                ->whereDate('fecha_emision', '>=', $fecha)))
                        ->when($data['hasta'] ?? null, fn (Builder $consulta, $fecha) => $consulta
                            ->whereHas('comprobante', fn (Builder $comprobante) => $comprobante
                                ->whereDate('fecha_emision', '<=', $fecha)))),
            ])
            ->recordActions([
                Action::make('pdf')
                    ->label('Reimprimir')
                    ->icon('heroicon-o-arrow-down-tray')
                    ->authorize('view')
                    ->action(fn (CertificadoSolvencia $record) => app(CertificadoSolvenciaPdfService::class)
                        ->descargar($record)),
                Action::make('anular')
                    ->label('Anular')
                    ->icon('heroicon-o-x-circle')
                    ->color('danger')
                    ->visible(fn (CertificadoSolvencia $record): bool => $record->comprobante?->estado === 'A')
                    ->authorize('anular')
                    ->schema([
                        Textarea::make('motivo')
                            ->label('Motivo de anulación')
                            ->required()
                            ->maxLength(500),
                    ])
                    ->action(function (CertificadoSolvencia $record, array $data): void {
                        app(AnulacionService::class)->anular(
                            $record->comprobante,
                            (string) $data['motivo'],
                            (int) auth()->id(),
                        );

                        Notification::make()
                            ->success()
                            ->title('Solvencia anulada')
                            ->send();
                    }),
            ])
            ->defaultSort('id', 'desc');
    }

    public static function numero(CertificadoSolvencia $record): string
    {
        $comprobante = $record->comprobante;
        $puntoEmision = $comprobante?->puntoEmision;

        if (! $comprobante || ! $puntoEmision) {
            return (string) $comprobante?->secuencial;
        }

        return sprintf(
            '%s-%s-%s',
            str_pad((string) $puntoEmision->establecimiento_codigo, 3, '0', STR_PAD_LEFT),
            str_pad((string) $puntoEmision->punto_codigo, 3, '0', STR_PAD_LEFT),
            $comprobante->secuencial,
        );
    }
}
