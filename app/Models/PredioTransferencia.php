<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PredioTransferencia extends Model
{
    protected $table = 'predio_transferencia';

    protected $fillable = [
        'predio_id', 'contribuyente_origen_id', 'contribuyente_destino_id',
        'fecha', 'usuario_id', 'motivo',
    ];

    protected function casts(): array
    {
        return ['fecha' => 'date'];
    }

    public function predio(): BelongsTo
    {
        return $this->belongsTo(Predio::class, 'predio_id');
    }

    public function origen(): BelongsTo
    {
        return $this->belongsTo(Contribuyente::class, 'contribuyente_origen_id');
    }

    public function destino(): BelongsTo
    {
        return $this->belongsTo(Contribuyente::class, 'contribuyente_destino_id');
    }

    public function usuario(): BelongsTo
    {
        return $this->belongsTo(User::class, 'usuario_id');
    }
}
