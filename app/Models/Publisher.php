<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

// use App\Models\Book;
use App\Models\Author;

class Publisher extends Model implements HasMedia
{
    use HasFactory, InteractsWithMedia;

    protected $fillable = [
        'name', 'address', 'website', 'email', 'phone', 'established_year'
    ];

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('logo')
            ->singleFile()
            ->registerMediaConversions(function (Media $media) {
                $this->addMediaConversion('thumb')
                    ->width(100)
                    ->height(100);
            });
    }

    // public function authors()
    // {
    //     return $this->belongsToMany(Author::class);
    // }

    // Relations in Book model
    // public function publisher()
    // {
    //     return $this->belongsTo(Publisher::class);
    // }

    // Relations in Author model
    // public function publishers()
    // {
    //     return $this->belongsToMany(Publisher::class);

    // }

    public function books()
    {
        return $this->hasMany(BookPublisher::class);
    }

    public function authors()
    {
        return $this->belongsToMany(Author::class);
    }

}
