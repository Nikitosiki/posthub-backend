<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Comment extends Model
{
    use HasFactory;

    protected $table = 'comments';
    protected $primaryKey = 'id';
    public $incrementing = true;
    protected $keyType = 'int';
    public $timestamps = true;

    protected $fillable = [
        'content', 'author_id', 'post_id', 'parent_comment_id', 'path', 'created_at', 'updated_at'
    ];

    public function post()
    {
        return $this->belongsTo(Post::class, 'post_id', 'id');
    }

    public function author()
    {
        return $this->belongsTo(User::class, 'author_id', 'id');
    }

    public function parent()
    {
        return $this->belongsTo(Comment::class, 'parent_comment_id', 'id');
    }

    public function children()
    {
        return $this->hasMany(Comment::class, 'parent_comment_id', 'id');
    }

    public function reactions()
    {
        return $this->hasMany(CommentReaction::class, 'comment_id', 'id');
    }
}
