@extends('layouts.dashboard')

@section('title', 'Jadwal Posyandu')

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
                                <div class="btn-group" role="group">
                                    <a href="{{ route('schedule.index') }}"
                                        class="btn {{ $filter == 'all' ? 'btn-primary' : 'btn-outline-primary' }}">Semua</a>
                                    <a href="{{ route('schedule.index', ['filter' => 'weekly']) }}"
                                        class="btn {{ $filter == 'weekly' ? 'btn-primary' : 'btn-outline-primary' }}">Minggu
                                        ini</a>
                                    <a href="{{ route('schedule.index', ['filter' => 'monthly']) }}"
                                        class="btn {{ $filter == 'monthly' ? 'btn-primary' : 'btn-outline-primary' }}">Bulan
                                        ini</a>
                                </div>

                                @php
                                    $canAccessContent =
                                        Auth::user()->officer_id !== null &&
                                        !in_array(Auth::user()->officers->position, ['Lurah', 'Kepala Lingkungan']) &&
                                        Auth::user()->role !== 'family_parent';

                                    $offset = \Carbon\Carbon::now()->getOffset() / 3600;
                                    $zone = match ($offset) {
                                        7 => 'WIB',
                                        8 => 'WITA',
                                        9 => 'WIT',
                                        default => 'N/A',
                                    };
                                @endphp

                                @if ($canAccessContent)
                                    <a href="{{ url('/schedule/create') }}" class="btn btn-primary ml-auto">Tambah</a>
                                @endif
                            </div>

                            <div class="table-responsive">
                                <table class="table-striped table" id="table-1">
                                    <thead>
                                        <tr class="text-center">
                                            <th>No.</th>
                                            <th>Tanggal</th>
                                            <th>Waktu ({{ $zone }})</th>
                                            <th>Nama Kegiatan</th>
                                            <th>Lokasi</th>
                                            <th>Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($schedules as $schedule)
                                            <tr>
                                                <td class="text-right">{{ $loop->iteration }}</td>
                                                <td>
                                                    {{ $schedule->event_date ? \Carbon\Carbon::parse($schedule->event_date)->locale('id')->isoFormat('D MMMM YYYY') : 'N/A' }}
                                                </td>
                                                <td class="text-center">
                                                    {{ $schedule->start_time ? \Carbon\Carbon::parse($schedule->start_time)->format('H:i') : 'N/A' }}
                                                    -
                                                    {{ $schedule->end_time ? \Carbon\Carbon::parse($schedule->end_time)->format('H:i') : 'N/A' }}
                                                </td>
                                                <td>{{ $schedule->title ?? 'N/A' }}</td>
                                                <td>{{ $schedule->event_location ?? 'N/A' }}</td>
                                                <td>
                                                    <div class="d-flex justify-content-center" style="gap: .5rem">
                                                        <a href="{{ url("/schedule/{$schedule->id}/show") }}"
                                                            class="btn btn-info" data-toggle="tooltip" title="Detail">
                                                            <i class="fas fa-info-circle"></i>
                                                        </a>
                                                        @if ($canAccessContent)
                                                            <a href="{{ url("/schedule/{$schedule->id}/edit") }}"
                                                                class="btn btn-primary" data-toggle="tooltip"
                                                                title="Ubah">
                                                                <i class="fas fa-pencil"></i>
                                                            </a>
                                                            <form action="{{ url("/schedule/{$schedule->id}") }}"
                                                                method="POST" id="delete-form-{{ $schedule->id }}"
                                                                class="d-inline">
                                                                @method('delete')
                                                                @csrf
                                                                <button type="submit" class="btn btn-danger btn-delete"
                                                                    data-toggle="tooltip" title="Hapus">
                                                                    <i class="fas fa-trash"></i>
                                                                </button>
                                                            </form>
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

    @if ($canAccessContent)
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
    @endif
@endpush
