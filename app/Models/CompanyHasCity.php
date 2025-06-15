<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CompanyHasCity extends Model
{
    protected $table = 'company_has_city';

    protected $fillable = ['city_id', 'company_id',];
}
