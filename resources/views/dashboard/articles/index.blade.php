@extends('layouts.dashboard')

@section('title', 'Artikel')

@push('styles')
    <link rel="stylesheet" href="{{ asset('modules/datatables/dataTables.min.css') }}">
    <style>
        .table {
            white-space: nowrap !important;
        }
    </style>
@endpush

@section('main')

    <section class="main-content">
        <div class="section-body">
            <h2 class="section-title">Daftar Artikel</h2>
            <p class="section-lead">
                Kelola semua artikel di sini.
            </p>

            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h5 class="card-title">
                                Daftar Artikel
                            </h5>
                            <a href="{{ route('article.create') }}" class="btn btn-primary">
                                <i class="fas fa-plus"></i> Tambah Artikel
                            </a>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-striped">
                                    <thead>
                                        <tr>
                                            <th>No</th>
                                            <th>Judul</th>
                                            <th>Status</th>
                                            <th>Penulis</th>
                                            <th>Tanggal</th>
                                            <th>Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($articles as $article)
                                            <tr>
                                                <td>{{ $loop->iteration }}</td>
                                                <td>{{ $article->title }}</td>
                                                <td>
                                                    @if ($article->is_published)
                                                        <span class="badge badge-success">Published</span>
                                                    @else
                                                        <span class="badge badge-warning">Draft</span>
                                                    @endif
                                                </td>
<td>
    @if($article->author)
        @if($article->author->officer_id)
            {{ $article->author->officers->fullname ?? $article->author->username }}
        @elseif($article->author->parent_id)
            {{ $article->author->familyParents->mother_fullname ?? $article->author->username }}
        @else
            {{ $article->author->username }}
        @endif
    @else
        N/A
    @endif
</td>
                                                <td>{{ $article->created_at->format('d M Y') }}</td>
                                                <td>
                                                    <a href="{{ route('article.edit', $article) }}"
                                                        class="btn btn-warning btn-sm">
                                                        <i class="fas fa-edit"></i>
                                                    </a>
                                                    {{-- Lihat Artikel --}}
                                                    <a href="{{ route('article.show', $article->slug) }}"
                                                        class="btn btn-info btn-sm">
                                                        <i class="fas fa-eye"></i>
                                                    </a>

                                                    <form action="{{ route('article.destroy', $article) }}" method="POST"
                                                        class="d-inline">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-danger btn-sm"
                                                            onclick="return confirm('Apakah Anda yakin ingin menghapus artikel ini?')">
                                                            <i class="fas fa-trash"></i>
                                                        </button>
                                                    </form>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="6" class="text-center">Tidak ada artikel</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                            @if ($articles->hasPages())
                                <div class="mt-4">
                                    {{ $articles->links() }}
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
