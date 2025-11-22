<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Family;
use App\Models\Tag;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class FamilyController extends Controller
{
    public function create()
    {
        return view('auth.register');
    }

    public function store(Request $request)
    {
        $request->validate([
            'family_name' => 'required|string|max:255',
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'account_name' => 'required|string|max:255',
        ]);

        DB::beginTransaction();

        try {
            // Create Family
            $family = Family::create([
                'family_name' => $request->family_name,
                'settings' => json_encode(['currency' => 'INR', 'timezone' => 'Asia/Kolkata']),
            ]);

            // Create Admin User
            $user = User::create([
                'family_id' => $family->family_id,
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'role' => 'admin',
                'account_name' => $request->account_name,
                'is_active' => true,
            ]);

            // Update Family with Admin ID
            $family->update(['admin_user_id' => $user->user_id]);

            // Create Default Categories
            $categories = [
                ['category_name' => 'Family', 'is_default' => true],
                ['category_name' => 'Personal', 'is_default' => true],
            ];

            foreach ($categories as $cat) {
                Category::create([
                    'family_id' => $family->family_id,
                    'category_name' => $cat['category_name'],
                    'is_default' => $cat['is_default'],
                    'created_by_user_id' => $user->user_id,
                ]);
            }

            // Create Default Tags
            $tags = [
                'Groceries',
                'Utilities',
                'Rent',
                'Education',
                'Healthcare',
                'Medicine',
                'Transport',
                'Entertainment',
                'Clothing',
                'House Maintenance',
                'EMI',
                'Insurance',
                'Investments',
                'Dining Out',
                'Subscriptions',
                'Mobile Recharge',
                'Gifts'
            ];

            foreach ($tags as $tagName) {
                Tag::create([
                    'family_id' => $family->family_id,
                    'tag_name' => $tagName,
                    'is_default' => true,
                    'created_by_user_id' => $user->user_id,
                ]);
            }

            DB::commit();

            Auth::login($user);

            return redirect()->route('dashboard');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'Registration failed: ' . $e->getMessage()])->withInput();
        }
    }
    public function showInviteForm()
    {
        return view('family.invite');
    }

    public function inviteMember(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'nullable|string|email|max:255|unique:users',
            'access_pin' => 'required|string|size:4',
            'role' => 'required|in:admin,member',
            'account_name' => 'required|string|max:255',
        ]);

        $admin = Auth::user();

        User::create([
            'family_id' => $admin->family_id,
            'name' => $request->name,
            'email' => $request->email,
            'password' => $request->email ? Hash::make('password') : null, // Password only if email provided
            'access_pin' => $request->access_pin,
            'role' => $request->role,
            'account_name' => $request->account_name,
            'is_active' => true,
        ]);

        return redirect()->route('dashboard')->with('success', 'Member added successfully!');
    }

    public function updateMember(Request $request, User $user)
    {
        $admin = Auth::user();

        if ($admin->family_id !== $user->family_id || $admin->role !== 'admin') {
            abort(403, 'Unauthorized action.');
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'nullable|string|email|max:255|unique:users,email,' . $user->user_id . ',user_id',
            'access_pin' => 'nullable|string|size:4',
            'password' => 'nullable|string|min:8',
            'role' => 'required|in:admin,member',
            'can_view_all_expenses' => 'boolean',
        ]);

        $data = [
            'name' => $request->name,
            'email' => $request->email,
            'role' => $request->role,
            'can_view_all_expenses' => $request->has('can_view_all_expenses'),
        ];

        if ($request->filled('access_pin')) {
            $data['access_pin'] = $request->access_pin;
        }

        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }

        $user->update($data);

        return back()->with('success', 'Member updated successfully.');
    }
}
