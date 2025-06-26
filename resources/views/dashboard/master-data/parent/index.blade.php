@extends('layouts.dashboard')

@section('title', 'Data Orang Tua')

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
                            @if (Auth::user() && Auth::user()->role == 'admin')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div class="d-flex flex-wrap" style="gap: 1rem;">
        <form method="GET" action="{{ route('parent-data.index') }}" style="width: 16rem">
            <input type="hidden" name="pregnancy_status" value="{{ $selectedPregnancyStatus }}">
            <input type="hidden" name="month" value="{{ $selectedMonth }}">
            <input type="hidden" name="year" value="{{ $selectedYear }}">
            <div class="input-group">
                <label class="input-group-prepend" for="status">
                    <span class="input-group-text bg-light">Status Akun</span>
                </label>
                <select class="custom-select" name="status" id="status" onchange="this.form.submit()">
                    @foreach ($statuses as $value => $label)
                        <option value="{{ $value }}" {{ $value == $selectedStatus ? 'selected' : '' }}>
                            {{ $label }}
                        </option>
                    @endforeach
                </select>
            </div>
        </form>

        <form method="GET" action="{{ route('parent-data.index') }}" style="width: 16rem">
            <input type="hidden" name="status" value="{{ $selectedStatus }}">
            <input type="hidden" name="month" value="{{ $selectedMonth }}">
            <input type="hidden" name="year" value="{{ $selectedYear }}">
            <div class="input-group">
                <label class="input-group-prepend" for="pregnancy_status">
                    <span class="input-group-text bg-light">Status Kehamilan</span>
                </label>
                <select class="custom-select" name="pregnancy_status" id="pregnancy_status" onchange="this.form.submit()">
                    @foreach ($pregnancyStatuses as $value => $label)
                        <option value="{{ $value }}" {{ $value == $selectedPregnancyStatus ? 'selected' : '' }}>
                            {{ $label }}
                        </option>
                    @endforeach
                </select>
            </div>
        </form>

        <form method="GET" action="{{ route('parent-data.index') }}" style="width: 14rem">
            <input type="hidden" name="status" value="{{ $selectedStatus }}">
            <input type="hidden" name="pregnancy_status" value="{{ $selectedPregnancyStatus }}">
            <input type="hidden" name="year" value="{{ $selectedYear }}">
            <div class="input-group">
                <label class="input-group-prepend" for="month">
                    <span class="input-group-text bg-light">Bulan</span>
                </label>
                <select class="custom-select" name="month" id="month" onchange="this.form.submit()">
                    @foreach ($months as $value => $label)
                        <option value="{{ $value }}" {{ $value == $selectedMonth ? 'selected' : '' }}>
                            {{ $label }}
                        </option>
                    @endforeach
                </select>
            </div>
        </form>

        <form method="GET" action="{{ route('parent-data.index') }}" style="width: 12rem">
            <input type="hidden" name="status" value="{{ $selectedStatus }}">
            <input type="hidden" name="pregnancy_status" value="{{ $selectedPregnancyStatus }}">
            <input type="hidden" name="month" value="{{ $selectedMonth }}">
            <div class="input-group">
                <label class="input-group-prepend" for="year">
                    <span class="input-group-text bg-light">Tahun</span>
                </label>
                <select class="custom-select" name="year" id="year" onchange="this.form.submit()">
                    @foreach ($years as $value => $label)
                        <option value="{{ $value }}" {{ $value == $selectedYear ? 'selected' : '' }}>
                            {{ $label }}
                        </option>
                    @endforeach
                </select>
            </div>
        </form>
    </div>

    <div class="d-flex" style="gap: 0.5rem;">
        <a href="{{ route('parent-data.print', ['status' => $selectedStatus, 'pregnancy_status' => $selectedPregnancyStatus, 'month' => $selectedMonth, 'year' => $selectedYear]) }}" 
           class="btn btn-success" target="_blank">
            <i class="fas fa-print mr-1"></i> Cetak Data Laporan
        </a>
        <a href="{{ url('/parent-data/create') }}" class="btn btn-primary">Tambah</a>
    </div>
