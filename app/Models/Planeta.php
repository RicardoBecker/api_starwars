<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Planeta extends Model
{

    protected $primaryKey = 'id_planeta';

    protected $fillable = [
        "nome_planeta", "id_user",
    ];
}
