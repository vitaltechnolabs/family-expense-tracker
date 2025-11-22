<?php

namespace App\Http\Controllers;

use App\Models\Family;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class FamilyPortalController extends Controller
{
    public function show($slug)
    {
        $family = Family::where('slug', $slug)->with('users')->firstOrFail();
        return view('family.portal', compact('family'));
    }

    public function login(Request $request, $slug)
    {
        $request->validate([
            'user_id' => 'required|exists:users,user_id',
            'access_pin' => 'required|string',
        ]);

        $user = User::findOrFail($request->user_id);

        if ($user->access_pin !== $request->access_pin) {
            return back()->withErrors(['access_pin' => 'Invalid PIN.']);
        }

        Auth::login($user);

        return redirect()->route('dashboard');
    }
}
