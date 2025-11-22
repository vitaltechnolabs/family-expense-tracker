<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Family extends Model
{
    protected $primaryKey = 'family_id';
    protected $guarded = [];

    protected static function boot()
    {
        parent::boot();
        static::creating(function ($family) {
            $family->slug = \Illuminate\Support\Str::slug($family->family_name . '-' . \Illuminate\Support\Str::random(4));
        });
    }

    protected $casts = [
        'settings' => 'array',
    ];

    public function users()
    {
        return $this->hasMany(User::class, 'family_id');
    }

    public function admin()
    {
        return $this->belongsTo(User::class, 'admin_user_id');
    }

    public function categories()
    {
        return $this->hasMany(Category::class, 'family_id');
    }

    public function tags()
    {
        return $this->hasMany(Tag::class, 'family_id');
    }

    public function expenses()
    {
        return $this->hasMany(Expense::class, 'family_id');
    }
}
