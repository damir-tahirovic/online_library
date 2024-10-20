<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    protected $table = 'categories';

    protected $fillable = [
        'name',
        'description',
        'icon',
    ];

    public function books()
    {
        return $this->hasMany(BookCategory::class);
    }
}
