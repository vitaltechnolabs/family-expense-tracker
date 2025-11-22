<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\FamilyController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('login');
});

Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'login'])->name('login');
    Route::post('/login', [AuthController::class, 'authenticate']);
    Route::get('/register', [FamilyController::class, 'create'])->name('register');
    Route::post('/register', [FamilyController::class, 'store']);
});

Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
    Route::get('/dashboard', function () {
        $user = \Illuminate\Support\Facades\Auth::user();
        $familyId = $user->family_id;

        $canViewAll = $user->role === 'admin' || $user->can_view_all_expenses;

        $expenseQuery = \App\Models\Expense::where('family_id', $familyId);
        if (!$canViewAll) {
            $expenseQuery->where('logged_by_user_id', $user->user_id);
        }

        $currentMonthTotal = (clone $expenseQuery)
            ->whereMonth('date', now()->month)
            ->whereYear('date', now()->year)
            ->sum('amount');

        $todayTotal = (clone $expenseQuery)
            ->where('date', now()->toDateString())
            ->sum('amount');

        $recentExpenses = (clone $expenseQuery)
            ->with(['category', 'user'])
            ->latest('date')
            ->take(5)
            ->get();

        return view('dashboard', compact('currentMonthTotal', 'todayTotal', 'recentExpenses'));
    })->name('dashboard');

    Route::get('/family/invite', [FamilyController::class, 'showInviteForm'])->name('family.invite');
    Route::post('/family/invite', [FamilyController::class, 'inviteMember'])->name('family.invite.store');

    Route::resource('categories', \App\Http\Controllers\CategoryController::class);
    Route::resource('tags', \App\Http\Controllers\TagController::class);
    Route::resource('expenses', \App\Http\Controllers\ExpenseController::class);
    Route::get('/reports', [\App\Http\Controllers\ReportController::class, 'index'])->name('reports.index');

    Route::get('/profile', [\App\Http\Controllers\ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [\App\Http\Controllers\ProfileController::class, 'update'])->name('profile.update');
    Route::put('/password', [\App\Http\Controllers\ProfileController::class, 'updatePassword'])->name('password.update');

    Route::get('/settings', [\App\Http\Controllers\SettingController::class, 'index'])->name('settings.index');
    Route::patch('/settings', [\App\Http\Controllers\SettingController::class, 'update'])->name('settings.update');
});

Route::get('/family/{slug}', [\App\Http\Controllers\FamilyPortalController::class, 'show'])->name('family.portal');
Route::post('/family/{slug}/login', [\App\Http\Controllers\FamilyPortalController::class, 'login'])->name('family.login');

Route::middleware(['auth'])->group(function () {
    Route::put('/family/member/{user}', [\App\Http\Controllers\FamilyController::class, 'updateMember'])->name('family.member.update');
});
