@extends('layouts.dashboard')

@section('title', 'Data Obat')

@push('styles')
    <link rel="stylesheet" href="{{ asset('modules/datatables/dataTables.min.css') }}">
    <style>
        .table {
            white-space: nowrap !important;
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
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-body">
                            <div class=" d-flex justify-content-between align-items-center mb-4" style="gap: .5rem">
                                <a href="{{ url('/history/medicine-data') }}" class="btn btn-warning ml-auto"><i
                                        class="fas fa-clock-rotate-left mr-1"></i>
                                    Riwayat Pemberian Obat</a>
                                <button type="button" data-toggle="modal" data-target="#reportModal"
                                    class="btn btn-success">
                                    <i class="fas fa-print mr-1"></i> Cetak Laporan
                                </button>
                                <a href="{{ url('/medicine-data/create') }}" class="btn btn-primary">Tambah</a>
                            </div>

                            <div class="table-responsive">
                                <table class="table-striped table" id="table-1">
                                    <thead>
                                        <tr class="text-center">
                                            <th>No.</th>
                                            <th>Nama Obat</th>
                                            <th>Jenis</th>
                                            <th>Unit</th>
                                            <th>Stok</th>
                                            <th>Tanggal Masuk</th>
                                            <th>Tanggal Kedaluwarsa</th>
                                            <th>Keterangan</th>
                                            <th>Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($medicines as $medicine)
                                            <tr>
                                                <td class="text-right">{{ $loop->iteration }}</td>
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
                                                <td>
                                                    <div class="d-flex justify-content-center" style="gap: .5rem">
                                                        <a href="{{ url("/medicine-data/{$medicine->id}/edit") }}"
                                                            class="btn btn-primary" data-toggle="tooltip" title="Ubah">
                                                            <i class="fas fa-pencil"></i>
                                                        </a>
                                                        <form action="{{ url("/medicine-data/{$medicine->id}") }}"
                                                            method="POST" id="delete-form-{{ $medicine->id }}"
                                                            class="d-inline">
                                                            @method('delete')
                                                            @csrf
                                                            <button type="submit" class="btn btn-danger btn-delete"
                                                                data-toggle="tooltip" title="Hapus">
                                                                <i class="fas fa-trash"></i>
                                                            </button>
                                                        </form>
                                                    </div>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>

    <!-- Modal cetak laporan -->
    <div class="modal fade" id="reportModal" tabindex="-1" role="dialog" aria-labelledby="reportModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <form class="modal-content" action="{{ url('/print-stock-report/medicine-data') }}" method="GET"
                target="printFrame" onsubmit="printReport();">
                <div class="modal-body p-3">
                    <div class="d-flex justify-content-between align-items-center" style="gap: .5rem">
                        <h5 class="modal-title mb-0" id="reportModalLabel">Cetak Laporan</h5>
                        <button type="button" class="px-2 py-1 close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>

                    <hr class="mt-3 mb-4">

                    <div class="row g-4">
                        <div class="form-group col-12">
                            <label for="print_criteria">Kriteria Cetak <span class="text-danger">*</span></label>
                            <select name="print_criteria" id="print_criteria"
                                class="form-control @error('print_criteria') is-invalid @enderror" required>
                                <option value="" selected disabled>-- Pilih Kriteria Cetak --</option>
                                <option value="Semua" {{ old('print_criteria') == 'Semua' ? 'selected' : '' }}>
                                    Semua
                                </option>
                                <option value="Tanggal Masuk"
                                    {{ old('print_criteria') == 'Tanggal Masuk' ? 'selected' : '' }}>
                                    Berdasarkan Tanggal Masuk
                                </option>
                                <option value="Tanggal Kedaluwarsa"
                                    {{ old('print_criteria') == 'Tanggal Kedaluwarsa' ? 'selected' : '' }}>
                                    Berdasarkan Tanggal Kedaluwarsa
                                </option>
                            </select>
                            @error('print_criteria')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>
                        <div class="form-group col-md-6 criterion-period d-none">
                            <label for="early_period">Periode Awal <span class="text-danger">*</span></label>
                            <input id="early_period" type="date"
                                class="form-control @error('early_period') is-invalid @enderror" name="early_period"
                                value="{{ old('early_period') }}">
                            @error('early_period')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>
                        <div class="form-group col-md-6 criterion-period d-none">
                            <label for="final_period">Periode Akhir <span class="text-danger">*</span></label>
                            <input id="final_period" type="date"
                                class="form-control @error('final_period') is-invalid @enderror" name="final_period"
                                value="{{ old('final_period') }}">
                            @error('final_period')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                            @enderror
                            <div class="invalid-feedback" id="final-period-error"></div>
                        </div>
                    </div>

                    <div class="d-flex justify-content-end align-items-center" style="gap: .5rem">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>
                        <button type="submit" class="btn btn-success"><i class="fas fa-print mr-1"></i> Cetak
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- iframe print report -->
    <iframe id="printFrame" name="printFrame" style="display: none;"></iframe>
@endsection

@push('scripts')
    <script src="{{ asset('modules/datatables/dataTables.min.js') }}"></script>
    <script src="{{ asset('js/page/modules-datatables.js') }}"></script>

    <script>
        $(document).ready(function() {
            // Gunakan delegasi untuk tombol hapus
            $(document).on('click', '.btn-delete', function(e) {
                e.preventDefault();

                const formId = $(this).closest('form').attr('id');

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
                        $('#' + formId).submit();
                    }
                });
            });
        });
    </script>

    <!-- Fungsi untuk cetak laporan -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const selectCriteria = document.getElementById('print_criteria');
            const periodFields = document.querySelectorAll('.criterion-period');
            const entryDate = document.getElementById('early_period');
            const expiryDate = document.getElementById('final_period');
            const errorDiv = document.getElementById('final-period-error');
            const submitBtn = document.querySelector('#reportModal button[type="submit"]');

            // Fungsi untuk toggle input periode berdasarkan kriteria
            function togglePeriodFields() {
                const selectedValue = selectCriteria.value;

                if (selectedValue === "" || selectedValue === "Semua") {
                    periodFields.forEach(field => field.classList.add('d-none'));
                    entryDate.disabled = true;
                    expiryDate.disabled = true;
                    entryDate.required = false;
                    expiryDate.required = false;
                    entryDate.value = '';
                    expiryDate.value = '';
                    clearPeriodError();
                } else if (selectedValue === "Tanggal Masuk" || selectedValue === "Tanggal Kedaluwarsa") {
                    periodFields.forEach(field => field.classList.remove('d-none'));
                    entryDate.disabled = false;
                    expiryDate.disabled = false;
                    entryDate.required = true;
                    expiryDate.required = true;
                } else {
                    periodFields.forEach(field => field.classList.add('d-none'));
                    entryDate.disabled = true;
                    expiryDate.disabled = true;
                    entryDate.required = false;
                    expiryDate.required = false;
                    entryDate.value = '';
                    expiryDate.value = '';
                    clearPeriodError();
                }

                // Validasi ulang setelah toggle
                validatePeriod();
            }

            // Fungsi untuk validasi tanggal periode
            function validatePeriod() {
                const startDate = new Date(entryDate.value);
                const endDate = new Date(expiryDate.value);

                // Reset error
                clearPeriodError();

                // Hanya validasi jika input aktif dan ada nilai
                if (!entryDate.disabled && entryDate.value && expiryDate.value) {
                    if (endDate < startDate) {
                        errorDiv.innerText = 'Periode akhir harus sama atau setelah periode awal.';
                        expiryDate.classList.add('is-invalid');
                        submitBtn.disabled = true;
                        return;
                    }
                }

                // Jika tidak ada error, aktifkan tombol
                submitBtn.disabled = false;
            }

            // Fungsi reset error
            function clearPeriodError() {
                errorDiv.textContent = '';
                expiryDate.classList.remove('is-invalid');
            }

            // Jalankan saat halaman dimuat
            togglePeriodFields();

            // Event listener
            selectCriteria.addEventListener('change', togglePeriodFields);
            entryDate.addEventListener('input', validatePeriod);
            expiryDate.addEventListener('input', validatePeriod);
        });

        function printReport() {
            var printFrame = document.getElementById('printFrame');

            printFrame.onload = function() {
                printFrame.contentWindow.print();
            };
        }
    </script>
@endpush
