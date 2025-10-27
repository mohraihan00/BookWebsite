<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Book extends Model
{
    protected $fillable = [
        'title',
        'author',
        'isbn',
        'category_id',
        'description',
        'publisher',
        'publication_year',
        'stock',
        'price',
        'cover_image',
    ];

    protected $casts = [
        'publication_year' => 'integer',
        'stock' => 'integer',
        'price' => 'decimal:2',
    ];

    /**
     * Get the category that owns the book.
     */
    public function category()
    {
        return $this->belongsTo(Category::class);
    }
}
