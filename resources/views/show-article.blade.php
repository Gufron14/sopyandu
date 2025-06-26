@extends('landing-page.layout')

@section('title', $article->title)

@section('content')
<div class="container p-5">
    <div class="row justify-content-center p-5">
        <div class="col-lg-8">
            <!-- Article Header -->
            <div class="mb-4">
                <h1 class="display-4 fw-bold text-dark mb-3">{{ $article->title }}</h1>
                
                <!-- Article Meta -->
                <div class="d-flex align-items-center text-muted mb-4">
                    <div class="d-flex align-items-center me-4">
                        <i class="fas fa-user me-2"></i>
                        <span>{{ $article->author->name ?? 'Admin' }}</span>
                    </div>
                    <div class="d-flex align-items-center me-4">
                        <i class="fas fa-calendar me-2"></i>
                        <span>{{ $article->created_at->locale('id')->isoFormat('D MMMM YYYY') }}</span>
                    </div>
                    @if($article->is_published)
                        <span class="badge bg-success">
                            <i class="fas fa-check me-1"></i>
                            Published
                        </span>
                    @else
                        <span class="badge bg-warning">
                            <i class="fas fa-clock me-1"></i>
                            Draft
                        </span>
                    @endif
                </div>

                <!-- Article Image -->
                @if($article->image)
                    <div class="mb-4">
                        <img src="{{ asset('storage/' . $article->image) }}" 
                             alt="{{ $article->title }}" 
                             class="img-fluid rounded shadow-sm w-100"
                             style="max-height: 400px; object-fit: cover;">
                    </div>
                @endif

                <!-- Article Excerpt -->
                @if($article->excerpt)
                    <div class="alert alert-light border-start border-primary border-4 mb-4">
                        <p class="lead mb-0 text-muted fst-italic">{{ $article->excerpt }}</p>
                    </div>
                @endif
            </div>

            <!-- Article Content -->
            <div class="article-content">
                <div class="card border-0 shadow-sm">
                    <div class="card-body p-4">
                        {!! nl2br(e($article->content)) !!}
                    </div>
                </div>
            </div>

            <!-- Article Footer -->
            <div class="mt-5 pt-4 border-top">
                <div class="row align-items-center">
                    <div class="col-md-6">
                        <div class="d-flex align-items-center">
                            <i class="fas fa-user-circle fa-2x text-primary me-3"></i>
                            <div>
                                <h6 class="mb-0">{{ $article->author->name ?? 'Admin' }}</h6>
                                <small class="text-muted">Author</small>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6 text-md-end mt-3 mt-md-0">
                        <small class="text-muted">
                            <i class="fas fa-clock me-1"></i>
                            Dipublikasikan {{ $article->created_at->locale('id')->diffForHumans() }}
                        </small>
                    </div>
                </div>
            </div>

            <!-- Navigation Buttons -->
            <div class="mt-4 d-flex justify-content-between">
                <a href="{{ url()->previous() }}" class="btn btn-outline-secondary">
                    <i class="fas fa-arrow-left me-2"></i>
                    Kembali
                </a>
                
                <div class="btn-group">
                    <button type="button" class="btn btn-outline-primary" onclick="shareArticle()">
                        <i class="fas fa-share-alt me-2"></i>
                        Bagikan
                    </button>
                    <button type="button" class="btn btn-outline-info" onclick="printArticle()">
                        <i class="fas fa-print me-2"></i>
                        Cetak
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Custom Styles -->
<style>
    .article-content {
        font-size: 1.1rem;
        line-height: 1.8;
        color: #333;
    }
    
    .article-content p {
        margin-bottom: 1.5rem;
    }
    
    .display-4 {
        line-height: 1.2;
    }
    
    @media print {
        .btn, .btn-group {
            display: none !important;
        }
    }
</style>

<!-- JavaScript -->
<script>
    function shareArticle() {
        if (navigator.share) {
            navigator.share({
                title: '{{ $article->title }}',
                text: '{{ $article->excerpt }}',
                url: window.location.href
            });
        } else {
            // Fallback: copy to clipboard
            navigator.clipboard.writeText(window.location.href).then(function() {
                alert('Link artikel telah disalin ke clipboard!');
            });
        }
    }
    
    function printArticle() {
        window.print();
    }
</script>
@endsection
