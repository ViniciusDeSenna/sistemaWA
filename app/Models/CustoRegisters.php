<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CustoRegisters extends Model
{
    public $table = "custo_registers";
    protected $fillable = [
        'custo_id',
        'date',
        'value',
        'description',
    ];
}
