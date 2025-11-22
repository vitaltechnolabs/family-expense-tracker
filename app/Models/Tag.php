<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Tag extends Model
{
    protected $primaryKey = 'tag_id';
    protected $guarded = [];

    protected $casts = [
        'is_default' => 'boolean',
    ];

    public function family()
    {
        return $this->belongsTo(Family::class, 'family_id');
    }

    public function category()
    {
        return $this->belongsTo(Category::class, 'category_id');
    }
}
