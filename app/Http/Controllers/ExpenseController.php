<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Expense;
use App\Models\Tag;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ExpenseController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        $query = Expense::with(['category', 'tag', 'user', 'forMember']);

        $query->where('family_id', $user->family_id);

        // Visibility Logic
        $canViewAll = $user->role === 'admin' || $user->can_view_all_expenses;
        $members = [];

        if ($canViewAll) {
            $members = User::where('family_id', $user->family_id)->get();
            if ($request->filled('member_id')) {
                $query->where('logged_by_user_id', $request->member_id);
            }
        } else {
            $query->where('logged_by_user_id', $user->user_id);
        }

        // Other Filters
        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        if ($request->filled('start_date')) {
            $query->whereDate('date', '>=', $request->start_date);
        }

        if ($request->filled('end_date')) {
            $query->whereDate('date', '<=', $request->end_date);
        }

        $expenses = $query->with(['category', 'tag', 'user'])
            ->orderBy('date', 'desc')
            ->orderBy('created_at', 'desc')
            ->paginate(10);
        // dd($expenses->toArray());
        $categories = Category::where('family_id', $user->family_id)->orderBy('category_name')->get();
        $tags = Tag::where('family_id', $user->family_id)->orderBy('tag_name')->get();

        return view('expenses.index', compact('expenses', 'categories', 'tags', 'members', 'canViewAll'));
    }

    public function create()
    {
        $user = Auth::user();
        $categories = Category::where('family_id', $user->family_id)->orderBy('category_name')->get();
        $tags = Tag::where('family_id', $user->family_id)->with('category')->orderBy('tag_name')->get();
        $members = User::where('family_id', $user->family_id)->get();

        return view('expenses.create', compact('categories', 'tags', 'members'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'date' => 'required|date',
            'amount' => 'required|numeric|min:0',
            'category_id' => 'required|exists:categories,category_id',
            'tag_id' => 'nullable|exists:tags,tag_id',
            'for_member_id' => 'nullable|exists:users,user_id',
            'payment_method' => 'required|in:Cash,Cheque,UPI,Net Banking',
            'remarks' => 'nullable|string',
        ]);

        $user = Auth::user();

        Expense::create([
            'family_id' => $user->family_id,
            'logged_by_user_id' => $user->user_id,
            'date' => $request->date,
            'amount' => $request->amount,
            'category_id' => $request->category_id,
            'tag_id' => $request->tag_id,
            'for_member_id' => $request->for_member_id ?? $user->user_id,
            'payment_method' => $request->payment_method,
            'from_account_user_id' => $user->user_id, // Simplification: always from logged user's account for now
            'remarks' => $request->remarks,
        ]);

        return redirect()->route('expenses.index')->with('success', 'Expense added successfully.');
    }

    public function show(Expense $expense)
    {
        $this->authorize('view', $expense);
        return view('expenses.show', compact('expense'));
    }

    public function edit(Expense $expense)
    {
        $this->authorize('update', $expense);
        $user = Auth::user();
        $categories = Category::where('family_id', $user->family_id)->orderBy('category_name')->get();
        $tags = Tag::where('family_id', $user->family_id)->with('category')->orderBy('tag_name')->get();
        $members = User::where('family_id', $user->family_id)->get();

        return view('expenses.edit', compact('expense', 'categories', 'tags', 'members'));
    }

    public function update(Request $request, Expense $expense)
    {
        $this->authorize('update', $expense);

        $request->validate([
            'date' => 'required|date',
            'amount' => 'required|numeric|min:0',
            'category_id' => 'required|exists:categories,category_id',
            'tag_id' => 'nullable|exists:tags,tag_id',
            'for_member_id' => 'nullable|exists:users,user_id',
            'payment_method' => 'required|in:Cash,Cheque,UPI,Net Banking',
            'remarks' => 'nullable|string',
        ]);

        $expense->update([
            'date' => $request->date,
            'amount' => $request->amount,
            'category_id' => $request->category_id,
            'tag_id' => $request->tag_id,
            'for_member_id' => $request->for_member_id,
            'payment_method' => $request->payment_method,
            'remarks' => $request->remarks,
        ]);

        return redirect()->route('expenses.index')->with('success', 'Expense updated successfully.');
    }

    public function destroy(Expense $expense)
    {
        $this->authorize('delete', $expense);
        $expense->delete();
        return redirect()->route('expenses.index')->with('success', 'Expense deleted successfully.');
    }
}
