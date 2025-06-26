@extends('layouts.print')

@section('title', 'Laporan Pemeriksaan Ibu Hamil')

@section('main')
    <div class="text-center mb-4">
        <h4>@yield('title')</h4>

        <p class="mb-0">Periode: {{ \Carbon\Carbon::parse($early_period)->locale('id')->isoFormat('D MMMM YYYY') }} -
            {{ \Carbon\Carbon::parse($final_period)->locale('id')->isoFormat('D MMMM YYYY') }}</p>
    </div>

    <table class="table-bordered table-sm table w-100">
        <thead>
            <tr class="text-center">
                <th>No.</th>
                <th>Tanggal Pemeriksaan</th>
                <th>NIK</th>
                <th>Nama Lengkap</th>
                <th>Tempat Lahir</th>
                <th>Tanggal Lahir</th>
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
            </tr>
        </thead>
        <tbody>
            @forelse ($pregnancy_checks as $pregnancy)
                @php
                    $parent = $pregnancy?->familyParents;
                    $officer = $pregnancy->officers;
                @endphp
                <tr data-toggle="tooltip" title="{{ $parent->nik . ' - ' . $parent->mother_fullname }}" data-placement="right">
                    <td class="text-right">{{ $loop->iteration }}</td>
                    <td>
                        {{ $pregnancy->check_date ? \Carbon\Carbon::parse($pregnancy->check_date)->locale('id')->isoFormat('D MMMM YYYY') : 'N/A' }}
                    </td>
                    <td class="text-right">
                        {{ $parent->nik ?? 'N/A' }}
                    </td>
                    <td>{{ $parent->mother_fullname ?? 'N/A' }}</td>
                    <td>{{ $parent->mother_birth_place ?? 'N/A' }}</td>
                    <td>
                        {{ $parent->mother_date_of_birth ? \Carbon\Carbon::parse($parent->mother_date_of_birth)->locale('id')->isoFormat('D MMMM YYYY') : 'N/A' }}
                    </td>
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
                    <td>{{ is_null($pregnancy->notes) || empty($pregnancy->notes) ? '-' : strip_tags($pregnancy->notes) }}
                    </td>
                    <td>
                        {{ $officer->fullname ?? 'N/A' }}
                    </td>
                    <td>
                        {{ $officer->position ?? 'N/A' }}
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="20" class="text-center">Tidak ada data untuk ditampilkan.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
@endsection
