<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Articles extends Model
{
    use HasFactory;
    protected $fillable = ["rubro_id","nombre","descripcion","caracteristicas","codigo","precio","stock_max",
    "stock_min"];

}
