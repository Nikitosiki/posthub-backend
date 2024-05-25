<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CommentReaction extends Model
{
    use HasFactory;

    protected $table = 'comment_reactions';
    public $incrementing = false;
    protected $primaryKey = ['comment_id', 'user_id', 'reaction_id'];
    public $timestamps = false;
    protected $keyType = 'string';

    protected function setKeysForSaveQuery($query)
    {
        $query
            ->where('comment_id', '=', $this->getAttribute('comment_id'))
            ->where('user_id', '=', $this->getAttribute('user_id'))
            ->where('reaction_id', '=', $this->getAttribute('reaction_id'));
        return $query;
    }

    protected $fillable = [
        'comment_id', 'user_id', 'reaction_id', 'created_at'
    ];

    public function comment()
    {
        return $this->belongsTo(Comment::class, 'comment_id', 'id');
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
