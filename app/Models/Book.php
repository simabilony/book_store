<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Book extends Model
{
    use HasFactory;
    protected $fillable =[
        'title',
        'slug',
        'author_id',
        'description',
        'price',
        'stock',
    ];

    protected $casts = [
        'author_id' => 'integer',
        'price' => 'decimal:2',
        'stock' => 'integer',
    ];

    protected static function boot()
    {
        parent::boot();

        // To Auto Generate Slug from Title
        static::creating(function ($book) {
            $book->slug = Str::slug($book->title);
        });

        static::updating(function ($book) {
            $book->slug = Str::slug($book->title);
            // Laravel is the best PHP Preamwork
            // Laravel-is-the-best-PHP-Preamwork
            //
        });
    }
}
