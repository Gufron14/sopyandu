<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\Article;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ArticleController extends Controller
{
    public function index()
    {
        $articles = Article::with('author')->latest()->paginate(10);
        return view('dashboard.articles.index', compact('articles'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('dashboard.articles.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|max:255',
            'content' => 'required',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'is_published' => 'boolean'
        ]);

        $data = $request->all();
        $data['author_id'] = Auth::id();
        $data['slug'] = Str::slug($request->title);
        $data['excerpt'] = Str::limit(strip_tags($request->content), 200);

        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('articles', 'public');
            $data['image'] = $path;
        }

        Article::create($data);

        return redirect()->route('article.index')
            ->with('success', 'Artikel berhasil ditambahkan');
    }

    public function show(Article $article)
    {
        return view('dashboard.articles.show', compact('article'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $article = Article::findOrFail($id);
        return view('dashboard.articles.edit', compact('article'));
    }

    /**
     * Update the specified resource in storage.
     */
public function update(Request $request, string $id)
{
    $article = Article::findOrFail($id);

    $validated = $request->validate([
        'title' => 'nullable|max:255',
        'content' => 'nullable',
        'category' => 'nullable|string|max:255',
        'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        'is_published' => 'boolean'
    ], [
        // Pesan error kustom
        'title.required' => 'Judul artikel wajib diisi.',
        'title.max' => 'Judul maksimal 255 karakter.',
        'content.required' => 'Konten artikel wajib diisi.',
        'category.required' => 'Kategori artikel wajib diisi.',
        'image.image' => 'File harus berupa gambar.',
        'image.mimes' => 'Gambar harus berformat jpeg, png, jpg, atau gif.',
        'image.max' => 'Ukuran gambar maksimal 2MB.',
    ]);

    // Update slug & excerpt berdasarkan title & content baru
    $validated['slug'] = Str::slug($request->title);
    $validated['excerpt'] = Str::limit(strip_tags($request->content), 200);

    // Update gambar jika ada file baru
    if ($request->hasFile('image')) {
        // Hapus gambar lama jika ada
        if ($article->image && \Storage::disk('public')->exists($article->image)) {
            \Storage::disk('public')->delete($article->image);
        }

        $path = $request->file('image')->store('articles', 'public');
        $validated['image'] = $path;
    }

    // Update author_id jika diinginkan
    $validated['author_id'] = Auth::id();

    $article->update($validated);

    return redirect()->route('article.index')
        ->with('success', 'Artikel berhasil diperbarui.');
}


    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $article = Article::findOrFail($id);
        $article->delete();

        return redirect()->route('article.index')
            ->with('success', 'Article deleted successfully');
    }
}
