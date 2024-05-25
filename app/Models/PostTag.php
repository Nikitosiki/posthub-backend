<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PostTag extends Model
{
    use HasFactory;

    protected $table = 'post_tags';
    public $incrementing = false;
    protected $primaryKey = ['tag_id', 'post_id'];
    public $timestamps = false;
    protected $keyType = 'string';

    protected function setKeysForSaveQuery($query)
    {
        $query
            ->where('tag_id', '=', $this->getAttribute('tag_id'))
            ->where('post_id', '=', $this->getAttribute('post_id'));
        return $query;
    }

    protected $fillable = [
        'tag_id', 'post_id', 'created_at'
    ];

    public function tag()
    {
        return $this->belongsTo(Tag::class, 'tag_id', 'id');
    }

    public function post()
    {
        return $this->belongsTo(Post::class, 'post_id', 'id');
    }
}
