<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CountViewsAuth extends Model
{
    use HasFactory;

    protected $table = 'count_views_auth';
    public $incrementing = false;
    protected $primaryKey = ['post_id', 'user_id'];
    public $timestamps = false;
    protected $keyType = 'string';

    protected function setKeysForSaveQuery($query)
    {
        $query
            ->where('post_id', '=', $this->getAttribute('post_id'))
            ->where('user_id', '=', $this->getAttribute('user_id'));
        return $query;
    }

    protected $fillable = [
        'post_id', 'user_id', 'created_at'
    ];

    public function post()
    {
        return $this->belongsTo(Post::class, 'post_id', 'id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }
}
