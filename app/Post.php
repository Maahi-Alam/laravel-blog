<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * @method static latest()
 */
class Post extends Model
{
    public function user(){
        return $this->belongsTo(User::class);
    }

    public function categories(){
        return $this->belongsToMany(Category::class);
    }

    public function tags(){
        return $this->belongsToMany(Tag::class);
    }
}