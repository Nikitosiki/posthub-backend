<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Reaction extends Model
{
    use HasFactory;

    protected $table = 'reactions';
    protected $primaryKey = 'id';
    public $incrementing = true;
    protected $keyType = 'int';
    public $timestamps = false;

    protected $fillable = [
        'emoji', 'grade'
    ];

    public function commentReactions()
    {
        return $this->hasMany(CommentReaction::class, 'reaction_id', 'id');
    }

    public function postReactions()
    {
        return $this->hasMany(PostReaction::class, 'reaction_id', 'id');
    }
}
