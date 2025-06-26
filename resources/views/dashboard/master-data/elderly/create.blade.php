@extends('layouts.dashboard')

@section('title', 'Tambah Lansia')

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
                            <form action="{{ url('/elderly-data') }}" method="POST">
                                @csrf
                                <div class="row g-4">
                                    <div class="col-12">
                                        <h5 class="card-title">Biodata</h5>
                                    </div>

                                    <div class="form-group col-md-6">
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

                                    <div class="form-group col-md-6">
                                        <label for="fullname">Nama Lengkap <span class="text-danger">*</span></label>
                                        <input id="fullname" type="text"
                                            class="form-control @error('fullname') is-invalid @enderror" name="fullname"
                                            value="{{ old('fullname') }}" placeholder="Jane Doe" autofocus>
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
                                            name="birth_place" value="{{ old('birth_place') }}" placeholder="Jakarta">
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
                                        <label for="blood_type">Golongan Darah <span class="text-danger">*</span></label>
                                        <select name="blood_type" id="blood_type"
                                            class="form-control @error('blood_type') is-invalid @enderror">
                                            <option value="" selected disabled>-- Pilih Golongan Darah --
                                            </option>
                                            <option value="-" {{ old('blood_type') == '-' ? 'selected' : '' }}>
                                                Belum Cek Golongan Darah
                                            </option>
                                            <option value="A" {{ old('blood_type') == 'A' ? 'selected' : '' }}>
                                                A
                                            </option>
                                            <option value="B" {{ old('blood_type') == 'B' ? 'selected' : '' }}>
                                                B
                                            </option>
                                            <option value="AB" {{ old('blood_type') == 'AB' ? 'selected' : '' }}>
                                                AB
                                            </option>
                                            <option value="O" {{ old('blood_type') == 'O' ? 'selected' : '' }}>
                                                O
                                            </option>
                                        </select>
                                        @error('blood_type')
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
                                        <label for="city">Kota/Kabupaten <span class="text-danger">*</span></label>
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
                                        <label for="subdistrict">Kecamatan <span class="text-danger">*</span></label>
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
                                        <label for="village">Kelurahan/Desa <span class="text-danger">*</span></label>
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
                                        <label for="hamlet">Lingkungan/Dusun <span class="text-danger">*</span></label>
                                        <input id="hamlet" type="text"
                                            class="form-control @error('hamlet') is-invalid @enderror" name="hamlet"
                                            data-old="{{ old('hamlet', $parent->hamlet ?? '') }}" value=""
                                            placeholder="Nama Lingkungan/Dusun">
                                        @error('hamlet')
                                            <div class="invalid-feedback">
                                                {{ $message }}
                                            </div>
                                        @enderror
                                    </div>

                                    <div class="form-group col-md-6">
                                        <label for="address">Alamat <span class="text-danger">*</span></label>
                                        <textarea name="address" id="address" class="form-control @error('address') is-invalid @enderror" rows="5"
                                            placeholder="Jl. Merdeka, No. 123, Tasikmalaya" data-old="{{ old('address', $parent->address ?? '') }}"></textarea>
                                        @error('address')
                                            <div class="invalid-feedback">
                                                {{ $message }}
                                            </div>
                                        @enderror
                                    </div>

                                    <div class="col-12">
                                        <div class="d-flex justify-content-center justify-content-md-end align-items-center"
                                            style="gap: .5rem">
                                            <a href="{{ url('/elderly-data') }}" class="btn btn-secondary">Kembali</a>
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
