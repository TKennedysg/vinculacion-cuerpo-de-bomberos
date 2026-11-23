<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Comprobante extends Model
{
    protected $table = 'comprobante';

    protected $fillable = [
        'tipo',
        'punto_emision_id',
        'secuencial',
        'fecha_emision',
        'contribuyente_id',
        'usuario_id',
        'valor_total',
        'observacion',
        'estado',
        'motivo_anulacion',
        'anulado_por',
        'anulado_en',
    ];

    protected function casts(): array
    {
        return [
            'fecha_emision' => 'date',
            'valor_total' => 'decimal:2',
            'anulado_en' => 'datetime',
        ];
    }

    public function puntoEmision(): BelongsTo
    {
        return $this->belongsTo(PuntoEmision::class, 'punto_emision_id');
    }

    public function usuario(): BelongsTo
    {
        return $this->belongsTo(User::class, 'usuario_id');
    }
}
