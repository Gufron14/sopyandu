@extends('layouts.dashboard')

@section('title', 'Ubah Anak')

@push('styles')
    <link rel="stylesheet" href="{{ asset('modules/select2/dist/css/select2.min.css') }}">
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
                            <form action="{{ url("/children-data/{$children->id}") }}" method="POST">
                                @csrf
                                @method('PUT')
                                <div class="row g-4">
                                    <div class="col-12">
                                        <h5 class="card-title">Biodata</h5>
                                    </div>

                                    <div class="form-group col-md-6">
                                        <label for="nik">Nomor Induk Keluarga (NIK) <span
                                                class="text-danger">*</span></label>
                                        <input id="nik" type="number"
                                            class="form-control @error('nik') is-invalid @enderror" name="nik"
                                            value="{{ old('nik', $children->nik) }}" placeholder="5271xxxxxxxxxxxx">
                                        @error('nik')
                                            <div class="invalid-feedback">
                                                {{ $message }}
                                            </div>
                                        @enderror
                                        <div class="invalid-feedback" id="nik-error"></div>
                                    </div>

                                    <div class="form-group col-md-6">
                                        <label for="fullname">Nama Lengkap <span class="text-danger">*</span></label>
                                        <input id="fullname" type="text"
                                            class="form-control @error('fullname') is-invalid @enderror" name="fullname"
                                            value="{{ old('fullname', $children->fullname) }}" placeholder="Jane Doe"
                                            autofocus>
                                        @error('fullname')
                                            <div class="invalid-feedback">
                                                {{ $message }}
                                            </div>
                                        @enderror
                                    </div>

                                    <div class="form-group col-md-6">
                                        <label for="birth_place">Tempat Lahir <span class="text-danger">*</span></label>
                                        <input id="birth_place" type="text"
                                            class="form-control @error('birth_place') is-invalid @enderror"
                                            name="birth_place" value="{{ old('birth_place', $children->birth_place) }}"
                                            placeholder="Jakarta">
                                        @error('birth_place')
                                            <div class="invalid-feedback">
                                                {{ $message }}
                                            </div>
                                        @enderror
                                    </div>

                                    <div class="form-group col-md-6">
                                        <label for="date_of_birth">Tanggal Lahir <span class="text-danger">*</span></label>
                                        <input id="date_of_birth" type="date"
                                            class="form-control @error('date_of_birth') is-invalid @enderror"
                                            name="date_of_birth"
                                            value="{{ old('date_of_birth', $children->date_of_birth) }}">
                                        @error('date_of_birth')
                                            <div class="invalid-feedback">
                                                {{ $message }}
                                            </div>
                                        @enderror
                                    </div>

                                    <div class="form-group col-md-6">
                                        <label for="gender">Jenis Kelamin <span class="text-danger">*</span></label>
                                        <select name="gender" id="gender"
                                            class="form-control @error('gender') is-invalid @enderror">
                                            <option value="" selected disabled>-- Pilih Jenis Kelamin --
                                            </option>
                                            <option value="L"
                                                {{ old('gender', $children->gender) == 'L' ? 'selected' : '' }}>Laki -
                                                Laki
                                            </option>
                                            <option value="P"
                                                {{ old('gender', $children->gender) == 'P' ? 'selected' : '' }}>
                                                Perempuan
                                            </option>
                                        </select>
                                        @error('gender')
                                            <div class="invalid-feedback">
                                                {{ $message }}
                                            </div>
                                        @enderror
                                    </div>

                                    <div class="form-group col-md-6">
                                        <label for="blood_type">Golongan Darah <span class="text-danger">*</span></label>
                                        <select name="blood_type" id="blood_type"
                                            class="form-control @error('blood_type') is-invalid @enderror">
                                            <option value="" selected disabled>-- Pilih Golongan Darah --
                                            </option>
                                            <option value="-"
                                                {{ old('blood_type', $children->blood_type) == '-' ? 'selected' : '' }}>
                                                Belum Cek Golongan Darah
                                            </option>
                                            <option value="A"
                                                {{ old('blood_type', $children->blood_type) == 'A' ? 'selected' : '' }}>
                                                A
                                            </option>
                                            <option value="B"
                                                {{ old('blood_type', $children->blood_type) == 'B' ? 'selected' : '' }}>
                                                B
                                            </option>
                                            <option value="AB"
                                                {{ old('blood_type', $children->blood_type) == 'AB' ? 'selected' : '' }}>
                                                AB
                                            </option>
                                            <option value="O"
                                                {{ old('blood_type', $children->blood_type) == 'O' ? 'selected' : '' }}>
                                                O
                                            </option>
                                        </select>
                                        @error('blood_type')
                                            <div class="invalid-feedback">
                                                {{ $message }}
                                            </div>
                                        @enderror
                                    </div>

                                    @if (auth()->check() && auth()->user()->role === 'family_parent')
                                        <input type="hidden" name="parent_id" id="parent_id"
                                            value="{{ Auth::user()->parent_id }}">
                                    @else
                                        <div class="form-group col-md-6">
                                            <label for="parent_id">Nama Ibu <span class="text-danger">*</span></label>
                                            <select name="parent_id" id="parent_id"
                                                class="form-control select2 @error('parent_id') is-invalid @enderror">
                                                <option value="" selected disabled>-- Pilih Nama Ibu --</option>
                                                @foreach ($parents as $parent)
                                                    <option value="{{ $parent->id }}"
                                                        {{ old('parent_id', $children->parent_id) == $parent->id ? 'selected' : '' }}>
                                                        {{ $parent->mother_fullname . ' - ' . $parent->nik }}
                                                    </option>
                                                @endforeach
                                            </select>
                                            @error('parent_id')
                                                <div class="invalid-feedback">
                                                    {{ $message }}
                                                </div>
                                            @enderror
                                        </div>

                                    @endif

                                    <div class="col-12">
                                        <div class="d-flex justify-content-center justify-content-md-end align-items-center"
                                            style="gap: .5rem">
                                            <a href="{{ url('/children-data') }}" class="btn btn-secondary">Kembali</a>
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
    <script src="{{ asset('modules/select2/dist/js/select2.full.min.js') }}"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Validasi NIK
            var nikInput = document.getElementById('nik');
            var nikError = document.getElementById('nik-error');
            nikInput.addEventListener('input', function() {
                // Validasi panjang NIK
                if (this.value.trim().length !== 16) {
                    nikInput.classList.add('is-invalid');
                    nikError.textContent = "NIK harus memiliki 16 karakter.";
                } else {
                    nikInput.classList.remove('is-invalid');
                    nikError.textContent = ""; // Hapus pesan error jika valid
                }
            });
        });
    </script>
@endpush
