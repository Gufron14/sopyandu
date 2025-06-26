@extends('layouts.dashboard')

@section('main')
    <section class="main-content">
        <div class="section-header">
            <h1>Tambah Artikel</h1>
        </div>

        <div class="section-body">
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-body">
@if(session('error'))
    <div class="alert alert-danger">
        {{ session('error') }}
    </div>
@endif

<form method="POST" action="{{ route('articles.store') }}" enctype="multipart/form-data">
    @csrf
    
    <div class="form-group">
        <label for="title">Judul</label>
        <input type="text" name="title" id="title" 
               class="form-control @error('title') is-invalid @enderror" 
               value="{{ old('title') }}" required>
        @error('title')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="form-group">
        <label for="content">Konten</label>
        <textarea name="content" id="content" 
                  class="form-control @error('content') is-invalid @enderror" 
                  rows="10" required>{{ old('content') }}</textarea>
        @error('content')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="form-group">
        <label for="image">Gambar</label>
        <input type="file" name="image" id="image"
               class="form-control @error('image') is-invalid @enderror" 
               accept="image/*">
        @error('image')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
        <small class="form-text text-muted">Format yang didukung: JPEG, PNG, JPG, GIF. Maksimal 2MB.</small>
    </div>

    <div class="form-group">
        <div class="form-check">
            <input type="checkbox" name="is_published" id="is_published" 
                   class="form-check-input" value="1" {{ old('is_published') ? 'checked' : '' }}>
            <label class="form-check-label" for="is_published">
                Publikasikan artikel
            </label>
        </div>
    </div>

    <button type="submit" class="btn btn-primary">Simpan</button>
</form>

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection

@push('scripts')
    <script src="https://cdn.ckeditor.com/4.16.2/standard/ckeditor.js"></script>
    <script>
        CKEDITOR.replace('content');
    </script>
@endpush
