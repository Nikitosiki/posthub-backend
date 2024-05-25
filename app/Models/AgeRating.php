<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AgeRating extends Model
{
    use HasFactory;

    protected $table = 'age_ratings';
    protected $primaryKey = 'id';
    public $incrementing = true;
    protected $keyType = 'int';
    public $timestamps = false;

    protected $fillable = [
        'age', 'name', 'description'
    ];

    public function posts()
    {
        return $this->hasMany(Post::class, 'age_rating_id', 'id');
    }
}
