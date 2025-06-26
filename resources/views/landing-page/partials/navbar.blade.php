<nav class="navbar navbar-expand-lg navbar-light navbar-custom fixed-top">
    <div class="container">
        <a class="navbar-brand" href="{{ route('home') }}">
            <img src="{{ isset($siteIdentity) ? asset($siteIdentity->logo_path) : asset('img/logo.png') }}" height="40" alt="Logo">
            {{ isset($siteIdentity) ? $siteIdentity->site_name : 'SIDU' }}
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ms-auto">
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('home') }}">Beranda</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="#about">Tentang</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="#services">Layanan</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="#articles">Artikel</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="#contact">Kontak</a>
                </li>
                <li class="nav-item">
                    <a class="btn btn-primary ms-2" href="{{ route('login') }}">Masuk</a>
                </li>
            </ul>
        </div>
    </div>
</nav>
