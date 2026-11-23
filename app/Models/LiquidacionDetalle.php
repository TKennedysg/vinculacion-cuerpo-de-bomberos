<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LiquidacionDetalle extends Model
{
    protected $table = 'liquidacion_detalle';

    protected $fillable = [
        'comprobante_id', 'predio_id', 'anio_fiscal', 'avaluo', 'subtotal',
        'porcentaje_descuento', 'valor_descuento', 'total_base', 'intereses',
        'multa', 'gastos_administrativos', 'total',
    ];

    protected function casts(): array
    {
        return [
            'anio_fiscal' => 'integer',
            'avaluo' => 'decimal:2', 'subtotal' => 'decimal:2',
            'porcentaje_descuento' => 'decimal:2', 'valor_descuento' => 'decimal:2',
            'total_base' => 'decimal:2', 'intereses' => 'decimal:2',
            'multa' => 'decimal:2', 'gastos_administrativos' => 'decimal:2',
            'total' => 'decimal:2',
        ];
    }

    public function comprobante(): BelongsTo
    {
        return $this->belongsTo(Comprobante::class);
    }

    public function predio(): BelongsTo
    {
        return $this->belongsTo(Predio::class);
    }
}
