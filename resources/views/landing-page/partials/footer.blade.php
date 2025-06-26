<footer class="footer pt-5 pb-3">
    <div class="container">
        <div class="row">
            <div class="col-lg-4 mb-4">
                <h5>Tentang SIDU</h5>
                <p>{{ isset($siteIdentity) ? $siteIdentity->site_description : 'Sistem Informasi Posyandu untuk pelayanan kesehatan ibu dan anak yang lebih baik.' }}</p>
            </div>
            <div class="col-lg-4 mb-4">
                <h5>Kontak</h5>
                <p>
                    <i class="fas fa-map-marker-alt me-2"></i> {{ isset($siteIdentity) ? $siteIdentity->address : 'Alamat Posyandu' }}<br>
                    <i class="fas fa-phone me-2"></i> {{ isset($siteIdentity) ? $siteIdentity->phone : 'Nomor Telepon' }}<br>
                    <i class="fas fa-envelope me-2"></i> {{ isset($siteIdentity) ? $siteIdentity->email : 'Email' }}
                </p>
            </div>
            <div class="col-lg-4 mb-4">
                <h5>Ikuti Kami</h5>
                <div class="social-links">
                    @if(isset($siteIdentity) && $siteIdentity->facebook)
                        <a href="{{ $siteIdentity->facebook }}" class="text-white me-3"><i class="fab fa-facebook-f"></i></a>
                    @endif
                    @if(isset($siteIdentity) && $siteIdentity->twitter)
                        <a href="{{ $siteIdentity->twitter }}" class="text-white me-3"><i class="fab fa-twitter"></i></a>
                    @endif
                    @if(isset($siteIdentity) && $siteIdentity->instagram)
                        <a href="{{ $siteIdentity->instagram }}" class="text-white me-3"><i class="fab fa-instagram"></i></a>
                    @endif
                </div>
            </div>
        </div>
        <hr class="mt-4 mb-3 border-light">
        <div class="text-center">
            <p class="mb-0">&copy; {{ date('Y') }} {{ isset($siteIdentity) ? $siteIdentity->site_name : 'SIDU' }}. All rights reserved.</p>
        </div>
    </div>
</footer>
