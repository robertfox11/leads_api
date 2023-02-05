<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Lead extends Model
{
    use HasFactory;
    protected $fillable=[
        'titulo',
        'estado_lead',
        'fecha_creacion',
        'fecha_cierre'
    ];
}
