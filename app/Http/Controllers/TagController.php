<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Tag;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TagController extends Controller
{
    public function index()
    {
        if (Auth::user()->role !== 'admin') {
            abort(403, 'Unauthorized action.');
        }

        $tags = Tag::where('family_id', Auth::user()->family_id)->orderBy('tag_name')->get();
        return view('tags.index', compact('tags'));
    }

    public function create()
    {
        $user = Auth::user();
        $categories = Category::where('family_id', $user->family_id)->orderBy('category_name')->get();
        return view('tags.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'tag_name' => 'required|string|max:255',
            'category_id' => 'nullable|exists:categories,category_id',
        ]);

        $user = Auth::user();

        Tag::create([
            'family_id' => $user->family_id,
            'tag_name' => $request->tag_name,
            'category_id' => $request->category_id,
            'is_default' => false,
            'created_by_user_id' => $user->user_id,
        ]);

        return redirect()->route('tags.index')->with('success', 'Tag created successfully.');
    }

    public function edit(Tag $tag)
    {
        $this->authorize('update', $tag);
        $user = Auth::user();
        $categories = Category::where('family_id', $user->family_id)->orderBy('category_name')->get();
        return view('tags.edit', compact('tag', 'categories'));
    }

    public function update(Request $request, Tag $tag)
    {
        $this->authorize('update', $tag);

        $request->validate([
            'tag_name' => 'required|string|max:255',
            'category_id' => 'nullable|exists:categories,category_id',
        ]);

        $tag->update([
            'tag_name' => $request->tag_name,
            'category_id' => $request->category_id,
        ]);

        return redirect()->route('tags.index')->with('success', 'Tag updated successfully.');
    }

    public function destroy(Tag $tag)
    {
        $this->authorize('delete', $tag);

        if ($tag->is_default) {
            return back()->with('error', 'Cannot delete default tags.');
        }

        $tag->delete();

        return redirect()->route('tags.index')->with('success', 'Tag deleted successfully.');
    }
}
