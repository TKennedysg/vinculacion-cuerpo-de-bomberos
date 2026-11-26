<?php

namespace App\Filament\Resources\CertificadoSolvencias\Schemas;

use App\Models\Contribuyente;
use App\Models\Predio;
use App\Models\PuntoEmision;
use App\Models\TipoTramite;
use App\Services\CertificadoSolvenciaService;
use Filament\Forms\Components\CheckboxList;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Illuminate\Database\Eloquent\Builder;

class CertificadoSolvenciaForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('contribuyente_id')
                    ->label('Contribuyente (solicitante)')
                    ->searchable()
                    ->required()
                    ->live()
                    ->getSearchResultsUsing(fn (string $search): array => Contribuyente::query()
                        ->where(fn (Builder $query) => $query
                            ->where('razon_social', 'like', "%{$search}%")
                            ->orWhere('identificacion', 'like', "%{$search}%"))
                        ->limit(50)
                        ->get()
                        ->mapWithKeys(fn (Contribuyente $contribuyente): array => [
                            $contribuyente->getKey() => self::etiquetaContribuyente($contribuyente),
                        ])
                        ->all())
                    ->getOptionLabelUsing(fn ($value): ?string => ($contribuyente = Contribuyente::query()->find($value))
                        ? self::etiquetaContribuyente($contribuyente)
                        : null)
                    ->afterStateUpdated(function (Set $set): void {
                        $set('predio_ids', []);
                    })
                    ->columnSpanFull(),
                Select::make('tipo_tramite_id')
                    ->label('Tipo de trámite')
                    ->relationship('tipoTramite', 'nombre')
                    ->required()
                    ->live()
                    ->helperText(fn (Get $get): string => ($tipoTramite = TipoTramite::query()->find($get('tipo_tramite_id')))
                        ? app(CertificadoSolvenciaService::class)->leyenda($tipoTramite)
                        : 'Seleccione el tipo de trámite para generar la leyenda del certificado.'),
                Select::make('punto_emision_id')
                    ->label('Punto de emisión')
                    ->options(fn (): array => PuntoEmision::query()
                        ->get()
                        ->mapWithKeys(fn (PuntoEmision $puntoEmision): array => [
                            $puntoEmision->getKey() => $puntoEmision->descripcion
                                ?? "{$puntoEmision->establecimiento_codigo}-{$puntoEmision->punto_codigo}",
                        ])
                        ->all())
                    ->default(fn (): ?int => auth()->user()?->punto_emision_id)
                    ->required(),
                Textarea::make('descripcion_tramite')
                    ->label('Descripción del trámite')
                    ->required()
                    ->maxLength(500)
                    ->columnSpanFull(),
                TextInput::make('a_favor_de')
                    ->label('Beneficiario (a favor de)')
                    ->required()
                    ->maxLength(255),
                CheckboxList::make('predio_ids')
                    ->label('Predios incluidos')
                    ->options(fn (Get $get): array => self::prediosCertificables($get('contribuyente_id')))
                    ->required()
                    ->minItems(1)
                    ->columnSpanFull(),
                Toggle::make('confirmo_mora')
                    ->label('Confirmo que informé al contribuyente de la deuda predial')
                    ->visible(fn (Get $get): bool => self::contribuyenteAdeuda($get('contribuyente_id')))
                    ->helperText(fn (Get $get): ?string => ($anios = self::aniosDeuda($get('contribuyente_id'))) !== []
                        ? 'Años adeudados: '.implode(', ', $anios)
                        : null)
                    ->accepted(fn (Get $get): bool => self::contribuyenteAdeuda($get('contribuyente_id')))
                    ->columnSpanFull(),
                Select::make('forma_pago')
                    ->label('Forma de pago')
                    ->options(self::formasPago())
                    ->default('EFECTIVO')
                    ->required()
                    ->live(),
                TextInput::make('valor')
                    ->label('Valor')
                    ->default(CertificadoSolvenciaService::VALOR)
                    ->disabled()
                    ->dehydrated(false),
                TextInput::make('numero_documento')
                    ->label('Número de documento')
                    ->maxLength(100)
                    ->visible(fn (Get $get): bool => self::requiereDocumento($get('forma_pago'))),
                TextInput::make('banco')
                    ->label('Banco')
                    ->maxLength(100)
                    ->visible(fn (Get $get): bool => self::requiereDocumento($get('forma_pago'))),
            ]);
    }

    /**
     * @return array{adeuda: bool, anios: array<int, int|string>}
     */
    public static function deuda(?int $contribuyenteId): array
    {
        if (! $contribuyenteId) {
            return ['adeuda' => false, 'anios' => []];
        }

        $contribuyente = Contribuyente::query()->find($contribuyenteId);

        if (! $contribuyente) {
            return ['adeuda' => false, 'anios' => []];
        }

        $clase = 'App\\Services\\EstadoCuentaPredialService';

        // TODO(Adrian): integrar EstadoCuentaPredialService::adeuda() cuando esté disponible.
        if (class_exists($clase)) {
            $resultado = app($clase)->adeuda($contribuyente);

            if (is_array($resultado)) {
                return [
                    'adeuda' => (bool) ($resultado['adeuda'] ?? $resultado['debe'] ?? false),
                    'anios' => collect($resultado['anios'] ?? $resultado['anios_adeudados'] ?? [])->all(),
                ];
            }

            return ['adeuda' => (bool) $resultado, 'anios' => []];
        }

        // TODO(Adrian): mock temporal para demostrar RF-37 mientras el servicio real no exista.
        if ((bool) config('solvencia.mock_deuda', false)) {
            return [
                'adeuda' => true,
                'anios' => collect(config('solvencia.mock_anios', []))
                    ->map(fn ($anio): int => (int) $anio)
                    ->all(),
            ];
        }

        return ['adeuda' => false, 'anios' => []];
    }

    public static function contribuyenteAdeuda(?int $contribuyenteId): bool
    {
        return self::deuda($contribuyenteId)['adeuda'];
    }

    /**
     * @return array<int, int|string>
     */
    public static function aniosDeuda(?int $contribuyenteId): array
    {
        return self::deuda($contribuyenteId)['anios'];
    }

    /**
     * @return array<string, string>
     */
    public static function formasPago(): array
    {
        return [
            'EFECTIVO' => 'Efectivo',
            'DEPOSITO' => 'Depósito',
            'CREDITO' => 'Crédito',
            'TRANSFERENCIA' => 'Transferencia',
            'CHEQUE_CERTIFICADO' => 'Cheque certificado',
            'PERMUTA' => 'Permuta',
            'TARJETA_DEBITO' => 'Tarjeta de débito',
            'TARJETA_CREDITO' => 'Tarjeta de crédito',
            'NOTA_CREDITO' => 'Nota de crédito',
        ];
    }

    /**
     * @return array<int, string>
     */
    public static function prediosCertificables(?int $contribuyenteId): array
    {
        if (! $contribuyenteId) {
            return [];
        }

        return Predio::query()
            ->where('contribuyente_id', $contribuyenteId)
            ->whereIn('estado', CertificadoSolvenciaService::ESTADOS_CERTIFICABLES)
            ->orderBy('codigo_catastral')
            ->get()
            ->mapWithKeys(fn (Predio $predio): array => [
                $predio->getKey() => $predio->matricula
                    ? "{$predio->codigo_catastral} — {$predio->matricula}"
                    : $predio->codigo_catastral,
            ])
            ->all();
    }

    private static function etiquetaContribuyente(Contribuyente $contribuyente): string
    {
        return "{$contribuyente->identificacion} — {$contribuyente->razon_social}";
    }

    private static function requiereDocumento(?string $formaPago): bool
    {
        return in_array($formaPago, [
            'DEPOSITO',
            'TRANSFERENCIA',
            'CHEQUE_CERTIFICADO',
            'TARJETA_DEBITO',
            'TARJETA_CREDITO',
            'NOTA_CREDITO',
        ], true);
    }
}
