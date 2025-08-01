@extends('layouts.dashboard')

@section('title', 'Ubah Imunisasi')

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
                            <form action="{{ url("/immunization-data/{$immunization->id}") }}" method="POST">
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
                                                    {{ old('children_id', $immunization->children_id) == $child->id ? 'selected' : '' }}>
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
                                        <h5 class="card-title">Imunisasi</h5>
                                    </div>

                                    <div class="form-group col-md-4">
                                        <label for="immunization_date">Tanggal Imunisasi <span
                                                class="text-danger">*</span></label>
                                        <input id="immunization_date" type="date"
                                            class="form-control @error('immunization_date') is-invalid @enderror today-input"
                                            name="immunization_date"
                                            value="{{ old('immunization_date', $immunization->immunization_date) }}">
                                        @error('immunization_date')
                                            <div class="invalid-feedback">
                                                {{ $message }}
                                            </div>
                                        @enderror
                                    </div>

                                    <div class="form-group col-md-4">
                                        <label for="age_in_checks">Usia Saat Imunisasi <span
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
                                        <label for="vaccine_status">Status Vaksinasi <span
                                                class="text-danger">*</span></label>
                                        @php
                                            $vaccine_status =
                                                old('vaccine_status') ??
                                                (is_null($immunization->vaccine_id) || empty($immunization->vaccine_id)
                                                    ? 'Tidak'
                                                    : 'Ya');
                                        @endphp
                                        <select name="vaccine_status" id="vaccine_status"
                                            class="form-control @error('vaccine_status') is-invalid @enderror">
                                            <option value="" selected disabled>-- Pilih Status Vaksinasi --
                                            </option>
                                            <option value="Ya"
                                                {{ old('vaccine_status', $vaccine_status) == 'Ya' ? 'selected' : '' }}>
                                                Divaksin
                                            </option>
                                            <option value="Tidak"
                                                {{ old('vaccine_status', $vaccine_status) == 'Tidak' ? 'selected' : '' }}>
                                                Tidak Divaksin
                                            </option>
                                        </select>
                                        @error('vaccine_status')
                                            <div class="invalid-feedback">
                                                {{ $message }}
                                            </div>
                                        @enderror
                                    </div>

                                    <div class="form-group col-md-6 vaccine-section d-none">
                                        <label for="vaccine_type">Kategori Vaksinasi <span
                                                class="text-danger">*</span></label>
                                        <select name="vaccine_type" id="vaccine_type"
                                            class="form-control @error('vaccine_type') is-invalid @enderror" disabled>
                                            <option value="" selected disabled>-- Pilih Kategori Vaksinasi --
                                            </option>
                                            <option value="Wajib"
                                                {{ old('vaccine_type', $immunization->vaccine_category) == 'Wajib' ? 'selected' : '' }}>
                                                Wajib
                                            </option>
                                            <option value="Tambahan"
                                                {{ old('vaccine_type', $immunization->vaccine_category) == 'Tambahan' ? 'selected' : '' }}>
                                                Tambahan
                                            </option>
                                            <option value="Khusus"
                                                {{ old('vaccine_type', $immunization->vaccine_category) == 'Khusus' ? 'selected' : '' }}>
                                                Khusus
                                            </option>
                                            <option value="Lainnya"
                                                {{ old('vaccine_type', $immunization->vaccine_category) == 'Lainnya' ? 'selected' : '' }}>
                                                Lainnya
                                            </option>
                                        </select>
                                        @error('vaccine_type')
                                            <div class="invalid-feedback">
                                                {{ $message }}
                                            </div>
                                        @enderror
                                    </div>

                                    <div class="form-group col-md-6 vaccine-section d-none">
                                        <label for="vaccine_id">Nama Vaksin <span class="text-danger">*</span></label>
                                        <select name="vaccine_id" id="vaccine_id"
                                            class="form-control select2 @error('vaccine_id') is-invalid @enderror"
                                            disabled>
                                            <option value="" selected disabled>-- Pilih Nama Vaksin --
                                            </option>
                                            @foreach ($vaccines as $vaccine)
                                            <option value="{{ $vaccine->id }}" 
                                            data-unit="{{ $vaccine->unit }}"
                                            data-stock="{{ $vaccine->stock }}"
                                            data-entry_date="{{ $vaccine->entry_date }}"
                                            data-expiry_date="{{ $vaccine->expiry_date }}"
                                            data-vaccine_type="{{ $vaccine->vaccine_type }}"
                                                {{ old('vaccine_id', $immunization->vaccine_id) == $vaccine->id ? 'selected' : '' }}>
                                                    {{ $vaccine->vaccine_name . ', Kedaluwarsa: ' . \Carbon\Carbon::parse($vaccine->expiry_date)->locale('id')->isoFormat('D MMMM YYYY') }}
                                                </option>
                                            @endforeach
                                        </select>
                                        @error('vaccine_id')
                                            <div class="invalid-feedback">
                                                {{ $message }}
                                            </div>
                                        @enderror
                                    </div>

                                    <div class="form-group col-md-3 vaccine-section d-none">
                                        <label for="unit">Unit Vaksin <span class="text-danger">*</span></label>
                                        <input id="unit" type="text" class="form-control" name="unit"
                                            value="" placeholder="dosis, vial, atau unit yang relevan" disabled>
                                    </div>

                                    <div class="form-group col-md-3 vaccine-section d-none">
                                        <label for="stock">Stok Vaksin <span class="text-danger">*</span></label>
                                        <input id="stock" type="number"
                                            class="form-control @error('stock') is-invalid @enderror" name="stock"
                                            name="stock" value="" placeholder="0" disabled>
                                        @error('stock')
                                            <div class="invalid-feedback">
                                                {{ $message }}
                                            </div>
                                        @enderror
                                        <div class="invalid-feedback d-none" id="error-stock"></div>
                                    </div>

                                    <div class="form-group col-md-3 vaccine-section d-none">
                                        <label for="entry_date">Tanggal Masuk Vaksin <span
                                                class="text-danger">*</span></label>
                                        <input id="entry_date" type="date" class="form-control" name="entry_date"
                                            value="" disabled>
                                    </div>

                                    <div class="form-group col-md-3 vaccine-section d-none">
                                        <label for="expiry_date">Tanggal Kedaluwarsa Vaksin <span
                                                class="text-danger">*</span></label>
                                        <input id="expiry_date" type="date" class="form-control" name="expiry_date"
                                            value="" disabled>
                                    </div>

                                    <div class="form-group col-md-6 vaccine-section d-none">
                                        <label for="side_effects">Efek Samping</label>
                                        <textarea name="side_effects" id="side_effects"
                                            class="form-control @error('side_effects') is-invalid @enderror summernote-simple" rows="5" disabled
                                            data-placeholder="Jika ada efek samping, silakan isi pada kegiatan posyandu berikutnya">{{ old('side_effects', $immunization->side_effects) }}</textarea>
                                        @error('side_effects')
                                            <div class="invalid-feedback">
                                                {{ $message }}
                                            </div>
                                        @enderror
                                    </div>

                                    <div class="form-group col-md-6">
                                        <label for="notes">Keterangan</label>
                                        <textarea name="notes" id="notes" class="form-control @error('notes') is-invalid @enderror summernote-simple"
                                            rows="5" data-placeholder="Jika ada keterangan atau catatan silakan isi">{{ old('notes', $immunization->notes) }}</textarea>
                                        @error('notes')
                                            <div class="invalid-feedback">
                                                {{ $message }}
                                            </div>
                                        @enderror
                                    </div>

                                    <input type="hidden" name="officer_id" id="officer_id"
                                        value="{{ $immunization->officer_id }}">

                                    <div class="col-12">
                                        <div class="d-flex justify-content-center justify-content-md-end align-items-center"
                                            style="gap: .5rem">
                                            <a href="{{ url('/immunization-data') }}"
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
            // ========== BIODATA ==========

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

            // ========== VAKSINASI ==========
            function toggleVaccineSection(status) {
                if (status === 'Ya') {
                    $('.vaccine-section').removeClass('d-none'); // Tampilkan semua

                    // Tunggu sedikit agar DOM update selesai
                    setTimeout(() => {
                        $('#vaccine_id').prop('disabled', false).select2(); // Reinit select2
                    }, 10);

                    $('#vaccine_type, #side_effects').prop('disabled', false);
                } else {
                    $('.vaccine-section').addClass('d-none'); // Sembunyikan semua
                    $('#vaccine_type, #vaccine_id, #unit, #stock, #entry_date, #expiry_date, #side_effects')
                        .val('')
                        .prop('disabled', true);

                    // Destroy Select2 agar tidak konflik
                    $('#vaccine_id').select2('destroy');
                }
            }

            function fillVaccineDetails() {
                const selected = $('#vaccine_id option:selected');
                $('#unit').val(selected.data('unit'));
                $('#stock').val(selected.data('stock'));
                $('#entry_date').val(selected.data('entry_date'));
                $('#expiry_date').val(selected.data('expiry_date'));
                $('#stock').trigger('input'); // ← Trigger pengecekan stok otomatis
            }

            function loadVaccinesByType(selectedType) {
                if (!selectedType) {
                    $('#vaccine_id').empty().append('<option value="" selected disabled>-- Pilih Nama Vaksin --</option>');
                    $('#vaccine_id').prop('disabled', true);
                    return;
                }

                // Show loading state
                $('#vaccine_id').empty().append('<option value="" selected disabled>Loading...</option>');

                // Fetch vaccines by type via AJAX
                $.ajax({
                    url: `/api/vaccines/type/${selectedType}`,
                    method: 'GET',
                    success: function(vaccines) {
                        $('#vaccine_id').empty().append('<option value="" selected disabled>-- Pilih Nama Vaksin --</option>');
                        
                        vaccines.forEach(function(vaccine) {
                            const isSelected = vaccine.id == {{ $immunization->vaccine_id ?: 'null' }};
                            $('#vaccine_id').append(`
                                <option value="${vaccine.id}" 
                                    data-unit="${vaccine.unit}"
                                    data-stock="${vaccine.stock}"
                                    data-entry_date="${vaccine.entry_date}"
                                    data-expiry_date="${vaccine.expiry_date}"
                                    data-vaccine_type="${vaccine.vaccine_type}"
                                    ${isSelected ? 'selected' : ''}>
                                    ${vaccine.display_text}
                                </option>
                            `);
                        });

                        $('#vaccine_id').prop('disabled', false);

                        // Fill vaccine details if vaccine is selected
                        if ($('#vaccine_id').val()) {
                            fillVaccineDetails();
                        }
                    },
                    error: function() {
                        $('#vaccine_id').empty().append('<option value="" selected disabled>Error loading vaccines</option>');
                        console.error('Failed to load vaccines');
                    }
                });
            }

            $('#vaccine_type').on('change', function() {
                const selectedType = $(this).val();
                loadVaccinesByType(selectedType);
            });

            // Trigger saat nilai status vaksinasi berubah
            $('#vaccine_status').on('change', function() {
                const status = $(this).val();
                toggleVaccineSection(status);
            });

            // Trigger saat nama vaksin dipilih
            $('#vaccine_id').on('change', function() {
                fillVaccineDetails();
            });

            // Saat halaman dimuat, cek apakah ada nilai sebelumnya (untuk edit form)
            const selectedStatus = $('#vaccine_status').val();
            if (selectedStatus) {
                toggleVaccineSection(selectedStatus);

                const selectedType = $('#vaccine_type').val();
                if (selectedType) {
                    loadVaccinesByType(selectedType);
                } else if ($('#vaccine_id').val()) {
                    fillVaccineDetails();
                }
            }

            // === VALIDASI REALTIME STOK ===
            $('#stock').on('input', function() {
                const stockVal = parseInt($(this).val());

                if (isNaN(stockVal) || stockVal <= 0) {
                    $('#error-stock')[0].textContent = 'Stok vaksin tidak mencukupi.';
                    $('#error-stock').removeClass('d-none'); // Menampilkan pesan error
                    $(this).addClass('is-invalid');
                } else {
                    $('#error-stock')[0].textContent = '';
                    $('#error-stock').addClass('d-none'); // Menyembunyikan pesan error
                    $(this).removeClass('is-invalid');
                }
            });
        });
    </script>
@endpush
