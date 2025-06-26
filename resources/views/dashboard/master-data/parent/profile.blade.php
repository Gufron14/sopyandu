@extends('layouts.dashboard')

@section('title', 'Profil')

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
                            <form action="{{ url('/parent-profile') }}" method="POST">
                                @csrf
                                @method('PUT')
                                <div class="row g-4">
                                    <div class="col-12">
                                        <h5 class="card-title">Biodata</h5>
                                    </div>

                                    <div class="col-12">
                                        <div class="row g-4">
                                            <div class="col-md-6">
                                                <h6>Ibu</h6>

                                                <div class="form-group">
                                                    <label for="mother_fullname">Nama Lengkap Ibu <span
                                                            class="text-danger">*</span></label>
                                                    <input id="mother_fullname" type="text"
                                                        class="form-control @error('mother_fullname') is-invalid @enderror"
                                                        name="mother_fullname"
                                                        value="{{ old('mother_fullname', $parent->mother_fullname) }}"
                                                        pattern="[A-Za-z ]+" placeholder="Imas" autofocus>
                                                    @error('mother_fullname')
                                                        <div class="invalid-feedback">
                                                            {{ $message }}
                                                        </div>
                                                    @enderror
                                                </div>

                                                <div class="form-group">
                                                    <label for="mother_birth_place">Tempat Lahir Ibu <span
                                                            class="text-danger">*</span></label>
                                                    <input id="mother_birth_place" type="text"
                                                        class="form-control @error('mother_birth_place') is-invalid @enderror"
                                                        name="mother_birth_place"
                                                        value="{{ old('mother_birth_place', $parent->mother_birth_place) }}"
                                                        placeholder="Tasik Malaya" pattern="[A-Za-z ]+">
                                                    @error('mother_birth_place')
                                                        <div class="invalid-feedback">
                                                            {{ $message }}
                                                        </div>
                                                    @enderror
                                                </div>

                                                <div class="form-group">
                                                    <label for="mother_date_of_birth">Tanggal Lahir Ibu <span
                                                            class="text-danger">*</span></label>
                                                    <input id="mother_date_of_birth" type="date"
                                                        class="form-control @error('mother_date_of_birth') is-invalid @enderror"
                                                        name="mother_date_of_birth"
                                                        value="{{ old('mother_date_of_birth', $parent->mother_date_of_birth) }}">
                                                    @error('mother_date_of_birth')
                                                        <div class="invalid-feedback">
                                                            {{ $message }}
                                                        </div>
                                                    @enderror
                                                </div>

                                                <div class="form-group">
                                                    <label for="mother_blood_type">Golongan Darah Ibu <span
                                                            class="text-danger">*</span></label>
                                                    <select name="mother_blood_type" id="mother_blood_type"
                                                        class="form-control @error('mother_blood_type') is-invalid @enderror">
                                                        <option value="" selected disabled>-- Pilih Golongan Darah --
                                                        </option>
                                                        <option value="-"
                                                            {{ old('mother_blood_type', $parent->mother_blood_type) == '-' ? 'selected' : '' }}>
                                                            Belum Cek Golongan Darah
                                                        </option>
                                                        <option value="A"
                                                            {{ old('mother_blood_type', $parent->mother_blood_type) == 'A' ? 'selected' : '' }}>
                                                            A
                                                        </option>
                                                        <option value="B"
                                                            {{ old('mother_blood_type', $parent->mother_blood_type) == 'B' ? 'selected' : '' }}>
                                                            B
                                                        </option>
                                                        <option value="AB"
                                                            {{ old('mother_blood_type', $parent->mother_blood_type) == 'AB' ? 'selected' : '' }}>
                                                            AB
                                                        </option>
                                                        <option value="O"
                                                            {{ old('mother_blood_type', $parent->mother_blood_type) == 'O' ? 'selected' : '' }}>
                                                            O
                                                        </option>
                                                    </select>
                                                    @error('mother_blood_type')
                                                        <div class="invalid-feedback">
                                                            {{ $message }}
                                                        </div>
                                                    @enderror
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <h6>Ayah</h6>

                                                <div class="form-group">
                                                    <label for="father_fullname">Nama Lengkap Ayah <span
                                                            class="text-danger">*</span></label>
                                                    <input id="father_fullname" type="text"
                                                        class="form-control @error('father_fullname') is-invalid @enderror"
                                                        name="father_fullname"
                                                        value="{{ old('father_fullname', $parent->father_fullname) }}"
                                                        placeholder="Dadan" pattern="[A-Za-z ]+">
                                                    @error('father_fullname')
                                                        <div class="invalid-feedback">
                                                            {{ $message }}
                                                        </div>
                                                    @enderror
                                                </div>

                                                <div class="form-group">
                                                    <label for="father_nik">NIK Ayah <span
                                                            class="text-danger">*</span></label>
                                                    <input id="nik" type="number"
                                                        class="form-control @error('father_nik') is-invalid @enderror"
                                                        name="father_nik"
                                                        value="{{ old('father_nik', $parent->father_nik) }}"
                                                        placeholder="5271xxxxxxxxxxxx">
                                                    @error('father_nik')
                                                        <div class="invalid-feedback">
                                                            {{ $message }}
                                                        </div>
                                                    @enderror
                                                </div>

                                                <div class="form-group">
                                                    <label for="father_birth_place">Tempat Lahir Ayah <span
                                                            class="text-danger">*</span></label>
                                                    <input id="father_birth_place" type="text"
                                                        class="form-control @error('father_birth_place') is-invalid @enderror"
                                                        name="father_birth_place"
                                                        value="{{ old('father_birth_place', $parent->father_birth_place) }}"
                                                        placeholder="Tasik Malaya" pattern="[A-Za-z ]+">
                                                    @error('father_birth_place')
                                                        <div class="invalid-feedback">
                                                            {{ $message }}
                                                        </div>
                                                    @enderror
                                                </div>

                                                <div class="form-group">
                                                    <label for="father_date_of_birth">Tanggal Lahir Ayah <span
                                                            class="text-danger">*</span></label>
                                                    <input id="father_date_of_birth" type="date"
                                                        class="form-control @error('father_date_of_birth') is-invalid @enderror"
                                                        name="father_date_of_birth"
                                                        value="{{ old('father_date_of_birth', $parent->father_date_of_birth) }}">
                                                    @error('father_date_of_birth')
                                                        <div class="invalid-feedback">
                                                            {{ $message }}
                                                        </div>
                                                    @enderror
                                                </div>

                                                <div class="form-group">
                                                    <label for="father_blood_type">Golongan Darah Ayah <span
                                                            class="text-danger">*</span></label>
                                                    <select name="father_blood_type" id="father_blood_type"
                                                        class="form-control @error('father_blood_type') is-invalid @enderror">
                                                        <option value="" selected disabled>-- Pilih Golongan Darah --
                                                        </option>
                                                        <option value="-"
                                                            {{ old('father_blood_type', $parent->father_blood_type) == '-' ? 'selected' : '' }}>
                                                            Belum Cek Golongan Darah
                                                        </option>
                                                        <option value="A"
                                                            {{ old('father_blood_type', $parent->father_blood_type) == 'A' ? 'selected' : '' }}>
                                                            A
                                                        </option>
                                                        <option value="B"
                                                            {{ old('father_blood_type', $parent->father_blood_type) == 'B' ? 'selected' : '' }}>
                                                            B
                                                        </option>
                                                        <option value="AB"
                                                            {{ old('father_blood_type', $parent->father_blood_type) == 'AB' ? 'selected' : '' }}>
                                                            AB
                                                        </option>
                                                        <option value="O"
                                                            {{ old('father_blood_type', $parent->father_blood_type) == 'O' ? 'selected' : '' }}>
                                                            O
                                                        </option>
                                                    </select>
                                                    @error('father_blood_type')
                                                        <div class="invalid-feedback">
                                                            {{ $message }}
                                                        </div>
                                                    @enderror
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-12">
                                        <h6>Data Lainnya</h6>

                                        <div class="row g-4">
                                            <div class="form-group col-md-6">
                                                <label for="is_pregnant">Status Kehamilan <span
                                                        class="text-danger">*</span></label>
                                                <select name="is_pregnant" id="is_pregnant"
                                                    class="form-control @error('is_pregnant') is-invalid @enderror">
                                                    <option value="" selected disabled>-- Pilih Status Kehamilan --
                                                    </option>
                                                    <option value="Tidak Hamil"
                                                        {{ old('is_pregnant', $parent->is_pregnant) == 'Tidak Hamil' ? 'selected' : '' }}>
                                                        Tidak Hamil
                                                    </option>
                                                    <option value="Hamil"
                                                        {{ old('is_pregnant', $parent->is_pregnant) == 'Hamil' ? 'selected' : '' }}>
                                                        Hamil
                                                    </option>
                                                </select>
                                                @error('is_pregnant')
                                                    <div class="invalid-feedback">
                                                        {{ $message }}
                                                    </div>
                                                @enderror
                                            </div>

                                            <div class="form-group col-md-6">
                                                <label for="number_of_children">Jumlah Anak <span
                                                        class="text-danger">*</span></label>
                                                <input id="number_of_children" type="number"
                                                    class="form-control @error('number_of_children') is-invalid @enderror"
                                                    name="number_of_children"
                                                    value="{{ old('number_of_children', $parent->number_of_children) }}"
                                                    placeholder="Isi 0 jika belum ada">
                                                @error('number_of_children')
                                                    <div class="invalid-feedback">
                                                        {{ $message }}
                                                    </div>
                                                @enderror
                                            </div>

                                            <div class="form-group col-md-6">
                                                <label for="province">Provinsi <span class="text-danger">*</span></label>
                                                <select name="province" id="province"
                                                    class="form-control select2 @error('province') is-invalid @enderror"
                                                    data-old="{{ old('province', $parent->province ?? '') }}">
                                                    <option value="" selected disabled>-- Pilih Provinsi --</option>
                                                </select>
                                                @error('province')
                                                    <div class="invalid-feedback">
                                                        {{ $message }}
                                                    </div>
                                                @enderror
                                            </div>

                                            <div class="form-group col-md-6">
                                                <label for="city">Kota/Kabupaten <span
                                                        class="text-danger">*</span></label>
                                                <select name="city" id="city"
                                                    class="form-control select2 @error('city') is-invalid @enderror"
                                                    data-old="{{ old('city', $parent->city ?? '') }}">
                                                    <option value="" selected disabled>-- Pilih Kota/Kabupaten --
                                                    </option>
                                                </select>
                                                @error('city')
                                                    <div class="invalid-feedback">
                                                        {{ $message }}
                                                    </div>
                                                @enderror
                                            </div>

                                            <div class="form-group col-md-6">
                                                <label for="subdistrict">Kecamatan <span
                                                        class="text-danger">*</span></label>
                                                <select name="subdistrict" id="subdistrict"
                                                    class="form-control select2 @error('subdistrict') is-invalid @enderror"
                                                    data-old="{{ old('subdistrict', $parent->subdistrict ?? '') }}">
                                                    <option value="" selected disabled>-- Pilih Kecamatan --</option>
                                                </select>
                                                @error('subdistrict')
                                                    <div class="invalid-feedback">
                                                        {{ $message }}
                                                    </div>
                                                @enderror
                                            </div>

                                            <div class="form-group col-md-6">
                                                <label for="village">Kelurahan/Desa <span
                                                        class="text-danger">*</span></label>
                                                <select name="village" id="village"
                                                    class="form-control select2 @error('village') is-invalid @enderror"
                                                    data-old="{{ old('village', $parent->village ?? '') }}">
                                                    <option value="" selected disabled>-- Pilih Kelurahan/Desa --
                                                    </option>
                                                </select>
                                                @error('village')
                                                    <div class="invalid-feedback">
                                                        {{ $message }}
                                                    </div>
                                                @enderror
                                            </div>

                                            <div class="form-group col-md-6">
                                                <label for="hamlet">Lingkungan/Dusun <span
                                                        class="text-danger">*</span></label>
                                                <input id="hamlet" type="text"
                                                    class="form-control @error('hamlet') is-invalid @enderror"
                                                    name="hamlet" data-old="{{ old('hamlet', $parent->hamlet ?? '') }}"
                                                    value="" placeholder="Nama Lingkungan/Dusun">
                                                @error('hamlet')
                                                    <div class="invalid-feedback">
                                                        {{ $message }}
                                                    </div>
                                                @enderror
                                            </div>

                                            <div class="form-group col-md-6">
                                                <label for="address">Alamat <span class="text-danger">*</span></label>
                                                <textarea name="address" id="address" class="form-control @error('address') is-invalid @enderror" rows="5"
                                                    placeholder="Jl. Merdeka, No. 123, Jakarta" data-old="{{ old('address', $parent->address ?? '') }}"></textarea>
                                                @error('address')
                                                    <div class="invalid-feedback">
                                                        {{ $message }}
                                                    </div>
                                                @enderror
                                            </div>
                                        </div>
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
                                            value="{{ old('nik', $parent->nik) }}" placeholder="5271xxxxxxxxxxxx">
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
                                        <input id="phone_number" type="text"
                                            class="form-control @error('phone_number') is-invalid @enderror"
                                            name="phone_number" value="{{ old('phone_number', $user->phone_number) }}"
                                            placeholder="+628xxxxxxxxxx" pattern="^\+?[0-9]+$">
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

                                    <div class="form-group
                                            col-md-4">
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
    <script src="{{ asset('modules/select2/dist/js/select2.full.min.js') }}"></script>

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

        $(document).ready(function() {
            $('#city, #subdistrict, #village, #hamlet, #address').prop('disabled', true);

            const oldProvince = $('#province').data('old');
            const oldCity = $('#city').data('old');
            const oldSubdistrict = $('#subdistrict').data('old');
            const oldVillage = $('#village').data('old');

            // Ambil nilai lama dari atribut data-old
            const oldHamlet = $('#hamlet').data('old');
            const oldAddress = $('#address').data('old');

            // Jika ada nilai lama untuk hamlet, set value dan enable field
            if (oldHamlet) {
                $('#hamlet').val(oldHamlet).prop('disabled', false);
            }

            // Jika ada nilai lama untuk address, set value dan enable field
            if (oldAddress) {
                $('#address').val(oldAddress).prop('disabled', false);
            }

            // Load Provinsi
            $('#province').html('<option>Memuat...</option>');
            $.get('/get-provinces')
                .done(function(data) {
                    $('#province').html('<option value="">-- Pilih Provinsi --</option>');
                    data.forEach(p => {
                        const selected = (p.id == oldProvince) ? 'selected' : '';
                        $('#province').append(`<option value="${p.id}" ${selected}>${p.name}</option>`);
                    });

                    if (oldProvince) {
                        loadCities(oldProvince);
                    }
                })
                .fail(() => {
                    $('#province').html('<option value="">Gagal memuat provinsi</option>');
                });

            // Change event
            $('#province').on('change', function() {
                const val = $(this).val();
                resetDropdowns(['#city', '#subdistrict', '#village']);
                disableInputs(['#city', '#subdistrict', '#village']);
                if (val) loadCities(val);
            });

            $('#city').on('change', function() {
                const val = $(this).val();
                resetDropdowns(['#subdistrict', '#village']);
                disableInputs(['#subdistrict', '#village']);
                if (val) loadDistricts(val);
            });

            $('#subdistrict').on('change', function() {
                const val = $(this).val();
                resetDropdowns(['#village']);
                disableInputs(['#village']);
                if (val) loadVillages(val);
            });

            $('#village').on('change', function() {
                const val = $(this).val();
                if (val) {
                    $('#hamlet').prop('disabled', false);
                } else {
                    $('#hamlet').prop('disabled', true).val('');
                    $('#address').prop('disabled', true).val('');
                }
            });

            $('#hamlet').on('input', function() {
                const val = $(this).val();
                if (val.trim() !== '') {
                    $('#address').prop('disabled', false);
                } else {
                    $('#address').prop('disabled', true).val('');
                }
            });

            // Load cities
            function loadCities(provinceId) {
                $('#city').html('<option>Memuat...</option>');
                $.get('/get-cities/' + provinceId)
                    .done(function(data) {
                        $('#city').html('<option value="">-- Pilih Kota/Kabupaten --</option>');
                        data.forEach(c => {
                            const selected = (c.id == oldCity) ? 'selected' : '';
                            $('#city').append(`<option value="${c.id}" ${selected}>${c.name}</option>`);
                        });
                        $('#city').prop('disabled', false);

                        if (oldCity) {
                            loadDistricts(oldCity);
                        }
                    })
                    .fail(() => {
                        $('#city').html('<option value="">Gagal memuat kota</option>');
                    });
            }

            function loadDistricts(cityId) {
                $('#subdistrict').html('<option>Memuat...</option>');
                $.get('/get-districts/' + cityId)
                    .done(function(data) {
                        $('#subdistrict').html('<option value="">-- Pilih Kecamatan --</option>');
                        data.forEach(d => {
                            const selected = (d.id == oldSubdistrict) ? 'selected' : '';
                            $('#subdistrict').append(
                                `<option value="${d.id}" ${selected}>${d.name}</option>`);
                        });
                        $('#subdistrict').prop('disabled', false);

                        if (oldSubdistrict) {
                            loadVillages(oldSubdistrict);
                        }
                    })
                    .fail(() => {
                        $('#subdistrict').html('<option value="">Gagal memuat kecamatan</option>');
                    });
            }

            function loadVillages(districtId) {
                $('#village').html('<option>Memuat...</option>');
                $.get('/get-villages/' + districtId)
                    .done(function(data) {
                        $('#village').html('<option value="">-- Pilih Kelurahan/Desa --</option>');
                        data.forEach(v => {
                            const selected = (v.id == oldVillage) ? 'selected' : '';
                            $('#village').append(
                                `<option value="${v.id}" ${selected}>${v.name}</option>`);
                        });
                        $('#village').prop('disabled', false);
                    })
                    .fail(() => {
                        $('#village').html('<option value="">Gagal memuat kelurahan</option>');
                    });
            }

            function resetDropdowns(ids) {
                ids.forEach(id => {
                    $(id).html('<option value="">-- Pilih --</option>');
                });
            }

            function disableInputs(ids) {
                ids.forEach(id => {
                    $(id).prop('disabled', true).val('');
                });
            }
        });
    </script>
@endpush
