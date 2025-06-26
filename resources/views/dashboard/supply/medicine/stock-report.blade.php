@extends('layouts.print')

@section('title', 'Laporan Stok Obat')

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
                <th>Nama Obat</th>
                <th>Jenis</th>
                <th>Unit</th>
                <th>Stok</th>
                <th>Tanggal Masuk</th>
                <th>Tanggal Kedaluwarsa</th>
                <th>Keterangan</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($medicines as $medicine)
                <tr>
                    <td class="text-right">{{ $loop->iteration }}</td>
                    <td>{{ $medicine->medicine_name ?? 'N/A' }}</td>
                    <td>{{ $medicine->type ?? 'N/A' }}</td>
                    <td>{{ $medicine->unit ?? 'N/A' }}</td>
                    <td class="text-right">{{ $medicine->stock ?? 'N/A' }}</td>
                    <td @if ($print_criteria === 'Tanggal Masuk') class="text-primary" @endif>
                        {{ $medicine->entry_date ? \Carbon\Carbon::parse($medicine->entry_date)->locale('id')->isoFormat('D MMMM YYYY') : 'N/A' }}
                    </td>
                    <td @if ($print_criteria === 'Tanggal Kedaluwarsa') class="text-danger" @endif>
                        {{ $medicine->expiry_date ? \Carbon\Carbon::parse($medicine->expiry_date)->locale('id')->isoFormat('D MMMM YYYY') : 'N/A' }}
                    </td>
                    <td>{{ $medicine->notes ?? 'N/A' }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="8" class="text-center">Tidak ada data untuk ditampilkan.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
@endsection