</div>


                            @endif

                            <div class="table-responsive">
                                <table class="table-striped table" id="table-1">
                                    <thead>
                                        <tr class="text-center">
                                            <th>No.</th>
                                            <th>NIK Ibu</th>
                                            <th>Nama Lengkap Ibu</th>
                                            <th>Nama Lengkap Ayah</th>
                                            <th>Jumlah Anak</th>
                                            <th>Status Kehamilan</th>
                                            @if (auth()->check() && auth()->user()->role == 'admin')
                                                <th>Nama Pengguna</th>
                                            @endif
                                            <th>Nomor HP/WA</th>
                                            <th>Status Akun</th>
                                            <th>Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($parents as $parent)
                                            <tr>
                                                <td class="text-right">{{ $loop->iteration }}</td>
                                                <td class="text-right">{{ $parent->nik }}</td>
                                                <td>{{ $parent->mother_fullname ?? 'N/A' }}</td>
                                                <td>{{ $parent->father_fullname ?? 'N/A' }}</td>
                                                <td class="text-right">{{ $parent->number_of_children ?? 'N/A' }}</td>
                                                <td class="text-center">
                                                    @if ($parent->is_pregnant == 'Hamil')
                                                        <span class="badge text-white"
                                                            style="background-color: violet">{{ $parent->is_pregnant }}</span>
                                                    @else
                                                        <span class="badge text-white"
                                                            style="background-color: turquoise">{{ $parent->is_pregnant }}</span>
                                                    @endif
                                                </td>
                                                @if (auth()->check() && auth()->user()->role == 'admin')
                                                    <td class="font-italic">
                                                        {{ $parent->users->first()->username ?? 'N/A' }}
                                                    </td>
                                                @endif
                                                <td class="text-right">
                                                    {{ $parent->users->first()->phone_number ?? 'N/A' }}
                                                </td>
                                                <td class="text-center">
                                                    @if (!empty($parent->users->first()->verified_at) || $parent->users->first()->verified_at !== null)
                                                        <span class="badge badge-success">Aktif</span>
                                                    @else
                                                        @if (auth()->check() && auth()->user()->role == 'admin')
                                                            <form action="{{ url("/parent-data/{$parent->id}/verify") }}"
                                                                method="POST" id="verify-form-{{ $parent->id }}"
                                                                class="d-inline">
                                                                @method('PUT')
                                                                @csrf
                                                                <button type="submit" class="btn btn-warning btn-verify">
                                                                    Verifikasi
                                                                </button>
                                                            </form>
                                                        @else
                                                            <span class="badge badge-danger">Tidak Aktif</span>
                                                        @endif
                                                    @endif
                                                </td>
                                                <td>
                                                    <div class="d-flex justify-content-center" style="gap: .5rem">
                                                        <a href="{{ url("/parent-data/{$parent->id}/show") }}"
                                                            class="btn btn-info" data-toggle="tooltip" title="Detail">
                                                            <i class="fas fa-info-circle"></i>
                                                        </a>
                                                        @if (Auth::user()->officer_id !== null)
                                                            @if (Auth::user()->officers->position !== 'Lurah' && Auth::user()->officers->position !== 'Kepala Lingkungan')
                                                                <a href="{{ url("/parent-data/{$parent->id}/edit") }}"
                                                                    class="btn btn-primary" data-toggle="tooltip"
                                                                    title="Ubah">
                                                                    <i class="fas fa-pencil"></i>
                                                                </a>
                                                                @if (auth()->check() && auth()->user()->role == 'admin')
                                                                    <form action="{{ url("/parent-data/{$parent->id}") }}"
                                                                        method="POST" id="delete-form-{{ $parent->id }}"
                                                                        class="d-inline">
                                                                        @method('delete')
                                                                        @csrf
                                                                        <button type="submit"
                                                                            class="btn btn-danger btn-delete"
                                                                            data-toggle="tooltip" title="Hapus">
                                                                            <i class="fas fa-trash"></i>
                                                                        </button>
                                                                    </form>
                                                                    <form
                                                                        action="{{ url("/parent-data/{$parent->id}/reset") }}"
                                                                        method="POST" id="reset-form-{{ $parent->id }}"
                                                                        class="d-inline">
                                                                        @method('PUT')
                                                                        @csrf
                                                                        <button type="submit"
                                                                            class="btn btn-warning btn-reset"
                                                                            data-toggle="tooltip" title="Reset Akun">
                                                                            <i class="fas fa-key"></i>
                                                                        </button>
                                                                    </form>
                                                                @endif
                                                            @endif
                                                        @endif
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
@endsection

@push('scripts')
    <script src="{{ asset('modules/datatables/dataTables.min.js') }}"></script>
    <script src="{{ asset('js/page/modules-datatables.js') }}"></script>

    @if (Auth::user()->officer_id !== null)
        @if (Auth::user()->officers->position !== 'Lurah' && Auth::user()->officers->position !== 'Kepala Lingkungan')
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

                    @if (auth()->check() && auth()->user()->role == 'admin')
                        // Hanya admin yang diperbolehkkan reset dan verifikasi akun
                        // Gunakan delegasi untuk tombol reset
                        $(document).on('click', '.btn-reset', function(e) {
                            e.preventDefault();

                            const formId = $(this).closest('form').attr('id');

                            swal({
                                title: 'Reset Akun',
                                text: 'Apakah Anda yakin ingin mereset Akun untuk data ini?',
                                icon: 'warning',
                                buttons: {
                                    cancel: 'Batal',
                                    confirm: {
                                        text: 'Ya, Reset!',
                                        value: true,
                                        className: 'btn-info',
                                    }
                                },
                                dangerMode: true,
                            }).then((willReset) => {
                                if (willReset) {
                                    $('#' + formId).submit();
                                }
                            });
                        });
                        // Gunakan delegasi untuk tombol verifikasi
                        $(document).on('click', '.btn-verify', function(e) {
                            e.preventDefault();

                            const formId = $(this).closest('form').attr('id');

                            swal({
                                title: 'Verifikasi Akun',
                                text: 'Apakah Anda yakin ingin verifikasi Akun untuk data ini?',
                                icon: 'warning',
                                buttons: {
                                    cancel: 'Batal',
                                    confirm: {
                                        text: 'Ya, Verifikasi!',
                                        value: true,
                                        className: 'btn-info',
                                    }
                                },
                                dangerMode: true,
                            }).then((willVerify) => {
                                if (willVerify) {
                                    $('#' + formId).submit();
                                }
                            });
                        });
                    @endif
                });
            </script>
        @endif
    @endif
    @if (auth()->check() && auth()->user()->role == 'admin')
        @if (session('whatsapp_url'))
            <script type="text/javascript">
                window.onload = function() {
                    setTimeout(function() {
                        var whatsappUrl = "{{ session('whatsapp_url') }}";
                        window.open(whatsappUrl, '_blank');
                    }, 1000); // Tunggu 1 detik sebelum membuka tab baru
                };
            </script>
        @endif
    @endif
@endpush
