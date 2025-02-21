<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Collaborator extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'document',
        'observation',
    ];

    public static function getActive()
    {
        return self::query()->where('active', '=', true)->get();
    }
}
