@extends('layouts.dashboard')

@section('title', 'Tambah Data')

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
                            <form action="{{ url('/officer-data') }}" method="POST">
                                @csrf
                                <div class="row g-4">
                                    <div class="col-12">
                                        <h5 class="card-title">Biodata</h5>
                                    </div>

                                    <div class="form-group col-md-6">
                                        <label for="fullname">Nama Lengkap <span class="text-danger">*</span></label>
                                        <input id="fullname" type="text"
                                            class="form-control @error('fullname') is-invalid @enderror" name="fullname"
                                            value="{{ old('fullname') }}" placeholder="Ujang" pattern="[A-Za-z\s]+" autofocus>
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
                                            name="birth_place" value="{{ old('birth_place') }}" placeholder="Tasikmalaya"
                                            pattern="[A-Za-z\s]+">
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
                                            name="date_of_birth" value="{{ old('date_of_birth') }}">
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
                                            <option value="L" {{ old('gender') == 'L' ? 'selected' : '' }}>Laki -
                                                Laki
                                            </option>
                                            <option value="P" {{ old('gender') == 'P' ? 'selected' : '' }}>
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
                                        <label for="last_education">Pendidikan Terakhir <span
                                                class="text-danger">*</span></label>
                                        <input id="last_education" type="text"
                                            class="form-control @error('last_education') is-invalid @enderror"
                                            name="last_education" value="{{ old('last_education') }}"
                                            placeholder="SMA, SMK, S1 Teknik Informatika">
                                        @error('last_education')
                                            <div class="invalid-feedback">
                                                {{ $message }}
                                            </div>
                                        @enderror
                                    </div>

                                    <div class="form-group col-md-6">
                                        <label for="position">Jabatan <span class="text-danger">*</span></label>
                                        <select name="position" id="position"
                                            class="form-control @error('position') is-invalid @enderror">
                                            <option value="" selected disabled>-- Pilih Jabatan --
                                            </option>
                                            {{-- <option value="Admin" {{ old('position') == 'Admin' ? 'selected' : '' }}>
                                                Admin
                                            </option> --}}
                                            <option value="Bidan" {{ old('position') == 'Bidan' ? 'selected' : '' }}>
                                                Bidan
                                            </option>
                                            <option value="Tenaga Medis Puskesmas"
                                                {{ old('position') == 'Tenaga Medis Puskesmas' ? 'selected' : '' }}>
                                                Tenaga Medis Puskesmas/Desa
                                            </option>
                                            {{-- <option value="Kepala Lingkungan"
                                                {{ old('position') == 'Kepala Lingkungan' ? 'selected' : '' }}>
                                                Kepala Lingkungan
                                            </option> --}}
                                            <option value="Kader" {{ old('position') == 'Kader' ? 'selected' : '' }}>
                                                Kader
                                            </option>
                                        </select>
                                        @error('position')
                                            <div class="invalid-feedback">
                                                {{ $message }}
                                            </div>
                                        @enderror
                                    </div>

                                    <div class="form-group col-md-6">
                                        <label for="address">Alamat <span class="text-danger">*</span></label>
                                        <textarea name="address" id="address" class="form-control @error('address') is-invalid @enderror" rows="5"
                                            placeholder="Jl. Merdeka, No. 123, Jakarta">{{ old('address') }}</textarea>
                                        @error('address')
                                            <div class="invalid-feedback">
                                                {{ $message }}
                                            </div>
                                        @enderror
                                    </div>

                                    {{-- <div class="col-12">
                                        <hr>
                                        <h5 class="card-title">Akun</h5>
                                    </div>

                                    <div class="form-group col-md-4">
                                        <label for="nik">Nomor Induk Keluarga (NIK) <span
                                                class="text-danger">*</span></label>
                                        <input id="nik" type="number"
                                            class="form-control @error('nik') is-invalid @enderror" name="nik"
                                            value="{{ old('nik') }}" placeholder="5271xxxxxxxxxxxx">
                                        @error('nik')
                                            <div class="invalid-feedback">
                                                {{ $message }}
                                            </div>
                                        @enderror
                                        <div class="invalid-feedback" id="nik-error"></div>
                                    </div>

                                    <div class="form-group col-md-4">
                                        <label for="username">Nama Pengguna <span class="text-danger">*</span></label>
                                        <input id="username" type="text"
                                            class="form-control @error('username') is-invalid @enderror" name="username"
                                            value="{{ old('username') }}" placeholder="ujang1" readonly>
                                        @error('username')
                                            <div class="invalid-feedback">
                                                {{ $message }}
                                            </div>
                                        @enderror
                                    </div>

                                    <div class="form-group col-md-4">
                                        <label for="phone_number">Nomor HP/WA (AKTIF) <span
                                                class="text-danger">*</span></label>
                                        <input id="phone_number" type="tel"
                                            class="form-control @error('phone_number') is-invalid @enderror"
                                            name="phone_number" value="628323323"
                                            placeholder="+628xxxxxxxxxx">
                                        @error('phone_number')
                                            <div class="invalid-feedback">
                                                {{ $message }}
                                            </div>
                                        @enderror
                                    </div> --}}

                                    <div class="col-12">
                                        <div class="d-flex justify-content-center justify-content-md-end align-items-center"
                                            style="gap: .5rem">
                                            <a href="{{ url('/officer-data') }}" class="btn btn-secondary">Kembali</a>
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

{{-- @push('scripts')
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
                    // Isi username otomatis berdasarkan NIK
                    var usernameInput = document.getElementById('username');
                    usernameInput.value = this.value.trim(); // Set seluruh NIK sebagai username
                }
            });

            // Jika NIK dihapus, kosongkan username
            nikInput.addEventListener('input', function() {
                if (this.value.trim() === "") {
                    document.getElementById('username').value = ""; // Kosongkan username jika NIK kosong
                }
            });
        });
    </script>
@endpush --}}
