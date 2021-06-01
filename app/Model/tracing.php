<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class tracing extends Model
{
    protected $fillable = [
        'id', 'idDocumento', 'idUsuarioTraslada', 'idUsuarioActual','fechaInicial','fechaFinal','estado','instruccion','created_at','updated_at'
    ];
}
