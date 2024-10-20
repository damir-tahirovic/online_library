<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Book extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'number_of_pages',
        'quantity',
        'isbn',
        'language',
        'script',
        'binding',
        'dimensions',
    ];

    public function Authors()
    {
        return $this->hasMany(Author::class);
    }

    public function Publishers()
    {
        return $this->hasMany(Publisher::class);
    }

    public function Genres()
    {
        return $this->hasMany(Genre::class);
    }

    public function Categories()
    {
        return $this->hasMany(Category::class);
    }

}
