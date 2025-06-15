<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserHasCompany extends Model
{
    protected $table = 'user_has_company';
    protected $fillable = [
        'user_id',
        'company_id',
        'is_active',
        'created_at',
        'updated_at',
    ];
}
