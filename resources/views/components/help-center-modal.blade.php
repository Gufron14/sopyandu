<!-- Help center modal -->
<div class="modal fade" id="helpCenterModal" tabindex="-1" role="dialog" aria-labelledby="helpCenterModalLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-body p-3">
                <div class="d-flex justify-content-between align-items-center" style="gap: .5rem">
                    <h5 class="modal-title mb-0" id="helpCenterModalLabel">Pusat Bantuan</h5>
                    <button type="button" class="px-2 py-1 close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>

                <hr class="mt-3 mb-4">

                <div class="mb-4">
                    <p class="text-lead">Jika Anda membutuhkan bantuan atau memiliki pertanyaan, silakan hubungi admin
                        melalui WhatsApp: <span class="text-success">{{ $site->phone_number }}</span> atau klik
                        tombol di bawah ini.</p>

                    @php
                        $offset = \Carbon\Carbon::now()->getOffset() / 3600;
                        $zone = match ($offset) {
                            7 => 'WIB',
                            8 => 'WITA',
                            9 => 'WIT',
                            default => 'N/A',
                        };
                    @endphp

                    <div class="rounded p-2" style="background-color: rgba(103,119,239,0.1)">
                        <p class="text-lead text-center mb-0">
                            <span class="text-danger">*</span> Layanan tersedia pada hari kerja, <br>
                            Senin hingga Sabtu, pukul 08.00 - 14.00 {{ $zone }}.
                        </p>
                    </div>
                </div>

                <a href="https://wa.me/{{ $site->phone_number }}?text=Hai Admin SIDU {{ $site->village_name }}."
                    target="_blank" class="btn btn-success w-100"><i class="fab fa-whatsapp mr-1"></i> Hubungi Admin</a>
            </div>
        </div>
    </div>
</div>
