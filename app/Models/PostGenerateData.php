<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PostGenerateData extends Model
{
    protected $guarded = [];

    protected $casts = [
        'post_path' => 'json',
        'custom_img_path' => 'json',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }
}
