
    <section class="section">
        <div class="section-header">
            <h1>{{ isset($article) ? 'Edit Artikel' : 'Tambah Artikel' }}</h1>
        </div>

        <div class="section-body">
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-body">
                            <form
                                action="{{ isset($article) ? route('articles.update', $article) : route('articles.store') }}"
                                method="POST" enctype="multipart/form-data">
                                @csrf
                                @if (isset($article))
                                    @method('PUT')
                                @endif

                                <div class="form-group">
                                    <label>Judul Artikel</label>
                                    <input type="text" name="title"
                                        class="form-control @error('title') is-invalid @enderror"
                                        value="{{ old('title', $article->title ?? '') }}" required>
                                    @error('title')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="form-group">
                                    <label>Gambar</label>
                                    <input type="file" name="image"
                                        class="form-control @error('image') is-invalid @enderror" accept="image/*">
                                    @error('image')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    @if (isset($article) && $article->image)
                                        <div class="mt-2">
                                            <img src="{{ asset('storage/' . $article->image) }}" alt="Current Image"
                                                class="img-thumbnail" style="max-height: 200px">
                                        </div>
                                    @endif
                                </div>

                                <div class="form-group">
                                    <label>Konten</label>
                                    <textarea name="content" class="form-control @error('content') is-invalid @enderror" rows="10" required>{{ old('content', $article->content ?? '') }}</textarea>
                                    @error('content')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="form-group">
                                    <div class="custom-control custom-switch">
                                        <input type="checkbox" class="custom-control-input" id="is_published"
                                            name="is_published" value="1"
                                            {{ old('is_published', $article->is_published ?? true) ? 'checked' : '' }}>
                                        <label class="custom-control-label" for="is_published">Publikasikan</label>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <button type="submit" class="btn btn-primary">
                                        {{ isset($article) ? 'Update' : 'Simpan' }}
                                    </button>
                                    <a href="{{ route('articles.index') }}" class="btn btn-secondary">Batal</a>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>



@push('scripts')
    <script src="https://cdn.ckeditor.com/4.16.2/standard/ckeditor.js"></script>
    <script>
        CKEDITOR.replace('content');
    </script>
@endpush
