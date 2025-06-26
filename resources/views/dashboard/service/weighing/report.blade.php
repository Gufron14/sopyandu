@extends('layouts.print')

@section('title', 'Laporan Penimbangan')

@section('main')
    <div class="text-center mb-4">
        <h4>@yield('title')</h4>

        @if ($nutrition_status !== 'Semua')
            <h5>Status Gizi: {{ $nutrition_status ?? 'N/A' }}</h5>
        @endif

        <p class="mb-0">Periode: {{ \Carbon\Carbon::parse($early_period)->locale('id')->isoFormat('D MMMM YYYY') }} -
            {{ \Carbon\Carbon::parse($final_period)->locale('id')->isoFormat('D MMMM YYYY') }}</p>
    </div>

    <table class="table-bordered table-sm table w-100">
        <thead>
            <tr class="text-center">
                <th>No.</th>
                <th>Tanggal Penimbangan</th>
                <th>NIK Anak</th>
                <th>Nama Lengkap Anak</th>
                <th>L/P</th>
                <th>Tempat Lahir</th>
                <th>Tanggal Lahir</th>
                <th>Usia Saat Penimbangan</th>
                <th>NIK Ibu</th>
                <th>Nama Lengkap Ibu</th>
                <th>Berat (kg)</th>
                <th>Tinggi (cm)</th>
                <th>Lingkar Kepala (cm)</th>
                <th>Lingkar Lengan (cm)</th>
                <th>Status Gizi</th>
                <th>Keterangan</th>
                <th>Nama Petugas</th>
                <th>Jabatan Petugas</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($weighings as $weighing)
                @php
                    $child = $weighing->familyChildren;
                    $parent = $child?->familyParents;
                    $officer = $weighing->officers;
                @endphp
                <tr>
                    <td class="text-right">{{ $loop->iteration }}</td>
                    <td>
                        {{ $weighing->weighing_date ? \Carbon\Carbon::parse($weighing->weighing_date)->locale('id')->isoFormat('D MMMM YYYY') : 'N/A' }}
                    </td>
                    <td class="text-right">{{ $child->nik ?? 'N/A' }}</td>
                    <td>{{ $child->fullname ?? 'N/A' }}</td>
                    <td>{{ $child->gender }}</td>
                    <td>{{ $child->birth_place ?? 'N/A' }}</td>
                    <td>
                        {{ $child->date_of_birth ? \Carbon\Carbon::parse($child->date_of_birth)->locale('id')->isoFormat('D MMMM YYYY') : 'N/A' }}
                    </td>
                    <td class="text-right">{{ $weighing->age_in_checks ?? 'N/A' }}</td>
                    <td class="text-right">{{ $parent->nik ?? 'N/A' }}</td>
                    <td>{{ $parent->mother_fullname ?? 'N/A' }}</td>
                    <td class="text-right">{{ number_format($weighing->weight, 2) }}</td>
                    <td class="text-right">{{ number_format($weighing->height, 2) }}</td>
                    <td class="text-right">
                        {{ number_format($weighing->head_circumference, 2) }}
                    </td>
                    <td class="text-right">
                        {{ number_format($weighing->arm_circumference, 2) }}
                    </td>
                    <td>{{ $weighing->nutrition_status ?? 'N/A' }}</td>
                    <td>
                        {{ is_null($weighing->notes) || empty($weighing->notes) ? '-' : strip_tags($weighing->notes) }}
                    </td>
                    <td>{{ $officer->fullname ?? 'N/A' }}</td>
                    <td>{{ $officer->position ?? 'N/A' }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="18" class="text-center">Tidak ada data untuk ditampilkan.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
@endsection
