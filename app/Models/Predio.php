<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Predio extends Model
{
    protected $table = 'predio';

    protected $fillable = [
        'contribuyente_id', 'codigo_catastral', 'matricula', 'fecha_escritura',
        'fecha_inicio_contribucion', 'zona', 'ubicacion', 'area_m2', 'avaluo_actual',
        'observaciones', 'estado',
    ];

    protected function casts(): array
    {
        return [
            'fecha_escritura' => 'date',
            'fecha_inicio_contribucion' => 'date',
            'area_m2' => 'decimal:2',
            'avaluo_actual' => 'decimal:2',
        ];
    }

    public function contribuyente(): BelongsTo
    {
        return $this->belongsTo(Contribuyente::class, 'contribuyente_id');
    }

    public function transferencias(): HasMany
    {
        return $this->hasMany(PredioTransferencia::class, 'predio_id');
    }
}
