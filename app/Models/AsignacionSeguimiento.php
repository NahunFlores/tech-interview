<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AsignacionSeguimiento extends Model
{
    use HasFactory;
    protected $fillable = ['proeycto_id','tarea_id'];
}
