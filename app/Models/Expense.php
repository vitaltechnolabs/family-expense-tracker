<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Expense extends Model
{
    use SoftDeletes;

    protected $primaryKey = 'expense_id';
    protected $guarded = [];

    protected $casts = [
        'date' => 'date',
        'amount' => 'decimal:2',
    ];

    public function family()
    {
        return $this->belongsTo(Family::class, 'family_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'logged_by_user_id');
    }

    public function category()
    {
        return $this->belongsTo(Category::class, 'category_id');
    }

    public function tag()
    {
        return $this->belongsTo(Tag::class, 'tag_id');
    }

    public function forMember()
    {
        return $this->belongsTo(User::class, 'for_member_id');
    }

    public function fromAccount()
    {
        return $this->belongsTo(User::class, 'from_account_user_id');
    }
}
