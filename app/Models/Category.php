<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    use HasFactory;

    protected $guarded = [];

    // 1 File atau Kategori ini Punya Banyak Buku
    public function books()
    {
        return $this->hasMany(Book::class);
    }
}