<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class Book extends Model implements HasMedia
{

    use HasFactory, InteractsWithMedia;

    protected $fillable = [
        'name',
        'description',
        'number_of_pages',
        'number_of_copies',
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
        return $this->belongsToMany(Author::class, 'book_authors');
    }

    public function publishers()
    {
        return $this->belongsToMany(Publisher::class, 'book_publishers');
    }

    public function genres()
    {
        return $this->belongsToMany(Genre::class, 'book_genres');
    }

    public function categories()
    {
        return $this->belongsToMany(Category::class, 'book_categories');
    }

}
