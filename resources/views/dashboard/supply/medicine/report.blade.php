@extends('layouts.print')

@section('title', 'Laporan Pemberian Obat')

@section('main')
    <div class="text-center mb-4">
        <h4>@yield('title')</h4>

        @if ($service_name !== 'Semua')
            <h5>Jenis Pelayanan: {{ $service_name }}</h5>
        @endif

        <p class="mb-0">Periode: {{ \Carbon\Carbon::parse($early_period)->locale('id')->isoFormat('D MMMM YYYY') }} -
            {{ \Carbon\Carbon::parse($final_period)->locale('id')->isoFormat('D MMMM YYYY') }}</p>
    </div>

    <table class="table-bordered table-sm table w-100">
        <thead>
            <tr class="text-center">
                <th>No.</th>
                <th>Tanggal</th>
                <th>Jenis Pelayanan</th>
                <th>Nama Pasien</th>
                <th>Obat & Jumlah</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($groupedMedicines as $item)
                <tr>
                    <td class="text-right">{{ $loop->iteration }}</td>
                    <td>{{ \Carbon\Carbon::parse($item->date)->locale('id')->isoFormat('D MMMM YYYY') }}
                    </td>
                    <td>
                        @switch($item->type)
                            @case('Imunisasi')
                                Imunisasi
                            @break

                            @case('Pemeriksaan Ibu Hamil')
                                Pemeriksaan Ibu Hamil
                            @break

                            @case('Pemeriksaan Lansia')
                                Pemeriksaan Lansia
                            @break
                        @endswitch
                    </td>
                    <td>
                        {{ $item->patient_name }}
                    </td>
                    <td>{{ $item->medicines }}</td>
                </tr>
                @empty
                    <tr>
                        <td colspan="5" class="text-center">Tidak ada data untuk ditampilkan.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    @endsection
