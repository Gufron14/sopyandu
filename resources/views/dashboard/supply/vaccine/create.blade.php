@extends('layouts.dashboard')

@section('title', 'Tambah Vaksin')

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
                            <form action="{{ url('/vaccine-data') }}" method="POST">
                                @csrf

                                <div class="row g-4">
                                    <div class="form-group col-md-6 vaccine-section">
                                        <label for="vaccine_type">Kategori Vaksinasi <span
                                                class="text-danger">*</span></label>
                                        <select name="vaccine_type" id="vaccine_type"
                                            class="form-control @error('vaccine_type') is-invalid @enderror">
                                            <option value="" selected disabled>-- Pilih Kategori Vaksinasi --
                                            </option>
                                            <option value="Wajib"
                                                {{ old('vaccine_type') == 'Wajib' ? 'selected' : '' }}>
                                                Wajib
                                            </option>
                                            <option value="Tambahan"
                                                {{ old('vaccine_type') == 'Tambahan' ? 'selected' : '' }}>
                                                Tambahan
                                            </option>
                                            <option value="Khusus"
                                                {{ old('vaccine_type') == 'Khusus' ? 'selected' : '' }}>
                                                Khusus
                                            </option>
                                            <option value="Lainnya"
                                                {{ old('vaccine_type') == 'Lainnya' ? 'selected' : '' }}>
                                                Lainnya
                                            </option>
                                        </select>
                                        @error('vaccine_type')
                                            <div class="invalid-feedback">
                                                {{ $message }}
                                            </div>
                                        @enderror
                                    </div>
                                    <div class="form-group col-md-6">
                                        <label for="vaccine_name">Nama Vaksin <span class="text-danger">*</span></label>
                                        <input id="vaccine_name" type="text"
                                            class="form-control @error('vaccine_name') is-invalid @enderror"
                                            name="vaccine_name" value="{{ old('vaccine_name') }}"
                                            placeholder="BCG, Polio, atau vaksin yang relevan" autofocus>
                                        @error('vaccine_name')
                                            <div class="invalid-feedback">
                                                {{ $message }}
                                            </div>
                                        @enderror
                                    </div>

                                    <div class="form-group col-md-6">
                                        <label for="unit">Unit Vaksin <span class="text-danger">*</span></label>
                                        <input id="unit" type="text"
                                            class="form-control @error('unit') is-invalid @enderror" name="unit"
                                            value="{{ old('unit') }}" placeholder="dosis, vial, atau unit yang relevan">
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
                                            <a href="{{ url('/vaccine-data') }}" class="btn btn-secondary">Kembali</a>
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
