@extends('layouts.dashboard')

@section('title', 'Detail Jadwal')

@push('styles')
    <style>
        .table {
            white-space: nowrap !important;
        }

        .table tr th {
            width: 10rem !important;
        }

        .table tr td:nth-child(2) {
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
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-body">
                            <div class="row g-4">
                                <div class="col-12">
                                    <h5 class="card-title text-center mb-4">{{ $schedule->title }}</h5>

                                    @php
                                        $offset = \Carbon\Carbon::now()->getOffset() / 3600;
                                        $zone = match ($offset) {
                                            7 => 'WIB',
                                            8 => 'WITA',
                                            9 => 'WIT',
                                            default => 'N/A',
                                        };
                                    @endphp

                                    <div class="table-responsive mb-2">
                                        <table class="table table-sm table-borderless">
                                            <tr>
                                                <th>Jadwal Dibuat Oleh</th>
                                                <td>:</td>
                                                <td>
                                                    @if (!is_null($schedule->officer_id))
                                                        @if (Auth::check())
                                                            @php
                                                                $user = Auth::user();
                                                            @endphp

                                                            @if ($user->role === 'admin')
                                                                <a
                                                                    href="{{ url("/officer-data/{$schedule->officer_id}/show") }}">
                                                                    {{ $schedule?->officers->fullname ?? 'N/A' }} -
                                                                    {{ $schedule?->officers->position ?? 'N/A' }}</a>
                                                            @elseif ($user->role === 'officer' && $user->officer_id === $schedule->officer_id)
                                                                <a
                                                                    href="{{ url("/officer-data/{$schedule->officer_id}/show") }}">
                                                                    {{ $schedule?->officers->fullname ?? 'N/A' }} -
                                                                    {{ $schedule?->officers->position ?? 'N/A' }}</a>
                                                            @else
                                                                {{ $schedule?->officers->fullname ?? 'N/A' }} -
                                                                {{ $schedule?->officers->position ?? 'N/A' }}
                                                            @endif
                                                        @endif
                                                    @else
                                                        N/A
                                                    @endif
                                                    | <span data-toggle="tooltip"
                                                        title="{{ $schedule->created_at ? \Carbon\Carbon::parse($schedule->created_at)->locale('id')->isoFormat('D MMMM YYYY - HH:mm') : 'N/A' }} {{ $zone }}">
                                                        {{ $schedule->created_at ? \Carbon\Carbon::parse($schedule->created_at)->locale('id')->diffForHumans() : 'N/A' }}
                                                    </span>
                                                </td>
                                            </tr>
                                            <tr>
                                                <th>Tanggal Kegiatan</th>
                                                <td>:</td>
                                                <td>
                                                    @php
                                                        $event_date = $schedule->event_date
                                                            ? \Carbon\Carbon::parse($schedule->event_date)
                                                                ->locale('id')
                                                                ->isoFormat('D MMMM YYYY')
                                                            : 'N/A';

                                                        echo $event_date;
                                                    @endphp
                                                </td>
                                            </tr>
                                            <tr>
                                                <th>Waktu Kegiatan</th>
                                                <td>:</td>
                                                <td>
                                                    {{ $schedule->start_time ? \Carbon\Carbon::parse($schedule->start_time)->format('H:i') : 'N/A' }}
                                                    -
                                                    {{ $schedule->end_time ? \Carbon\Carbon::parse($schedule->end_time)->format('H:i') : 'N/A' }}
                                                    {{ $zone }}
                                                </td>
                                            </tr>
                                            <tr>
                                                <th>Lokasi Kegiatan</th>
                                                <td>:</td>
                                                <td>{{ $schedule->event_location ?? 'N/A' }}</td>
                                            </tr>
                                        </table>
                                    </div>

                                    <article class="mb-5">
                                        {!! $schedule->description !!}
                                    </article>
                                </div>
                                <div class="col-12">
                                    <div class="d-flex justify-content-center justify-content-md-end align-items-center"
                                        style="gap: .5rem">
                                        <a href="javascript:history.back()" class="btn btn-secondary">Kembali</a>
                                        <button id="shareBtn" class="btn btn-primary">
                                            <i class="fas fa-share-nodes mr-1"></i> Bagikan
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
@endsection

@push('scripts')
    <script>
        document.getElementById('shareBtn').addEventListener('click', function() {
            if (navigator.share) {
                navigator.share({
                        title: document.title,
                        text: 'Jadwal Posyandu, {{ $event_date }}',
                        url: window.location.href
                    })
                    .then(() => console.log('Berhasil dibagikan!'))
                    .catch((error) => console.error('Gagal membagikan:', error));
            } else {
                swal({
                    title: 'Peringatan!',
                    text: 'Browser Anda tidak mendukung fitur berbagi ini.',
                    icon: 'warning',
                    buttons: {
                        confirm: {
                            text: 'Oke',
                            visible: true
                        }
                    },
                    closeOnClickOutside: true,
                    closeOnEsc: true
                });
            }
        });
    </script>
@endpush
