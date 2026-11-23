<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Contribuyente extends Model
{
    protected $table = 'contribuyente';

    protected $fillable = [
        'pais_origen', 'tipo_contribuyente', 'tipo_identificacion', 'identificacion',
        'razon_social', 'representante_legal', 'direccion', 'zona', 'correo',
        'telefono_domicilio', 'celular', 'telefono', 'fecha_nacimiento', 'estado',
    ];

    protected function casts(): array
    {
        return ['fecha_nacimiento' => 'date'];
    }

    public function establecimientos(): HasMany
    {
        return $this->hasMany(Establecimiento::class, 'contribuyente_id');
    }

    public function predios(): HasMany
    {
        return $this->hasMany(Predio::class, 'contribuyente_id');
    }

    public function comprobantes(): HasMany
    {
        return $this->hasMany(Comprobante::class, 'contribuyente_id');
    }

    public function usuarios(): HasMany
    {
        return $this->hasMany(User::class, 'contribuyente_id');
    }
}
