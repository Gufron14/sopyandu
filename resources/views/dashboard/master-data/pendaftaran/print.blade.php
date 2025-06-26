<!DOCTYPE html>
<html>
<head>
    <title>Laporan Data Pendaftaran</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
        }
        .header h2 {
            margin: 0;
            padding: 0;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        table, th, td {
            border: 1px solid black;
        }
        th, td {
            padding: 8px;
            text-align: left;
        }
        th {
            background-color: #f2f2f2;
            text-align: center;
        }
        .text-center {
            text-align: center;
        }
        .badge {
            padding: 2px 6px;
            border-radius: 3px;
            font-size: 10px;
        }
        .badge-warning {
            background-color: #ffc107;
            color: #000;
        }
        .badge-success {
            background-color: #28a745;
            color: #fff;
        }
        .badge-info {
            background-color: #17a2b8;
            color: #fff;
        }
    </style>
</head>
<body>
    <div class="header">
        <h2>LAPORAN DATA PENDAFTARAN</h2>
        <p>Tanggal Cetak: {{ date('d/m/Y H:i:s') }}</p>
    </div>

    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>Tanggal Pendaftaran</th>
                <th>Nama Pendaftar</th>
                <th>Sasaran</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            @forelse($pendaftarans as $index => $pendaftaran)
                <tr>
                    <td class="text-center">{{ $index + 1 }}</td>
                    <td class="text-center">{{ $pendaftaran->tanggal_pendaftaran->format('d/m/Y') }}</td>
                    <td>{{ $pendaftaran->nama_pendaftar }}</td>
                    <td class="text-center">{{ $pendaftaran->sasaran_label }}</td>
                    <td class="text-center">{{ $pendaftaran->status_label }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="5" class="text-center">Tidak ada data</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div style="margin-top: 30px;">
        <p><strong>Total Data: {{ $pendaftarans->count() }}</strong></p>
    </div>
</body>
</html>
