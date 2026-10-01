<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Book extends Model
{
    use HasFactory;

    protected $fillable = [
        'title', 'author', 'publisher', 'description',
        'price', 'cover_image', 'file_path', 'category_id',
    ];

    /**
     * Relasi tunggal ke Kategori
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    /**
     * Relasi jamak ke Kategori
     */
    public function categories(): BelongsToMany
    {
        return $this->belongsToMany(Category::class);
    }

    /**
     * Relasi ke Voucher
     */
    public function vouchers(): BelongsToMany
    {
        return $this->belongsToMany(Voucher::class);
    }

    /**
     * Accessor untuk memformat nama kategori di Blade ($book->formatted_categories)
     */
    public function getFormattedCategoriesAttribute(): string
    {
        if ($this->relationLoaded('categories') && $this->categories->isNotEmpty()) {
            return $this->categories->pluck('name')->implode(', ');
        }

        return $this->category->name ?? 'Umum';
    }

    public function wishlists()
    {
        return $this->hasMany(Wishlist::class);
    }

    public function wishlistedBy()
    {
        return $this->belongsToMany(User::class, 'wishlists');
    }

    public function reviews()
    {
        return $this->hasMany(Review::class);
    }
}