@extends('layouts.dashboard')

@section('title', 'Identitas Situs')

@push('styles')
    <style>
        .qr-wrapper {
            position: relative;
            width: 180px;
            height: 180px;
            margin: 0 auto;
        }

        .qr-code {
            width: 100%;
            height: 100%;
        }

        .qr-icon {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            font-size: 36px;
            color: #25D366;
            background: white;
            border-radius: 50%;
            padding: 8px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        }

        .qr-link {
            word-break: break-all;
            font-size: 0.9rem;
        }

        .qr-link a {
            display: inline-block;
            max-width: 300px;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
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
                            <form action="{{ url('/site-identity') }}" method="POST">
                                @csrf
                                @method('PUT')
                                <div class="row g-4">
                                    <div class="form-group col-md-6">
                                        <label for="village_name">Kelurahan/Desa <span class="text-danger">*</span></label>
                                        <input id="village_name" type="text"
                                            class="form-control @error('village_name') is-invalid @enderror"
                                            name="village_name" value="{{ $site->village_name }}"
                                            placeholder="Nama Kelurahan/Desa" autofocus>
                                        @error('village_name')
                                            <div class="invalid-feedback">
                                                {{ $message }}
                                            </div>
                                        @enderror
                                    </div>

                                    <div class="form-group col-md-6">
                                        <label for="phone_number">Nomor HP/WA Situs (AKTIF)<span
                                                class="text-danger">*</span></label>
                                        <input id="phone_number" type="tel"
                                            class="form-control @error('phone_number') is-invalid @enderror"
                                            name="phone_number" value="{{ $site->phone_number }}"
                                            placeholder="+628xxxxxxxxxx">
                                        @error('phone_number')
                                            <div class="invalid-feedback">
                                                {{ $message }}
                                            </div>
                                        @enderror
                                    </div>

                                    <div class="form-group col-md-6">
                                        <label for="officer_wa_group_url">Whatsapp Grup URL (Petugas)<span
                                                class="text-danger">*</span></label>
                                        <input id="officer_wa_group_url" type="text"
                                            class="form-control @error('officer_wa_group_url') is-invalid @enderror"
                                            name="officer_wa_group_url" value="{{ $site->officer_wa_group_url }}"
                                            placeholder="https://www.whatsapp.com/">
                                        @error('officer_wa_group_url')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror

                                        <div class="p-2 my-2 text-center">
                                            <div class="qr-wrapper" data-qr-id="officer_qr">
                                                <div class="qr-code" data-url="{{ $site->officer_wa_group_url }}"></div>
                                                <i class="fab fa-whatsapp qr-icon"></i>
                                            </div>
                                        </div>

                                        <div class="text-center qr-link">
                                            <a href="{{ $site->officer_wa_group_url }}"
                                                target="_blank">{{ $site->officer_wa_group_url }}</a>
                                        </div>
                                    </div>

                                    <div class="form-group col-md-6">
                                        <label for="wa_group_url">Whatsapp Grup URL <span
                                                class="text-danger">*</span></label>
                                        <input id="wa_group_url" type="text"
                                            class="form-control @error('wa_group_url') is-invalid @enderror"
                                            name="wa_group_url" value="{{ $site->wa_group_url }}"
                                            placeholder="https://www.whatsapp.com/">
                                        @error('wa_group_url')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror

                                        <div class="p-2 my-2 text-center">
                                            <div class="qr-wrapper" data-qr-id="user_qr">
                                                <div class="qr-code" data-url="{{ $site->wa_group_url }}"></div>
                                                <i class="fab fa-whatsapp qr-icon"></i>
                                            </div>
                                        </div>

                                        <div class="text-center qr-link">
                                            <a href="{{ $site->wa_group_url }}"
                                                target="_blank">{{ $site->wa_group_url }}</a>
                                        </div>
                                    </div>

                                    <div class="col-12">
                                        <div class="d-flex flex-wrap justify-content-center justify-content-md-between align-items-center"
                                            style="gap: 1.5rem">
                                            @php
                                                $offset = \Carbon\Carbon::now()->getOffset() / 3600;
                                                $zone = match ($offset) {
                                                    7 => 'WIB',
                                                    8 => 'WITA',
                                                    9 => 'WIT',
                                                    default => 'N/A',
                                                };
                                            @endphp

                                            <div class="d-flex flex-wrap justify-content-center justify-content-md-end align-items-center text-center"
                                                style="gap: .5rem">
                                                <span>Terakhir diperbarui:</span>
                                                <span data-toggle="tooltip"
                                                    title="{{ $site->updated_at ? \Carbon\Carbon::parse($site->updated_at)->locale('id')->isoFormat('D MMMM YYYY - HH:mm') : 'N/A' }} {{ $zone }}">
                                                    {{ $site->updated_at ? \Carbon\Carbon::parse($site->updated_at)->locale('id')->diffForHumans() : 'N/A' }}
                                                </span>
                                            </div>

                                            <button type="submit" class="btn btn-primary">Simpan</button>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
@endsection

@push('scripts')
    <!-- QR Code Generator -->
    <script src="https://cdn.jsdelivr.net/npm/qrcodejs/qrcode.min.js"></script>
    <script>
        function generateAllQRCodes() {
            const qrWrappers = document.querySelectorAll('.qr-code');

            qrWrappers.forEach(wrapper => {
                const url = wrapper.getAttribute('data-url');
                wrapper.innerHTML = ""; // prevent duplicate

                if (url) {
                    new QRCode(wrapper, {
                        text: url,
                        width: 180,
                        height: 180,
                        colorDark: "#000000",
                        colorLight: "#ffffff",
                        correctLevel: QRCode.CorrectLevel.H
                    });
                }
            });
        }

        document.addEventListener('DOMContentLoaded', generateAllQRCodes);
    </script>
@endpush
