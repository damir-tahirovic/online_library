<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Rental extends Model
{

    protected $fillable = [
        'book_id', 
        'rented_at', 
        'student_id',
        'librarian_id',
        'returned_at'
    ];

    // public function book()
    // {
    //     return $this->belongsTo(Book::class);
    // }

    public function student()
    {
        return $this->belongsTo(User::class)->where('user_type', User::USER_TYPE_STUDENT);
    }

    public function librarian()
    {
        return $this->belongsTo(User::class)->where('user_type', User::USER_TYPE_LIBRARIAN);
    }

    public function scopeByLibrarian($query, $librarianId)
    {
        return $query->where('librarian_id', $librarianId);
    }
}
