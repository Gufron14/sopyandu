@extends('layouts.dashboard')

@section('title', 'Tambah Jadwal')

@push('styles')
    <link rel="stylesheet" href="{{ asset('modules/summernote/summernote-bs4.css') }}">
    <style>
        .note-group-select-from-files {
            display: none !important;
        }
    </style>
@endpush

@section('main')
    <div class="main-content">
        <section class="section">
            <div class="section-header">
                <h1>@yield('title')</h1>
                <div class="section-header-breadcrumb">
                    <div class="breadcrumb-item active"><a href="{{ url('/dashboard') }}">Dashboard</a></div>
                    <div class="breadcrumb-item">@yield('title')</div>
                </div>
            </div>
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-body">
                            <form action="{{ url('/schedule') }}" method="POST">
                                @csrf
                                <div class="row g-4">
                                    <div class="form-group col-md-6">
                                        <label for="title">Nama Kegiatan <span class="text-danger">*</span></label>
                                        <input id="title" type="text"
                                            class="form-control @error('title') is-invalid @enderror" name="title"
                                            value="{{ old('title') }}" placeholder="Imunisasi Anak">
                                        @error('title')
                                            <div class="invalid-feedback">
                                                {{ $message }}
                                            </div>
                                        @enderror
                                    </div>

                                    <div class="form-group col-md-6">
                                        <label for="event_location">Lokasi <span class="text-danger">*</span></label>
                                        <input id="event_location" type="text"
                                            class="form-control @error('event_location') is-invalid @enderror"
                                            name="event_location" value="{{ old('event_location') }}"
                                            placeholder="Aula kantor lurah atau lokasi yang relevan">
                                        @error('event_location')
                                            <div class="invalid-feedback">
                                                {{ $message }}
                                            </div>
                                        @enderror
                                    </div>

                                    <div class="form-group col-md-4">
                                        <label for="event_date">Tanggal Posyandu <span class="text-danger">*</span></label>
                                        <input id="event_date" type="date"
                                            class="form-control @error('event_date') is-invalid @enderror" name="event_date"
                                            value="{{ old('event_date') }}">
                                        @error('event_date')
                                            <div class="invalid-feedback">
                                                {{ $message }}
                                            </div>
                                        @enderror
                                    </div>

                                    <div class="form-group col-md-4">
                                        <label for="start_time">Waktu Mulai <span class="text-danger">*</span></label>
                                        <input id="start_time" type="time"
                                            class="form-control @error('start_time') is-invalid @enderror" name="start_time"
                                            value="{{ old('start_time') }}">
                                        @error('start_time')
                                            <div class="invalid-feedback">
                                                {{ $message }}
                                            </div>
                                        @enderror
                                    </div>

                                    <div class="form-group col-md-4">
                                        <label for="end_time">Waktu Selesai <span class="text-danger">*</span></label>
                                        <input id="end_time" type="time"
                                            class="form-control @error('end_time') is-invalid @enderror" name="end_time"
                                            value="{{ old('end_time') }}">
                                        @error('end_time')
                                            <div class="invalid-feedback">
                                                {{ $message }}
                                            </div>
                                        @enderror
                                    </div>

                                    <div class="form-group col-12">
                                        <label for="description">Deskripsi <span class="text-danger">*</span></label>
                                        <textarea name="description" id="description" class="form-control @error('description') is-invalid @enderror summernote"
                                            rows="10" data-placeholder="Deskripsikan kegiatan">{!! old('description') !!}</textarea>
                                        @error('description')
                                            <div class="invalid-feedback">
                                                {{ $message }}
                                            </div>
                                        @enderror
                                    </div>

                                    <input type="hidden" name="officer_id" id="officer_id"
                                        value="{{ Auth::user()->officer_id }}">

                                    <div class="col-12">
                                        <div class="d-flex justify-content-center justify-content-md-end align-items-center"
                                            style="gap: .5rem">
                                            <a href="{{ url('/schedule') }}" class="btn btn-secondary">Kembali</a>
                                            <button type="submit" class="btn btn-primary">Simpan</button>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
@endsection

@push('scripts')
    <script src="{{ asset('modules/summernote/summernote-bs4.js') }}"></script>
    <script src="{{ asset('modules/summernote/lang/summernote-id-ID.js') }}"></script>
@endpush
