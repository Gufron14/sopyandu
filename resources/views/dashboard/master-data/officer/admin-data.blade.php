@extends('layouts.dashboard')

@section('title', 'Data Admin')

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
                            @if (auth()->check() && auth()->user()->role == 'admin')
                                <div class=" d-flex justify-content-between align-items-center mb-4">
                                    @include('components.role-list')

                                    <a href="{{ url('/officer-data/create') }}" class="btn btn-primary ml-auto">Tambah</a>
                                </div>
                            @endif

                            <div class="table-responsive">
                                <table class="table-striped table" id="table-1">
                                    <thead>
                                        <tr class="text-center">
                                            <th>No.</th>
                                            <th>Nama Lengkap</th>
                                            <th>L/P</th>
                                            @if (auth()->check() && auth()->user()->role == 'admin')
                                                <th>Nama Pengguna</th>
                                            @endif
                                            <th>Nomor HP/WA</th>
                                            <th>Jabatan</th>
                                            <th>Status Akun</th>
                                            <th>Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($admins as $admin)
                                            <tr>
                                                <td class="text-right">{{ $loop->iteration }}</td>
                                                <td>{{ $admin->fullname ?? 'N/A' }}</td>
                                                <td class="text-center">{{ $admin->gender ?? 'N/A' }}</td>
                                                @if (auth()->check() && auth()->user()->role == 'admin')
                                                    <td class="font-italic">
                                                        {{ $admin->users->first()->username ?? 'N/A' }}
                                                    </td>
                                                @endif
                                                <td class="text-right">
                                                    {{ $admin->users->first()->phone_number ?? 'N/A' }}
                                                </td>
                                                <td>{{ $admin->position ?? 'N/A' }}</td>
                                                <td class="text-center">
                                                    @if (!empty($admin->users->first()->verified_at) || $admin->users->first()->verified_at !== null)
                                                        <span class="badge badge-success">Aktif</span>
                                                    @else
                                                        <span class="badge badge-danger">Tidak Aktif</span>
                                                    @endif
                                                </td>
                                                <td>
                                                    <div class="d-flex justify-content-center" style="gap: .5rem">
                                                        <a href="{{ url("/officer-data/{$admin->id}/show") }}"
                                                            class="btn btn-info" data-toggle="tooltip" title="Detail">
                                                            <i class="fas fa-info-circle"></i>
                                                        </a>
                                                        @if (auth()->check() && auth()->user()->role == 'admin')
                                                            <a href="{{ url("/officer-data/{$admin->id}/edit") }}"
                                                                class="btn btn-primary" data-toggle="tooltip"
                                                                title="Ubah">
                                                                <i class="fas fa-pencil"></i>
                                                            </a>
                                                            <form action="{{ url("/officer-data/{$admin->id}") }}"
                                                                method="POST" id="delete-form-{{ $admin->id }}"
                                                                class="d-inline">
                                                                @method('delete')
                                                                @csrf
                                                                <button type="submit" class="btn btn-danger btn-delete"
                                                                    data-toggle="tooltip" title="Hapus">
                                                                    <i class="fas fa-trash"></i>
                                                                </button>
                                                            </form>
                                                            <form action="{{ url("/officer-data/{$admin->id}/reset") }}"
                                                                method="POST" id="reset-form-{{ $admin->id }}"
                                                                class="d-inline">
                                                                @method('PUT')
                                                                @csrf
                                                                <button type="submit" class="btn btn-warning btn-reset"
                                                                    data-toggle="tooltip" title="Reset Akun">
                                                                    <i class="fas fa-key"></i>
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

    @if (auth()->check() && auth()->user()->role == 'admin')
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
            });
        </script>
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
