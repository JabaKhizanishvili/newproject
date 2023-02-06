<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Iconimage extends Model
{
    use HasFactory;
    protected $fillable = [
        'name',
        'size',
        'type',
        'blog_id',
    ];

    public function blog()
    {
        return $this->hasOne(Blog::class);
    }
}
