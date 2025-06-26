@extends('layouts.dashboard')

@section('title', 'Kelola Obat')

@push('styles')
    <link rel="stylesheet" href="{{ asset('modules/datatables/dataTables.min.css') }}">

    <style>
        .table-modal,
        .table-main {
            white-space: nowrap !important;
        }

        .table-main tr>* {
            vertical-align: middle;
        }

        .table-modal tr th {
            width: 10rem !important;
        }

        .table-modal tr td:nth-child(2) {
            width: 1rem !important;
        }
    </style>
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

            <div class="row g-4">
                <div class="col-12">
                    <div class="card">
                        <div class="card-body">
                            <form action="{{ route('pregnancy-check-data.medicine.store', $pregnancy->id) }}" method="POST"
                                id="form-create">
                                @csrf

                                <div class="d-flex justify-content-between mb-4" style="gap: .5rem">
                                    <h5 class="card-title">Data Obat</h5>
                                </div>

                                <div class="table-responsive mb-5">
                                    <table class="table-striped table table-main" id="table-1">
                                        <thead>
                                            <tr class="text-center">
                                                <th>
                                                    <div class="custom-checkbox custom-control">
                                                        <input type="checkbox" class="custom-control-input"
                                                            id="checkbox-medicine-all">
                                                        <label for="checkbox-medicine-all"
                                                            class="custom-control-label">&nbsp;</label>
                                                    </div>
                                                </th>
                                                <th>Nama Obat</th>
                                                <th>Jenis</th>
                                                <th>Unit</th>
                                                <th>Stok</th>
                                                <th>Tanggal Masuk</th>
                                                <th>Tanggal Kedaluwarsa</th>
                                                <th>Keterangan Obat</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($medicines as $medicine)
                                                <tr>
                                                    <td>
                                                        <div class="custom-checkbox custom-control">
                                                            <input type="checkbox" name="medicine_ids[]"
                                                                value="{{ $medicine->id }}"
                                                                class="custom-control-input checkbox-medicine"
                                                                id="checkbox-medicine-{{ $medicine->id }}">
                                                            <label for="checkbox-medicine-{{ $medicine->id }}"
                                                                class="custom-control-label">&nbsp;</label>
                                                        </div>
                                                    </td>
                                                    <td>{{ $medicine->medicine_name ?? 'N/A' }}</td>
                                                    <td>{{ $medicine->type ?? 'N/A' }}</td>
                                                    <td>{{ $medicine->unit ?? 'N/A' }}</td>
                                                    <td class="text-right">{{ $medicine->stock ?? 'N/A' }}</td>
                                                    <td>
                                                        {{ $medicine->entry_date ? \Carbon\Carbon::parse($medicine->entry_date)->locale('id')->isoFormat('D MMMM YYYY') : 'N/A' }}
                                                    </td>
                                                    <td>
                                                        {{ $medicine->expiry_date ? \Carbon\Carbon::parse($medicine->expiry_date)->locale('id')->isoFormat('D MMMM YYYY') : 'N/A' }}
                                                    </td>
                                                    <td>{{ $medicine->notes ?? 'N/A' }}</td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>

                                <div class="d-flex justify-content-center justify-content-md-end align-items-center"
                                    style="gap: .5rem">
                                    <button type="submit" class="btn btn-primary" data-toggle="tooltip"
                                        title="Tambah obat berdasarkan pilihan" id="btn-medicine">Tambah Obat</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                <div class="col-12">
                    <div class="card" id="pivot-table">
                        <div class="card-body">
                            <form action="{{ route('pregnancy-check-data.medicine.update', $pregnancy->id) }}"
                                method="POST" id="form-edit">
                                @csrf
                                @method('PUT')


                                <div class="d-flex justify-content-between mb-4" style="gap: .5rem">
                                    <h5 class="card-title">Pemberian Obat</h5>
                                </div>

                                <div class="table-responsive mb-5">
                                    <table class="table table-sm table-striped table-main">
                                        <thead>
                                            <tr class="text-center">
                                                <th>
                                                    <div class="custom-checkbox custom-control">
                                                        <input type="checkbox" class="custom-control-input"
                                                            id="checkbox-pivot-medicine-all">
                                                        <label for="checkbox-pivot-medicine-all"
                                                            class="custom-control-label">&nbsp;</label>
                                                    </div>
                                                </th>
                                                <th>Nama Obat</th>
                                                <th>Jenis</th>
                                                <th>Unit</th>
                                                <th>Stok</th>
                                                <th>Jumlah</th>
                                                <th>Aturan Pakai</th>
                                                <th>Waktu Makan</th>
                                                <th>Keterangan Tambahan (Opsional)</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @php $availableAction = false @endphp

                                            @forelse ($pregnancy->medicines as $pregnancyMedicine)
                                                @if ($loop->first)
                                                    @php $availableAction = true @endphp
                                                @endif

                                                <tr>
                                                    <td class="text-center">
                                                        <div class="custom-checkbox custom-control">
                                                            <input type="checkbox" name="medicine_ids[]"
                                                                value="{{ $pregnancyMedicine->id }}"
                                                                class="custom-control-input checkbox-pivot-medicine"
                                                                id="checkbox-pivot-medicine-{{ $pregnancyMedicine->id }}">
                                                            <label
                                                                for="checkbox-pivot-medicine-{{ $pregnancyMedicine->id }}"
                                                                class="custom-control-label">&nbsp;</label>
                                                        </div>
                                                    </td>
                                                    <td>{{ $pregnancyMedicine->medicine_name ?? 'N/A' }}</td>
                                                    <td>{{ $pregnancyMedicine->type ?? 'N/A' }}</td>
                                                    <td>{{ $pregnancyMedicine->unit ?? 'N/A' }}</td>
                                                    <td>
                                                        <span class="remaining-stock"
                                                            id="remaining-stock-{{ $pregnancyMedicine->id }}"
                                                            data-stock="{{ $pregnancyMedicine->stock }}">
                                                            {{ $pregnancyMedicine->stock - old('quantities.' . $pregnancyMedicine->id, $pregnancyMedicine->pivot->quantity) }}
                                                        </span>
                                                    </td>
                                                    <td>
                                                        <label for="quantity-{{ $pregnancyMedicine->id }}"
                                                            class="d-none">&nbsp;</label>
                                                        <div class="input-group">
                                                            <div class="input-group-prepend">
                                                                <button class="btn btn-primary btn-minus" type="button"
                                                                    data-id="{{ $pregnancyMedicine->id }}"><i
                                                                        class="fas fa-minus"></i></button>
                                                            </div>
                                                            <input id="quantity-{{ $pregnancyMedicine->id }}"
                                                                type="text"
                                                                class="form-control text-center @error('quantities.' . $pregnancyMedicine->id) is-invalid @enderror"
                                                                name="quantities[{{ $pregnancyMedicine->id }}]"
                                                                value="{{ old('quantities.' . $pregnancyMedicine->id, $pregnancyMedicine->pivot->quantity) }}"
                                                                placeholder="0">
                                                            <div class="input-group-append">
                                                                <button class="btn btn-primary btn-plus" type="button"
                                                                    data-id="{{ $pregnancyMedicine->id }}"><i
                                                                        class="fas fa-plus"></i></button>
                                                            </div>
                                                            @error('quantities.' . $pregnancyMedicine->id)
                                                                <div class="invalid-feedback">{{ $message }}</div>
                                                            @enderror
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <label for="dosage_instructions-{{ $pregnancyMedicine->id }}"
                                                            class="d-none">&nbsp;</label>
                                                        <input id="dosage_instructions-{{ $pregnancyMedicine->id }}"
                                                            type="text"
                                                            class="form-control @error('dosage_instructions.' . $pregnancyMedicine->id) is-invalid @enderror"
                                                            name="dosage_instructions[{{ $pregnancyMedicine->id }}]"
                                                            value="{{ old('dosage_instructions.' . $pregnancyMedicine->id, $pregnancyMedicine->pivot->dosage_instructions) }}"
                                                            placeholder="2 x 1 hari">
                                                        @error('dosage_instructions.' . $pregnancyMedicine->id)
                                                            <div class="invalid-feedback">{{ $message }}</div>
                                                        @enderror
                                                    </td>
                                                    <td>
                                                        <label for="meal_time-{{ $pregnancyMedicine->id }}"
                                                            class="d-none">&nbsp;</label>
                                                        <select name="meal_time[{{ $pregnancyMedicine->id }}]"
                                                            id="meal_time-{{ $pregnancyMedicine->id }}"
                                                            class="form-control @error('meal_time.' . $pregnancyMedicine->id) is-invalid @enderror">
                                                            <option value="" disabled>-- Pilih Waktu Makan --
                                                            </option>
                                                            <option value="Sesudah Makan"
                                                                {{ old('meal_time.' . $pregnancyMedicine->id, $pregnancyMedicine->pivot->meal_time) == 'Sesudah Makan' ? 'selected' : '' }}>
                                                                Sesudah Makan</option>
                                                            <option value="Sebelum Makan"
                                                                {{ old('meal_time.' . $pregnancyMedicine->id, $pregnancyMedicine->pivot->meal_time) == 'Sebelum Makan' ? 'selected' : '' }}>
                                                                Sebelum Makan</option>
                                                            <option value="-"
                                                                {{ old('meal_time.' . $pregnancyMedicine->id, $pregnancyMedicine->pivot->meal_time) == '-' ? 'selected' : '' }}>
                                                                Lainnya</option>
                                                        </select>
                                                        @error('meal_time.' . $pregnancyMedicine->id)
                                                            <div class="invalid-feedback">{{ $message }}</div>
                                                        @enderror
                                                    </td>
                                                    <td>
                                                        <label for="notes-{{ $pregnancyMedicine->id }}"
                                                            class="d-none"></label>
                                                        <textarea name="notes[{{ $pregnancyMedicine->id }}]" id="notes-{{ $pregnancyMedicine->id }}" rows="5"
                                                            maxlength="100" class="form-control @error('notes.' . $pregnancyMedicine->id) is-invalid @enderror"
                                                            placeholder="Harus dihabiskan">{{ old('notes.' . $pregnancyMedicine->id, $pregnancyMedicine->pivot->notes) }}</textarea>
                                                        @error('notes.' . $pregnancyMedicine->id)
                                                            <div class="invalid-feedback">{{ $message }}</div>
                                                        @enderror
                                                    </td>
                                                </tr>

                                            @empty
                                                <tr>
                                                    <td colspan="9" class="text-center">Tidak ada data untuk
                                                        ditampilkan.</td>
                                                </tr>
                                            @endforelse
                                        </tbody>
                                    </table>
                                </div>

                                <div class="d-flex justify-content-between align-items-center" style="gap: .5rem">
                                    <button type="button" data-toggle="modal" data-target="#detailModal"
                                        class="btn btn-info">
                                        <i class="fas fa-info-circle mr-1"></i> Detail Pemeriksaan
                                    </button>

                                    <div class="d-flex justify-content-end align-items-center" style="gap: .5rem">
                                        <a href="{{ url("/pregnancy-check-data/{$pregnancy->id}/show") }}"
                                            class="btn btn-secondary">Kembali</a>

                                        @if ($availableAction === true)
                                            <button type="submit" class="btn btn-primary"
                                                id="btn-pivot-medicine">Simpan</button>

                                            <!-- Gunakan tombol biasa di dalam form-edit -->
                                            <button type="button" class="btn btn-danger btn-delete"
                                                data-id="{{ $pregnancy->id }}"
                                                data-action="{{ route('pregnancy-check-data.medicine.destroy', $pregnancy->id) }}"
                                                data-token="{{ csrf_token() }}" data-toggle="tooltip"
                                                title="Hapus data berdasarkan pilihan">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        @endif
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>

    <div class="modal fade" id="detailModal" tabindex="-1" role="dialog" aria-labelledby="detailModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-body p-3">
                    <div class="d-flex justify-content-between align-items-center" style="gap: .5rem">
                        <h5 class="modal-title mb-0" id="detailModalLabel">Detail Pemeriksaan</h5>
                        <button type="button" class="px-2 py-1 close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>

                    <hr class="mt-3 mb-4">

                    @php
                        $parent = $pregnancy->familyParents;
                        $officer = $pregnancy->officers;
                        $medicines = $pregnancy->medicines;
                    @endphp

                    <div class="row g-4">
                        <div class="col-lg-6 col-md-6 col-sm-12 col-12">
                            <h5 class="card-title">Biodata</h5>

                            <div class="table-responsive">
                                <table class="table table-sm table-borderless">
                                    <tr>
                                        <th>NIK</th>
                                        <td>:</td>
                                        <td>
                                            @if (Auth::user() && Auth::user()->role === 'family_parent')
                                                {{ $parent->nik ?? 'N/A' }}
                                            @else
                                                <a
                                                    href="{{ url("/parent-data/{$parent->id}/show") }}">{{ $parent->nik ?? 'N/A' }}</a>
                                            @endif
                                        </td>
                                    </tr>
                                    <tr>
                                        <th>Nama Lengkap</th>
                                        <td>:</td>
                                        <td>{{ $parent->mother_fullname ?? 'N/A' }}</td>
                                    </tr>
                                    <tr>
                                        <th>Tempat, Tanggal Lahir</th>
                                        <td>:</td>
                                        <td>{{ $parent->mother_birth_place ?? 'N/A' }},
                                            {{ $parent->mother_date_of_birth ? \Carbon\Carbon::parse($parent->mother_date_of_birth)->locale('id')->isoFormat('D MMMM YYYY') : 'N/A' }}
                                        </td>
                                    </tr>
                                    <tr>
                                        <th>Golongan Darah</th>
                                        <td>:</td>
                                        <td>{{ $parent->mother_blood_type ?? 'N/A' }}</td>
                                    </tr>
                                    <tr>
                                        <th>Usia</th>
                                        <td>:</td>
                                        <td>
                                            {{ $parent->mother_date_of_birth ? \Carbon\Carbon::parse($parent->mother_date_of_birth)->diff(\Carbon\Carbon::now())->y : 'N/A' }}
                                            tahun,
                                            {{ $parent->mother_date_of_birth ? \Carbon\Carbon::parse($parent->mother_date_of_birth)->diff(\Carbon\Carbon::now())->m : 'N/A' }}
                                            bulan,
                                            {{ $parent->mother_date_of_birth ? \Carbon\Carbon::parse($parent->mother_date_of_birth)->diff(\Carbon\Carbon::now())->d : 'N/A' }}
                                            hari
                                        </td>
                                    </tr>
                                    <tr>
                                        <th>Jumlah Anak</th>
                                        <td>:</td>
                                        <td>{{ $parent->number_of_children ?? 'N/A' }}</td>
                                    </tr>
                                    <tr>
                                        <th>Alamat</th>
                                        <td>:</td>
                                        <td>{{ $parent->address ?? 'N/A' }}</td>
                                    </tr>
                                    <tr>
                                        <th>Lingkungan/Dusun</th>
                                        <td>:</td>
                                        <td>{{ $parent->hamlet ?? 'N/A' }}</td>
                                    </tr>
                                    <tr>
                                        <th>Kelurahan/Desa</th>
                                        <td>:</td>
                                        <td>{{ $village ?? 'N/A' }}</td>
                                    </tr>
                                    <tr>
                                        <th>Kecamatan</th>
                                        <td>:</td>
                                        <td>{{ $subdistrict ?? 'N/A' }}</td>
                                    </tr>
                                    <tr>
                                        <th>Kota/Kabupaten</th>
                                        <td>:</td>
                                        <td>{{ $city ?? 'N/A' }}</td>
                                    </tr>
                                    <tr>
                                        <th>Provinsi</th>
                                        <td>:</td>
                                        <td>{{ $province ?? 'N/A' }}</td>
                                    </tr>
                                </table>
                            </div>
                        </div>

                        <div class="col-12 d-md-none">
                            <hr class="mb-4">
                        </div>

                        <div class="col-lg-6 col-md-6 col-sm-12 col-12">
                            <h5 class="card-title">Hasil Pemeriksaan</h5>

                            <div class="table-responsive">
                                <table class="table table-sm table-borderless">
                                    <tr>
                                        <th>Tanggal Pemeriksaan</th>
                                        <td>:</td>
                                        <td>
                                            {{ $pregnancy->check_date ? \Carbon\Carbon::parse($pregnancy->check_date)->locale('id')->isoFormat('D MMMM YYYY') : 'N/A' }}
                                        </td>
                                    </tr>
                                    <tr>
                                        <th>Usia Saat Pemeriksaan</th>
                                        <td>:</td>
                                        <td>{{ $pregnancy->age_in_checks ?? 'N/A' }}</td>
                                    </tr>
                                    <tr>
                                        <th>Usia Kehamilan</th>
                                        <td>:</td>
                                        <td>{{ $pregnancy->gestational_age ?? 'N/A' }} minggu</td>
                                    </tr>
                                    <tr>
                                        <th>Berat Badan</th>
                                        <td>:</td>
                                        <td>{{ $pregnancy->mother_weight !== null ? number_format($pregnancy->mother_weight, 2) : 'N/A' }}
                                            kg</td>
                                    </tr>
                                    <tr>
                                        <th>Tekanan Darah</th>
                                        <td>:</td>
                                        <td>{{ $pregnancy->blood_pressure ?? 'N/A' }}</td>
                                    </tr>
                                    <tr>
                                        <th>Denyut Nadi</th>
                                        <td>:</td>
                                        <td>{{ $pregnancy->pulse_rate ?? '-' }} bpm</td>
                                    </tr>
                                    <tr>
                                        <th>Kadar Gula Darah</th>
                                        <td>:</td>
                                        <td>{{ $pregnancy->blood_sugar !== null ? number_format($pregnancy->blood_sugar, 2) : '-' }}
                                            mg/dL</td>
                                    </tr>
                                    <tr>
                                        <th>Kadar Kolesterol</th>
                                        <td>:</td>
                                        <td>{{ $pregnancy->cholesterol !== null ? number_format($pregnancy->cholesterol, 2) : '-' }}
                                            mg/dL</td>
                                    </tr>
                                    <tr>
                                        <th>Tinggi Fundus</th>
                                        <td>:</td>
                                        <td>{{ $pregnancy->fundus_height ?? '-' }} cm</td>
                                    </tr>
                                    <tr>
                                        <th>Detak Jantung Janin</th>
                                        <td>:</td>
                                        <td>{{ $pregnancy->fetal_heart_rate ?? '-' }} bpm</td>
                                    </tr>
                                    <tr>
                                        <th>Presentasi Janin</th>
                                        <td>:</td>
                                        <td>{{ $pregnancy->fetal_presentation ?? 'N/A' }}</td>
                                    </tr>
                                    <tr>
                                        <th>Edema</th>
                                        <td>:</td>
                                        <td>{{ $pregnancy->edema ?? 'N/A' }}</td>
                                    </tr>
                                    <tr>
                                        <th>Keterangan</th>
                                        <td>:</td>
                                        <td>{{ is_null($pregnancy->notes) || empty($pregnancy->notes) ? '-' : strip_tags($pregnancy->notes) }}
                                        </td>
                                    </tr>
                                    <tr>
                                        <th>Petugas Pemeriksa</th>
                                        <td>:</td>
                                        <td>
                                            @if (!is_null($pregnancy->officer_id))
                                                @if (Auth::check())
                                                    @php
                                                        $user = Auth::user();
                                                    @endphp

                                                    @if ($user->role === 'admin')
                                                        <a href="{{ url("/officer-data/{$officer->id}/show") }}">{{ $officer->fullname ?? 'N/A' }}
                                                            - {{ $officer->position ?? 'N/A' }}</a>
                                                    @elseif ($user->role === 'officer' && $user->officer_id === $officer->id)
                                                        <a href="{{ url("/officer-data/{$officer->id}/show") }}">{{ $officer->fullname ?? 'N/A' }}
                                                            - {{ $officer->position ?? 'N/A' }}</a>
                                                    @else
                                                        {{ $officer->fullname ?? 'N/A' }} -
                                                        {{ $officer->position ?? 'N/A' }}
                                                    @endif
                                                @endif
                                            @else
                                                N/A
                                            @endif
                                        </td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                    </div>

                    <div class="d-flex justify-content-end align-items-center" style="gap: .5rem">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script src="{{ asset('modules/datatables/dataTables.min.js') }}"></script>
    <script src="{{ asset('js/page/modules-datatables.js') }}"></script>

    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const checkboxAll = document.getElementById('checkbox-medicine-all');
            const checkboxes = document.querySelectorAll('.checkbox-medicine');
            const submitButton = document.getElementById('btn-medicine');
            const formCreate = document.getElementById('form-create');
            const table = $('#table-1').DataTable();

            // Toggle all checkboxes when "select all" is clicked
            checkboxAll.addEventListener('change', function() {
                const isChecked = checkboxAll.checked;
                // Select all checkboxes in the entire table, not just the current page
                table.$('.checkbox-medicine').each(function() {
                    this.checked = isChecked;
                });
            });

            // Uncheck "select all" if any checkbox is unchecked
            table.$('.checkbox-medicine').each(function() {
                this.addEventListener('change', function() {
                    if (!this.checked) {
                        checkboxAll.checked = false;
                    } else {
                        checkboxAll.checked = table.$('.checkbox-medicine:checked').length === table
                            .$('.checkbox-medicine').length;
                    }
                });
            });

            // Handle form submission
            formCreate.addEventListener('submit', function(e) {
                const selectedCheckboxes = table.$('.checkbox-medicine:checked');
                const selectedValues = [];

                // Collect the values of all selected checkboxes across all pages
                selectedCheckboxes.each(function() {
                    selectedValues.push(this.value);
                });

                // Remove duplicate values
                const uniqueValues = [...new Set(selectedValues)];

                // Clear any existing hidden inputs from previous submissions
                formCreate.querySelectorAll('input[name="medicine_ids[]"]').forEach(input => input
                    .remove());

                // Append the selected values to the form as hidden inputs (unique values only)
                if (uniqueValues.length > 0) {
                    uniqueValues.forEach(function(value) {
                        const input = document.createElement('input');
                        input.type = 'hidden';
                        input.name =
                            'medicine_ids[]'; // Same as the name in the original checkboxes
                        input.value = value;
                        formCreate.appendChild(input);
                    });
                } else {
                    e.preventDefault(); // Stop form submission if no item is selected

                    swal({
                        title: 'Peringatan!',
                        text: 'Silakan pilih minimal satu item untuk ditambahkan.',
                        icon: 'warning',
                        buttons: {
                            confirm: {
                                text: 'Oke',
                                visible: true
                            }
                        },
                        closeOnClickOutside: true,
                        closeOnEsc: true
                    }).then(() => {
                        location.reload();
                    });

                    // Auto reload after 5 seconds if no interaction
                    setTimeout(() => {
                        location.reload();
                    }, 5000);

                    // Reload if user clicks outside swal manually
                    document.addEventListener('click', function handleClickOutside() {
                        location.reload();
                        document.removeEventListener('click',
                            handleClickOutside); // Hapus listener supaya tidak reload berkali-kali
                    });
                }
            });
        });

        @if ($availableAction === true)
            document.addEventListener('DOMContentLoaded', function() {
                const updateRemainingStock = (id) => {
                    const quantityInput = document.getElementById(`quantity-${id}`);
                    const remainingStockSpan = document.getElementById(`remaining-stock-${id}`);
                    const maxStock = parseInt(remainingStockSpan.dataset.stock);
                    let quantity = parseInt(quantityInput.value) || 0;

                    // Batasi agar tidak lebih dari stok tersedia
                    if (quantity > maxStock) {
                        quantity = maxStock;
                        quantityInput.value = quantity;
                    } else if (quantity < 0) {
                        quantity = 0;
                        quantityInput.value = 0;
                    }

                    const remaining = maxStock - quantity;
                    remainingStockSpan.textContent = remaining >= 0 ? remaining : 0;
                };

                document.querySelectorAll('.btn-plus').forEach(button => {
                    button.addEventListener('click', function() {
                        const id = this.dataset.id;
                        const input = document.getElementById(`quantity-${id}`);
                        let value = parseInt(input.value) || 0;
                        input.value = value + 1;
                        updateRemainingStock(id);
                    });
                });

                document.querySelectorAll('.btn-minus').forEach(button => {
                    button.addEventListener('click', function() {
                        const id = this.dataset.id;
                        const input = document.getElementById(`quantity-${id}`);
                        let value = parseInt(input.value) || 0;
                        input.value = value > 0 ? value - 1 : 0;
                        updateRemainingStock(id);
                    });
                });

                // Update saat input manual
                document.querySelectorAll('input[id^="quantity-"]').forEach(input => {
                    input.addEventListener('input', function() {
                        const id = this.id.split('-')[1];
                        updateRemainingStock(id);
                    });
                });
            });

            document.addEventListener('DOMContentLoaded', function() {
                const deleteButtons = document.querySelectorAll('.btn-delete');

                deleteButtons.forEach((deleteButton) => {
                    deleteButton.addEventListener('click', function(e) {
                        e.preventDefault();

                        const formEdit = document.querySelector('#form-edit');
                        if (!formEdit) {
                            console.error('Form dengan id "form-edit" tidak ditemukan.');
                            return;
                        }

                        const selectedCheckboxes = formEdit.querySelectorAll(
                            '.checkbox-pivot-medicine:checked');

                        if (selectedCheckboxes.length === 0) {
                            swal({
                                title: 'Peringatan!',
                                text: 'Silakan pilih minimal satu item untuk dihapus.',
                                icon: 'warning',
                                button: 'Oke'
                            });
                            return;
                        }

                        swal({
                            title: 'Hapus Data',
                            text: 'Apakah Anda yakin ingin menghapus data ini?',
                            icon: 'warning',
                            buttons: {
                                cancel: 'Batal',
                                confirm: {
                                    text: 'Ya, Hapus!',
                                    value: true,
                                    className: 'btn-danger',
                                }
                            },
                            dangerMode: true,
                        }).then((willDelete) => {
                            if (willDelete) {
                                const deleteForm = document.createElement('form');
                                deleteForm.method = 'POST';
                                deleteForm.action = deleteButton.dataset.action;

                                const csrfInput = document.createElement('input');
                                csrfInput.type = 'hidden';
                                csrfInput.name = '_token';
                                csrfInput.value = deleteButton.dataset.token;

                                const methodInput = document.createElement('input');
                                methodInput.type = 'hidden';
                                methodInput.name = '_method';
                                methodInput.value = 'DELETE';

                                selectedCheckboxes.forEach(cb => {
                                    const hidden = document.createElement('input');
                                    hidden.type = 'hidden';
                                    hidden.name = 'medicine_ids[]';
                                    hidden.value = cb.value;
                                    deleteForm.appendChild(hidden);
                                });

                                deleteForm.appendChild(csrfInput);
                                deleteForm.appendChild(methodInput);
                                document.body.appendChild(deleteForm);
                                deleteForm.submit();
                            }
                        });
                    });
                });

                // Checkbox Select All behavior hanya untuk form-edit
                const formEdit = document.querySelector('#form-edit');
                if (formEdit) {
                    const checkAll = formEdit.querySelector('#checkbox-pivot-medicine-all');
                    const checkboxes = formEdit.querySelectorAll('.checkbox-pivot-medicine');

                    // Event select all
                    checkAll.addEventListener('change', function() {
                        checkboxes.forEach(cb => cb.checked = this.checked);
                    });

                    // Update status "select all" jika ada perubahan di checkbox item
                    checkboxes.forEach(cb => {
                        cb.addEventListener('change', function() {
                            const total = checkboxes.length;
                            const checked = formEdit.querySelectorAll(
                                '.checkbox-pivot-medicine:checked').length;
                            checkAll.checked = total === checked;
                        });
                    });
                }
            });
        @endif
    </script>
@endpush
