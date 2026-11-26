<?php

namespace App\Http\Controllers;

use App\Models\KnowledgeArticles;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Inertia\Inertia;

class KnowledgeArticleController extends Controller
{
    public function create()
    {
        return Inertia::render('knowledge-mgm/create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'content' => ['required', 'string'],
            'source_url' => ['nullable', 'url', 'max:2048'],
            'category' => ['nullable', 'string', 'max:255'],
            'author' => ['nullable', 'string', 'max:255'],
            'is_published' => ['boolean'],
        ]);

        $validated['slug'] = Str::slug($validated['title']);
        $validated['author'] = $validated['author']
            ?? $request->user()->name;

        $validated['is_published'] =
            $validated['is_published'] ?? false;

        KnowledgeArticles::create($validated);

        return redirect()
            ->route('knowledge.index')
            ->with('success', 'Artikel berhasil disimpan.');
    }
}
