@extends('layouts.dashboard')

@section('title', 'Tambah Orang Tua')

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
                            @if(session('success'))
                            <div class="alert alert-success alert-dismissible show fade">
                                <div class="alert-body">
                                    <button class="close" data-dismiss="alert">
                                        <span>×</span>
                                    </button>
                                    {{ session('success') }}
                                </div>
                            </div>
                            @endif

                            @if(session('error'))
                            <div class="alert alert-danger alert-dismissible show fade">
                                <div class="alert-body">
                                    <button class="close" data-dismiss="alert">
                                        <span>×</span>
                                    </button>
                                    {{ session('error') }}
                                </div>
                            </div>
                            @endif

                            @if($errors->any())
                            <div class="alert alert-danger alert-dismissible show fade">
                                <div class="alert-body">
                                    <button class="close" data-dismiss="alert">
                                        <span>×</span>
                                    </button>
                                    <ul class="mb-0">
                                        @foreach($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                            </div>
                            @endif

                            <form action="{{ url('/parent-data') }}" method="POST">
                                @csrf
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
                                                        name="mother_fullname" value="{{ old('mother_fullname') }}"
                                                        placeholder="Imas" autofocus>
                                                    @error('mother_fullname')
                                                        <div class="invalid-feedback">
                                                            {{ $message }}
                                                        </div>
                                                    @enderror
                                                </div>                                                <div class="form-group">
                                                    <label for="father_nik">Nomor Induk Keluarga (NIK) Ayah <span class="text-danger">*</span></label>
                                                        <input id="father_nik" type="number"
                                                        class="form-control @error('father_nik') is-invalid @enderror" name="father_nik"
                                                        value="{{ old('father_nik') }}" placeholder="5271xxxxxxxxxxxx">
                                                        @error('father_nik')
                                                        <div class="invalid-feedback">
                                                            {{ $message }}
                                                        </div>
                                                    @enderror
                                                    <div class="invalid-feedback" id="father-nik-error"></div>
                                                </div>

                                                <div class="form-group">
                                                    <label for="mother_birth_place">Tempat Lahir Ibu <span
                                                            class="text-danger">*</span></label>
                                                    <input id="mother_birth_place" type="text"
                                                        class="form-control @error('mother_birth_place') is-invalid @enderror"
                                                        name="mother_birth_place" value="{{ old('mother_birth_place') }}"
                                                        placeholder="Tasikmalaya" pattern="[A-Za-z ]+">
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
                                                        value="{{ old('mother_date_of_birth') }}">
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
                                                            {{ old('mother_blood_type') == '-' ? 'selected' : '' }}>
                                                            Belum Cek Golongan Darah
                                                        </option>
                                                        <option value="A"
                                                            {{ old('mother_blood_type') == 'A' ? 'selected' : '' }}>
                                                            A
                                                        </option>
                                                        <option value="B"
                                                            {{ old('mother_blood_type') == 'B' ? 'selected' : '' }}>
                                                            B
                                                        </option>
                                                        <option value="AB"
                                                            {{ old('mother_blood_type') == 'AB' ? 'selected' : '' }}>
                                                            AB
                                                        </option>
                                                        <option value="O"
                                                            {{ old('mother_blood_type') == 'O' ? 'selected' : '' }}>
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
                                                        name="father_fullname" value="{{ old('father_fullname') }}"
                                                        placeholder="Dadan">
                                                    @error('father_fullname')
                                                        <div class="invalid-feedback">
                                                            {{ $message }}
                                                        </div>
                                                    @enderror
                                                </div>
                                                
                                                <div class="form-group">
                                                    <label for="mother_nik">Nomor Induk Keluarga (NIK) Ibu <span class="text-danger">*</span></label>
                                                        <input id="mother_nik" type="number"
                                                        class="form-control @error('nik') is-invalid @enderror" name="nik"
                                                        value="{{ old('nik') }}" placeholder="5271xxxxxxxxxxxx">
                                                        @error('nik')
                                                        <div class="invalid-feedback">
                                                            {{ $message }}
                                                        </div>
                                                    @enderror
                                                    <div class="invalid-feedback" id="mother-nik-error"></div>
                                                </div>
                                                

                                                <div class="form-group">
                                                    <label for="father_birth_place">Tempat Lahir Ayah <span
                                                            class="text-danger">*</span></label>
                                                    <input id="father_birth_place" type="text"
                                                        class="form-control @error('father_birth_place') is-invalid @enderror"
                                                        name="father_birth_place" value="{{ old('father_birth_place') }}"
                                                        placeholder="Tasikmalaya" pattern="[A-Za-z ]+">
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
                                                        value="{{ old('father_date_of_birth') }}">
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
                                                            {{ old('father_blood_type') == '-' ? 'selected' : '' }}>
                                                            Belum Cek Golongan Darah
                                                        </option>
                                                        <option value="A"
                                                            {{ old('father_blood_type') == 'A' ? 'selected' : '' }}>
                                                            A
                                                        </option>
                                                        <option value="B"
                                                            {{ old('father_blood_type') == 'B' ? 'selected' : '' }}>
                                                            B
                                                        </option>
                                                        <option value="AB"
                                                            {{ old('father_blood_type') == 'AB' ? 'selected' : '' }}>
                                                            AB
                                                        </option>
                                                        <option value="O"
                                                            {{ old('father_blood_type') == 'O' ? 'selected' : '' }}>
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
                                                        {{ old('is_pregnant') == 'Tidak Hamil' ? 'selected' : '' }}>
                                                        Tidak Hamil
                                                    </option>
                                                    <option value="Hamil"
                                                        {{ old('is_pregnant') == 'Hamil' ? 'selected' : '' }}>
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
                                                    name="number_of_children" value="{{ old('number_of_children') }}"
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

                                    {{-- <div class="form-group col-md-4">
                                        <label for="nik">Nomor Induk Keluarga (NIK) Ibu <span
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
                                    </div> --}}

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
                                            name="phone_number" value="{{ old('phone_number') }}"
                                            placeholder="+628xxxxxxxxxx">
                                        @error('phone_number')
                                            <div class="invalid-feedback">
                                                {{ $message }}
                                            </div>
                                        @enderror
                                    </div>

                                    <div class="col-12">
                                        <div class="d-flex justify-content-center justify-content-md-end align-items-center"
                                            style="gap: .5rem">
                                            <a href="{{ url('/parent-data') }}" class="btn btn-secondary">Kembali</a>
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
            // Validasi NIK Ibu
            var motherNikInput = document.getElementById('mother_nik');
            var motherNikError = document.getElementById('mother-nik-error');
            
            motherNikInput.addEventListener('input', function() {
                validateNik(this, motherNikError, true);
            });

            // Validasi NIK Ayah
            var fatherNikInput = document.getElementById('father_nik');
            var fatherNikError = document.getElementById('father-nik-error');
            
            fatherNikInput.addEventListener('input', function() {
                validateNik(this, fatherNikError, false);
            });

            function validateNik(input, errorElement, isMotherNik) {
                const value = input.value.trim();
                if (value.length !== 16) {
                    input.classList.add('is-invalid');
                    errorElement.textContent = "NIK harus memiliki 16 karakter.";
                    if (isMotherNik) {
                        document.getElementById('username').value = "";
                    }
                } else {
                    input.classList.remove('is-invalid');
                    errorElement.textContent = "";
                    if (isMotherNik) {
                        // Set username based on mother's NIK
                        document.getElementById('username').value = value;
                    }
                }
            }

            // Form submission validation
            document.querySelector('form').addEventListener('submit', function(e) {
                const motherNik = motherNikInput.value.trim();
                const fatherNik = fatherNikInput.value.trim();
                let hasError = false;

                if (motherNik.length !== 16) {
                    motherNikInput.classList.add('is-invalid');
                    motherNikError.textContent = "NIK harus memiliki 16 karakter.";
                    hasError = true;
                }

                if (fatherNik.length !== 16) {
                    fatherNikInput.classList.add('is-invalid');
                    fatherNikError.textContent = "NIK harus memiliki 16 karakter.";
                    hasError = true;
                }

                if (hasError) {
                    e.preventDefault();
                    alert('Mohon periksa kembali data yang diinput.');
                }
            });
        });
        

        $(document).ready(function() {
            $('#city, #subdistrict, #village, #hamlet, #address').prop('disabled', true);

            const getProvincies = "{{ url('/get-provinces') }}";
            const getCities = "{{ url('/get-cities') }}";
            const getDistricts = "{{ url('/get-districts') }}";
            const getVillages = "{{ url('/get-villages') }}";

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
            $.get(getProvincies)
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
                $.get(getCities + '/' + provinceId)
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
                $.get(getDistricts + '/' + cityId)
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
                $.get(getVillages + '/' + districtId)
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
