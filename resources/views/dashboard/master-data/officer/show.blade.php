@extends('layouts.dashboard')

@section('title', 'Detail Data')

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
                                    <h5 class="card-title">Biodata</h5>

                                    <div class="table-responsive">
                                        <table class="table table-sm table-borderless">
                                            @if (Auth::user() && Auth::user()->role !== 'admin')
                                                <tr>
                                                    <th>NIK</th>
                                                    <td>:</td>
                                                    <td>{{ $officer->nik ?? 'N/A' }}</td>
                                                </tr>
                                            @endif
                                            <tr>
                                                <th>Nama Lengkap</th>
                                                <td>:</td>
                                                <td>{{ $officer->fullname ?? 'N/A' }}</td>
                                            </tr>
                                            <tr>
                                                <th>Tempat, Tanggal Lahir</th>
                                                <td>:</td>
                                                <td>{{ $officer->birth_place ?? 'N/A' }},
                                                    {{ $officer->date_of_birth ? \Carbon\Carbon::parse($officer->date_of_birth)->locale('id')->isoFormat('D MMMM YYYY') : 'N/A' }}
                                                </td>
                                            </tr>
                                            <tr>
                                                <th>Jenis Kelamin</th>
                                                <td>:</td>
                                                <td>{{ $officer->gender === 'L' ? 'Laki-Laki' : 'Perempuan' }}</td>
                                            </tr>
                                            <tr>
                                                <th>Pendidikan Terakhir</th>
                                                <td>:</td>
                                                <td>{{ $officer->last_education ?? 'N/A' }}</td>
                                            </tr>
                                            <tr>
                                                <th>Jabatan</th>
                                                <td>:</td>
                                                <td>{{ $officer->position ?? 'N/A' }}</td>
                                            </tr>
                                            <tr>
                                                <th>Alamat</th>
                                                <td>:</td>
                                                <td>{{ $officer->address ?? 'N/A' }}</td>
                                            </tr>
                                        </table>
                                    </div>
                                </div>

                                @if (Auth::user() && Auth::user()->role === 'admin')
                                    <div class="col-12">
                                        <hr class="mb-4">
                                        <h5 class="card-title">Akun</h5>

                                        <div class="table-responsive">
                                            <table class="table table-sm table-borderless">
                                                <tr>
                                                    <th>NIK</th>
                                                    <td>:</td>
                                                    <td>{{ $officer->nik ?? 'N/A' }}</td>
                                                </tr>
                                                <tr>
                                                    <th>Nama Pengguna</th>
                                                    <td>:</td>
                                                    <td>{{ $user->username ?? 'N/A' }}</td>
                                                </tr>
                                                <tr>
                                                    <th>Nomor HP/WA (Aktif)</th>
                                                    <td>:</td>
                                                    <td>{{ $user->phone_number ?? 'N/A' }}</td>
                                                </tr>
                                                <tr>
                                                    <th>Status Akun</th>
                                                    <td>:</td>
                                                    <td>
                                                        @if (is_null($user->verified_at) || empty($user->verified_at))
                                                            <span class="badge badge-danger">Tidak Aktif</span>
                                                        @else
                                                            <span class="badge badge-success">Aktif</span>
                                                        @endif
                                                    </td>
                                                </tr>
                                            </table>
                                        </div>
                                    </div>
                                @endif

                                <div class="col-12">
                                    <div class="d-flex justify-content-center justify-content-md-end align-items-center"
                                        style="gap: .5rem">
                                        <a href="javascript:history.back()" class="btn btn-secondary">Kembali</a>
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
@endpush
