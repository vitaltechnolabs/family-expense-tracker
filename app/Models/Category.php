<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    protected $primaryKey = 'category_id';
    protected $guarded = [];

    protected $casts = [
        'is_default' => 'boolean',
    ];

    public function family()
    {
        return $this->belongsTo(Family::class, 'family_id');
    }

    public function tags()
    {
        return $this->hasMany(Tag::class, 'category_id');
    }
}
