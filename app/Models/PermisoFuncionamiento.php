<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PermisoFuncionamiento extends Model
{
    protected $table = 'permiso_funcionamiento';

    protected $fillable = ['comprobante_id', 'establecimiento_id', 'concepto', 'anio_desde', 'anio_hasta', 'direccion_local'];

    public function comprobante(): BelongsTo
    {
        return $this->belongsTo(Comprobante::class);
    }

    public function establecimiento(): BelongsTo
    {
        return $this->belongsTo(Establecimiento::class);
    }
}
