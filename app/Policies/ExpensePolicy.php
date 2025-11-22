<?php

namespace App\Policies;

use App\Models\Expense;
use App\Models\User;

class ExpensePolicy
{
    public function view(User $user, Expense $expense): bool
    {
        return $user->family_id === $expense->family_id &&
            ($user->role === 'admin' || $user->user_id === $expense->logged_by_user_id);
    }

    public function update(User $user, Expense $expense): bool
    {
        return $user->family_id === $expense->family_id &&
            ($user->role === 'admin' || $user->user_id === $expense->logged_by_user_id);
    }

    public function delete(User $user, Expense $expense): bool
    {
        return $user->family_id === $expense->family_id &&
            ($user->role === 'admin' || $user->user_id === $expense->logged_by_user_id);
    }
}
