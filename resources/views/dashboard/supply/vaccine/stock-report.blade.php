@extends('layouts.print')

@section('title', 'Laporan Stok Vaksin')

@section('main')
    <div class="text-center mb-4">
        <h4>@yield('title')</h4>

        @if ($print_criteria !== 'Semua')
            <h5>Berdasarkan {{ $print_criteria }}</h5>

            <p class="mb-0">Periode: {{ \Carbon\Carbon::parse($early_period)->locale('id')->isoFormat('D MMMM YYYY') }} -
                {{ \Carbon\Carbon::parse($final_period)->locale('id')->isoFormat('D MMMM YYYY') }}</p>
        @endif
    </div>

    <table class="table-bordered table-sm table w-100">
        <thead>
            <tr class="text-center">
                <th>No.</th>
                <th>Nama Vaksin</th>
                <th>Unit</th>
                <th>Stok</th>
                <th>Tanggal Masuk</th>
                <th>Tanggal Kedaluwarsa</th>
                <th>Keterangan</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($vaccines as $vaccine)
                <tr>
                    <td class="text-right">{{ $loop->iteration }}</td>
                    <td>{{ $vaccine->vaccine_name ?? 'N/A' }}</td>
                    <td>{{ $vaccine->unit ?? 'N/A' }}</td>
                    <td class="text-right">{{ $vaccine->stock ?? 'N/A' }}</td>
                    <td @if ($print_criteria === 'Tanggal Masuk') class="text-primary" @endif>
                        {{ $vaccine->entry_date ? \Carbon\Carbon::parse($vaccine->entry_date)->locale('id')->isoFormat('D MMMM YYYY') : 'N/A' }}
                    </td>
                    <td @if ($print_criteria === 'Tanggal Kedaluwarsa') class="text-danger" @endif>
                        {{ $vaccine->expiry_date ? \Carbon\Carbon::parse($vaccine->expiry_date)->locale('id')->isoFormat('D MMMM YYYY') : 'N/A' }}
                    </td>
                    <td>{{ $vaccine->notes ?? 'N/A' }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" class="text-center">Tidak ada data untuk ditampilkan.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
@endsection
