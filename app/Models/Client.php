<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Client extends Model
{
    use HasFactory;
    // Tu PK no es 'id', sino 'id_cliente'
    protected $primaryKey = 'id_cliente';

    // Permite asignación masiva para estos campos
    protected $fillable = [
        'nombre_cliente',
        'fecha_nacimiento',
        'residencia',
        'fecha_inicio_membresia',
        'fecha_fin_membresia',
        'importe_membresia',
        'estado',
    ];

    public function locker()
    {
        return $this->hasOne(Locker::class, 'id_cliente', 'id_cliente');
    }
}
