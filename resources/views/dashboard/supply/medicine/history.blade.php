@extends('layouts.dashboard')

@section('title', 'Riwayat Pemberian Obat')

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
                                <a href="{{ url('/medicine-data') }}" class="btn btn-secondary ml-auto">Kembali</a>
                                <button type="button" data-toggle="modal" data-target="#reportModal"
                                    class="btn btn-success">
                                    <i class="fas fa-print mr-1"></i> Cetak Laporan
                                </button>
                            </div>

                            <div class="table-responsive">
                                <table class="table-striped table" id="table-1">
                                    <thead>
                                        <tr class="text-center">
                                            <th>No.</th>
                                            <th>Tanggal</th>
                                            <th>Jenis Pelayanan</th>
                                            <th>Nama Pasien</th>
                                            <th>Obat & Jumlah</th>
                                            <th>Detail</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($groupedMedicines as $item)
                                            <tr>
                                                <td class="text-right">{{ $loop->iteration }}</td>
                                                <td>{{ \Carbon\Carbon::parse($item->date)->locale('id')->isoFormat('D MMMM YYYY') }}
                                                </td>
                                                <td class="text-center">
                                                    @switch($item->type)
                                                        @case('Imunisasi')
                                                            <span class="badge text-white"
                                                                style="background-color: tomato">Imunisasi</span>
                                                        @break

                                                        @case('Pemeriksaan Ibu Hamil')
                                                            <span class="badge text-white"
                                                                style="background-color: fuchsia">Pemeriksaan Ibu Hamil</span>
                                                        @break

                                                        @case('Pemeriksaan Lansia')
                                                            <span class="badge text-white"
                                                                style="background-color: darksalmon">Pemeriksaan Lansia</span>
                                                        @break
                                                    @endswitch
                                                </td>
                                                <td>
                                                    {{ $item->patient_name }}
                                                </td>
                                                <td>
                                                    <div class="text-wrap-overflow">
                                                        {{ $item->medicines }}
                                                    </div>
                                                </td>
                                                <td>
                                                    <div class="d-flex justify-content-center" style="gap: .5rem">
                                                        @switch($item->type)
                                                            @case('Imunisasi')
                                                                <a href="{{ url("/immunization-data/{$item->related_id}/show") }}"
                                                                    class="btn btn-info" data-toggle="tooltip" title="Detail">
                                                                    <i class="fas fa-info-circle"></i>
                                                                </a>
                                                            @break

                                                            @case('Pemeriksaan Ibu Hamil')
                                                                <a href="{{ url("/pregnancy-check-data/{$item->related_id}/show") }}"
                                                                    class="btn btn-info" data-toggle="tooltip" title="Detail">
                                                                    <i class="fas fa-info-circle"></i>
                                                                </a>
                                                            @break

                                                            @case('Pemeriksaan Lansia')
                                                                <a href="{{ url("/elderly-check-data/{$item->related_id}/show") }}"
                                                                    class="btn btn-info" data-toggle="tooltip" title="Detail">
                                                                    <i class="fas fa-info-circle"></i>
                                                                </a>
                                                            @break
                                                        @endswitch
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
            <form class="modal-content" action="{{ url('/print-report/medicine-data') }}" method="GET"
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
                        <div class="form-group col-md-6">
                            <label for="early_period">Periode Awal <span class="text-danger">*</span></label>
                            <input id="early_period" type="date"
                                class="form-control @error('early_period') is-invalid @enderror" name="early_period"
                                value="{{ old('early_period') }}" required>
                            @error('early_period')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>
                        <div class="form-group col-md-6">
                            <label for="final_period">Periode Akhir <span class="text-danger">*</span></label>
                            <input id="final_period" type="date"
                                class="form-control @error('final_period') is-invalid @enderror" name="final_period"
                                value="{{ old('final_period') }}" required>
                            @error('final_period')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                            @enderror
                            <div class="invalid-feedback" id="final-period-error"></div>
                        </div>
                        <div class="form-group col-12">
                            <label for="service_name">Jenis Pelayanan <span class="text-danger">*</span></label>
                            <select name="service_name" id="service_name"
                                class="form-control @error('service_name') is-invalid @enderror" required>
                                <option value="" selected disabled>-- Pilih Jenis Pelayanan --</option>
                                <option value="Semua" {{ old('service_name') == 'Semua' ? 'selected' : '' }}>
                                    Semua
                                </option>
                                <option value="Imunisasi" {{ old('service_name') == 'Imunisasi' ? 'selected' : '' }}>
                                    Imunisasi
                                </option>
                                <option value="Pemeriksaan Ibu Hamil"
                                    {{ old('service_name') == 'Pemeriksaan Ibu Hamil' ? 'selected' : '' }}>
                                    Pemeriksaan Ibu Hamil
                                </option>
                                <option value="Pemeriksaan Lansia"
                                    {{ old('service_name') == 'Pemeriksaan Lansia' ? 'selected' : '' }}>
                                    Pemeriksaan Lansia
                                </option>
                            </select>
                            @error('service_name')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                            @enderror
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

    <!-- Fungsi untuk cetak laporan -->
    <script>
        function validatePeriod() {
            const startInput = document.getElementById('early_period');
            const endInput = document.getElementById('final_period');
            const errorDiv = document.getElementById('final-period-error');
            const submitBtn = document.querySelector('#reportModal button[type="submit"]');

            const startDate = new Date(startInput.value);
            const endDate = new Date(endInput.value);

            // Reset error dan validasi
            errorDiv.textContent = '';
            endInput.classList.remove('is-invalid');
            submitBtn.disabled = false;

            // Cek apakah tanggal valid dan akhir < awal
            if (endInput.value && startInput.value && endDate < startDate) {
                errorDiv.innerText = 'Periode akhir harus sama atau setelah periode awal.';
                endInput.classList.add('is-invalid');
                submitBtn.disabled = true;
            }
        }

        // Pasang event listener
        document.addEventListener('DOMContentLoaded', function() {
            document.getElementById('early_period').addEventListener('input', validatePeriod);
            document.getElementById('final_period').addEventListener('input', validatePeriod);
        });

        function printReport() {
            var printFrame = document.getElementById('printFrame');

            printFrame.onload = function() {
                printFrame.contentWindow.print();
            };
        }
    </script>
@endpush
