@extends('landing-page.layout')

@section('content')
    <!-- Hero Section -->
    <section id="home" class="hero-section">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-6">
                    <h1 class="display-4 fw-bold mb-4">Selamat Datang di SIDU</h1>
                    <p class="lead mb-4">Sistem Informasi Posyandu yang memudahkan pemantauan kesehatan ibu dan anak.
                        Bergabunglah dengan kami untuk pelayanan kesehatan yang lebih baik.</p>
                    <a href="{{ route('register') }}" class="btn btn-primary btn-lg">Daftar Sekarang</a>
                </div>
            </div>
        </div>
    </section>

    <!-- About Section -->
    <section id="about" class="py-5">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-6 mb-4 mb-lg-0">
                    <img src="{{ asset('img/about.jpg') }}" class="img-fluid rounded" alt="About Us">
                </div>
                <div class="col-lg-6">
                    <h2 class="mb-4">Tentang SIDU</h2>
                    <p class="lead">SIDU adalah Sistem Informasi Posyandu yang dirancang untuk memudahkan pelayanan dan
                        pemantauan kesehatan ibu dan anak.</p>
                    <div class="mt-4">
                        <div class="d-flex mb-3">
                            <i class="fas fa-check-circle text-primary me-3 fs-4"></i>
                            <div>
                                <h5>Pemantauan Kesehatan</h5>
                                <p>Pantau perkembangan kesehatan ibu hamil dan anak secara berkala.</p>
                            </div>
                        </div>
                        <div class="d-flex mb-3">
                            <i class="fas fa-calendar-check text-primary me-3 fs-4"></i>
                            <div>
                                <h5>Jadwal Teratur</h5>
                                <p>Atur dan pantau jadwal pemeriksaan, imunisasi, dan penimbangan.</p>
                            </div>
                        </div>
                        <div class="d-flex">
                            <i class="fas fa-users text-primary me-3 fs-4"></i>
                            <div>
                                <h5>Tim Profesional</h5>
                                <p>Ditangani oleh tenaga kesehatan dan kader yang terlatih.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Services Section -->
    <section id="services" class="py-5 bg-light">
        <div class="container">
            <h2 class="text-center mb-5">Layanan Kami</h2>
            <div class="row g-4">
                <div class="col-md-4">
                    <div class="card feature-card h-100">
                        <div class="card-body text-center p-4">
                            <i class="fas fa-baby-carriage text-primary mb-3" style="font-size: 2.5rem;"></i>
                            <h4 class="card-title">Imunisasi</h4>
                            <p class="card-text">Program imunisasi lengkap untuk bayi dan balita sesuai dengan standar
                                kesehatan.</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card feature-card h-100">
                        <div class="card-body text-center p-4">
                            <i class="fas fa-weight text-primary mb-3" style="font-size: 2.5rem;"></i>
                            <h4 class="card-title">Penimbangan</h4>
                            <p class="card-text">Pemantauan pertumbuhan dan perkembangan anak melalui penimbangan rutin.</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card feature-card h-100">
                        <div class="card-body text-center p-4">
                            <i class="fas fa-user-nurse text-primary mb-3" style="font-size: 2.5rem;"></i>
                            <h4 class="card-title">Pemeriksaan Ibu Hamil</h4>
                            <p class="card-text">Pemeriksaan rutin untuk memantau kesehatan ibu dan janin selama kehamilan.
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Articles Section -->
    <section id="articles" class="py-5 bg-light">
        <div class="container">
            <div class="text-center mb-5">
                <h2 class="fw-bold text-dark">Artikel Kesehatan Terbaru</h2>
                <p class="text-muted">Informasi kesehatan terkini untuk keluarga Indonesia</p>
            </div>
            
            <div class="row g-4">
                @forelse($articles as $article)
                    <div class="col-lg-4 col-md-6">
                        <div class="card article-card h-100 shadow-sm border-0">
                            <!-- Article Image -->
                            <div class="position-relative overflow-hidden">
                                @if($article->image)
                                    <img src="{{ asset('storage/' . $article->image) }}" 
                                         class="card-img-top" 
                                         alt="{{ $article->title }}"
                                         style="height: 200px; object-fit: cover;">
                                @else
                                    <div class="card-img-top bg-primary d-flex align-items-center justify-content-center" 
                                         style="height: 200px;">
                                        <i class="fas fa-newspaper fa-3x text-white opacity-50"></i>
                                    </div>
                                @endif
                                
                                <!-- Published Badge -->
                                @if($article->is_published)
                                    <span class="position-absolute top-0 end-0 m-2">
                                        <span class="badge bg-success">
                                            <i class="fas fa-check me-1"></i>
                                            Published
                                        </span>
                                    </span>
                                @endif
                            </div>
                            
                            <div class="card-body d-flex flex-column">
                                <!-- Article Meta -->
                                <div class="d-flex align-items-center text-muted small mb-2">
                                    <i class="fas fa-calendar me-2"></i>
                                    <span>{{ $article->created_at->locale('id')->isoFormat('D MMM YYYY') }}</span>
                                    @if($article->author)
                                        <span class="mx-2">•</span>
                                        <i class="fas fa-user me-1"></i>
                                        <span>{{ $article->author->name }}</span>
                                    @endif
                                </div>
                                
                                <!-- Article Title -->
                                <h5 class="card-title fw-bold text-dark mb-3">
                                    {{ Str::limit($article->title, 60) }}
                                </h5>
                                
                                <!-- Article Excerpt -->
                                <p class="card-text text-muted flex-grow-1">
                                    {{ Str::limit($article->excerpt, 120) }}
                                </p>
                                
                                <!-- Read More Button -->
                                <div class="mt-auto">
                                    <a href="{{ route('article.show', $article->slug) }}" 
                                       class="btn btn-outline-primary btn-sm w-100">
                                        <i class="fas fa-book-open me-2"></i>
                                        Baca Selengkapnya
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="col-12">
                        <div class="text-center py-5">
                            <div class="mb-4">
                                <i class="fas fa-newspaper fa-4x text-muted opacity-50"></i>
                            </div>
                            <h4 class="text-muted">Belum Ada Artikel</h4>
                            <p class="text-muted">Artikel kesehatan akan segera hadir untuk Anda.</p>
                        </div>
                    </div>
                @endforelse
            </div>
            
            <!-- View All Articles Button -->
            @if($articles->count() > 0)
                <div class="text-center mt-5">
                    <a href="{{ route('articles.index') }}" class="btn btn-primary btn-lg">
                        <i class="fas fa-list me-2"></i>
                        Lihat Semua Artikel
                    </a>
                </div>
            @endif
        </div>
    </section>


    <!-- Contact Section -->
    <section id="contact" class="py-5 bg-light">
        <div class="container">
            <div class="row">
                <div class="col-lg-6 mb-4 mb-lg-0">
                    <h2 class="mb-4">Hubungi Kami</h2>
                    <p class="lead mb-4">Punya pertanyaan? Jangan ragu untuk menghubungi kami.</p>
                    <div class="d-flex mb-3">
                        <i class="fas fa-map-marker-alt text-primary me-3 fs-4"></i>
                        <p>{{ isset($siteIdentity) ? $siteIdentity->address : 'Alamat Posyandu' }}</p>
                    </div>
                    <div class="d-flex mb-3">
                        <i class="fas fa-phone text-primary me-3 fs-4"></i>
                        <p>{{ isset($siteIdentity) ? $siteIdentity->phone : 'Nomor Telepon' }}</p>
                    </div>
                    <div class="d-flex">
                        <i class="fas fa-envelope text-primary me-3 fs-4"></i>
                        <p>{{ isset($siteIdentity) ? $siteIdentity->email : 'Email' }}</p>
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="contact-form">
                        <form>
                            <div class="mb-3">
                                <input type="text" class="form-control" placeholder="Nama Lengkap">
                            </div>
                            <div class="mb-3">
                                <input type="email" class="form-control" placeholder="Alamat Email">
                            </div>
                            <div class="mb-3">
                                <textarea class="form-control" rows="5" placeholder="Pesan"></textarea>
                            </div>
                            <button type="submit" class="btn btn-primary">Kirim Pesan</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
