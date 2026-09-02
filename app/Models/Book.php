<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Book extends Model
{
    use HasFactory;

    protected $guarded = [];

    // Ubah nama fungsi ini agar tidak sama persis dengan nama kolom database 'category'
    public function categoryRelation()
    {
        return $this->belongsTo(Category::class, 'category_id');
    }
}