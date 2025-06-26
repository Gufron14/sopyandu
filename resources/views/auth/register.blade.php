@extends('layouts.auth')

@section('title', 'Registrasi')

@push('styles')
<style>
    body {
        background: #f8fafc;
    }
    .auth-card {
        max-width: 600px;
        margin: 40px auto;
        border-radius: 18px;
        box-shadow: 0 4px 32px rgba(0,0,0,0.08);
        background: #fff;
        padding: 2.5rem 2rem 2rem 2rem;
    }
    .auth-title {
        font-weight: 700;
        font-size: 2rem;
        margin-bottom: 1.5rem;
        color: #2d3748;
        text-align: center;
    }
    .form-label {
        font-weight: 500;
        color: #4a5568;
    }
    .btn-primary {
        background: #2563eb;
        border: none;
        border-radius: 8px;
        font-weight: 600;
        font-size: 1.1rem;
    }
    .btn-primary:hover {
        background: #1d4ed8;
    }
    .form-control:focus {
        border-color: #2563eb;
        box-shadow: 0 0 0 0.2rem rgba(37,99,235,.15);
    }
    .text-danger {
        font-size: 0.95rem;
    }
</style>
@endpush

@section('main')
<div class="auth-card">
    <div class="auth-title">Registrasi Akun</div>
    <form method="POST" action="{{ route('auth-register') }}">
        @csrf

        <div class="mb-3">
            <label for="nik" class="form-label">NIK Ibu <span class="text-danger">*</span></label>
            <input id="nik" type="number" class="form-control @error('nik') is-invalid @enderror" name="nik"
                value="{{ old('nik') }}" placeholder="5271xxxxxxxxxxxx" autofocus>
            @error('nik')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
            <div class="invalid-feedback" id="nik-error"></div>
        </div>

        <div class="mb-3">
            <label for="fullname" class="form-label">Nama Lengkap Ibu <span class="text-danger">*</span></label>
            <input id="fullname" type="text" class="form-control @error('fullname') is-invalid @enderror"
                name="fullname" value="{{ old('fullname') }}" pattern="[A-Za-z ]+" placeholder="Imas">
            @error('fullname')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="mb-3">
            <label for="phone_number" class="form-label">Nomor HP/WA <span class="text-danger">*</span></label>
            <input id="phone_number" type="tel" class="form-control @error('phone_number') is-invalid @enderror"
                name="phone_number" value="{{ old('phone_number') }}" placeholder="+628xxxxxxxxxx">
            @error('phone_number')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="mb-3">
            <label for="username" class="form-label">Nama Pengguna <span class="text-danger">*</span></label>
            <input id="username" type="text" class="form-control @error('username') is-invalid @enderror"
                name="username" value="{{ old('username') }}" placeholder="ujang1">
            @error('username')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="mb-3">
            <label for="password" class="form-label">Kata Sandi <span class="text-danger">*</span></label>
            <input id="password" type="password" class="form-control @error('password') is-invalid @enderror"
                name="password" placeholder="Minimal 8 karakter">
            @error('password')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
            <div class="invalid-feedback" id="password-error"></div>
        </div>

        <div class="mb-4">
            <label for="password_confirmation" class="form-label">Konfirmasi Kata Sandi <span class="text-danger">*</span></label>
            <input id="password_confirmation" type="password"
                class="form-control @error('password_confirmation') is-invalid @enderror" name="password_confirmation">
            @error('password_confirmation')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
            <div class="invalid-feedback" id="password-confirmation-error"></div>
        </div>

        <button type="submit" class="btn btn-primary w-100 mb-3">Registrasi</button>
        <div class="text-center small">Sudah punya akun? <a href="{{ route('login') }}">Masuk</a></div>
    </form>
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
                nikError.textContent = "";
            }
        });

        // Validasi Password
        var passwordInput = document.getElementById('password');
        var passwordError = document.getElementById('password-error');
        passwordInput.addEventListener('input', function() {
            if (this.value.length < 8) {
                passwordInput.classList.add('is-invalid');
                passwordError.textContent = "Kata sandi minimal 8 karakter.";
            } else {
                passwordInput.classList.remove('is-invalid');
                passwordError.textContent = "";
            }
        });

        // Validasi Konfirmasi Password
        var passwordConfirmationInput = document.getElementById('password_confirmation');
        var passwordConfirmationError = document.getElementById('password-confirmation-error');
        if (passwordConfirmationInput && passwordConfirmationError) {
            passwordConfirmationInput.addEventListener('input', function() {
                if (this.value !== passwordInput.value) {
                    passwordConfirmationInput.classList.add('is-invalid');
                    passwordConfirmationError.textContent = "Konfirmasi kata sandi tidak sesuai.";
                } else {
                    passwordConfirmationInput.classList.remove('is-invalid');
                    passwordConfirmationError.textContent = "";
                }
            });
        }
    });
</script>
@endpush