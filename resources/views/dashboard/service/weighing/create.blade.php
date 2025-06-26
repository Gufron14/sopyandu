@extends('layouts.dashboard')

@section('title', 'Tambah Penimbangan')

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
                            <form action="{{ url('/weighing-data') }}" method="POST">
                                @csrf
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
                                                    {{ old('children_id') == $child->id ? 'selected' : '' }}>
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
                                            name="mother_fullname" value="" pattern="[A-Za-z ]+" placeholder="Imas"
                                            disabled>
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
                                            name="weighing_date" value="{{ date('Y-m-d') }}" readonly>
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
                                            name="age_in_checks" value="{{ old('age_in_checks') }}"
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
                                            value="{{ old('weight') }}" placeholder="Contoh: 5 atau 10.55">
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
                                            value="{{ old('height') }}" placeholder="Contoh: 50 atau 75.55">
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
                                            name="head_circumference" value="{{ old('head_circumference') }}"
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
                                            name="arm_circumference" value="{{ old('arm_circumference') }}"
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
                                        <input type="text" id="nutrition_status_display" class="form-control"
                                            readonly>
                                        <input type="hidden" id="nutrition_status" name="nutrition_status"
                                            class="@error('nutrition_status') is-invalid @enderror">
                                        @error('nutrition_status')
                                            <div class="invalid-feedback">
                                                {{ $message }}
                                            </div>
                                        @enderror
                                    </div>

                                    <!-- Z-Score Results -->
                                    <div class="col-12">
                                        <hr>
                                        <h5 class="card-title">Hasil Pengukuran Antropometri</h5>
                                    </div>

                                    <div class="form-group col-md-3">
                                        <label>BB/U (Berat/Umur)</label>
                                        <input type="text" class="form-control" id="bb_u_zscore" readonly>
                                        <small class="form-text text-muted" id="bb_u_status"></small>
                                    </div>

                                    <div class="form-group col-md-3">
                                        <label>TB/U (Tinggi/Umur)</label>
                                        <input type="text" class="form-control" id="tb_u_zscore" readonly>
                                        <small class="form-text text-muted" id="tb_u_status"></small>
                                    </div>

                                    <div class="form-group col-md-3">
                                        <label>BB/TB (Berat/Tinggi)</label>
                                        <input type="text" class="form-control" id="bb_tb_zscore" readonly>
                                        <small class="form-text text-muted" id="bb_tb_status"></small>
                                    </div>

                                    <div class="form-group col-md-3">
                                        <label>IMT/U (BMI/Umur)</label>
                                        <input type="text" class="form-control" id="imt_u_zscore" readonly>
                                        <small class="form-text text-muted" id="imt_u_status"></small>
                                    </div>

                                    <div class="form-group col-md-6">
                                        <label for="notes">Keterangan</label>
                                        <textarea name="notes" id="notes" class="form-control @error('notes') is-invalid @enderror summernote-simple"
                                            rows="5" data-placeholder="Jika ada keterangan atau catatan silakan isi">{{ old('notes') }}</textarea>
                                        @error('notes')
                                            <div class="invalid-feedback">
                                                {{ $message }}
                                            </div>
                                        @enderror
                                    </div>

                                    <input type="hidden" name="officer_id" id="officer_id"
                                        value="{{ Auth::user()->officer_id }}">

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
            $(".select2").select2();
            
            // WHO Standards data (simplified)
            const whoStandards = {
                weight_for_age_L: {
                    0: {median: 3.3, sd: 0.4}, 1: {median: 4.5, sd: 0.5}, 2: {median: 5.6, sd: 0.6},
                    3: {median: 6.4, sd: 0.7}, 6: {median: 7.9, sd: 0.8}, 12: {median: 9.6, sd: 1.0},
                    24: {median: 12.2, sd: 1.3}, 36: {median: 14.3, sd: 1.5}, 48: {median: 16.3, sd: 1.7},
                    60: {median: 18.3, sd: 1.9}
                },
                weight_for_age_P: {
                    0: {median: 3.2, sd: 0.4}, 1: {median: 4.2, sd: 0.5}, 2: {median: 5.1, sd: 0.6},
                    3: {median: 5.8, sd: 0.7}, 6: {median: 7.3, sd: 0.8}, 12: {median: 8.9, sd: 1.0},
                    24: {median: 11.5, sd: 1.3}, 36: {median: 13.9, sd: 1.5}, 48: {median: 15.9, sd: 1.7},
                    60: {median: 17.9, sd: 1.9}
                },
                height_for_age_L: {
                    0: {median: 49.9, sd: 1.9}, 1: {median: 54.7, sd: 2.0}, 2: {median: 58.4, sd: 2.1},
                    3: {median: 61.4, sd: 2.2}, 6: {median: 67.6, sd: 2.4}, 12: {median: 75.7, sd: 2.6},
                    24: {median: 87.1, sd: 3.0}, 36: {median: 96.1, sd: 3.3}, 48: {median: 103.3, sd: 3.6},
                    60: {median: 110.0, sd: 3.9}
                },
                height_for_age_P: {
                    0: {median: 49.1, sd: 1.9}, 1: {median: 53.7, sd: 2.0}, 2: {median: 57.1, sd: 2.1},
                    3: {median: 59.8, sd: 2.2}, 6: {median: 65.7, sd: 2.4}, 12: {median: 74.0, sd: 2.6},
                    24: {median: 85.7, sd: 3.0}, 36: {median: 95.1, sd: 3.3}, 48: {median: 102.7, sd: 3.6},
                    60: {median: 109.4, sd: 3.9}
                }
            };

            function getClosestStandard(standards, targetValue) {
                let closestKey = null;
                let minDiff = Infinity;
                
                for (let key in standards) {
                    const diff = Math.abs(parseInt(key) - targetValue);
                    if (diff < minDiff) {
                        minDiff = diff;
                        closestKey = key;
                    }
                }
                
                return standards[closestKey];
            }

            function calculateZScore(value, median, sd) {
                return (value - median) / sd;
            }

            function getNutritionStatus(zScore) {
                if (zScore < -3) return 'Buruk';
                if (zScore < -2) return 'Kurang';
                if (zScore <= 2) return 'Baik';
                return 'Lebih';
            }

            function getAgeInMonths() {
                const dobInput = $('#date_of_birth').val();
                const weighingDateInput = $('#weighing_date').val();
                
                if (!dobInput || !weighingDateInput) return null;
                
                const dob = new Date(dobInput);
                const weighingDate = new Date(weighingDateInput);
                
                let months = (weighingDate.getFullYear() - dob.getFullYear()) * 12;
                months += weighingDate.getMonth() - dob.getMonth();
                
                // Adjust if day hasn't reached yet
                if (weighingDate.getDate() < dob.getDate()) {
                    months--;
                }
                
                return Math.max(0, months);
            }

            function updateNutritionStatus() {
                const bbUStatus = $('#bb_u_status').text() || 'Baik';
                const tbUStatus = $('#tb_u_status').text() || 'Baik';
                const bbTbStatus = $('#bb_tb_status').text() || 'Baik';
                const imtUStatus = $('#imt_u_status').text() || 'Baik';

                const statuses = [bbUStatus, tbUStatus, bbTbStatus, imtUStatus];
                
                let overallStatus = 'Baik';
                if (statuses.includes('Buruk')) {
                    overallStatus = 'Buruk';
                } else if (statuses.includes('Kurang')) {
                    overallStatus = 'Kurang';
                } else if (statuses.includes('Lebih')) {
                    overallStatus = 'Lebih';
                }

                $('#nutrition_status_display').val(overallStatus);
                $('#nutrition_status').val(overallStatus);
            }

            function calculateAnthropometricIndices() {
                const weight = parseFloat($('#weight').val());
                const height = parseFloat($('#height').val());
                const gender = $('#gender').val();
                const ageInMonths = getAgeInMonths();
                
                if (!weight || !height || !gender || ageInMonths === null) {
                    return;
                }

                // BB/U calculation
                const weightStandard = getClosestStandard(whoStandards[`weight_for_age_${gender}`], ageInMonths);
                if (weightStandard) {
                    const bbUZScore = calculateZScore(weight, weightStandard.median, weightStandard.sd);
                    const bbUStatus = getNutritionStatus(bbUZScore);
                    $('#bb_u_zscore').val(bbUZScore.toFixed(2));
                    $('#bb_u_status').text(bbUStatus);
                }

                // TB/U calculation
                const heightStandard = getClosestStandard(whoStandards[`height_for_age_${gender}`], ageInMonths);
                if (heightStandard) {
                    const tbUZScore = calculateZScore(height, heightStandard.median, heightStandard.sd);
                    const tbUStatus = getNutritionStatus(tbUZScore);
                    $('#tb_u_zscore').val(tbUZScore.toFixed(2));
                    $('#tb_u_status').text(tbUStatus);
                }

                // BB/TB calculation (simplified - using height as reference)
                const expectedWeight = height * 0.1; // Simplified calculation
                const bbTbZScore = (weight - expectedWeight) / 1.5;
                const bbTbStatus = getNutritionStatus(bbTbZScore);
                $('#bb_tb_zscore').val(bbTbZScore.toFixed(2));
                $('#bb_tb_status').text(bbTbStatus);

                // IMT/U calculation
                const bmi = weight / Math.pow(height/100, 2);
                const expectedBMI = 16 + (ageInMonths * 0.05); // Simplified calculation
                const imtUZScore = (bmi - expectedBMI) / 2;
                const imtUStatus = getNutritionStatus(imtUZScore);
                $('#imt_u_zscore').val(imtUZScore.toFixed(2));
                $('#imt_u_status').text(imtUStatus);

                updateNutritionStatus();
            }

            // Attach event listeners
            $('#weight, #height').on('input', calculateAnthropometricIndices);
            $('#children_id').on('change', function() {
                setTimeout(calculateAnthropometricIndices, 100);
            });

            // Handle form submission
            $('form').on('submit', function(e) {
                if (!$('#nutrition_status').val()) {
                    e.preventDefault();
                    alert('Status gizi belum dihitung. Pastikan semua pengukuran telah diisi.');
                    return false;
                }
                return true;
            });

            // Handle child selection
            $('#children_id').on('change', function() {
                const selectedOption = $(this).find('option:selected');
                
                if (selectedOption.val()) {
                    $('#gender').val(selectedOption.data('gender'));
                    $('#birth_place').val(selectedOption.data('birth_place'));
                    $('#date_of_birth').val(selectedOption.data('date_of_birth'));
                    $('#mother_nik').val(selectedOption.data('mother_nik'));
                    $('#mother_fullname').val(selectedOption.data('mother_fullname'));
                    
                    // Calculate age
                    const dob = new Date(selectedOption.data('date_of_birth'));
                    const weighingDate = new Date($('#weighing_date').val());
                    
                    let ageInYears = weighingDate.getFullYear() - dob.getFullYear();
                    let ageInMonths = weighingDate.getMonth() - dob.getMonth();
                    let ageInDays = weighingDate.getDate() - dob.getDate();
                    
                    if (ageInDays < 0) {
                        ageInMonths--;
                        ageInDays += new Date(weighingDate.getFullYear(), weighingDate.getMonth(), 0).getDate();
                    }
                    
                    if (ageInMonths < 0) {
                        ageInYears--;
                        ageInMonths += 12;
                    }
                    
                    const ageString = `${ageInYears} tahun, ${ageInMonths} bulan, ${ageInDays} hari`;
                    $('#age_in_checks').val(ageString);
                } else {
                    $('#gender, #birth_place, #date_of_birth, #mother_nik, #mother_fullname, #age_in_checks').val('');
                }
            });
        });
    </script>
@endpush

