<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ArqueoDetalle extends Model
{
    protected $table = 'arqueo_detalle';

    protected $fillable = ['arqueo_id', 'tipo', 'denominacion', 'cantidad', 'subtotal'];

    protected function casts(): array
    {
        return ['denominacion' => 'decimal:2', 'cantidad' => 'integer', 'subtotal' => 'decimal:2'];
    }

    public function arqueo(): BelongsTo
    {
        return $this->belongsTo(ArqueoCaja::class, 'arqueo_id');
    }
}
