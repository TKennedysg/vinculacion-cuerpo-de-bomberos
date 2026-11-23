<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ArqueoCaja extends Model
{
    protected $table = 'arqueo_caja';

    protected $fillable = ['usuario_id', 'fecha', 'total_contado', 'total_sistema', 'diferencia'];

    protected function casts(): array
    {
        return ['fecha' => 'date', 'total_contado' => 'decimal:2', 'total_sistema' => 'decimal:2', 'diferencia' => 'decimal:2'];
    }

    public function usuario(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function detalles(): HasMany
    {
        return $this->hasMany(ArqueoDetalle::class, 'arqueo_id');
    }
}
