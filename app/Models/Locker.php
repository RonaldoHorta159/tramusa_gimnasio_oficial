<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Locker extends Model
{
    use HasFactory;

    protected $primaryKey = 'id_locker';

    // Relación con el cliente
    public function cliente()
    {
        return $this->belongsTo(Client::class, 'id_cliente', 'id_cliente');
    }
}
