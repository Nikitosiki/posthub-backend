<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
    use HasFactory;

    protected $primaryKey = 'id';

    protected $fillable = [
        'created_at',
        'updated_at',
        'author_id',
        'title',
        'content',
        'count_view',
        'image_path',
        'age_rating_id'
    ];
}
