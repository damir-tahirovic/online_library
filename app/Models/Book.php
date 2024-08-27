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

//    public function bookAuthors()
//    {
//        return $this->hasMany(BookAuthor::class);
//    }
//
//    public function bookPublishers()
//    {
//        return $this->hasMany(BookPublisher::class);
//    }
//
//    public function bookGenres()
//    {
//        return $this->hasMany(BookGenre::class);
//    }
//
//    public function bookCategories()
//    {
//        return $this->hasMany(BookCategory::class);
//    }

}
