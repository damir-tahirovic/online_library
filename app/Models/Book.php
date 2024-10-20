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

    const SCRIPTS = ['cyrilic', 'latin', 'arabic'];
    const BINDINGS = ['hardcover', 'paperback', 'spiral-bound'];
    const DIMENSIONS = ['A1', 'A2', '21cm x 29.7cm', '15cm x 21cm'];

    public function authors()
    {
        return $this->hasMany(BookAuthor::class);
    }

    public function publishers()
    {
        return $this->hasMany(BookPublisher::class);
    }

    public function genres()
    {
        return $this->hasMany(BookGenre::class);
    }

    public function categories()
    {
        return $this->hasMany(BookCategory::class);
    }

}
