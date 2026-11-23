<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PuntoEmision extends Model
{
    protected $table = 'punto_emision';

    protected $fillable = [
        'establecimiento_codigo',
        'punto_codigo',
        'secuencia_actual',
        'descripcion',
    ];

    protected function casts(): array
    {
        return [
            'secuencia_actual' => 'integer',
        ];
    }

    public function usuarios(): HasMany
    {
        return $this->hasMany(User::class, 'punto_emision_id');
    }
}
