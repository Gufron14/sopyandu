@extends('layouts.dashboard')

@section('title', 'Detail Orang Tua')

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

                                    <div class="row g-4">
                                        <div class="col-lg-6 col-md-6 col-sm-12 col-12">
                                            <h6>Ibu</h6>
                                            <div class="table-responsive">
                                                <table class="table table-sm table-borderless">
                                                    @if (Auth::user() && Auth::user()->role !== 'admin')
                                                        <tr>
                                                            <th>NIK Ibu</th>
                                                            <td>:</td>
                                                            <td>{{ $parent->nik ?? 'N/A' }}</td>
                                                        </tr>
                                                    @endif
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
                                                        <th>Satus Kehamilan</th>
                                                        <td>:</td>
                                                        <td>
                                                            @if ($parent->is_pregnant == 'Hamil')
                                                                <span class="badge text-white"
                                                                    style="background-color: violet">{{ $parent->is_pregnant }}</span>
                                                            @else
                                                                <span class="badge text-white"
                                                                    style="background-color: turquoise">{{ $parent->is_pregnant }}</span>
                                                            @endif
                                                        </td>
                                                    </tr>
                                                </table>
                                            </div>
                                        </div>
                                        <div class="col-lg-6 col-md-6 col-sm-12 col-12">
                                            <h6>Ayah</h6>
                                            <div class="table-responsive">
                                                <table class="table table-sm table-borderless">
                                                    <tr>
                                                        <th>Nama Lengkap</th>
                                                        <td>:</td>
                                                        <td>{{ $parent->father_fullname ?? 'N/A' }}</td>
                                                    </tr>
                                                    <tr>
                                                        <th>Tempat, Tanggal Lahir</th>
                                                        <td>:</td>
                                                        <td>{{ $parent->father_birth_place ?? 'N/A' }},
                                                            {{ $parent->father_date_of_birth ? \Carbon\Carbon::parse($parent->father_date_of_birth)->locale('id')->isoFormat('D MMMM YYYY') : 'N/A' }}
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <th>Golongan Darah</th>
                                                        <td>:</td>
                                                        <td>{{ $parent->father_blood_type ?? 'N/A' }}</td>
                                                    </tr>
                                                    <tr>
                                                        <th>Usia</th>
                                                        <td>:</td>
                                                        <td>
                                                            {{ $parent->father_date_of_birth ? \Carbon\Carbon::parse($parent->father_date_of_birth)->diff(\Carbon\Carbon::now())->y : 'N/A' }}
                                                            tahun,
                                                            {{ $parent->father_date_of_birth ? \Carbon\Carbon::parse($parent->father_date_of_birth)->diff(\Carbon\Carbon::now())->m : 'N/A' }}
                                                            bulan,
                                                            {{ $parent->father_date_of_birth ? \Carbon\Carbon::parse($parent->father_date_of_birth)->diff(\Carbon\Carbon::now())->d : 'N/A' }}
                                                            hari
                                                        </td>
                                                    </tr>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-12">
                                    <hr class="mb-4">
                                    <h5 class="card-title">Lainnya</h5>

                                    <div class="table-responsive">
                                        <table class="table table-sm table-borderless">
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

                                @if (Auth::user() && Auth::user()->role === 'admin')
                                    <div class="col-12">
                                        <hr class="mb-4">
                                        <h5 class="card-title">Akun</h5>

                                        <div class="table-responsive">
                                            <table class="table table-sm table-borderless">
                                                <tr>
                                                    <th>NIK Ibu</th>
                                                    <td>:</td>
                                                    <td>{{ $parent->nik ?? 'N/A' }}</td>
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
