<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PostReaction extends Model
{
    use HasFactory;

    protected $table = 'post_reactions';
    public $incrementing = false;
    protected $primaryKey = ['post_id', 'user_id', 'reaction_id'];
    public $timestamps = false;
    protected $keyType = 'string';

    protected function setKeysForSaveQuery($query)
    {
        $query
            ->where('post_id', '=', $this->getAttribute('post_id'))
            ->where('user_id', '=', $this->getAttribute('user_id'))
            ->where('reaction_id', '=', $this->getAttribute('reaction_id'));
        return $query;
    }

    protected $fillable = [
        'post_id', 'user_id', 'reaction_id', 'created_at'
    ];

    public function post()
    {
        return $this->belongsTo(Post::class, 'post_id', 'id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public function reaction()
    {
        return $this->belongsTo(Reaction::class, 'reaction_id', 'id');
    }
}
