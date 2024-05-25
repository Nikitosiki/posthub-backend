<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
    use HasFactory;

    protected $table = 'posts';
    protected $primaryKey = 'id';
    public $incrementing = false;
    protected $keyType = 'uuid';
    public $timestamps = true;

    protected $fillable = [
        'author_id', 'title', 'content', 'count_view', 'image_path', 'age_rating_id', 'created_at', 'updated_at'
    ];

    public function author()
    {
        return $this->belongsTo(User::class, 'author_id', 'id');
    }

    public function ageRating()
    {
        return $this->belongsTo(AgeRating::class, 'age_rating_id', 'id');
    }

    public function comments()
    {
        return $this->hasMany(Comment::class, 'post_id', 'id');
    }

    public function reactions()
    {
        return $this->hasMany(PostReaction::class, 'post_id', 'id');
    }

    public function tags()
    {
        return $this->belongsToMany(Tag::class, 'post_tags', 'post_id', 'tag_id');
    }
}
