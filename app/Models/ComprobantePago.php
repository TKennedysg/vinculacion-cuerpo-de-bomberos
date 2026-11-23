<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ComprobantePago extends Model
{
    protected $table = 'comprobante_pago';

    protected $fillable = ['comprobante_id', 'forma_pago', 'valor', 'numero_documento', 'banco'];

    protected function casts(): array
    {
        return ['valor' => 'decimal:2'];
    }

    public function comprobante(): BelongsTo
    {
        return $this->belongsTo(Comprobante::class);
    }
}
