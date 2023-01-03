<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Bookvisit extends Model
{
    use HasFactory;
    protected $fillable = [
        'name',
        'company',
        'position',
        'phone',
        'mail',
        'quantity',
        'contact',
    ];
}
