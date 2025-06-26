@extends('layouts.print')

@section('title', 'Laporan Data Orang Tua')

@section('main')
    <div class="main-content">
        <div class="text-center mb-4">
            <h2>LAPORAN DATA ORANG TUA</h2>
            <h3>SISTEM INFORMASI POSYANDU (SIDU)</h3>
            <hr style="border: 2px solid #000; margin: 20px 0;">
        </div>

<div class="mb-3">
    <table class="table-borderless" style="width: 100%;">
        <tr>
            <td style="width: 150px;"><strong>Tanggal Cetak</strong></td>
            <td style="width: 10px;">:</td>
            <td>{{ \Carbon\Carbon::now()->locale('id')->isoFormat('dddd, D MMMM Y') }}</td>
        </tr>
        <tr>
            <td><strong>Filter Status Akun</strong></td>
            <td>:</td>
            <td>
                @if($selectedStatus === 'all')
                    Semua Status
                @elseif($selectedStatus === 'active')
                    Aktif
                @elseif($selectedStatus === 'not-active')
                    Belum Verifikasi
                @endif
            </td>
        </tr>
        <tr>
            <td><strong>Filter Status Kehamilan</strong></td>
            <td>:</td>
            <td>
                @if($selectedPregnancyStatus === 'all')
                    Semua Status
                @elseif($selectedPregnancyStatus === 'pregnant')
                    Hamil
                @elseif($selectedPregnancyStatus === 'not-pregnant')
                    Tidak Hamil
                @endif
            </td>
        </tr>
        <tr>
            <td><strong>Filter Bulan Terdaftar</strong></td>
            <td>:</td>
            <td>
                @if($selectedMonth === 'all')
                    Semua Bulan
                @else
                    @php
                        $monthNames = [
                            '1' => 'Januari', '2' => 'Februari', '3' => 'Maret', '4' => 'April',
                            '5' => 'Mei', '6' => 'Juni', '7' => 'Juli', '8' => 'Agustus',
                            '9' => 'September', '10' => 'Oktober', '11' => 'November', '12' => 'Desember'
                        ];
                    @endphp
                    {{ $monthNames[$selectedMonth] ?? $selectedMonth }}
                @endif
            </td>
        </tr>
        <tr>
            <td><strong>Filter Tahun Terdaftar</strong></td>
            <td>:</td>
            <td>
                @if($selectedYear === 'all')
                    Semua Tahun
                @else
                    {{ $selectedYear }}
                @endif
            </td>
        </tr>
        <tr>
            <td><strong>Total Data</strong></td>
            <td>:</td>
            <td>{{ $parents->count() }} Orang Tua</td>
        </tr>
    </table>
</div>


        <div class="table-responsive">
            <table class="table table-bordered table-striped" style="font-size: 12px;">
                <thead class="thead-dark">
                    <tr class="text-center">
                        <th style="width: 5%;">No.</th>
                        <th style="width: 12%;">NIK Ibu</th>
                        <th style="width: 18%;">Nama Lengkap Ibu</th>
                        <th style="width: 18%;">Nama Lengkap Ayah</th>
                        <th style="width: 8%;">Jumlah Anak</th>
                        <th style="width: 12%;">Status Kehamilan</th>
                        <th style="width: 15%;">Nomor HP/WA</th>
                        <th style="width: 12%;">Status Akun</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($parents as $parent)
                        <tr>
                            <td class="text-center">{{ $loop->iteration }}</td>
                            <td class="text-center">{{ $parent->nik }}</td>
                            <td>{{ $parent->mother_fullname ?? 'N/A' }}</td>
                            <td>{{ $parent->father_fullname ?? 'N/A' }}</td>
                            <td class="text-center">{{ $parent->number_of_children ?? 'N/A' }}</td>
                            <td class="text-center">
                                <span class="badge {{ $parent->is_pregnant == 'Hamil' ? 'badge-info' : 'badge-secondary' }}">
                                    {{ $parent->is_pregnant }}
                                </span>
                            </td>
                            <td class="text-center">{{ $parent->users->first()->phone_number ?? 'N/A' }}</td>
                            <td class="text-center">
                                @if (!empty($parent->users->first()->verified_at) || $parent->users->first()->verified_at !== null)
                                    <span class="badge badge-success">Aktif</span>
                                @else
                                    <span class="badge badge-danger">Tidak Aktif</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center">Tidak ada data yang ditemukan</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($parents->count() > 0)
            <div class="mt-4">
                <div class="row">
                    <div class="col-md-6">
                        <h5>Ringkasan Data:</h5>
                        <ul class="list-unstyled">
                            <li><strong>Total Orang Tua:</strong> {{ $parents->count() }} orang</li>
                            <li><strong>Ibu Hamil:</strong> {{ $parents->where('is_pregnant', 'Hamil')->count() }} orang</li>
                            <li><strong>Ibu Tidak Hamil:</strong> {{ $parents->where('is_pregnant', 'Tidak Hamil')->count() }} orang</li>
                            <li><strong>Akun Aktif:</strong> {{ $parents->filter(function($parent) { return !empty($parent->users->first()->verified_at); })->count() }} orang</li>
                            <li><strong>Akun Belum Verifikasi:</strong> {{ $parents->filter(function($parent) { return empty($parent->users->first()->verified_at); })->count() }} orang</li>
                        </ul>
                    </div>
                    <div class="col-md-6 text-right">
                        <div class="mt-5">
                            <p>Dicetak pada: {{ \Carbon\Carbon::now()->locale('id')->isoFormat('dddd, D MMMM Y HH:mm') }} WIB</p>
                            <div class="mt-5">
                                <p>Petugas,</p>
                                <br><br><br>
                                <p><strong>{{ Auth::user()->officers->fullname ?? Auth::user()->username }}</strong></p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endif
    </div>

    <style>
        @media print {
            .table {
                font-size: 10px !important;
            }
            .badge {
                color: #000 !important;
                background-color: transparent !important;
                border: 1px solid #000 !important;
            }
            .thead-dark th {
                background-color: #343a40 !important;
                color: white !important;
                -webkit-print-color-adjust: exact;
            }
        }
    </style>

        <script>
        window.onload = function() {
            window.print();
        }
    </script>
@endsection
