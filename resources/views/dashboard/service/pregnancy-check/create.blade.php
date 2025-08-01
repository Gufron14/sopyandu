@extends('layouts.dashboard')

@section('title', 'Tambah Pemeriksaan')

@push('styles')
    <link rel="stylesheet" href="{{ asset('modules/select2/dist/css/select2.min.css') }}">
    <link rel="stylesheet" href="{{ asset('modules/summernote/summernote-bs4.css') }}">
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
                        <div class="card-header mb-0">
                            <h5 class="card-title">Biodata</h5>
                        </div>
                        <div class="card-body">
                            <form action="{{ url('/pregnancy-check-data') }}" method="POST">
                                @csrf
                                <div class="d-flex gap-2">
                                    <div class="form-group col-md-4">
                                        <label for="parent_id">Nama Ibu <span class="text-danger">*</span></label>
                                        <select name="parent_id" id="parent_id"
                                            class="form-control select2 @error('parent_id') is-invalid @enderror">
                                            <option value="" selected disabled>-- Pilih Nama Ibu --
                                            </option>
                                            @foreach ($parents as $parent)
                                                <option value="{{ $parent->id }}" data-gender="{{ $parent->gender }}"
                                                    data-mother_birth_place="{{ $parent->mother_birth_place }}"
                                                    data-mother_date_of_birth="{{ $parent->mother_date_of_birth }}"
                                                    {{ old('parent_id') == $parent->id ? 'selected' : '' }}>
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

                                    <div class="form-group col-md-4">
                                        <label for="mother_birth_place">Tempat Lahir <span
                                                class="text-danger">*</span></label>
                                        <input id="mother_birth_place" type="text" class="form-control"
                                            name="mother_birth_place" value="" placeholder="Jakarta" disabled>
                                    </div>

                                    <div class="form-group col-md-4">
                                        <label for="mother_date_of_birth">Tanggal Lahir <span
                                                class="text-danger">*</span></label>
                                        <input id="mother_date_of_birth" type="date" class="form-control dob-input"
                                            name="mother_date_of_birth" value="" disabled>
                                    </div>
                                </div>

                                <div class="row">
                                    <label for="" class="form-label fs-5 fw-bold">Pemeriksaan</label>

                                    <div class="form-group col-md-6">
                                        <label for="check_date">Tanggal Pemeriksaan <span
                                                class="text-danger">*</span></label>
                                        <input id="check_date" type="date"
                                            class="form-control @error('check_date') is-invalid @enderror today-input"
                                            name="check_date" value="{{ date('Y-m-d') }}" readonly>
                                        @error('check_date')
                                            <div class="invalid-feedback">
                                                {{ $message }}
                                            </div>
                                        @enderror
                                    </div>

                                    <div class="form-group col-md-6">
                                        <label for="age_in_checks">Usia Saat Pemeriksaan <span
                                                class="text-danger">*</span></label>
                                        <input id="age_in_checks" type="text"
                                            class="form-control @error('age_in_checks') is-invalid @enderror"
                                            name="age_in_checks" value="{{ old('age_in_checks') }}"
                                            placeholder="0 tahun, 0 bulan, 0 hari" readonly>
                                        @error('age_in_checks')
                                            <div class="invalid-feedback">
                                                {{ $message }}
                                            </div>
                                        @enderror
                                    </div>

                                    <div class="form-group col-md-6">
                                        <label for="gestational_age">Usia Kehamilan (minggu) <span
                                                class="text-danger">*</span></label>
                                        <input id="gestational_age" type="text"
                                            class="form-control @error('gestational_age') is-invalid @enderror"
                                            name="gestational_age" value="{{ old('gestational_age') }}"
                                            placeholder="Contoh: 24">
                                        @error('gestational_age')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="form-group col-md-6">
                                        <label for="mother_weight">Berat Badan Ibu (kg) <span
                                                class="text-danger">*</span></label>
                                        <input id="mother_weight" type="text"
                                            class="form-control @error('mother_weight') is-invalid @enderror"
                                            name="mother_weight" value="{{ old('mother_weight') }}"
                                            placeholder="Contoh: 55.5">
                                        @error('mother_weight')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="form-group col-md-6">
                                        <label for="mother_height">Tinggi Ibu (cm) <span
                                                class="text-danger">*</span></label>
                                        <input id="mother_height" type="text"
                                            class="form-control @error('mother_height') is-invalid @enderror"
                                            name="mother_height" value="{{ old('mother_height') }}"
                                            placeholder="Contoh: 55.5">
                                        @error('mother_height')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="form-group col-md-6">
                                        <label for="imt_status" class="form-label">Index Masa Tubuh</label>
                                        <div class="input-group">
                                            <span class="input-group-text">Status</span>
                                            <input type="text" name="bmi_status" id="imt_status" value=""
                                                class="form-control" readonly>
                                            <span class="input-group-text">IMT</span>
                                            <input type="text" class="form-control" readonly id="bmi_value"
                                                value="" placeholder="0.0">
                                        </div>
                                    </div>

                                    <div class="form-group col-md-4">
                                        <label for="status_vaksin" class="form-label">Status Vaksin</label>
                                        <select name="status_vaksin" id="status_vaksin"
                                            class="form-select @error('status_vaksin') is-invalid @enderror">
                                            <option value="" selected disabled>-- Pilih Status Vaksin --</option>
                                            <option value="Sudah">Sudah Divaksin</option>
                                            {{-- <option value="Sekarang">Divaksin Sekarang</option> --}}
                                            <option value="Tidak">Belum Divaksin</option>
                                        </select>
                                        @error('status_vaksin')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="form-group col-md-4" id="jenis_vaksin_group" style="display: none;">
                                        <label for="jenis_vaksin">Jenis Vaksin <span
                                                class="text-danger">*</span></label>
                                        <select name="jenis_vaksin" id="jenis_vaksin"
                                            class="form-select @error('jenis_vaksin') is-invalid @enderror">
                                            <option value="" selected disabled>-- Pilih Jenis Vaksin --</option>
                                            <option value="Wajib"
                                                {{ old('jenis_vaksin') == 'Wajib' ? 'selected' : '' }}>
                                                Wajib</option>
                                            <option value="Tambahan"
                                                {{ old('jenis_vaksin') == 'Tambahan' ? 'selected' : '' }}>Tambahan
                                            </option>
                                            <option value="Khusus"
                                                {{ old('jenis_vaksin') == 'Khusus' ? 'selected' : '' }}>
                                                Khusus</option>
                                        </select>
                                        @error('jenis_vaksin')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="form-group col-md-4" id="vaccine_id_group" style="display: none;">
                                        <label for="vaccine_id">Nama Vaksin <span class="text-danger">*</span></label>
                                        <select name="vaccine_id" id="vaccine_id"
                                            class="form-select @error('vaccine_id') is-invalid @enderror">
                                            <option value="" selected disabled>-- Pilih Nama Vaksin --</option>
                                        </select>
                                        @error('vaccine_id')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <h5>Khusus Bidan</h5>

                                    @php
                                        $isAdmin = Auth::user()->role === 'admin';
                                        $readonly = $isAdmin ? 'readonly' : '';
                                    @endphp

                                    <div class="form-group col-md-6">
                                        <label for="blood_pressure">Tekanan Darah <span
                                                class="text-danger">*</span></label>
                                        <input id="blood_pressure" type="text"
                                            class="form-control @error('blood_pressure') is-invalid @enderror"
                                            name="blood_pressure" value="{{ old('blood_pressure') }}"
                                            placeholder="Contoh: 120/80" {{ $readonly }}>
                                        @error('blood_pressure')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="form-group col-md-6">
                                        <label for="pulse_rate">Denyut Nadi (bpm) <span
                                                class="text-danger">*</span></label>
                                        <input id="pulse_rate" type="text"
                                            class="form-control @error('pulse_rate') is-invalid @enderror"
                                            name="pulse_rate" value="{{ old('pulse_rate') }}" placeholder="Contoh: 80" {{ $readonly }}>
                                        @error('pulse_rate')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="form-group col-md-6">
                                        <label for="blood_sugar">Gula Darah (mg/dL)</label>
                                        <input id="blood_sugar" type="text"
                                            class="form-control @error('blood_sugar') is-invalid @enderror"
                                            name="blood_sugar" value="{{ old('blood_sugar') }}"
                                            placeholder="Contoh: 90.5" {{ $readonly }}>
                                        @error('blood_sugar')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="form-group col-md-6">
                                        <label for="cholesterol">Kolesterol (mg/dL)</label>
                                        <input id="cholesterol" type="text"
                                            class="form-control @error('cholesterol') is-invalid @enderror"
                                            name="cholesterol" value="{{ old('cholesterol') }}"
                                            placeholder="Contoh: 200.0" {{ $readonly }}>
                                        @error('cholesterol')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="form-group col-md-6">
                                        <label for="fundus_height">Tinggi Fundus (cm)</label>
                                        <input id="fundus_height" type="text"
                                            class="form-control @error('fundus_height') is-invalid @enderror"
                                            name="fundus_height" value="{{ old('fundus_height') }}"
                                            placeholder="Contoh: 30" {{ $readonly }}>
                                        @error('fundus_height')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="form-group col-md-6">
                                        <label for="fetal_heart_rate">Detak Jantung Janin (bpm)</label>
                                        <input id="fetal_heart_rate" type="text"
                                            class="form-control @error('fetal_heart_rate') is-invalid @enderror"
                                            name="fetal_heart_rate" value="{{ old('fetal_heart_rate') }}"
                                            placeholder="Contoh: 140" {{ $readonly }}>
                                        @error('fetal_heart_rate')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="form-group col-md-6">
                                        <label for="fetal_presentation">Presentasi Janin <span
                                                class="text-danger">*</span></label>
                                        <select name="fetal_presentation" id="fetal_presentation"
                                            class="form-control @error('fetal_presentation') is-invalid @enderror" {{ $readonly }}>
                                            <option value="" selected disabled>-- Pilih Presentasi Janin --</option>
                                            <option value="Kepala"
                                                {{ old('fetal_presentation') == 'Kepala' ? 'selected' : '' }}>Kepala
                                            </option>
                                            <option value="Bokong"
                                                {{ old('fetal_presentation') == 'Bokong' ? 'selected' : '' }}>Bokong
                                            </option>
                                            <option value="Lainnya"
                                                {{ old('fetal_presentation') == 'Lainnya' ? 'selected' : '' }}>Lainnya
                                            </option>
                                        </select>
                                        @error('fetal_presentation')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="form-group col-md-6">
                                        <label for="edema">Edema <span class="text-danger">*</span></label>
                                        <select name="edema" id="edema"
                                            class="form-control @error('edema') is-invalid @enderror" {{ $readonly }}>
                                            <option value="" selected disabled>-- Pilih Tingkat Edema --</option>
                                            <option value="Tidak" {{ old('edema') == 'Tidak' ? 'selected' : '' }}>Tidak
                                            </option>
                                            <option value="Ringan" {{ old('edema') == 'Ringan' ? 'selected' : '' }}>Ringan
                                            </option>
                                            <option value="Sedang" {{ old('edema') == 'Sedang' ? 'selected' : '' }}>Sedang
                                            </option>
                                            <option value="Berat" {{ old('edema') == 'Berat' ? 'selected' : '' }}>Berat
                                            </option>
                                        </select>
                                        @error('edema')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="form-group col-md-6">
                                        <label for="notes">Keterangan</label>
                                        <textarea name="notes" id="notes" class="form-control @error('notes') is-invalid @enderror summernote-simple"
                                            rows="5" data-placeholder="Jika ada keterangan atau catatan silakan isi" {{ $readonly }}>{{ old('notes') }}</textarea>
                                        @error('notes')
                                            <div class="invalid-feedback">
                                                {{ $message }}
                                            </div>
                                        @enderror
                                    </div>
                                </div>

                                <input type="hidden" name="officer_id" id="officer_id"
                                    value="{{ Auth::user()->officer_id }}">

                                <div class="col-12">
                                    <div class="d-flex justify-content-center justify-content-md-end align-items-center"
                                        style="gap: .5rem">
                                        <a href="{{ url('/pregnancy-check-data') }}"
                                            class="btn btn-secondary">Kembali</a>
                                        <button type="submit" class="btn btn-primary">Simpan</button>
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
    <script src="{{ asset('modules/summernote/summernote-bs4.js') }}"></script>

    <script>
        $(document).ready(function() {
            // Function to calculate BMI and status
            function calculateBMI() {
                const weight = parseFloat($('#mother_weight').val());
                const height = parseFloat($('#mother_height').val());

                if (weight && height && height > 0) {
                    const heightInMeters = height / 100;
                    const bmi = weight / (heightInMeters * heightInMeters);
                    const roundedBMI = Math.round(bmi * 100) / 100;

                    // Set BMI value
                    $('#bmi_value').val(roundedBMI);

                    // Determine BMI status
                    let status = '';
                    if (bmi < 18.5) {
                        status = 'Kurang Berat Badan';
                    } else if (bmi >= 18.5 && bmi < 25) {
                        status = 'Normal';
                    } else if (bmi >= 25 && bmi < 30) {
                        status = 'Kelebihan Berat Badan';
                    } else if (bmi >= 30 && bmi < 40) {
                        status = 'Obesitas';
                    } else if (bmi >= 40 && bmi < 50) {
                        status = 'Obesitas Parah';
                    } else {
                        status = 'Obesitas Ekstrem';
                    }

                    $('#imt_status').val(status);
                } else {
                    $('#imt_status').val('');
                    $('#bmi_value').val('');
                }
            }

            // Event listeners for BMI calculation
            $('#mother_weight, #mother_height').on('input', function() {
                calculateBMI();
            });

            // Handle vaccine status change
            $('#status_vaksin').on('change', function() {
                const selectedValue = $(this).val();

                if (selectedValue === 'Sekarang') {
                    $('#jenis_vaksin_group').show();
                    $('#vaccine_id_group').show();
                    $('#jenis_vaksin').prop('required', true);
                    $('#vaccine_id').prop('required', true);
                    $('#vaccine_id').prop('disabled', false);
                } else {
                    $('#jenis_vaksin_group').hide();
                    $('#vaccine_id_group').hide();
                    $('#jenis_vaksin').prop('required', false);
                    $('#vaccine_id').prop('required', false);
                    $('#jenis_vaksin').val('');
                    $('#vaccine_id').val('');
                    $('#vaccine_id').empty().append(
                        '<option value="" selected disabled>-- Pilih Nama Vaksin --</option>');
                    $('#vaccine_id').prop('disabled', true);
                    $('#vaccine_id_group .text-danger').remove();
                }
            });

            // Handle vaccine type change
            $('#jenis_vaksin').on('change', function() {
                const selectedType = $(this).val();

                if (selectedType) {
                    // Clear vaccine options
                    $('#vaccine_id').empty().append(
                        '<option value="" selected disabled>-- Loading... --</option>');

                    // Fetch vaccines by type
                    $.ajax({
                        url: `/api/vaccines/type/${selectedType}`,
                        method: 'GET',
                        success: function(response) {
                            $('#vaccine_id').empty().append(
                                '<option value="" selected disabled>-- Pilih Nama Vaksin --</option>'
                            );

                            if (response.length > 0) {
                                response.forEach(function(vaccine) {
                                    $('#vaccine_id').append(
                                        `<option value="${vaccine.id}">${vaccine.vaccine_name} (Stok: ${vaccine.stock})</option>`
                                    );
                                });
                            } else {
                                $('#vaccine_id').append(
                                    '<option value="" disabled>Tidak ada vaksin tersedia</option>'
                                );
                            }
                        },
                        error: function() {
                            $('#vaccine_id').empty().append(
                                '<option value="" disabled>Error loading vaccines</option>');
                        }
                    });
                } else {
                    $('#vaccine_id').empty().append(
                        '<option value="" selected disabled>-- Pilih Nama Vaksin --</option>');
                }
            });

            // Initialize on page load if editing
            if ($('#status_vaksin').val() === 'Sekarang') {
                $('#jenis_vaksin_group').show();
                $('#vaccine_id_group').show();
            }

            // Calculate BMI on page load if values exist
            if ($('#mother_weight').val() && $('#mother_height').val()) {
                calculateBMI();
            }

            // Existing functions for parent data and age calculation
            function fillChildData() {
                const selected = $('#parent_id option:selected');

                if (selected.length === 0) return;

                $('#mother_birth_place').val(selected.data('mother_birth_place'));
                $('#mother_date_of_birth').val(selected.data('mother_date_of_birth'));

                calculateAndFillAge();
            }

            function calculateAndFillAge() {
                const dobInput = document.querySelector('.dob-input')?.value;
                const todayInput = document.querySelector('.today-input')?.value;

                if (!dobInput || !todayInput) return;

                const dob = new Date(dobInput);
                const today = new Date(todayInput);

                const age = calculateAge(dob, today);
                $('#age_in_checks').val(age);
            }

            function calculateAge(dob, today) {
                let years = today.getFullYear() - dob.getFullYear();
                let months = today.getMonth() - dob.getMonth();
                let days = today.getDate() - dob.getDate();

                if (days < 0) {
                    months--;
                    days += new Date(today.getFullYear(), today.getMonth(), 0).getDate();
                }

                if (months < 0) {
                    years--;
                    months += 12;
                }

                return `${years} tahun, ${months} bulan, ${days} hari`;
            }

            // Triggers for parent data
            if ($('#parent_id').val()) {
                fillChildData();
            }

            $('#parent_id').change(function() {
                fillChildData();
            });

            $('.dob-input').on('change', function() {
                calculateAndFillAge();
            });

            $('.today-input').on('change', function() {
                calculateAndFillAge();
            });
        });
    </script>
@endpush
