@extends('layouts.dashboard')

@section('title', 'Detail Penimbangan')

@push('styles')
    <style>
        .detail-card {
            border: none;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }
        .detail-row {
            border-bottom: 1px solid #f0f0f0;
            padding: 12px 0;
        }
        .detail-row:last-child {
            border-bottom: none;
        }
        .detail-label {
            font-weight: 600;
            color: #495057;
            margin-bottom: 5px;
        }
        .detail-value {
            color: #6c757d;
        }
        .status-badge {
            padding: 5px 10px;
            border-radius: 15px;
            font-size: 12px;
            font-weight: 600;
        }
        .status-baik { background-color: #d4edda; color: #155724; }
        .status-buruk { background-color: #f8d7da; color: #721c24; }
        .status-kurang { background-color: #fff3cd; color: #856404; }
        .status-lebih { background-color: #d1ecf1; color: #0c5460; }
    </style>
@endpush

@section('main')
    <div class="main-content">
        <section class="section">
            <div class="section-header">
                <h1>@yield('title')</h1>
                <div class="section-header-breadcrumb">
                    <div class="breadcrumb-item active"><a href="{{ url('/dashboard') }}">Dashboard</a></div>
                    <div class="breadcrumb-item"><a href="{{ url('/weighing-data') }}">Data Penimbangan</a></div>
                    <div class="breadcrumb-item">Detail</div>
                </div>
            </div>

            <div class="row">
                <div class="col-12">
                    <div class="card detail-card">
                        <div class="card-header">
                            <h4>Detail Data Penimbangan</h4>
                            <div class="card-header-action">
                                <a href="{{ url('/weighing-data') }}" class="btn btn-secondary">
                                    <i class="fas fa-arrow-left mr-1"></i> Kembali
                                </a>
                                @php
                                    $userCheckForThisPage = Auth::user() && Auth::user()->role === 'admin';
                                @endphp
                                @if ($userCheckForThisPage)
                                    <a href="{{ url('/weighing-data/' . $weighing->id . '/edit') }}" class="btn btn-primary">
                                        <i class="fas fa-edit mr-1"></i> Edit
                                    </a>
                                @endif
                            </div>
                        </div>
                        <div class="card-body">
                            @php
                                $child = $weighing->familyChildren;
                                $parent = $child?->familyParents;
                                $officer = $weighing->officers;
                            @endphp

                            <div class="row">
                                <!-- Data Anak -->
                                <div class="col-md-6">
                                    <h5 class="mb-3 text-primary">
                                        <i class="fas fa-child mr-2"></i>Data Anak
                                    </h5>
                                    
                                    <div class="detail-row">
                                        <div class="detail-label">NIK Anak</div>
                                        <div class="detail-value">{{ $child->nik ?? 'N/A' }}</div>
                                    </div>
                                    
                                    <div class="detail-row">
                                        <div class="detail-label">Nama Lengkap Anak</div>
                                        <div class="detail-value">{{ $child->fullname ?? 'N/A' }}</div>
                                    </div>
                                    
                                    <div class="detail-row">
                                        <div class="detail-label">Jenis Kelamin</div>
                                        <div class="detail-value">{{ $child->gender ?? 'N/A' }}</div>
                                    </div>
                                    
                                    <div class="detail-row">
                                        <div class="detail-label">Tempat Lahir</div>
                                        <div class="detail-value">{{ $child->birth_place ?? 'N/A' }}</div>
                                    </div>
                                    
                                    <div class="detail-row">
                                        <div class="detail-label">Tanggal Lahir</div>
                                        <div class="detail-value">
                                            {{ $child->date_of_birth ? \Carbon\Carbon::parse($child->date_of_birth)->locale('id')->isoFormat('D MMMM YYYY') : 'N/A' }}
                                        </div>
                                    </div>

                                    @if (Auth::user() && Auth::user()->role !== 'family_parent')
                                        <div class="detail-row">
                                            <div class="detail-label">Nama Lengkap Ibu</div>
                                            <div class="detail-value">{{ $parent->mother_fullname ?? 'N/A' }}</div>
                                        </div>
                                    @endif
                                </div>

                                <!-- Data Penimbangan -->
                                <div class="col-md-6">
                                    <h5 class="mb-3 text-success">
                                        <i class="fas fa-weight mr-2"></i>Data Penimbangan
                                    </h5>
                                    
                                    <div class="detail-row">
                                        <div class="detail-label">Tanggal Penimbangan</div>
                                        <div class="detail-value">
                                            {{ $weighing->weighing_date ? \Carbon\Carbon::parse($weighing->weighing_date)->locale('id')->isoFormat('D MMMM YYYY') : 'N/A' }}
                                        </div>
                                    </div>
                                    
                                    <div class="detail-row">
                                        <div class="detail-label">Usia Saat Penimbangan</div>
                                        <div class="detail-value">{{ $weighing->age_in_checks ?? 'N/A' }}</div>
                                    </div>
                                    
                                    <div class="detail-row">
                                        <div class="detail-label">Berat Badan</div>
                                        <div class="detail-value">{{ number_format($weighing->weight, 2) }} kg</div>
                                    </div>
                                    
                                    <div class="detail-row">
                                        <div class="detail-label">Tinggi Badan</div>
                                        <div class="detail-value">{{ number_format($weighing->height, 2) }} cm</div>
                                    </div>
                                    
                                    <div class="detail-row">
                                        <div class="detail-label">Lingkar Kepala</div>
                                        <div class="detail-value">{{ number_format($weighing->head_circumference, 2) }} cm</div>
                                    </div>
                                    
                                    <div class="detail-row">
                                        <div class="detail-label">Lingkar Lengan</div>
                                        <div class="detail-value">{{ number_format($weighing->arm_circumference, 2) }} cm</div>
                                    </div>
                                    
                                    <div class="detail-row">
                                        <div class="detail-label">Status Gizi</div>
                                        <div class="detail-value">
                                            @php
                                                $statusClass = '';
                                                switch(strtolower($weighing->nutrition_status ?? '')) {
                                                    case 'baik': $statusClass = 'status-baik'; break;
                                                    case 'buruk': $statusClass = 'status-buruk'; break;
                                                    case 'kurang': $statusClass = 'status-kurang'; break;
                                                    case 'lebih': $statusClass = 'status-lebih'; break;
                                                    default: $statusClass = 'status-baik';
                                                }
                                            @endphp
                                            <span class="status-badge {{ $statusClass }}">
                                                {{ $weighing->nutrition_status ?? 'N/A' }}
                                            </span>
                                        </div>
                                    </div>
                                    
                                    <div class="detail-row">
                                        <div class="detail-label">Keterangan</div>
                                        <div class="detail-value">
                                            {{ is_null($weighing->notes) || empty($weighing->notes) ? '-' : strip_tags($weighing->notes) }}
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <hr class="my-4">

                            <!-- Data Petugas -->
                            <div class="row">
                                <div class="col-12">
                                    <h5 class="mb-3 text-info">
                                        <i class="fas fa-user-md mr-2"></i>Data Petugas
                                    </h5>
                                    
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="detail-row">
                                                <div class="detail-label">Nama Petugas</div>
                                                <div class="detail-value">{{ $officer->fullname ?? 'N/A' }}</div>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="detail-row">
                                                <div class="detail-label">Jabatan Petugas</div>
                                                <div class="detail-value">{{ $officer->position ?? 'N/A' }}</div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Action Buttons -->
                            <div class="row mt-4">
                                <div class="col-12">
                                    <div class="d-flex justify-content-between">
                                        <a href="{{ url('/weighing-data') }}" class="btn btn-secondary">
                                            <i class="fas fa-arrow-left mr-1"></i> Kembali ke Daftar
                                        </a>
                                        
                                        @if ($userCheckForThisPage)
                                            <div>
                                                <a href="{{ url('/weighing-data/' . $weighing->id . '/edit') }}" class="btn btn-warning">
                                                    <i class="fas fa-edit mr-1"></i> Edit Data
                                                </a>
                                                <form action="{{ url('/weighing-data/' . $weighing->id) }}" method="POST" class="d-inline ml-2">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-danger" 
                                                            onclick="return confirm('Apakah Anda yakin ingin menghapus data ini?')">
                                                        <i class="fas fa-trash mr-1"></i> Hapus Data
                                                    </button>
                                                </form>
                                            </div>
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
