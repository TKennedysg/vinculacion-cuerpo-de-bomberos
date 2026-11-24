<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SolvenciaPredio extends Model
{
    protected $table = 'solvencia_predio';

    protected $fillable = ['certificado_id', 'codigo_catastral', 'matricula'];

    public function certificado(): BelongsTo
    {
        return $this->belongsTo(CertificadoSolvencia::class, 'certificado_id');
    }
}
