<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SettingController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $family = $user->family;
        $members = $family->users; // Fetch all family members

        return view('settings.index', compact('family', 'members'));
    }

    public function update(Request $request)
    {
        $user = Auth::user();

        if ($user->role !== 'admin') {
            abort(403, 'Unauthorized action.');
        }

        $request->validate([
            'currency' => 'required|string|max:3',
            'language' => 'required|string|in:en,hi,gu',
        ]);

        $family = $user->family;
        $settings = $family->settings;

        if (is_string($settings)) {
            $settings = json_decode($settings, true) ?? [];
        } elseif (!is_array($settings)) {
            $settings = [];
        }
        $settings['currency'] = $request->currency;
        $settings['language'] = $request->language;

        $family->update(['settings' => $settings]);

        return back()->with('success', 'Settings updated successfully.');
    }
}
