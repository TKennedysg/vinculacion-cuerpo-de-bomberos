<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CertificadoSolvencia extends Model
{
    protected $table = 'certificado_solvencia';

    protected $fillable = ['comprobante_id', 'tipo_tramite_id', 'descripcion_tramite', 'a_favor_de', 'leyenda', 'valor'];

    protected function casts(): array
    {
        return ['valor' => 'decimal:2'];
    }

    public function comprobante(): BelongsTo
    {
        return $this->belongsTo(Comprobante::class);
    }

    public function tipoTramite(): BelongsTo
    {
        return $this->belongsTo(TipoTramite::class);
    }

    public function predios(): HasMany
    {
        return $this->hasMany(SolvenciaPredio::class, 'certificado_id');
    }
}
