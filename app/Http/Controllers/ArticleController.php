<?php

namespace App\Http\Controllers;

use App\Models\Article;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ArticleController extends Controller
{
    /**
     * Display a listing of the articles.
     */
public function index()
{
    $articles = Article::with('author')->get();
    return view('articles.index', compact('articles'));
}


    /**
     * Show the form for creating a new article.
     */
    public function create()
    {
        return view('articles.create');
    }

    /**
     * Store a newly created article in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate(
            [
                'title' => 'required|max:255',
                'content' => 'required',
                'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
                'is_published' => 'nullable',
            ],
            [
                'title.required' => 'Judul artikel wajib diisi.',
                'title.max' => 'Judul artikel maksimal 255 karakter.',
                'content.required' => 'Konten artikel wajib diisi.',
                'image.image' => 'File harus berupa gambar.',
                'image.mimes' => 'Format gambar harus JPEG, PNG, JPG, atau GIF.',
                'image.max' => 'Ukuran gambar maksimal 2MB.',
            ],
        );

        try {
            // Handle image upload
            if ($request->hasFile('image')) {
                $validated['image'] = $request->file('image')->store('articles', 'public');
            }

            // Set author_id if user is authenticated
            if (auth()->check()) {
                $validated['author_id'] = auth()->user()->id;
            }

            $validated['is_published'] = $request->has('is_published') ? 1 : 0;

            Article::create($validated);

            return redirect()->route('articles.index')->with('success', 'Artikel berhasil dibuat.');
        } catch (\Exception $e) {
            return back()
                ->withInput()
                ->with('error', 'Gagal membuat artikel: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified article.
     */
    public function show(Article $article)
    {
        return view('dashboard.articles.show', compact('article'));
    }

    /**
     * Show the form for editing the specified article.
     */
    public function edit(Article $article)
    {
        return view('articles.edit', compact('article'));
    }

    /**
     * Update the specified article in storage.
     */


// ...existing code...
public function update(Request $request, Article $article)
{
    $validated = $request->validate(
        [
            'title' => 'nullable|max:255',
            'content' => 'nullable',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'is_published' => 'nullable',
        ],
        [
            'title.max' => 'Judul artikel maksimal 255 karakter.',
            'image.image' => 'File harus berupa gambar.',
            'image.mimes' => 'Format gambar harus JPEG, PNG, JPG, atau GIF.',
            'image.max' => 'Ukuran gambar maksimal 2MB.',
        ],
    );

    try {
        // Handle image upload
        if ($request->hasFile('image')) {
            // Delete old image if exists
            if ($article->image && Storage::disk('public')->exists($article->image)) {
                Storage::disk('public')->delete($article->image);
            }
            $validated['image'] = $request->file('image')->store('articles', 'public');
        } else {
            // Jika tidak upload gambar baru, jangan update kolom image
            unset($validated['image']);
        }

        $validated['is_published'] = $request->has('is_published') ? 1 : 0;

        $article->update($validated);

        return redirect()->route('dashboard.articles.index')->with('success', 'Artikel berhasil diperbarui.');
    } catch (\Exception $e) {
        return back()
            ->withInput()
            ->with('error', 'Gagal memperbarui artikel: ' . $e-->getMessage());
    }
}


    /**
     * Remove the specified article from storage.
     */
    public function destroy(Article $article)
    {
        $article->delete();

        return redirect()->route('articles.index')->with('success', 'Article deleted successfully');
    }
}
