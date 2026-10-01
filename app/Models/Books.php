<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Books extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'books';

    protected $fillable = [
        'category_id',
        'isbn',
        'title',
        'author',
        'publisher',
        'publication_year',
        'stock',
        'cover_image',
        'pdf_file',
        'synopsis',
    ];

    public function category()
    {
        return $this->belongsTo(Categories::class, 'category_id');
    }
}
