<?php

namespace App\Http\Controllers;

use App\Models\Article;
use App\Models\SiteIdentity;
use Illuminate\Http\Request;

class LandingPageController extends Controller
{
    public function index()
    {
        $siteIdentity = SiteIdentity::first();
        // We'll add article fetching later
        // Ambil 3 artikel terakhir yang sudah dipublish
        $articles = Article::where('is_published', true)
                          ->with('author')
                          ->latest()
                          ->take(3)
                          ->get();
        
        return view('landing-page.index', compact('siteIdentity', 'articles'));
    }

    public function showArticle($slug)
    {
        $article = Article::where('slug', $slug)->firstOrFail();
        return view('show-article', compact('article'));
    }
}
