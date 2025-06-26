@extends('layouts.dashboard')

@section('title', 'Detail Pemeriksaan Ibu Hamil')

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
                                $parent = $pregnancy->familyParents;
                                $officer = $pregnancy->officers;
                                $medicines = $pregnancy->medicines;
                            @endphp

                            <div class="row g-4">
                                <div class="col-lg-6 col-md-6 col-sm-12 col-12">
                                    <h5 class="card-title">Biodata</h5>

                                    <div class="table-responsive">
                                        <table class="table table-sm table-borderless">
                                            <tr>
                                                <th>NIK</th>
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
                                                <th>Nama Lengkap</th>
                                                <td>:</td>
                                                <td>{{ $parent->mother_fullname ?? 'N/A' }}</td>
                                            </tr>
                                            <tr>
                                                <th>Tempat, Tanggal Lahir</th>
                                                <td>:</td>
                                                <td>{{ $parent->mother_birth_place ?? 'N/A' }},
                                                    {{ $parent->mother_date_of_birth ? \Carbon\Carbon::parse($parent->mother_date_of_birth)->locale('id')->isoFormat('D MMMM YYYY') : 'N/A' }}
                                                </td>
                                            </tr>
                                            <tr>
                                                <th>Golongan Darah</th>
                                                <td>:</td>
                                                <td>{{ $parent->mother_blood_type ?? 'N/A' }}</td>
                                            </tr>
                                            <tr>
                                                <th>Usia</th>
                                                <td>:</td>
                                                <td>
                                                    {{ $parent->mother_date_of_birth ? \Carbon\Carbon::parse($parent->mother_date_of_birth)->diff(\Carbon\Carbon::now())->y : 'N/A' }}
                                                    tahun,
                                                    {{ $parent->mother_date_of_birth ? \Carbon\Carbon::parse($parent->mother_date_of_birth)->diff(\Carbon\Carbon::now())->m : 'N/A' }}
                                                    bulan,
                                                    {{ $parent->mother_date_of_birth ? \Carbon\Carbon::parse($parent->mother_date_of_birth)->diff(\Carbon\Carbon::now())->d : 'N/A' }}
                                                    hari
                                                </td>
                                            </tr>
                                            <tr>
                                                <th>Jumlah Anak</th>
                                                <td>:</td>
                                                <td>{{ $parent->number_of_children ?? 'N/A' }}</td>
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

                                <div class="col-12 d-md-none">
                                    <hr class="mb-4">
                                </div>

                                <div class="col-lg-6 col-md-6 col-sm-12 col-12">
                                    <h5 class="card-title">Hasil Pemeriksaan</h5>

                                    <div class="table-responsive">
                                        <table class="table table-sm table-borderless">
                                            <tr>
                                                <th>Tanggal Pemeriksaan</th>
                                                <td>:</td>
                                                <td>
                                                    {{ $pregnancy->check_date ? \Carbon\Carbon::parse($pregnancy->check_date)->locale('id')->isoFormat('D MMMM YYYY') : 'N/A' }}
                                                </td>
                                            </tr>
                                            <tr>
                                                <th>Usia Saat Pemeriksaan</th>
                                                <td>:</td>
                                                <td>{{ $pregnancy->age_in_checks ?? 'N/A' }}</td>
                                            </tr>
                                            <tr>
                                                <th>Usia Kehamilan</th>
                                                <td>:</td>
                                                <td>{{ $pregnancy->gestational_age ?? 'N/A' }} minggu</td>
                                            </tr>
                                            <tr>
                                                <th>Berat Badan</th>
                                                <td>:</td>
                                                <td>{{ $pregnancy->mother_weight !== null ? number_format($pregnancy->mother_weight, 2) : 'N/A' }}
                                                    kg</td>
                                            </tr>
                                            <tr>
                                                <th>Tekanan Darah</th>
                                                <td>:</td>
                                                <td>{{ $pregnancy->blood_pressure ?? 'N/A' }}</td>
                                            </tr>
                                            <tr>
                                                <th>Denyut Nadi</th>
                                                <td>:</td>
                                                <td>{{ $pregnancy->pulse_rate ?? '-' }} bpm</td>
                                            </tr>
                                            <tr>
                                                <th>Kadar Gula Darah</th>
                                                <td>:</td>
                                                <td>{{ $pregnancy->blood_sugar !== null ? number_format($pregnancy->blood_sugar, 2) : '-' }}
                                                    mg/dL</td>
                                            </tr>
                                            <tr>
                                                <th>Kadar Kolesterol</th>
                                                <td>:</td>
                                                <td>{{ $pregnancy->cholesterol !== null ? number_format($pregnancy->cholesterol, 2) : '-' }}
                                                    mg/dL</td>
                                            </tr>
                                            <tr>
                                                <th>Tinggi Fundus</th>
                                                <td>:</td>
                                                <td>{{ $pregnancy->fundus_height ?? '-' }} cm</td>
                                            </tr>
                                            <tr>
                                                <th>Detak Jantung Janin</th>
                                                <td>:</td>
                                                <td>{{ $pregnancy->fetal_heart_rate ?? '-' }} bpm</td>
                                            </tr>
                                            <tr>
                                                <th>Presentasi Janin</th>
                                                <td>:</td>
                                                <td>{{ $pregnancy->fetal_presentation ?? 'N/A' }}</td>
                                            </tr>
                                            <tr>
                                                <th>Edema</th>
                                                <td>:</td>
                                                <td>{{ $pregnancy->edema ?? 'N/A' }}</td>
                                            </tr>
                                            <tr>
                                                <th>Keterangan</th>
                                                <td>:</td>
                                                <td>{{ is_null($pregnancy->notes) || empty($pregnancy->notes) ? '-' : strip_tags($pregnancy->notes) }}
                                                </td>
                                            </tr>
                                            <tr>
                                                <th>Petugas Pemeriksa</th>
                                                <td>:</td>
                                                <td>
                                                    @if (!is_null($pregnancy->officer_id))
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
                                                <a href="{{ url("/pregnancy-check-data/{$pregnancy->id}/medicine/manage") }}"
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
                                        <a href="{{ Str::contains(url()->previous(), url('/history/medicine-data')) ? 'javascript:history.back()' : url('/pregnancy-check-data') }}"
                                            class="btn btn-secondary">Kembali</a>

                                        @if ($isPageAccessDeniedForParent)
                                            <a href="{{ url("/pregnancy-check-data/{$pregnancy->id}/edit") }}"
                                                class="btn btn-primary" data-toggle="tooltip" title="Ubah data pemeriksaan">
                                                <i class="fas fa-pencil"></i>
                                            </a>
                                            <form action="{{ url("/pregnancy-check-data/{$pregnancy->id}") }}"
                                                method="POST" id="delete-form-{{ $pregnancy->id }}" class="d-inline">
                                                @method('delete')
                                                @csrf
                                                <button type="submit" class="btn btn-danger btn-delete"
                                                    data-toggle="tooltip" title="Hapus data pemeriksaan">
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
