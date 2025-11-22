<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Expense;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ReportController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();

        // Date Range Filter
        $startDate = $request->input('start_date', now()->startOfMonth()->format('Y-m-d'));
        $endDate = $request->input('end_date', now()->endOfMonth()->format('Y-m-d'));

        $baseQuery = Expense::where('family_id', $user->family_id)
            ->whereBetween('date', [$startDate, $endDate]);

        // Visibility Logic
        $canViewAll = $user->role === 'admin' || $user->can_view_all_expenses;
        $members = [];

        if ($canViewAll) {
            $members = User::where('family_id', $user->family_id)->get();
            if ($request->filled('member_id')) {
                $baseQuery->where('logged_by_user_id', $request->member_id);
            }
        } else {
            $baseQuery->where('logged_by_user_id', $user->user_id);
        }

        // 1. Category-wise Breakdown
        $categoryData = (clone $baseQuery)
            ->select('category_id', DB::raw('SUM(amount) as total'))
            ->groupBy('category_id')
            ->with('category')
            ->get()
            ->map(function ($item) {
                return [
                    'label' => $item->category->category_name,
                    'value' => $item->total,
                    'color' => $this->getRandomColor(),
                ];
            });

        // 2. Member-wise Comparison (Admin Only)
        $memberData = [];
        if ($user->role === 'admin') {
            $memberData = (clone $baseQuery)
                ->select('logged_by_user_id', DB::raw('SUM(amount) as total'))
                ->groupBy('logged_by_user_id')
                ->with('user')
                ->get()
                ->map(function ($item) {
                    return [
                        'label' => $item->user->name,
                        'value' => $item->total,
                    ];
                });
        }

        // 3. Monthly Trend (Last 6 Months)
        $trendData = Expense::where('family_id', $user->family_id)
            ->where('date', '>=', now()->subMonths(6)->startOfMonth())
            ->orderBy('date')
            ->get()
            ->groupBy(function ($expense) {
                return $expense->date->format('Y-m');
            })
            ->map(function ($expenses, $month) {
                return (object) [
                    'month' => $month,
                    'total' => $expenses->sum('amount'),
                ];
            })
            ->values();

        return view('reports.index', compact('categoryData', 'memberData', 'trendData', 'startDate', 'endDate', 'members', 'canViewAll'));
    }

    private function getRandomColor()
    {
        return '#' . str_pad(dechex(mt_rand(0, 0xFFFFFF)), 6, '0', STR_PAD_LEFT);
    }
}
