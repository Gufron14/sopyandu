@extends('layouts.dashboard')

@section('title', 'Data Pemeriksaan Ibu Hamil')

@push('styles')
    <link rel="stylesheet" href="{{ asset('modules/datatables/dataTables.min.css') }}">
    <link rel="stylesheet" href="{{ asset('modules/select2/dist/css/select2.min.css') }}">
    <style>
        .table {
            white-space: nowrap !important;
        }

        .table tbody tr td {
            vertical-align: middle !important;
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
                            <div class="d-flex justify-content-between align-items-center mb-4">
                                @if (Auth::user() && Auth::user()->role !== 'family_parent')
                                    <form method="GET" action="{{ route('pregnancy-check-data.index') }}"
                                        style="width: 10rem">
                                        <div class="input-group">
                                            <label class="input-group-text bg-secondary" for="year">
                                                Tahun
                                            </label>
                                            <select class="form-select" name="year" id="year"
                                                onchange="this.form.submit()">
                                                @foreach ($availableYears as $year)
                                                    <option value="{{ $year }}"
                                                        {{ $year == $selectedYear ? 'selected' : '' }}>
                                                        {{ $year }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </form>
                                @endif

                                <div class="d-flex justify-content-end align-items-center ml-auto" style="gap: .5rem">
                                    @if (Auth::user() && Auth::user()->role !== 'family_parent')
                                        <button type="button" data-toggle="modal" data-target="#reportModal"
                                            class="btn btn-success">
                                            <i class="fas fa-print mr-1"></i> Cetak Laporan
                                        </button>
                                    @endif

                                    @if (Auth::user() &&
                                            (Auth::user()->role !== 'family_parent' &&
                                                (Auth::user()->officer_id !== null &&
                                                    Auth::user()->officers->position !== 'Lurah' &&
                                                    Auth::user()->officers->position !== 'Kepala Lingkungan')))
                                        <a href="{{ url('/pregnancy-check-data/create') }}"
                                            class="btn btn-primary">Tambah</a>
                                    @endif

                                    @php
                                        $userCheckForThisPage =
                                            Auth::user() &&
                                            (Auth::user()->role === 'family_parent' ||
                                                (Auth::user()->officer_id !== null &&
                                                    Auth::user()->officers->position !== 'Lurah' &&
                                                    Auth::user()->officers->position !== 'Kepala Lingkungan'));
                                    @endphp
                                </div>
                            </div>

                            <div class="table-responsive">
                                <table class="table-striped table" id="table-1">
                                    <thead>
                                        <tr class="text-center">
                                            <th>No.</th>
                                            <th>Tanggal Pemeriksaan</th>
                                            @if (Auth::user() && Auth::user()->role !== 'family_parent')
                                                <th>NIK</th>
                                                <th>Nama Lengkap</th>
                                                <th>Tempat Lahir</th>
                                                <th>Tanggal Lahir</th>
                                            @else
                                                <th>Nama Lengkap</th>
                                            @endif
                                            <th>Usia Saat Pemeriksaan</th>
                                            <th>Usia Kehamilan (minggu)</th>
                                            <th>Berat Badan (kg)</th>
                                            <th>Tekanan Darah</th>
                                            <th>Denyut Nadi (bpm)</th>
                                            <th>Kadar Gula Darah (mg/dL)</th>
                                            <th>Kadar Kolesterol (mg/dL)</th>
                                            <th>Tinggi Fundus (cm)</th>
                                            <th>Detak Jantung Janin (bpm)</th>
                                            <th>Presentasi Janin</th>
                                            <th>Edema</th>
                                            <th>Keterangan</th>
                                            <th>Nama Petugas</th>
                                            <th>Jabatan Petugas</th>
                                            @if ($userCheckForThisPage)
                                                <th>Aksi</th>
                                            @endif
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($pregnancy_checks as $pregnancy)
                                            @php
                                                $parent = $pregnancy?->familyParents;
                                                $officer = $pregnancy->officers;
                                            @endphp
                                            <tr data-toggle="tooltip"
                                                title="{{ $parent->nik . ' - ' . $parent->mother_fullname }}"
                                                data-placement="right">
                                                <td class="text-right">{{ $loop->iteration }}</td>
                                                <td>
                                                    {{ $pregnancy->check_date ? \Carbon\Carbon::parse($pregnancy->check_date)->locale('id')->isoFormat('D MMMM YYYY') : 'N/A' }}
                                                </td>
                                                @if (Auth::user() && Auth::user()->role !== 'family_parent')
                                                    <td class="text-right">
                                                        <a
                                                            href="{{ url("/parent-data/{$parent->id}/show") }}">{{ $parent->nik ?? 'N/A' }}</a>
                                                    </td>
                                                    <td>{{ $parent->mother_fullname ?? 'N/A' }}</td>
                                                    <td>{{ $parent->mother_birth_place ?? 'N/A' }}</td>
                                                    <td>
                                                        {{ $parent->mother_date_of_birth ? \Carbon\Carbon::parse($parent->mother_date_of_birth)->locale('id')->isoFormat('D MMMM YYYY') : 'N/A' }}
                                                    </td>
                                                @else
                                                    <td>{{ $parent->mother_fullname ?? 'N/A' }}</td>
                                                @endif
                                                <td class="text-right">{{ $pregnancy->age_in_checks ?? 'N/A' }}</td>
                                                <td class="text-right">{{ $pregnancy->gestational_age ?? 'N/A' }}</td>
                                                <td class="text-right">
                                                    {{ number_format($pregnancy->mother_weight, 2) ?? 'N/A' }}</td>
                                                <td class="text-right">{{ $pregnancy->blood_pressure ?? 'N/A' }}</td>
                                                <td class="text-right">{{ $pregnancy->pulse_rate ?? 'N/A' }}</td>
                                                <td class="text-right">
                                                    {{ $pregnancy->blood_sugar !== null ? number_format($pregnancy->blood_sugar, 2) : '-' }}
                                                </td>
                                                <td class="text-right">
                                                    {{ $pregnancy->cholesterol !== null ? number_format($pregnancy->cholesterol, 2) : '-' }}
                                                </td>
                                                <td class="text-right">
                                                    {{ $pregnancy->fundus_height ?? '-' }}
                                                </td>
                                                <td class="text-right">
                                                    {{ $pregnancy->fetal_heart_rate ?? '-' }}
                                                </td>
                                                <td>{{ $pregnancy->fetal_presentation ?? 'N/A' }}</td>
                                                <td>{{ $pregnancy->edema ?? 'N/A' }}</td>
                                                <td>
                                                    <div class="text-wrap-overflow">
                                                        {{ is_null($pregnancy->notes) || empty($pregnancy->notes) ? '-' : strip_tags($pregnancy->notes) }}
                                                    </div>
                                                </td>
                                                <td>
                                                    @if (!is_null($pregnancy->officer_id))
                                                        @if (Auth::check())
                                                            @php
                                                                $user = Auth::user();
                                                            @endphp

                                                            @if ($user->role === 'admin')
                                                                <a
                                                                    href="{{ url("/officer-data/{$officer->id}/show") }}">{{ $officer->fullname ?? 'N/A' }}</a>
                                                            @elseif ($user->role === 'officer' && $user->officer_id === $officer->id)
                                                                <a
                                                                    href="{{ url("/officer-data/{$officer->id}/show") }}">{{ $officer->fullname ?? 'N/A' }}</a>
                                                            @else
                                                                {{ $officer->fullname ?? 'N/A' }}
                                                            @endif
                                                        @endif
                                                    @else
                                                        N/A
                                                    @endif
                                                </td>
                                                <td>
                                                    {{ $officer->position ?? 'N/A' }}
                                                </td>
@if ($userCheckForThisPage)
    <td>
        <div class="d-flex justify-content-center" style="gap: .5rem">
            <a href="{{ url("/pregnancy-check-data/{$pregnancy->id}/show") }}"
                class="btn btn-info" data-toggle="tooltip" title="Detail">
                <i class="fas fa-info-circle"></i>
            </a>
            <a href="{{ url("/pregnancy-check-data/{$pregnancy->id}/edit") }}"
                class="btn btn-warning" data-toggle="tooltip" title="Edit">
                <i class="fas fa-edit"></i>
            </a>
        </div>
    </td>
@endif
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

    @if (Auth::user() && Auth::user()->role !== 'family_parent')
        <!-- Modal cetak laporan -->
        <div class="modal fade" id="reportModal" tabindex="-1" role="dialog" aria-labelledby="reportModalLabel"
            aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered" role="document">
                <form class="modal-content" action="{{ url('/print-report/pregnancy-check-data') }}" method="GET"
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
    @endif
@endsection

@push('scripts')
    <script src="{{ asset('modules/datatables/dataTables.min.js') }}"></script>
    <script src="{{ asset('js/page/modules-datatables.js') }}"></script>
    <script src="{{ asset('modules/select2/dist/js/select2.full.min.js') }}"></script>

    @if (Auth::user() && Auth::user()->role !== 'family_parent')
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
    @endif
@endpush
