@extends('layouts.dashboard')

@section('title', 'Profil')

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
                            <form action="{{ url('/officer-profile') }}" method="POST">
                                @csrf
                                @method('PUT')
                                <div class="row g-4">
                                    <div class="col-12">
                                        <h5 class="card-title">Biodata</h5>
                                    </div>

                                    <div class="form-group col-md-6">
                                        <label for="fullname">Nama Lengkap <span class="text-danger">*</span></label>
                                        <input id="fullname" type="text"
                                            class="form-control @error('fullname') is-invalid @enderror" name="fullname"
                                            value="{{ old('fullname', $officer->fullname) }}" placeholder="Ujang" pattern="^[a-zA-Z\s]+$"
                                            autofocus>
                                        @error('fullname')
                                            <div class="invalid-feedback">
                                                {{ $message }}
                                            </div>
                                        @enderror
                                    </div>

                                    {{-- <div class="form-group col-md-6">
                                        <label for="birth_place">Tempat Lahir <span class="text-danger">*</span></label>
                                        <input id="birth_place" type="text"
                                            class="form-control @error('birth_place') is-invalid @enderror"
                                            name="birth_place" value="{{ old('birth_place', $officer->birth_place) }}"
                                            placeholder="Tasikmalaya" pattern="^[a-zA-Z\s]+$">
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
                                            value="{{ old('date_of_birth', $officer->date_of_birth) }}">
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
                                                {{ old('gender', $officer->gender) == 'L' ? 'selected' : '' }}>Laki -
                                                Laki
                                            </option>
                                            <option value="P"
                                                {{ old('gender', $officer->gender) == 'P' ? 'selected' : '' }}>
                                                Perempuan
                                            </option>
                                        </select>
                                        @error('gender')
                                            <div class="invalid-feedback">
                                                {{ $message }}
                                            </div>
                                        @enderror
                                    </div> --}}

                                    <div class="form-group col-md-6">
                                        <label for="last_education">Pendidikan Terakhir <span
                                                class="text-danger">*</span></label>
                                        <input id="last_education" type="text"
                                            class="form-control @error('last_education') is-invalid @enderror"
                                            name="last_education"
                                            value="{{ old('last_education', $officer->last_education) }}"
                                            placeholder="SMA, SMK, S1 Teknik Informatika">
                                        @error('last_education')
                                            <div class="invalid-feedback">
                                                {{ $message }}
                                            </div>
                                        @enderror
                                    </div>

                                    <div class="form-group col-md-6">
                                        <label for="address">Alamat <span class="text-danger">*</span></label>
                                        <textarea name="address" id="address" class="form-control @error('address') is-invalid @enderror" rows="5"
                                            placeholder="Jl. Merdeka, No. 123, Jakarta">{{ old('address', $officer->address) }}</textarea>
                                        @error('address')
                                            <div class="invalid-feedback">
                                                {{ $message }}
                                            </div>
                                        @enderror
                                    </div>

                                    <div class="col-12">
                                        <hr>
                                        <h5 class="card-title">Akun</h5>
                                    </div>

                                    <div class="form-group col-md-4">
                                        <label for="nik">Nomor Induk Keluarga (NIK) <span
                                                class="text-danger">*</span></label>
                                        <input id="nik" type="number"
                                            class="form-control @error('nik') is-invalid @enderror" name="nik"
                                            value="{{ old('nik', $officer->nik) }}" placeholder="5271xxxxxxxxxxxx">
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
                                            value="{{ old('username', $user->username) }}" placeholder="ujang1">
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
                                            name="phone_number" value="{{ old('phone_number', $user->phone_number) }}"
                                            placeholder="+628xxxxxxxxxx">
                                        @error('phone_number')
                                            <div class="invalid-feedback">
                                                {{ $message }}
                                            </div>
                                        @enderror
                                    </div>

                                    <div class="col-12">
                                        <div class="font-italic my-2"><span class="text-danger">*</span> Jika
                                            Anda ingin mengubah
                                            kata sandi, silakan isi kolom berikut.</div>
                                    </div>

                                    <div class="form-group col-md-4">
                                        <label for="password_old" class="d-block">Kata Sandi Lama</label>
                                        <input id="password_old" type="password"
                                            class="form-control @error('password_old') is-invalid @enderror"
                                            name="password_old">
                                        @error('password_old')
                                            <div class="invalid-feedback">
                                                {{ $message }}
                                            </div>
                                        @enderror
                                    </div>

                                    <div class="form-group col-md-4">
                                        <label for="password" class="d-block">Kata Sandi Baru</label>
                                        <input id="password" type="password"
                                            class="form-control @error('password') is-invalid @enderror" name="password"
                                            disabled>
                                        @error('password')
                                            <div class="invalid-feedback">
                                                {{ $message }}
                                            </div>
                                        @enderror
                                        <div class="invalid-feedback" id="password-error"></div>
                                    </div>

                                    <div class="form-group col-md-4">
                                        <label for="password_confirmation" class="d-block">Konfirmasi Kata Sandi
                                            Baru</label>
                                        <input id="password_confirmation" type="password"
                                            class="form-control @error('password_confirmation') is-invalid @enderror"
                                            name="password_confirmation" disabled>
                                        @error('password_confirmation')
                                            <div class="invalid-feedback">
                                                {{ $message }}
                                            </div>
                                        @enderror
                                        <div class="invalid-feedback" id="password-confirmation-error"></div>
                                    </div>

                                    <div class="col-12">
                                        <div class="d-flex justify-content-center justify-content-md-end align-items-center"
                                            style="gap: .5rem">
                                            <a href="javascript:history.back()" class="btn btn-secondary">Kembali</a>
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
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Validasi NIK
            var nikInput = document.getElementById('nik');
            var nikError = document.getElementById('nik-error');
            nikInput.addEventListener('input', function() {
                if (this.value.trim().length !== 16) {
                    nikInput.classList.add('is-invalid');
                    nikError.textContent = "NIK harus memiliki 16 karakter.";
                } else {
                    nikInput.classList.remove('is-invalid');
                    nikError.textContent = ""; // Hapus pesan error jika valid
                }
            });

            // Validasi pengisian password
            const passwordOld = document.getElementById('password_old');
            const password = document.getElementById('password');
            const passwordError = document.getElementById('password-error');
            const passwordConfirmation = document.getElementById('password_confirmation');
            const passwordConfirmationError = document.getElementById('password-confirmation-error');

            passwordOld.addEventListener('input', function() {
                const isPasswordOldFilled = passwordOld.value.trim() !== '';

                password.disabled = !isPasswordOldFilled;
                passwordConfirmation.disabled = !isPasswordOldFilled;

                if (isPasswordOldFilled) {
                    password.classList.add('is-invalid');
                    passwordError.textContent = "Kata sandi baru wajib diisi.";
                    password.setAttribute('required', 'required');

                    passwordConfirmation.classList.add('is-invalid');
                    passwordConfirmationError.textContent = "Konfirmasi kata sandi baru wajib diisi.";
                    passwordConfirmation.setAttribute('required', 'required');
                } else {
                    password.classList.remove('is-invalid');
                    passwordError.textContent = '';
                    password.removeAttribute('required');

                    passwordConfirmation.classList.remove('is-invalid');
                    passwordConfirmationError.textContent = '';
                    passwordConfirmation.removeAttribute('required');
                }

                if (!isPasswordOldFilled) {
                    password.value = '';
                    passwordConfirmation.value = '';
                }
            });

            // Validasi password baru
            password.addEventListener('input', function() {
                if (this.value.length < 8) {
                    password.classList.add('is-invalid');
                    passwordError.textContent = "Kata sandi baru minimal 8 karakter.";
                } else {
                    password.classList.remove('is-invalid');
                    passwordError.textContent = ""; // Hapus pesan error
                }
            });

            // Validasi konfirmasi password baru
            if (passwordConfirmation && passwordConfirmationError) {
                passwordConfirmation.addEventListener('input', function() {
                    if (this.value !== password.value) {
                        passwordConfirmation.classList.add('is-invalid');
                        passwordConfirmationError.textContent = "Konfirmasi kata sandi baru tidak sesuai.";
                    } else {
                        passwordConfirmation.classList.remove('is-invalid');
                        passwordConfirmationError.textContent = ""; // Hapus pesan error
                    }
                });
            }
        });
    </script>
@endpush
