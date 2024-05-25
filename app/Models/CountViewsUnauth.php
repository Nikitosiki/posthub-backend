<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CountViewsUnauth extends Model
{
    use HasFactory;

    protected $table = 'count_views_unauth';
    public $incrementing = false;
    protected $primaryKey = ['post_id', 'fingerprint_id'];
    public $timestamps = false;
    protected $keyType = 'string';

    protected function setKeysForSaveQuery($query)
    {
        $query
            ->where('post_id', '=', $this->getAttribute('post_id'))
            ->where('fingerprint_id', '=', $this->getAttribute('fingerprint_id'));
        return $query;
    }

    protected $fillable = [
        'post_id', 'fingerprint_id', 'created_at'
    ];

    public function post()
    {
        return $this->belongsTo(Post::class, 'post_id', 'id');
    }
}
