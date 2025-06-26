@extends('layouts.dashboard')

@section('title', 'Detail Imunisasi')

@push('styles')
    <style>
        .table {
            white-space: nowrap !important;
        }

        .table tr th {
            width: 10rem !important;
        }

        .table tr td:nth-child(2) {
            width: 1rem !important;
        }
    </style>
@endpush

@section('main')
    <div class="main-content">
        <section class="section">
            <div class="section-header">
                <h1>@yield('title')</h1>
                <div class="section-header-breadcrumb">
                    <div class="breadcrumb-item active"><a href="{{ url('/dashboard') }}">Dashboard</a></div>
                    <div class="breadcrumb-item">@yield('title')</div>
                </div>
            </div>
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-body">
                            @php
                                $child = $immunization->familyChildren;
                                $parent = $child?->familyParents;
                                $officer = $immunization->officers;
                                $medicines = $immunization->medicines;
                            @endphp

                            <div class="row g-4">
                                <div class="col-12">
                                    <h5 class="card-title">Biodata</h5>

                                    <div class="row g-4">
                                        <div class="col-lg-6 col-md-6 col-sm-12 col-12">
                                            <div class="table-responsive">
                                                <table class="table table-sm table-borderless">
                                                    <tr>
                                                        <th>NIK</th>
                                                        <td>:</td>
                                                        <td>
                                                            @if (Auth::user() && Auth::user()->role === 'family_parent')
                                                                {{ $child->nik ?? 'N/A' }}
                                                            @else
                                                                <a
                                                                    href="{{ url("/children-data/{$child->id}/show") }}">{{ $child->nik ?? 'N/A' }}</a>
                                                            @endif
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <th>Nama Lengkap</th>
                                                        <td>:</td>
                                                        <td>{{ $child->fullname ?? 'N/A' }}</td>
                                                    </tr>
                                                    <tr>
                                                        <th>Tempat, Tanggal Lahir</th>
                                                        <td>:</td>
                                                        <td>{{ $child->birth_place ?? 'N/A' }},
                                                            {{ $child->date_of_birth ? \Carbon\Carbon::parse($child->date_of_birth)->locale('id')->isoFormat('D MMMM YYYY') : 'N/A' }}
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <th>Usia</th>
                                                        <td>:</td>
                                                        <td>
                                                            {{ $child->date_of_birth ? \Carbon\Carbon::parse($child->date_of_birth)->diff(\Carbon\Carbon::now())->y : 'N/A' }}
                                                            tahun,
                                                            {{ $child->date_of_birth ? \Carbon\Carbon::parse($child->date_of_birth)->diff(\Carbon\Carbon::now())->m : 'N/A' }}
                                                            bulan,
                                                            {{ $child->date_of_birth ? \Carbon\Carbon::parse($child->date_of_birth)->diff(\Carbon\Carbon::now())->d : 'N/A' }}
                                                            hari
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <th>Golongan Darah</th>
                                                        <td>:</td>
                                                        <td>{{ $child->blood_type ?? 'N/A' }}</td>
                                                    </tr>
                                                    <tr>
                                                        <th>Jenis Kelamin</th>
                                                        <td>:</td>
                                                        <td>
                                                            {{ $child->gender === 'L' ? 'Laki-Laki' : 'Perempuan' }}
                                                        </td>
                                                    </tr>
                                                </table>
                                            </div>
                                        </div>
                                        <div class="col-lg-6 col-md-6 col-sm-12 col-12">
                                            <div class="table-responsive">
                                                <table class="table table-sm table-borderless">
                                                    <tr>
                                                        <th>NIK Ibu</th>
                                                        <td>:</td>
                                                        <td>
                                                            @if (Auth::user() && Auth::user()->role === 'family_parent')
                                                                {{ $parent->nik ?? 'N/A' }}
                                                            @else
                                                                <a
                                                                    href="{{ url("/parent-data/{$parent->id}/show") }}">{{ $parent->nik ?? 'N/A' }}</a>
                                                            @endif
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <th>Nama Lengkap Ibu</th>
                                                        <td>:</td>
                                                        <td>{{ $parent->mother_fullname ?? 'N/A' }}</td>
                                                    </tr>
                                                    <tr>
                                                        <th>Alamat</th>
                                                        <td>:</td>
                                                        <td>{{ $parent->address ?? 'N/A' }}</td>
                                                    </tr>
                                                    <tr>
                                                        <th>Lingkungan/Dusun</th>
                                                        <td>:</td>
                                                        <td>{{ $parent->hamlet ?? 'N/A' }}</td>
                                                    </tr>
                                                    <tr>
                                                        <th>Kelurahan/Desa</th>
                                                        <td>:</td>
                                                        <td>{{ $village ?? 'N/A' }}</td>
                                                    </tr>
                                                    <tr>
                                                        <th>Kecamatan</th>
                                                        <td>:</td>
                                                        <td>{{ $subdistrict ?? 'N/A' }}</td>
                                                    </tr>
                                                    <tr>
                                                        <th>Kota/Kabupaten</th>
                                                        <td>:</td>
                                                        <td>{{ $city ?? 'N/A' }}</td>
                                                    </tr>
                                                    <tr>
                                                        <th>Provinsi</th>
                                                        <td>:</td>
                                                        <td>{{ $province ?? 'N/A' }}</td>
                                                    </tr>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-12">
                                    <hr class="mb-4">
                                    <h5 class="card-title">Imunisasi</h5>

                                    <div class="table-responsive">
                                        <table class="table table-sm table-borderless">
                                            <tr>
                                                <th>Tanggal Imunisasi</th>
                                                <td>:</td>
                                                <td>
                                                    {{ $immunization->immunization_date ? \Carbon\Carbon::parse($immunization->immunization_date)->locale('id')->isoFormat('D MMMM YYYY') : 'N/A' }}
                                                </td>
                                            </tr>
                                            <tr>
                                                <th>Usia Saat Imunisasi</th>
                                                <td>:</td>
                                                <td>{{ $immunization->age_in_checks ?? 'N/A' }}</td>
                                            </tr>
                                            <tr>
                                                <th>Vaksinasi</th>
                                                <td>:</td>
                                                <td>
                                                    @if (is_null($immunization->vaccine_id) || empty($immunization->vaccine_id))
                                                        <span class="badge badge-danger">Tidak</span>
                                                    @else
                                                        <span class="badge badge-success px-3">Ya</span>
                                                    @endif
                                                </td>
                                            </tr>
                                            <tr>
                                                <th>Nama Vaksin</th>
                                                <td>:</td>
                                                <td>
                                                    {{ $immunization->vaccines->vaccine_name ?? '-' }}
                                                </td>
                                            </tr>
                                            <tr>
                                                <th>Kategori Vaksin</th>
                                                <td>:</td>
                                                <td>
                                                    {{ $immunization->vaccine_category ?? 'N/A' }}
                                                </td>
                                            </tr>
                                            <tr>
                                                <th>Efek Samping</th>
                                                <td>:</td>
                                                <td>
                                                    {{ is_null($immunization->side_effects) || empty($immunization->side_effects) ? '-' : strip_tags($immunization->side_effects) }}
                                                </td>
                                            </tr>
                                            <tr>
                                                <th>Keterangan</th>
                                                <td>:</td>
                                                <td>
                                                    {{ is_null($immunization->notes) || empty($immunization->notes) ? '-' : strip_tags($immunization->notes) }}
                                                </td>
                                            </tr>
                                            <tr>
                                                <th>Petugas Pemeriksa</th>
                                                <td>:</td>
                                                <td>
                                                    @if (!is_null($immunization->officer_id))
                                                        @if (Auth::check())
                                                            @php
                                                                $user = Auth::user();
                                                            @endphp

                                                            @if ($user->role === 'admin')
                                                                <a href="{{ url("/officer-data/{$officer->id}/show") }}">{{ $officer->fullname ?? 'N/A' }}
                                                                    - {{ $officer->position ?? 'N/A' }}</a>
                                                            @elseif ($user->role === 'officer' && $user->officer_id === $officer->id)
                                                                <a href="{{ url("/officer-data/{$officer->id}/show") }}">{{ $officer->fullname ?? 'N/A' }}
                                                                    - {{ $officer->position ?? 'N/A' }}</a>
                                                            @else
                                                                {{ $officer->fullname ?? 'N/A' }} -
                                                                {{ $officer->position ?? 'N/A' }}
                                                            @endif
                                                        @endif
                                                    @else
                                                        N/A
                                                    @endif
                                                </td>
                                            </tr>
                                        </table>
                                    </div>
                                </div>

                                @php
                                    $user = Auth::user();

                                    if ($user) {
                                        $isFamilyParent = $user->role === 'family_parent';
                                        $isNotVillageHeadOrNeighborhoodHead =
                                            $user->officer_id !== null &&
                                            !in_array($user->officers->position, ['Lurah', 'Kepala Lingkungan']);

                                        $isPageAccessAllowedForParent =
                                            $isFamilyParent || $isNotVillageHeadOrNeighborhoodHead;
                                        $isPageAccessDeniedForParent =
                                            !$isFamilyParent || $isNotVillageHeadOrNeighborhoodHead;
                                    }
                                @endphp

                                @if ($isPageAccessAllowedForParent)
                                    <div class="col-12">
                                        <hr class="mb-4">
                                        <div class="d-flex justify-content-between mb-4" style="gap: .5rem">
                                            <h5 class="card-title">Pemberian Obat</h5>
                                            @if ($isPageAccessDeniedForParent)
                                                <a href="{{ url("/immunization-data/{$immunization->id}/medicine/manage") }}"
                                                    class="btn btn-primary" data-toggle="tooltip"
                                                    title="Kelola obat untuk pasien">
                                                    Kelola Obat
                                                </a>
                                            @endif
                                        </div>

                                        <div class="table-responsive">
                                            <table class="table-striped table table-sm mb-5">
                                                <thead>
                                                    <tr class="text-center">
                                                        <th>No.</th>
                                                        <th>Nama Obat</th>
                                                        <th>Jumlah</th>
                                                        <th>Aturan Pakai</th>
                                                        <th>Keterangan</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @forelse ($medicines as $medicine)
                                                        <tr>
                                                            <td class="text-right">{{ $loop->iteration }}</td>
                                                            <td>{{ $medicine->medicine_name ?? 'N/A' }}</td>
                                                            <td>{{ $medicine->pivot->quantity ?? '0' }}
                                                                {{ $medicine->unit ?? 'N/A' }}</td>
                                                            <td>{{ $medicine->pivot->dosage_instructions ?? '-' }},
                                                                {{ $medicine->pivot->meal_time ?? 'N/A' }}</td>
                                                            <td>
                                                                {{ is_null($medicine->pivot->notes) || empty($medicine->pivot->notes) ? '-' : $medicine->pivot->notes }}
                                                            </td>
                                                        </tr>
                                                    @empty
                                                        <tr>
                                                            <td colspan="5" class="text-center">Tidak ada data untuk
                                                                ditampilkan.</td>
                                                        </tr>
                                                    @endforelse
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                @endif

                                <div class="col-12">
                                    <div class="d-flex justify-content-center justify-content-md-end align-items-center"
                                        style="gap: .5rem">
                                        <!-- Redirect sesuai URL sebelumnya -->
                                        <a href="{{ Str::contains(url()->previous(), url('/history/medicine-data')) ? 'javascript:history.back()' : url('/immunization-data') }}"
                                            class="btn btn-secondary">Kembali</a>

                                        @if ($isPageAccessDeniedForParent)
                                            <a href="{{ url("/immunization-data/{$immunization->id}/edit") }}"
                                                class="btn btn-primary" data-toggle="tooltip" title="Ubah data imunisasi">
                                                <i class="fas fa-pencil"></i>
                                            </a>
                                            <form action="{{ url("/immunization-data/{$immunization->id}") }}"
                                                method="POST" id="delete-form-{{ $immunization->id }}" class="d-inline">
                                                @method('delete')
                                                @csrf
                                                <button type="submit" class="btn btn-danger btn-delete"
                                                    data-toggle="tooltip" title="Hapus data imunisasi">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
@endsection

@push('scripts')
    @if ($isPageAccessDeniedForParent)
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const deleteButton = document.querySelector('.btn-delete');

                if (deleteButton) {
                    deleteButton.addEventListener('click', function(e) {
                        e.preventDefault();
                        const form = this.closest('form');

                        swal({
                            title: 'Hapus Data',
                            text: 'Apakah Anda yakin ingin menghapus data ini?',
                            icon: 'warning',
                            buttons: {
                                cancel: 'Batal',
                                confirm: {
                                    text: 'Ya, Hapus!',
                                    value: true,
                                    className: 'btn-danger',
                                }
                            },
                            dangerMode: true,
                        }).then((willDelete) => {
                            if (willDelete) {
                                form.submit();
                            }
                        });
                    });
                }
            });
        </script>
    @endif
@endpush
