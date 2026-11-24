<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Establecimiento extends Model
{
    protected $table = 'establecimiento';

    protected $fillable = [
        'contribuyente_id', 'tipo', 'nombre', 'direccion', 'telefono', 'observaciones', 'estado',
    ];

    public function contribuyente(): BelongsTo
    {
        return $this->belongsTo(Contribuyente::class, 'contribuyente_id');
    }

    public function permisos(): HasMany
    {
        return $this->hasMany(PermisoFuncionamiento::class, 'establecimiento_id');
    }
}
