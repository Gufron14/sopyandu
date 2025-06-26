@extends('layouts.print')

@section('title', 'Laporan Data Anak')

@section('main')
    <div class="header">
        <h2>LAPORAN DATA ANAK</h2>
        <div class="mb-3">
            <table class="table-borderless" style="width: 100%;">
                <tr>
                    <td style="width: 150px;"><strong>Tanggal Cetak</strong></td>
                    <td style="width: 10px;">:</td>
                    <td>{{ \Carbon\Carbon::now()->locale('id')->isoFormat('dddd, D MMMM Y') }}</td>
                </tr>
                <tr>
                    <td><strong>Filter Bulan Terdaftar</strong></td>
                    <td>:</td>
                    <td>
                        @if($selectedMonth === 'all')
                            Semua Bulan
                        @else
                            @switch($selectedMonth)
                                @case('1') Januari @break
                                @case('2') Februari @break
                                @case('3') Maret @break
                                @case('4') April @break
                                @case('5') Mei @break
                                @case('6') Juni @break
                                @case('7') Juli @break
                                @case('8') Agustus @break
                                @case('9') September @break
                                @case('10') Oktober @break
                                @case('11') November @break
                                @case('12') Desember @break
                                @default {{ $selectedMonth }}
                            @endswitch
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
                    <td>{{ $children->count() }} Anak</td>
                </tr>
            </table>
        </div>
    </div>

    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>NIK</th>
                <th>Nama Lengkap</th>
                <th>Jenis Kelamin</th>
                <th>Tempat Lahir</th>
                <th>Tanggal Lahir</th>
                <th>Nama Ibu</th>
                <th>Tanggal Terdaftar</th>
            </tr>
        </thead>
        <tbody>
            @forelse($children as $index => $child)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ $child->nik ?? 'N/A' }}</td>
                    <td>{{ $child->fullname ?? 'N/A' }}</td>
                    <td>{{ $child->gender }}</td>
                    <td>{{ $child->birth_place ?? 'N/A' }}</td>
                    <td>{{ $child->date_of_birth ? \Carbon\Carbon::parse($child->date_of_birth)->locale('id')->isoFormat('D MMMM YYYY') : 'N/A' }}</td>
                    <td>{{ $child->familyParents->mother_fullname ?? 'N/A' }}</td>
                    <td>{{ $child->created_at ? \Carbon\Carbon::parse($child->created_at)->locale('id')->isoFormat('D MMMM YYYY') : 'N/A' }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="8" style="text-align: center;">Tidak ada data</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <script>
        window.onload = function() {
            window.print();
        }
    </script>
@endsection
