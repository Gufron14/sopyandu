@extends('layouts.dashboard')

@section('title', 'Laporan')

@push('styles')
    <link rel="stylesheet" href="{{ asset('modules/select2/dist/css/select2.min.css') }}">
@endpush

@section('main')
    <div class="main-content">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center bg-light">
                <h5 class="card-title mb-0">
                    Cetak Laporan
                </h5>
            </div>
            <div class="card-body">
                <div class="row gap-3">
                    <div class="col">
                        <a href="#" data-bs-toggle="modal" data-bs-target="#modalPenimbangan" style="text-decoration: none; color: inherit;">
                            <div class="card text-bg-primary">
                                <div class="card-body">
                                    <h5 class="text-center">Cetak Laporan Penimbangan Anak</h5>
                                </div>
                            </div>
                        </a>
                    </div>
                    <div class="col">
                        <a href="#" data-bs-toggle="modal" data-bs-target="#modalKehamilan" style="text-decoration: none; color: inherit;">
                            <div class="card text-bg-success">
                                <div class="card-body">
                                    <h5 class="text-center">Cetak Laporan Pemeriksaan Kehamilan</h5>
                                </div>
                            </div>
                        </a>
                    </div>
                    <div class="col">
                        <a href="#" data-bs-toggle="modal" data-bs-target="#modalImunisasi" style="text-decoration: none; color: inherit;">
                            <div class="card text-bg-warning">
                                <div class="card-body">
                                    <h5 class="text-center">Cetak Laporan Imunisasi Anak</h5>
                                </div>
                            </div>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Penimbangan -->
    <div class="modal fade" id="modalPenimbangan" tabindex="-1" aria-labelledby="modalPenimbanganLabel" aria-hidden="true">
      <div class="modal-dialog">
        <form action="{{ route('laporan.cetak.penimbangan') }}" method="GET" target="_blank">
            <div class="modal-content">
              <div class="modal-header">
                <h5 class="modal-title" id="modalPenimbanganLabel">Filter Laporan Penimbangan Anak</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
              </div>
              <div class="modal-body">
                <div class="mb-3">
                    <label>Dari Tanggal</label>
                    <input type="date" name="from" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label>Sampai Tanggal</label>
                    <input type="date" name="to" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label>Status Gizi</label>
                    <select name="status_gizi" class="form-control">
                        <option value="">Semua</option>
                        <option value="Baik">Baik</option>
                        <option value="Buruk">Buruk</option>
                        <option value="Kurang">Kurang</option>
                        <option value="Lebih">Lebih</option>
                    </select>
                </div>
              </div>
              <div class="modal-footer">
                <button type="submit" class="btn btn-primary">Cetak</button>
              </div>
            </div>
        </form>
      </div>
    </div>

    <!-- Modal Kehamilan -->
    <div class="modal fade" id="modalKehamilan" tabindex="-1" aria-labelledby="modalKehamilanLabel" aria-hidden="true">
      <div class="modal-dialog">
        <form action="{{ route('laporan.cetak.kehamilan') }}" method="GET" target="_blank">
            <div class="modal-content">
              <div class="modal-header">
                <h5 class="modal-title" id="modalKehamilanLabel">Filter Laporan Pemeriksaan Kehamilan</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
              </div>
              <div class="modal-body">
                <div class="mb-3">
                    <label>Dari Tanggal</label>
                    <input type="date" name="from" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label>Sampai Tanggal</label>
                    <input type="date" name="to" class="form-control" required>
                </div>
                {{-- <div class="mb-3">
                    <label>Status Vaksin</label>
                    <select name="status_vaksin" class="form-control">
                        <option value="">Semua</option>
                        <option value="Tidak">Tidak Divaksin</option>
                        <option value="Divaksin">Divaksin</option>
                    </select>
                </div> --}}
                <!-- Add Status BMI filter here -->
                <div class="mb-3">
                    <label>Status BMI</label>
                    <select name="status_bmi" class="form-control">
                        <option value="">Semua</option>
                        <option value="Kurang Berat Badan">Kurang Berat Badan</option>
                        <option value="Normal">Normal</option>
                        <option value="Kelebihan Berat Badan">Kelebihan Berat Badan</option>
                        <option value="Obesitas">Obesitas</option>
                        <option value="Obesitas Parah">Obesitas Parah</option>
                        <option value="Obesitas Ekstrem">Obesitas Ekstrem</option>
                    </select>
                </div>
              </div>
              <div class="modal-footer">
                <button type="submit" class="btn btn-success">Cetak</button>
              </div>
            </div>
        </form>
      </div>
    </div>

    <!-- Modal Imunisasi -->
    <div class="modal fade" id="modalImunisasi" tabindex="-1" aria-labelledby="modalImunisasiLabel" aria-hidden="true">
      <div class="modal-dialog">
        <form action="{{ route('laporan.cetak.imunisasi') }}" method="GET" target="_blank">
            <div class="modal-content">
              <div class="modal-header">
                <h5 class="modal-title" id="modalImunisasiLabel">Filter Laporan Imunisasi Anak</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
              </div>
              <div class="modal-body">
                <div class="mb-3">
                    <label>Dari Tanggal</label>
                    <input type="date" name="from" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label>Sampai Tanggal</label>
                    <input type="date" name="to" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label>Status Vaksin</label>
                    <select name="status_vaksin" class="form-control">
                        <option value="">Semua</option>
                        <option value="Tidak Divaksin">Tidak Divaksin</option>
                        <option value="Divaksin">Divaksin</option>
                    </select>
                </div>
              </div>
              <div class="modal-footer">
                <button type="submit" class="btn btn-warning">Cetak</button>
              </div>
            </div>
        </form>
      </div>
    </div>

    <script>
    document.addEventListener('DOMContentLoaded', function () {
        // Fungsi untuk format tanggal yyyy-mm-dd
        function formatDate(date) {
            let d = new Date(date),
                month = '' + (d.getMonth() + 1),
                day = '' + d.getDate(),
                year = d.getFullYear();

            if (month.length < 2) month = '0' + month;
            if (day.length < 2) day = '0' + day;

            return [year, month, day].join('-');
        }

        // Hitung tanggal hari ini dan 1 bulan sebelumnya
        let today = new Date();
        let lastMonth = new Date();
        lastMonth.setMonth(today.getMonth() - 1);

        // Set semua input[name=from] dan input[name=to] di modal
        document.querySelectorAll('input[name="from"]').forEach(function(input) {
            input.value = formatDate(lastMonth);
        });
        document.querySelectorAll('input[name="to"]').forEach(function(input) {
            input.value = formatDate(today);
        });
    });
</script>
@endsection