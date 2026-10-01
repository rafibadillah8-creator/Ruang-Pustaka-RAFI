<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    use HasFactory;

    protected $table = 'transactions';

    protected $fillable = [
        'user_id',
        'book_id', // Ditambahkan agar ID buku tersimpan di database
        'title',
        'author',
        'price',
    ];

    // Relasi ke User (Opsional tapi direkomendasikan)
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Relasi ke Book (Opsional tapi direkomendasikan)
    public function book()
    {
        return $this->belongsTo(Book::class);
    }
}