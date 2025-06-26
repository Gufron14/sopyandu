@extends('layouts.print')

@section('title', 'Laporan Imunisasi')

@section('main')
    <div class="text-center mb-4">
        <h4>@yield('title')</h4>

        @if ($vaccine_status !== 'Semua')
            <h5>Status Imunisasi: {{ $vaccine_status === 'Ya' ? 'Divaksin' : 'Tidak Divaksin' }}</h5>
        @endif

        <p class="mb-0">Periode: {{ \Carbon\Carbon::parse($early_period)->locale('id')->isoFormat('D MMMM YYYY') }} -
            {{ \Carbon\Carbon::parse($final_period)->locale('id')->isoFormat('D MMMM YYYY') }}</p>
    </div>

    <table class="table-bordered table-sm table w-100">
        <thead>
            <tr class="text-center">
                <th>No.</th>
                <th>Tanggal Imunisasi</th>
                <th>NIK Anak</th>
                <th>Nama Lengkap Anak</th>
                <th>L/P</th>
                <th>Tempat Lahir</th>
                <th>Tanggal Lahir</th>
                <th>Usia Saat Imunisasi</th>
                <th>NIK Ibu</th>
                <th>Nama Lengkap Ibu</th>
                <th>Vaksinasi</th>
                <th>Nama Vaksin</th>
                <th>Kategori Vaksin</th>
                <th>Efek Samping</th>
                <th>Keterangan</th>
                <th>Nama Petugas</th>
                <th>Jabatan Petugas</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($immunizations as $immunization)
                @php
                    $child = $immunization->familyChildren;
                    $parent = $child?->familyParents;
                    $officer = $immunization->officers;
                @endphp
                <tr>
                    <td class="text-right">{{ $loop->iteration }}</td>
                    <td>
                        {{ $immunization->immunization_date ? \Carbon\Carbon::parse($immunization->immunization_date)->locale('id')->isoFormat('D MMMM YYYY') : 'N/A' }}
                    </td>
                    <td class="text-right">{{ $child->nik ?? 'N/A' }}</td>
                    <td>{{ $child->fullname ?? 'N/A' }}</td>
                    <td>{{ $child->gender }}</td>
                    <td>{{ $child->birth_place ?? 'N/A' }}</td>
                    <td>
                        {{ $child->date_of_birth ? \Carbon\Carbon::parse($child->date_of_birth)->locale('id')->isoFormat('D MMMM YYYY') : 'N/A' }}
                    </td>
                    <td class="text-right">{{ $immunization->age_in_checks ?? 'N/A' }}</td>
                    <td class="text-right">{{ $parent->nik ?? 'N/A' }}</td>
                    <td>{{ $parent->mother_fullname ?? 'N/A' }}</td>
                    <td class="text-center">
                        {{ is_null($immunization->vaccine_id) || empty($immunization->vaccine_id) ? 'Tidak' : 'Ya' }}
                    </td>
                    <td>
                        {{ is_null($immunization->vaccine_id) || empty($immunization->vaccine_id) ? '-' : $immunization->vaccines->vaccine_name }}
                    </td>
                    <td class="text-center">
                        {{ is_null($immunization->vaccine_id) || empty($immunization->vaccine_id) ? '-' : $immunization->vaccine_category }}
                    </td>
                    <td>
                        {{ is_null($immunization->side_effects) || empty($immunization->side_effects) ? '-' : strip_tags($immunization->side_effects) }}
                    </td>
                    <td>
                        {{ is_null($immunization->notes) || empty($immunization->notes) ? '-' : strip_tags($immunization->notes) }}
                    </td>
                    <td>{{ $officer->fullname ?? 'N/A' }}</td>
                    <td>{{ $officer->position ?? 'N/A' }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="17" class="text-center">Tidak ada data untuk ditampilkan.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
@endsection
