@extends('layouts.dashboard')

@section('title', 'Tambah Obat')

@push('styles')
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
                            <form action="{{ url('/medicine-data') }}" method="POST">
                                @csrf
                                <div class="row g-4">
                                    <div class="form-group col-md-6">
                                        <label for="medicine_name">Nama Obat <span class="text-danger">*</span></label>
                                        <input id="medicine_name" type="text"
                                            class="form-control @error('medicine_name') is-invalid @enderror"
                                            name="medicine_name" value="{{ old('medicine_name') }}"
                                            placeholder="Vitamin A, Vitamin B, atau obat yang relevan" autofocus>
                                        @error('medicine_name')
                                            <div class="invalid-feedback">
                                                {{ $message }}
                                            </div>
                                        @enderror
                                    </div>

                                    <div class="form-group col-md-6">
                                        <label for="type">Jenis Obat <span class="text-danger">*</span></label>
                                        <input id="type" type="text"
                                            class="form-control @error('type') is-invalid @enderror" name="type"
                                            value="{{ old('type') }}"
                                            placeholder="Suplemen, antibiotik, atau jenis yang relevan">
                                        @error('type')
                                            <div class="invalid-feedback">
                                                {{ $message }}
                                            </div>
                                        @enderror
                                    </div>

                                    <div class="form-group col-md-6">
                                        <label for="unit">Unit Obat <span class="text-danger">*</span></label>
                                        <input id="unit" type="text"
                                            class="form-control @error('unit') is-invalid @enderror" name="unit"
                                            value="{{ old('unit') }}"
                                            placeholder="1 strip, 1 botol, atau unit yang relevan">
                                        @error('unit')
                                            <div class="invalid-feedback">
                                                {{ $message }}
                                            </div>
                                        @enderror
                                    </div>

                                    <div class="form-group col-md-6">
                                        <label for="stock">Stok <span class="text-danger">*</span></label>
                                        <input id="stock" type="number"
                                            class="form-control @error('stock') is-invalid @enderror" name="stock"
                                            value="{{ old('stock') }}" placeholder="0">
                                        @error('stock')
                                            <div class="invalid-feedback">
                                                {{ $message }}
                                            </div>
                                        @enderror
                                    </div>

                                    <div class="form-group col-md-6">
                                        <label for="entry_date">Tanggal Masuk <span class="text-danger">*</span></label>
                                        <input id="entry_date" type="date"
                                            class="form-control @error('entry_date') is-invalid @enderror" name="entry_date"
                                            value="{{ old('entry_date') }}">
                                        @error('entry_date')
                                            <div class="invalid-feedback">
                                                {{ $message }}
                                            </div>
                                        @enderror
                                    </div>

                                    <div class="form-group col-md-6">
                                        <label for="expiry_date">Tanggal Kedaluwarsa <span
                                                class="text-danger">*</span></label>
                                        <input id="expiry_date" type="date"
                                            class="form-control @error('expiry_date') is-invalid @enderror"
                                            name="expiry_date" value="{{ old('expiry_date') }}">
                                        @error('expiry_date')
                                            <div class="invalid-feedback">
                                                {{ $message }}
                                            </div>
                                        @enderror
                                    </div>

                                    <div class="form-group col-md-6">
                                        <label for="notes">Keterangan <span class="text-danger">*</span></label>
                                        <textarea name="notes" id="notes" class="form-control @error('notes') is-invalid @enderror" rows="5"
                                            maxlength="100" placeholder="Untuk anak 9-12 bulan">{{ old('notes') }}</textarea>
                                        @error('notes')
                                            <div class="invalid-feedback">
                                                {{ $message }}
                                            </div>
                                        @enderror
                                    </div>

                                    <div class="col-12">
                                        <div class="d-flex justify-content-center justify-content-md-end align-items-center"
                                            style="gap: .5rem">
                                            <a href="{{ url('/medicine-data') }}" class="btn btn-secondary">Kembali</a>
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
@endpush
