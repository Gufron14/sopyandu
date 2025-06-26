@extends('layouts.dashboard')

@section('title', 'Ubah Pemeriksaan')

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
                        <div class="card-body">
                            <form action="{{ url("/elderly-check-data/{$elderlyCheck->id}") }}" method="POST">
                                @csrf
                                @method('PUT')
                                <div class="row g-4">
                                    <div class="col-12">
                                        <h5 class="card-title">Biodata</h5>
                                    </div>

                                    <div class="form-group col-md-6">
                                        <label for="elderly_id">Nama Lansia <span class="text-danger">*</span></label>
                                        <select name="elderly_id" id="elderly_id"
                                            class="form-control select2 @error('elderly_id') is-invalid @enderror">
                                            <option value="" selected disabled>-- Pilih --
                                            </option>
                                            @foreach ($elderlies as $elderly)
                                                <option value="{{ $elderly->id }}" data-gender="{{ $elderly->gender }}"
                                                    data-birth_place="{{ $elderly->birth_place }}"
                                                    data-date_of_birth="{{ $elderly->date_of_birth }}"
                                                    {{ old('elderly_id', $elderlyCheck->elderly_id) == $elderly->id ? 'selected' : '' }}>
                                                    {{ $elderly->fullname . ' - ' . $elderly->nik }}
                                                </option>
                                            @endforeach
                                        </select>
                                        @error('elderly_id')
                                            <div class="invalid-feedback">
                                                {{ $message }}
                                            </div>
                                        @enderror
                                    </div>

                                    <div class="form-group col-md-6">
                                        <label for="gender">Jenis Kelamin <span class="text-danger">*</span></label>
                                        <select name="gender" id="gender" class="form-control" disabled>
                                            <option value="" selected disabled>-- Pilih Jenis Kelamin --</option>
                                            <option value="L">Laki - Laki</option>
                                            <option value="P">Perempuan</option>
                                        </select>
                                    </div>

                                    <div class="form-group col-md-6">
                                        <label for="birth_place">Tempat Lahir <span class="text-danger">*</span></label>
                                        <input id="birth_place" type="text" class="form-control" name="birth_place"
                                            value="" placeholder="Jakarta" disabled>
                                    </div>

                                    <div class="form-group col-md-6">
                                        <label for="date_of_birth">Tanggal Lahir <span class="text-danger">*</span></label>
                                        <input id="date_of_birth" type="date" class="form-control dob-input"
                                            name="date_of_birth" value="" disabled>
                                    </div>

                                    <div class="col-12">
                                        <hr>
                                        <h5 class="card-title">Pemeriksaan</h5>
                                    </div>

                                    <div class="form-group col-md-6">
                                        <label for="check_date">Tanggal Pemeriksaan <span
                                                class="text-danger">*</span></label>
                                        <input id="check_date" type="date"
                                            class="form-control @error('check_date') is-invalid @enderror today-input"
                                            name="check_date" value="{{ old('check_date', $elderlyCheck->check_date) }}">
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

                                    <div class="form-group col-md-4">
                                        <label for="body_weight">Berat Badan (kg) <span class="text-danger">*</span></label>
                                        <input id="body_weight" type="text"
                                            class="form-control @error('body_weight') is-invalid @enderror"
                                            name="body_weight" value="{{ old('body_weight', $elderlyCheck->body_weight) }}"
                                            placeholder="Contoh: 55.5">
                                        @error('body_weight')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="form-group col-md-4">
                                        <label for="blood_pressure">Tekanan Darah <span class="text-danger">*</span></label>
                                        <input id="blood_pressure" type="text"
                                            class="form-control @error('blood_pressure') is-invalid @enderror"
                                            name="blood_pressure"
                                            value="{{ old('blood_pressure', $elderlyCheck->blood_pressure) }}"
                                            placeholder="Contoh: 120/80">
                                        @error('blood_pressure')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="form-group col-md-4">
                                        <label for="pulse_rate">Denyut Nadi (bpm) <span
                                                class="text-danger">*</span></label>
                                        <input id="pulse_rate" type="text"
                                            class="form-control @error('pulse_rate') is-invalid @enderror"
                                            name="pulse_rate" value="{{ old('pulse_rate', $elderlyCheck->pulse_rate) }}"
                                            placeholder="Contoh: 80">
                                        @error('pulse_rate')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="form-group col-md-4">
                                        <label for="blood_sugar">Gula Darah (mg/dL)</label>
                                        <input id="blood_sugar" type="text"
                                            class="form-control @error('blood_sugar') is-invalid @enderror"
                                            name="blood_sugar"
                                            value="{{ old('blood_sugar', $elderlyCheck->blood_sugar) }}"
                                            placeholder="Contoh: 90.5">
                                        @error('blood_sugar')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="form-group col-md-4">
                                        <label for="cholesterol">Kolesterol (mg/dL)</label>
                                        <input id="cholesterol" type="text"
                                            class="form-control @error('cholesterol') is-invalid @enderror"
                                            name="cholesterol"
                                            value="{{ old('cholesterol', $elderlyCheck->cholesterol) }}"
                                            placeholder="Contoh: 200.0">
                                        @error('cholesterol')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="form-group col-md-4">
                                        <label for="uric_acid">Asam Urat (mg/dL)</label>
                                        <input id="uric_acid" type="text"
                                            class="form-control @error('uric_acid') is-invalid @enderror" name="uric_acid"
                                            value="{{ old('uric_acid', $elderlyCheck->uric_acid) }}"
                                            placeholder="Contoh: 7.2">
                                        @error('uric_acid')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="form-group col-md-4">
                                        <label for="mobility_status">Status Mobilitas <span
                                                class="text-danger">*</span></label>
                                        <select name="mobility_status" id="mobility_status"
                                            class="form-control @error('mobility_status') is-invalid @enderror">
                                            <option value="" disabled selected>-- Pilih Status Mobilitas --</option>
                                            <option value="Mandiri"
                                                {{ old('mobility_status', $elderlyCheck->mobility_status) == 'Mandiri' ? 'selected' : '' }}>
                                                Mandiri
                                            </option>
                                            <option value="Bantuan Alat"
                                                {{ old('mobility_status', $elderlyCheck->mobility_status) == 'Bantuan Alat' ? 'selected' : '' }}>
                                                Bantuan
                                                Alat</option>
                                            <option value="Dibantu Orang Lain"
                                                {{ old('mobility_status', $elderlyCheck->mobility_status) == 'Dibantu Orang Lain' ? 'selected' : '' }}>
                                                Dibantu Orang Lain</option>
                                        </select>
                                        @error('mobility_status')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="form-group col-md-4">
                                        <label for="cognitive_status">Status Kognitif <span
                                                class="text-danger">*</span></label>
                                        <select name="cognitive_status" id="cognitive_status"
                                            class="form-control @error('cognitive_status') is-invalid @enderror">
                                            <option value="" disabled selected>-- Pilih Status Kognitif --</option>
                                            <option value="Normal"
                                                {{ old('cognitive_status', $elderlyCheck->cognitive_status) == 'Normal' ? 'selected' : '' }}>
                                                Normal</option>
                                            <option value="Penurunan Ringan"
                                                {{ old('cognitive_status', $elderlyCheck->cognitive_status) == 'Penurunan Ringan' ? 'selected' : '' }}>
                                                Penurunan Ringan</option>
                                            <option value="Demensia"
                                                {{ old('cognitive_status', $elderlyCheck->cognitive_status) == 'Demensia' ? 'selected' : '' }}>
                                                Demensia
                                            </option>
                                        </select>
                                        @error('cognitive_status')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="form-group col-md-4">
                                        <label for="nutritional_status">Status Gizi <span
                                                class="text-danger">*</span></label>
                                        <select name="nutritional_status" id="nutritional_status"
                                            class="form-control @error('nutritional_status') is-invalid @enderror">
                                            <option value="" disabled selected>-- Pilih Status Gizi --</option>
                                            <option value="Baik"
                                                {{ old('nutritional_status', $elderlyCheck->nutritional_status) == 'Baik' ? 'selected' : '' }}>
                                                Baik</option>
                                            <option value="Kurang"
                                                {{ old('nutritional_status', $elderlyCheck->nutritional_status) == 'Kurang' ? 'selected' : '' }}>
                                                Kurang
                                            </option>
                                            <option value="Lebih"
                                                {{ old('nutritional_status', $elderlyCheck->nutritional_status) == 'Lebih' ? 'selected' : '' }}>
                                                Lebih</option>
                                        </select>
                                        @error('nutritional_status')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="form-group col-md-6">
                                        <label for="notes">Keterangan</label>
                                        <textarea name="notes" id="notes" class="form-control @error('notes') is-invalid @enderror summernote-simple"
                                            rows="5" data-placeholder="Jika ada keterangan atau catatan silakan isi">{{ old('notes', $elderlyCheck->notes) }}</textarea>
                                        @error('notes')
                                            <div class="invalid-feedback">
                                                {{ $message }}
                                            </div>
                                        @enderror
                                    </div>

                                    <input type="hidden" name="officer_id" id="officer_id"
                                        value="{{ $elderlyCheck->officer_id }}">

                                    <div class="col-12">
                                        <div class="d-flex justify-content-center justify-content-md-end align-items-center"
                                            style="gap: .5rem">
                                            <a href="{{ url('/elderly-check-data') }}"
                                                class="btn btn-secondary">Kembali</a>
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
    <script src="{{ asset('modules/summernote/summernote-bs4.js') }}"></script>

    <script>
        $(document).ready(function() {
            // Fungsi untuk mengisi data dan menghitung usia
            function fillChildData() {
                const selected = $('#elderly_id option:selected');

                if (selected.length === 0) return;

                $('#gender').val(selected.data('gender'));
                $('#birth_place').val(selected.data('birth_place'));
                $('#date_of_birth').val(selected.data('date_of_birth'));

                calculateAndFillAge();
            }

            // Fungsi untuk menghitung dan mengisi usia
            function calculateAndFillAge() {
                const dobInput = document.querySelector('.dob-input')?.value;
                const todayInput = document.querySelector('.today-input')?.value;

                if (!dobInput || !todayInput) return;

                const dob = new Date(dobInput);
                const today = new Date(todayInput);

                const age = calculateAge(dob, today);
                $('#age_in_checks').val(age);
            }

            // Fungsi hitung usia dari dua tanggal
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

            // ========== TRIGGERS ==========

            // Saat halaman dimuat (edit mode), isi otomatis jika sudah ada terpilih
            if ($('#elderly_id').val()) {
                fillChildData();
            }

            // Saat user memilih (create mode atau edit mode)
            $('#elderly_id').change(function() {
                fillChildData();
            });

            // Saat tanggal lahir diubah (input manual atau ganti)
            $('.dob-input').on('change', function() {
                calculateAndFillAge();
            });

            // Saat tanggal Pemeriksaan diubah (opsional, jika field bisa diedit)
            $('.today-input').on('change', function() {
                calculateAndFillAge();
            });
        });
    </script>
@endpush
