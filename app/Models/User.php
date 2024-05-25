<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $table = 'users';
    protected $primaryKey = 'id';
    public $incrementing = false;
    protected $keyType = 'uuid';
    public $timestamps = true;

    protected $fillable = [
        'uid', 'email', 'name', 'avatar_url', 'gender_id', 'created_at'
    ];

    public function gender()
    {
        return $this->belongsTo(Gender::class, 'gender_id', 'id');
    }

    public function posts()
    {
        return $this->hasMany(Post::class, 'author_id', 'id');
    }

    public function comments()
    {
        return $this->hasMany(Comment::class, 'author_id', 'id');
    }

    public function postReactions()
    {
        return $this->hasMany(PostReaction::class, 'user_id', 'id');
    }

    public function commentReactions()
    {
        return $this->hasMany(CommentReaction::class, 'user_id', 'id');
    }

    public function tags()
    {
        return $this->hasMany(Tag::class, 'author_id', 'id');
    }

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    // protected function casts(): array
    // {
    //     return [
    //         'email_verified_at' => 'datetime',
    //         'password' => 'hashed',
    //     ];
    // }
}
