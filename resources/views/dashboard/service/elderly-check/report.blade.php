@extends('layouts.print')

@section('title', 'Laporan Pemeriksaan Lansia')

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
                <th>Berat Badan (kg)</th>
                <th>Tekanan Darah</th>
                <th>Denyut Nadi (bpm)</th>
                <th>Kadar Gula Darah (mg/dL)</th>
                <th>Kadar Kolesterol (mg/dL)</th>
                <th>Kadar Asam Urat (mg/dL)</th>
                <th>Status Mobilitas</th>
                <th>Status Kognitif</th>
                <th>Status Gizi</th>
                <th>Keterangan</th>
                <th>Nama Petugas</th>
                <th>Jabatan Petugas</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($elderly_checks as $elderlyCheck)
                @php
                    $person = $elderlyCheck?->elderlies;
                    $officer = $elderlyCheck->officers;
                @endphp
                <tr>
                    <td class="text-right">{{ $loop->iteration }}</td>
                    <td>
                        {{ $elderlyCheck->check_date ? \Carbon\Carbon::parse($elderlyCheck->check_date)->locale('id')->isoFormat('D MMMM YYYY') : 'N/A' }}
                    </td>
                    <td class="text-right">{{ $person->nik ?? 'N/A' }}</td>
                    <td>{{ $person->fullname ?? 'N/A' }}</td>
                    <td>{{ $person->birth_place ?? 'N/A' }}</td>
                    <td>
                        {{ $person->date_of_birth ? \Carbon\Carbon::parse($person->date_of_birth)->locale('id')->isoFormat('D MMMM YYYY') : 'N/A' }}
                    </td>
                    <td class="text-right">{{ $elderlyCheck->age_in_checks ?? 'N/A' }}</td>
                    <td class="text-right">
                        {{ number_format($elderlyCheck->body_weight, 2) ?? 'N/A' }}</td>
                    <td class="text-right">{{ $elderlyCheck->blood_pressure ?? 'N/A' }}</td>
                    <td class="text-right">{{ $elderlyCheck->pulse_rate ?? 'N/A' }}</td>
                    <td class="text-right">
                        {{ $elderlyCheck->blood_sugar !== null ? number_format($elderlyCheck->blood_sugar, 2) : '-' }}
                    </td>
                    <td class="text-right">
                        {{ $elderlyCheck->cholesterol !== null ? number_format($elderlyCheck->cholesterol, 2) : '-' }}
                    </td>
                    <td class="text-right">
                        {{ $elderlyCheck->uric_acid !== null ? number_format($elderlyCheck->uric_acid, 2) : '-' }}
                    </td>
                    <td>{{ $elderlyCheck->mobility_status ?? '-' }}</td>
                    <td>{{ $elderlyCheck->cognitive_status ?? '-' }}</td>
                    <td>{{ $elderlyCheck->nutritional_status ?? '-' }}</td>
                    <td>{{ is_null($elderlyCheck->notes) || empty($elderlyCheck->notes) ? '-' : strip_tags($elderlyCheck->notes) }}
                    </td>
                    <td>
                        {{ $officer->fullname ?? 'N/A' }}
                    </td>
                    <td>{{ $officer->position ?? 'N/A' }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="19" class="text-center">Tidak ada data untuk ditampilkan.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
@endsection
