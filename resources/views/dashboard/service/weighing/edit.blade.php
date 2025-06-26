@extends('layouts.dashboard')

@section('title', 'Ubah Penimbangan')

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
                            <form action="{{ url("/weighing-data/{$weighing->id}") }}" method="POST">
                                @csrf
                                @method('PUT')
                                <div class="row g-4">
                                    <div class="col-12">
                                        <h5 class="card-title">Biodata</h5>
                                    </div>

                                    <div class="form-group col-md-6">
                                        <label for="children_id">Nama Anak <span class="text-danger">*</span></label>
                                        <select name="children_id" id="children_id"
                                            class="form-control select2 @error('children_id') is-invalid @enderror">
                                            <option value="" selected disabled>-- Pilih Nama Anak --
                                            </option>
                                            @foreach ($children as $child)
                                                <option value="{{ $child->id }}" data-gender="{{ $child->gender }}"
                                                    data-birth_place="{{ $child->birth_place }}"
                                                    data-date_of_birth="{{ $child->date_of_birth }}"
                                                    data-mother_nik="{{ $child->familyParents->nik ?? '' }}"
                                                    data-mother_fullname="{{ $child->familyParents->mother_fullname ?? '' }}"
                                                    {{ old('children_id', $weighing->children_id) == $child->id ? 'selected' : '' }}>
                                                    {{ $child->fullname . ' - ' . $child->nik }}
                                                </option>
                                            @endforeach
                                        </select>
                                        @error('children_id')
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

                                    <div class="form-group col-md-6">
                                        <label for="mother_nik">Nomor Induk Keluarga (NIK) Ibu <span
                                                class="text-danger">*</span></label>
                                        <input id="mother_nik" type="number" class="form-control" name="mother_nik"
                                            value="" placeholder="5271xxxxxxxxxxxx" disabled>
                                    </div>

                                    <div class="form-group col-md-6">
                                        <label for="mother_fullname">Nama Lengkap Ibu <span
                                                class="text-danger">*</span></label>
                                        <input id="mother_fullname" type="text" class="form-control"
                                            name="mother_fullname" pattern="[A-Za-z ]+" value="" placeholder="Imas" disabled>
                                    </div>

                                    <div class="col-12">
                                        <hr>
                                        <h5 class="card-title">Penimbangan</h5>
                                    </div>

                                    <div class="form-group col-md-6">
                                        <label for="weighing_date">Tanggal Penimbangan <span
                                                class="text-danger">*</span></label>
                                        <input id="weighing_date" type="date"
                                            class="form-control @error('weighing_date') is-invalid @enderror today-input"
                                            name="weighing_date"
                                            value="{{ old('weighing_date', $weighing->weighing_date) }}">
                                        @error('weighing_date')
                                            <div class="invalid-feedback">
                                                {{ $message }}
                                            </div>
                                        @enderror
                                    </div>

                                    <div class="form-group col-md-6">
                                        <label for="age_in_checks">Usia Saat Penimbangan <span
                                                class="text-danger">*</span></label>
                                        <input id="age_in_checks" type="text"
                                            class="form-control @error('age_in_checks') is-invalid @enderror"
                                            name="age_in_checks"
                                            value="{{ old('age_in_checks', $weighing->age_in_checks) }}"
                                            placeholder="0 tahun, 0 bulan, 0 hari" readonly>
                                        @error('age_in_checks')
                                            <div class="invalid-feedback">
                                                {{ $message }}
                                            </div>
                                        @enderror
                                    </div>

                                    <div class="form-group col-md-6">
                                        <label for="weight">Berat Badan (kg) <span class="text-danger">*</span></label>
                                        <input id="weight" type="text"
                                            class="form-control @error('weight') is-invalid @enderror" name="weight"
                                            value="{{ old('weight', $weighing->weight) }}"
                                            placeholder="Contoh: 5 atau 10.55">
                                        @error('weight')
                                            <div class="invalid-feedback">
                                                {{ $message }}
                                            </div>
                                        @enderror
                                    </div>

                                    <div class="form-group col-md-6">
                                        <label for="height">Tinggi Badan (cm) <span class="text-danger">*</span></label>
                                        <input id="height" type="text"
                                            class="form-control @error('height') is-invalid @enderror" name="height"
                                            value="{{ old('height', $weighing->height) }}"
                                            placeholder="Contoh: 50 atau 75.55">
                                        @error('height')
                                            <div class="invalid-feedback">
                                                {{ $message }}
                                            </div>
                                        @enderror
                                    </div>

                                    <div class="form-group col-md-6">
                                        <label for="head_circumference">Ukuran Lingkar Kepala (cm) <span
                                                class="text-danger">*</span></label>
                                        <input id="head_circumference" type="text"
                                            class="form-control @error('head_circumference') is-invalid @enderror"
                                            name="head_circumference"
                                            value="{{ old('head_circumference', $weighing->head_circumference) }}"
                                            placeholder="Contoh: 20 atau 30.75">
                                        @error('head_circumference')
                                            <div class="invalid-feedback">
                                                {{ $message }}
                                            </div>
                                        @enderror
                                    </div>

                                    <div class="form-group col-md-6">
                                        <label for="arm_circumference">Ukuran Lingkar Lengan (cm) <span
                                                class="text-danger">*</span></label>
                                        <input id="arm_circumference" type="text"
                                            class="form-control @error('arm_circumference') is-invalid @enderror"
                                            name="arm_circumference"
                                            value="{{ old('arm_circumference', $weighing->arm_circumference) }}"
                                            placeholder="Contoh: 7 atau 10.75">
                                        @error('arm_circumference')
                                            <div class="invalid-feedback">
                                                {{ $message }}
                                            </div>
                                        @enderror
                                    </div>

                                    <div class="form-group col-md-6">
                                        <label for="nutrition_status">Status Gizi <span
                                                class="text-danger">*</span></label>
                                        <select name="nutrition_status" id="nutrition_status"
                                            class="form-control @error('nutrition_status') is-invalid @enderror">
                                            <option value="" selected disabled>-- Pilih Status Gizi --
                                            </option>
                                            <option value="Baik"
                                                {{ old('nutrition_status', $weighing->nutrition_status) == 'Baik' ? 'selected' : '' }}>
                                                Baik
                                            </option>
                                            <option value="Buruk"
                                                {{ old('nutrition_status', $weighing->nutrition_status) == 'Buruk' ? 'selected' : '' }}>
                                                Buruk
                                            </option>
                                            <option value="Kurang"
                                                {{ old('nutrition_status', $weighing->nutrition_status) == 'Kurang' ? 'selected' : '' }}>
                                                Kurang
                                            </option>
                                            <option value="Lebih"
                                                {{ old('nutrition_status', $weighing->nutrition_status) == 'Lebih' ? 'selected' : '' }}>
                                                Lebih
                                            </option>
                                        </select>
                                        @error('nutrition_status')
                                            <div class="invalid-feedback">
                                                {{ $message }}
                                            </div>
                                        @enderror
                                    </div>

                                    <div class="form-group col-md-6">
                                        <label for="notes">Keterangan</label>
                                        <textarea name="notes" id="notes" class="form-control @error('notes') is-invalid @enderror summernote-simple"
                                            rows="5" data-placeholder="Jika ada keterangan atau catatan silakan isi">{{ old('notes', $weighing->notes) }}</textarea>
                                        @error('notes')
                                            <div class="invalid-feedback">
                                                {{ $message }}
                                            </div>
                                        @enderror
                                    </div>

                                    <input type="hidden" name="officer_id" id="officer_id"
                                        value="{{ $weighing->officer_id }}">

                                    <div class="col-12">
                                        <div class="d-flex justify-content-center justify-content-md-end align-items-center"
                                            style="gap: .5rem">
                                            <a href="{{ url('/weighing-data') }}" class="btn btn-secondary">Kembali</a>
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
            // Fungsi untuk mengisi data anak dan menghitung usia
            function fillChildData() {
                const selected = $('#children_id option:selected');

                if (selected.length === 0) return;

                $('#gender').val(selected.data('gender'));
                $('#birth_place').val(selected.data('birth_place'));
                $('#date_of_birth').val(selected.data('date_of_birth'));
                $('#mother_nik').val(selected.data('mother_nik'));
                $('#mother_fullname').val(selected.data('mother_fullname'));

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

            // Saat halaman dimuat (edit mode), isi otomatis jika sudah ada anak terpilih
            if ($('#children_id').val()) {
                fillChildData();
            }

            // Saat user memilih anak (create mode atau edit mode)
            $('#children_id').change(function() {
                fillChildData();
            });

            // Saat tanggal lahir diubah (input manual atau ganti anak)
            $('.dob-input').on('change', function() {
                calculateAndFillAge();
            });

            // Saat tanggal penimbangan diubah (opsional, jika field bisa diedit)
            $('.today-input').on('change', function() {
                calculateAndFillAge();
            });
        });
    </script>
@endpush
