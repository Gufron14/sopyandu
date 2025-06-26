@extends('layouts.dashboard')

@section('title', 'Pendaftaran')

@push('styles')
    <link rel="stylesheet" href="{{ asset('modules/select2/dist/css/select2.min.css') }}">
@endpush

@section('main')
    <div class="main-content">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center bg-light">
                <h5 class="card-title mb-0">
                    Data Pendaftaran
                </h5>
                <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#addModal">Tambah</button>
            </div>
            <div class="card-body">
                {{-- Filter Section --}}
                <div class="row mb-3">
                    <div class="col-12">
                        <form method="GET" id="filterForm">
                            <div class="row g-3">
                                <div class="col-md-2">
                                    <label class="form-label">Status</label>
                                    <select name="status" class="form-select form-select-sm">
                                        <option value="">Semua Status</option>
                                        <option value="menunggu" {{ request('status') == 'menunggu' ? 'selected' : '' }}>Menunggu</option>
                                        <option value="sudah_dilayani" {{ request('status') == 'sudah_dilayani' ? 'selected' : '' }}>Sudah Dilayani</option>
                                    </select>
                                </div>
                                <div class="col-md-2">
                                    <label class="form-label">Tanggal Mulai</label>
                                    <input type="date" name="start_date" class="form-control form-control-sm" value="{{ request('start_date') }}">
                                </div>
                                <div class="col-md-2">
                                    <label class="form-label">Tanggal Akhir</label>
                                    <input type="date" name="end_date" class="form-control form-control-sm" value="{{ request('end_date') }}">
                                </div>
                                <div class="col-md-2">
                                    <label class="form-label">Sasaran</label>
                                    <select name="sasaran" class="form-select form-select-sm">
                                        <option value="">Semua Sasaran</option>
                                        <option value="balita" {{ request('sasaran') == 'balita' ? 'selected' : '' }}>Balita</option>
                                        <option value="ibu_hamil" {{ request('sasaran') == 'ibu_hamil' ? 'selected' : '' }}>Ibu Hamil</option>
                                    </select>
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label">Cari Nama</label>
                                    <input type="text" name="search" class="form-control form-control-sm" placeholder="Nama pendaftar..." value="{{ request('search') }}">
                                </div>
                                <div class="col-md-1">
                                    <label class="form-label">&nbsp;</label>
                                    <div class="d-flex gap-1">
                                        <button type="submit" class="btn btn-primary btn-sm">
                                            <i class="fas fa-search"></i>
                                        </button>
                                        <a href="{{ route('pendaftaran.index') }}" class="btn btn-secondary btn-sm">
                                            <i class="fas fa-refresh"></i>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>

                {{-- Print Button --}}
                <div class="row mb-3">
                    <div class="col-12">
                        <form method="GET" action="{{ route('pendaftaran.print') }}" target="_blank">
                            <input type="hidden" name="status" value="{{ request('status') }}">
                            <input type="hidden" name="start_date" value="{{ request('start_date') }}">
                            <input type="hidden" name="end_date" value="{{ request('end_date') }}">
                            <input type="hidden" name="sasaran" value="{{ request('sasaran') }}">
                            <input type="hidden" name="search" value="{{ request('search') }}">
                            <button type="submit" class="btn btn-success btn-sm">
                                <i class="fas fa-print"></i> Cetak Laporan
                            </button>
                        </form>
                    </div>
                </div>

                <div class="table-responsive">
                    <table class="table">
                        <thead>
                            <tr class="text-center">
                                <th>No</th>
                                <th>
                                    <a href="{{ request()->fullUrlWithQuery(['sort_by' => 'tanggal_pendaftaran', 'sort_order' => request('sort_order') == 'asc' ? 'desc' : 'asc']) }}" 
                                       class="text-decoration-none text-dark">
                                        Tanggal Pendaftaran
                                        @if(request('sort_by') == 'tanggal_pendaftaran')
                                            <i class="fas fa-sort-{{ request('sort_order') == 'asc' ? 'up' : 'down' }}"></i>
                                        @else
                                            <i class="fas fa-sort"></i>
                                        @endif
                                    </a>
                                </th>
                                <th>
                                    <a href="{{ request()->fullUrlWithQuery(['sort_by' => 'nama', 'sort_order' => request('sort_order') == 'asc' ? 'desc' : 'asc']) }}" 
                                       class="text-decoration-none text-dark">
                                        Nama Pendaftar
                                        @if(request('sort_by') == 'nama')
                                            <i class="fas fa-sort-{{ request('sort_order') == 'asc' ? 'up' : 'down' }}"></i>
                                        @else
                                            <i class="fas fa-sort"></i>
                                        @endif
                                    </a>
                                </th>
                                <th>Sasaran</th>
                                <th>Status</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($pendaftarans as $index => $pendaftaran)
                                <tr>
                                    <td class="text-center">{{ $pendaftarans->firstItem() + $index }}</td>
                                    <td class="text-center">{{ $pendaftaran->tanggal_pendaftaran->format('d/m/Y') }}</td>
                                    <td>{{ $pendaftaran->nama_pendaftar }}</td>
                                    <td class="text-center">
                                        <span class="badge bg-{{ $pendaftaran->sasaran == 'balita' ? 'info' : 'warning' }}">
                                            {{ $pendaftaran->sasaran_label }}
                                        </span>
                                    </td>
                                    <td class="text-center">
                                        <span class="badge bg-{{ $pendaftaran->status == 'menunggu' ? 'warning' : 'success' }}">
                                            {{ $pendaftaran->status_label }}
                                        </span>
                                    </td>
                                    <td class="text-center">
                                        @if($pendaftaran->status == 'menunggu' && !$pendaftaran->status_changed)
                                            <form method="POST" action="{{ route('pendaftaran.updateStatus', $pendaftaran->id) }}" class="d-inline">
                                                @csrf
                                                @method('PATCH')
                                                <button type="submit" class="btn btn-warning btn-sm" 
                                                        onclick="return confirm('Ubah status menjadi Sudah Dilayani?')">
                                                    <i class="fas fa-check"></i>
                                                </button>
                                            </form>
                                        @endif
                                        
                                        <button class="btn btn-primary btn-sm" onclick="showDetail({{ $pendaftaran->id }})">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                        <button class="btn btn-success btn-sm" onclick="editPendaftaran({{ $pendaftaran->id }})">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <form method="POST" action="{{ route('pendaftaran.destroy', $pendaftaran->id) }}" class="d-inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-danger btn-sm" 
                                                    onclick="return confirm('Yakin ingin menghapus data ini?')">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center">Tidak ada data</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                {{-- Pagination --}}
                <div class="d-flex justify-content-center">
                    {{ $pendaftarans->links() }}
                </div>
            </div>

            {{-- Add Modal --}}
            <div class="modal fade" id="addModal" tabindex="-1" aria-labelledby="addModalLabel" aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <form method="POST" action="{{ route('pendaftaran.store') }}">
                            @csrf
                            <div class="modal-header">
                                <h1 class="modal-title fs-5" id="addModalLabel">Tambah Pendaftaran</h1>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                <div class="form-group mb-3">
                                    <label for="tanggal_pendaftaran" class="form-label">Tanggal Pendaftaran</label>
                                    <input type="date" class="form-control @error('tanggal_pendaftaran') is-invalid @enderror" 
                                           id="tanggal_pendaftaran" name="tanggal_pendaftaran" value="{{ old('tanggal_pendaftaran') }}" required>
                                    @error('tanggal_pendaftaran')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                
                                <div class="form-group mb-3">
                                    <label for="sasaran" class="form-label">Sasaran Balita/Ibu Hamil</label>
                                    <select class="form-control @error('sasaran') is-invalid @enderror" 
                                            id="sasaran" name="sasaran" required>
                                        <option value="" selected disabled>-- Pilih Sasaran --</option>
                                        <option value="balita" {{ old('sasaran') == 'balita' ? 'selected' : '' }}>Balita</option>
                                        <option value="ibu_hamil" {{ old('sasaran') == 'ibu_hamil' ? 'selected' : '' }}>Ibu Hamil</option>
                                    </select>
                                    @error('sasaran')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="form-group mb-3" id="children_select" style="display: none;">
                                    <label for="children_id" class="form-label">Pilih Anak</label>
                                    <select class="form-control select2" id="children_id" name="children_id">
                                        <option value="" selected disabled>-- Pilih Anak --</option>
                                        @foreach($children as $child)
                                            <option value="{{ $child->id }}" {{ old('children_id') == $child->id ? 'selected' : '' }}>
                                                {{ $child->fullname }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('children_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="form-group mb-3" id="parent_select" style="display: none;">
                                    <label for="parent_id" class="form-label">Pilih Ibu Hamil</label>
                                    <select class="form-control select2" id="parent_id" name="parent_id">
                                        <option value="" selected disabled>-- Pilih Ibu Hamil --</option>
                                        @foreach($parents as $parent)
                                            <option value="{{ $parent->id }}" {{ old('parent_id') == $parent->id ? 'selected' : '' }}>
                                                {{ $parent->mother_fullname }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('parent_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                {{-- <div class="form-group mb-3">
                                    <label for="status" class="form-label">Status</label>
                                    <select class="form-select @error('status') is-invalid @enderror" 
                                            id="status" name="status" required>
                                        <option value="" selected disabled>-- Pilih Status --</option>
                                        <option value="menunggu" {{ old('status') == 'menunggu' ? 'selected' : '' }}>Menunggu</option>
                                        <option value="sudah_dilayani" {{ old('status') == 'sudah_dilayani' ? 'selected' : '' }}>Sudah Dilayani</option>
                                    </select>
                                    @error('status')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div> --}}
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                                <button type="submit" class="btn btn-primary">Simpan</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            {{-- Edit Modal --}}
            <div class="modal fade" id="editModal" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <form method="POST" id="editForm">
                            @csrf
                            @method('PUT')
                            <div class="modal-header">
                                <h1 class="modal-title fs-5" id="editModalLabel">Edit Pendaftaran</h1>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                <div class="form-group mb-3">
                                    <label for="edit_tanggal_pendaftaran" class="form-label">Tanggal Pendaftaran</label>
                                    <input type="date" class="form-control" id="edit_tanggal_pendaftaran" name="tanggal_pendaftaran" required>
                                </div>
                                
                                <div class="form-group mb-3">
                                    <label for="edit_sasaran" class="form-label">Sasaran Balita/Ibu Hamil</label>
                                    <select class="form-control" id="edit_sasaran" name="sasaran" required>
                                        <option value="" disabled>-- Pilih Sasaran --</option>
                                        <option value="balita">Balita</option>
                                        <option value="ibu_hamil">Ibu Hamil</option>
                                    </select>
                                </div>

                                <div class="form-group mb-3" id="edit_children_select" style="display: none;">
                                    <label for="edit_children_id" class="form-label">Pilih Anak</label>
                                    <select class="form-control select2" id="edit_children_id" name="children_id">
                                        <option value="" disabled>-- Pilih Anak --</option>
                                        @foreach($children as $child)
                                            <option value="{{ $child->id }}">{{ $child->fullname }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="form-group mb-3" id="edit_parent_select" style="display: none;">
                                    <label for="edit_parent_id" class="form-label">Pilih Ibu Hamil</label>
                                    <select class="form-control select2" id="edit_parent_id" name="parent_id">
                                        <option value="" disabled>-- Pilih Ibu Hamil --</option>
                                        @foreach($parents as $parent)
                                            <option value="{{ $parent->id }}">{{ $parent->mother_fullname }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="form-group mb-3">
                                    <label for="edit_status" class="form-label">Status</label>
                                    <select class="form-select" id="edit_status" name="status" required>
                                        <option value="" disabled>-- Pilih Status --</option>
                                        <option value="menunggu">Menunggu</option>
                                        <option value="sudah_dilayani">Sudah Dilayani</option>
                                    </select>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                                <button type="submit" class="btn btn-primary">Update</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            {{-- Detail Modal --}}
            <div class="modal fade" id="detailModal" tabindex="-1" aria-labelledby="detailModalLabel" aria-hidden="true">
                <div class="modal-dialog modal-lg">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h1 class="modal-title fs-5" id="detailModalLabel">Detail Pendaftaran</h1>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <h6 class="mb-3">Informasi Pendaftaran</h6>
                                    <table class="table table-borderless">
                                        <tr>
                                            <td><strong>Tanggal Pendaftaran</strong></td>
                                            <td>:</td>
                                            <td id="detail_tanggal"></td>
                                        </tr>
                                        <tr>
                                            <td><strong>Sasaran</strong></td>
                                            <td>:</td>
                                            <td id="detail_sasaran"></td>
                                        </tr>
                                        <tr>
                                            <td><strong>Status</strong></td>
                                            <td>:</td>
                                            <td id="detail_status"></td>
                                        </tr>
                                    </table>
                                </div>
                                <div class="col-md-6">
                                    <h6 class="mb-3">Data Pendaftar</h6>
                                    <div id="detail_pendaftar_info"></div>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>

@endsection

@push('scripts')
    <script src="{{ asset('modules/select2/dist/js/select2.full.min.js') }}"></script>
    <script>
        $(document).ready(function() {
            // Initialize Select2
            $('.select2').select2({
                dropdownParent: $('.modal')
            });

            // Handle sasaran change for add modal
            $('#sasaran').change(function() {
                const sasaran = $(this).val();
                if (sasaran === 'balita') {
                    $('#children_select').show();
                    $('#parent_select').hide();
                    $('#children_id').prop('required', true);
                    $('#parent_id').prop('required', false);
                } else if (sasaran === 'ibu_hamil') {
                    $('#children_select').hide();
                    $('#parent_select').show();
                    $('#children_id').prop('required', false);
                    $('#parent_id').prop('required', true);
                } else {
                    $('#children_select').hide();
                    $('#parent_select').hide();
                    $('#children_id').prop('required', false);
                    $('#parent_id').prop('required', false);
                }
            });

            // Handle sasaran change for edit modal
            $('#edit_sasaran').change(function() {
                const sasaran = $(this).val();
                if (sasaran === 'balita') {
                    $('#edit_children_select').show();
                    $('#edit_parent_select').hide();
                    $('#edit_children_id').prop('required', true);
                    $('#edit_parent_id').prop('required', false);
                } else if (sasaran === 'ibu_hamil') {
                    $('#edit_children_select').hide();
                    $('#edit_parent_select').show();
                    $('#edit_children_id').prop('required', false);
                    $('#edit_parent_id').prop('required', true);
                } else {
                    $('#edit_children_select').hide();
                    $('#edit_parent_select').hide();
                    $('#edit_children_id').prop('required', false);
                    $('#edit_parent_id').prop('required', false);
                }
            });

            // Trigger change on page load if old value exists
            @if(old('sasaran'))
                $('#sasaran').trigger('change');
            @endif
        });

        // Function to show detail
        function showDetail(id) {
            $.get(`/pendaftaran/${id}`, function(data) {
                const tanggal = new Date(data.tanggal_pendaftaran).toLocaleDateString('id-ID');
                const sasaran = data.sasaran === 'balita' ? 'Balita' : 'Ibu Hamil';
                const status = data.status === 'menunggu' ? 'Menunggu' : 'Sudah Dilayani';
                
                $('#detail_tanggal').text(tanggal);
                $('#detail_sasaran').text(sasaran);
                $('#detail_status').text(status);
                
                // Show detailed information based on sasaran
                let pendaftarInfo = '';
                
                if (data.sasaran === 'balita' && data.child) {
                    const child = data.child;
                    const birthDate = new Date(child.date_of_birth).toLocaleDateString('id-ID');
                    const age = calculateAge(child.date_of_birth);
                    
                    pendaftarInfo = `
                        <table class="table table-borderless table-sm">
                            <tr>
                                <td><strong>Nama Lengkap</strong></td>
                                <td>:</td>
                                <td>${child.fullname}</td>
                            </tr>
                            <tr>
                                <td><strong>NIK</strong></td>
                                <td>:</td>
                                <td>${child.nik || '-'}</td>
                            </tr>
                            <tr>
                                <td><strong>Jenis Kelamin</strong></td>
                                <td>:</td>
                                <td>${child.gender === 'male' ? 'Laki-laki' : 'Perempuan'}</td>
                            </tr>
                            <tr>
                                <td><strong>Tempat Lahir</strong></td>
                                <td>:</td>
                                <td>${child.birth_place || '-'}</td>
                            </tr>
                            <tr>
                                <td><strong>Tanggal Lahir</strong></td>
                                <td>:</td>
                                <td>${birthDate}</td>
                            </tr>
                            <tr>
                                <td><strong>Umur</strong></td>
                                <td>:</td>
                                <td>${age}</td>
                            </tr>
                            <tr>
                                <td><strong>Golongan Darah</strong></td>
                                <td>:</td>
                                <td>${child.blood_type || '-'}</td>
                            </tr>
                        </table>
                    `;
                    
                    // Add parent info for balita
                    if (child.family_parents) {
                        pendaftarInfo += `
                            <hr>
                            <h6 class="mb-2">Data Orang Tua</h6>
                            <table class="table table-borderless table-sm">
                                <tr>
                                    <td><strong>Nama Ibu</strong></td>
                                    <td>:</td>
                                    <td>${child.family_parents.mother_fullname || '-'}</td>
                                </tr>
                                <tr>
                                    <td><strong>Nama Ayah</strong></td>
                                    <td>:</td>
                                    <td>${child.family_parents.father_fullname || '-'}</td>
                                </tr>
                                <tr>
                                    <td><strong>No. Telepon</strong></td>
                                    <td>:</td>
                                    <td>${child.family_parents.phone_number || '-'}</td>
                                </tr>
                                <tr>
                                    <td><strong>Alamat</strong></td>
                                    <td>:</td>
                                    <td>${child.family_parents.address || '-'}</td>
                                </tr>
                            </table>
                        `;
                    }
                    
                } else if (data.sasaran === 'ibu_hamil' && data.parent) {
                    const parent = data.parent;
                    const motherBirthDate = parent.mother_date_of_birth ? new Date(parent.mother_date_of_birth).toLocaleDateString('id-ID') : '-';
                    const fatherBirthDate = parent.father_date_of_birth ? new Date(parent.father_date_of_birth).toLocaleDateString('id-ID') : '-';
                    
                    pendaftarInfo = `
                        <table class="table table-borderless table-sm">
                            <tr>
                                <td colspan="3"><strong>Data Ibu</strong></td>
                            </tr>
                            <tr>
                                <td><strong>Nama Lengkap</strong></td>
                                <td>:</td>
                                <td>${parent.mother_fullname}</td>
                            </tr>
                            <tr>
                                <td><strong>NIK</strong></td>
                                <td>:</td>
                                <td>${parent.mother_nik || '-'}</td>
                            </tr>
                            <tr>
                                <td><strong>Tempat Lahir</strong></td>
                                <td>:</td>
                                <td>${parent.mother_birth_place || '-'}</td>
                            </tr>
                            <tr>
                                <td><strong>Tanggal Lahir</strong></td>
                                <td>:</td>
                                <td>${motherBirthDate}</td>
                            </tr>
                            <tr>
                                <td><strong>Golongan Darah</strong></td>
                                <td>:</td>
                                <td>${parent.mother_blood_type || '-'}</td>
                            </tr>
                            <tr>
                                <td colspan="3"><hr><strong>Data Ayah</strong></td>
                            </tr>
                            <tr>
                                <td><strong>Nama Lengkap</strong></td>
                                <td>:</td>
                                <td>${parent.father_fullname || '-'}</td>
                            </tr>
                            <tr>
                                <td><strong>NIK</strong></td>
                                <td>:</td>
                                <td>${parent.father_nik || '-'}</td>
                            </tr>
                            <tr>
                                <td><strong>Tempat Lahir</strong></td>
                                <td>:</td>
                                <td>${parent.father_birth_place || '-'}</td>
                            </tr>
                            <tr>
                                <td><strong>Tanggal Lahir</strong></td>
                                <td>:</td>
                                <td>${fatherBirthDate}</td>
                            </tr>
                            <tr>
                                <td><strong>Golongan Darah</strong></td>
                                <td>:</td>
                                <td>${parent.father_blood_type || '-'}</td>
                            </tr>
                            <tr>
                                <td colspan="3"><hr><strong>Kontak & Alamat</strong></td>
                            </tr>
                            <tr>
                                <td><strong>No. Telepon</strong></td>
                                <td>:</td>
                                <td>${parent.phone_number || '-'}</td>
                            </tr>
                            <tr>
                                <td><strong>Alamat</strong></td>
                                <td>:</td>
                                <td>${parent.address || '-'}</td>
                            </tr>
                            <tr>
                                <td><strong>Provinsi</strong></td>
                                <td>:</td>
                                <td>${parent.province || '-'}</td>
                            </tr>
                            <tr>
                                <td><strong>Kota/Kabupaten</strong></td>
                                <td>:</td>
                                <td>${parent.city || '-'}</td>
                            </tr>
                            <tr>
                                <td><strong>Kecamatan</strong></td>
                                <td>:</td>
                                <td>${parent.subdistrict || '-'}</td>
                            </tr>
                            <tr>
                                <td><strong>Kelurahan/Desa</strong></td>
                                <td>:</td>
                                <td>${parent.village || '-'}</td>
                            </tr>
                            <tr>
                                <td><strong>Lingkungan/Dusun</strong></td>
                                <td>:</td>
                                <td>${parent.hamlet || '-'}</td>
                            </tr>
                        </table>
                    `;
                }
                
                $('#detail_pendaftar_info').html(pendaftarInfo);
                $('#detailModal').modal('show');
            });
        }

        // Helper function to calculate age
        function calculateAge(birthDate) {
            const today = new Date();
            const birth = new Date(birthDate);
            let age = today.getFullYear() - birth.getFullYear();
            const monthDiff = today.getMonth() - birth.getMonth();
            
            if (monthDiff < 0 || (monthDiff === 0 && today.getDate() < birth.getDate())) {
                age--;
            }
            
            // For children under 2 years, show in months
            if (age < 2) {
                const months = (today.getFullYear() - birth.getFullYear()) * 12 + (today.getMonth() - birth.getMonth());
                if (months < 1) {
                    const days = Math.floor((today - birth) / (1000 * 60 * 60 * 24));
                    return `${days} hari`;
                }
                return `${months} bulan`;
            }
            
            return `${age} tahun`;
        }

        // Function to edit pendaftaran
        function editPendaftaran(id) {
            $.get(`/pendaftaran/${id}`, function(data) {
                // Set form action
                $('#editForm').attr('action', `/pendaftaran/${id}`);
                
                // Fill form fields
                $('#edit_tanggal_pendaftaran').val(data.tanggal_pendaftaran);
                $('#edit_sasaran').val(data.sasaran);
                $('#edit_status').val(data.status);
                
                // Handle sasaran-specific fields
                if (data.sasaran === 'balita') {
                    $('#edit_children_select').show();
                    $('#edit_parent_select').hide();
                    $('#edit_children_id').val(data.children_id).prop('required', true);
                    $('#edit_parent_id').prop('required', false);
                } else if (data.sasaran === 'ibu_hamil') {
                    $('#edit_children_select').hide();
                    $('#edit_parent_select').show();
                    $('#edit_parent_id').val(data.parent_id).prop('required', true);
                    $('#edit_children_id').prop('required', false);
                }
                
                // Reinitialize Select2 for edit modal
                $('#edit_children_id, #edit_parent_id').select2({
                    dropdownParent: $('#editModal')
                });
                
                $('#editModal').modal('show');
            });
        }

        // Show success/error messages
        @if(session('success'))
            alert('{{ session('success') }}');
        @endif

        @if(session('error'))
            alert('{{ session('error') }}');
        @endif

        // Auto-submit filter form when filter values change
        $('#filterForm select, #filterForm input[type="date"]').change(function() {
            $('#filterForm').submit();
        });
    </script>
@endpush
