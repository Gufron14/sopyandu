<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ isset($siteIdentity) ? $siteIdentity->site_name : 'SIDU' }} - Sistem Informasi Posyandu</title>
    
    <!-- Favicon -->
    <link rel="shortcut icon" href="{{ asset('favicon.ico') }}" type="image/x-icon">
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Custom CSS -->
    <style>
        /* Custom styles */
        .hero-section {
            background: linear-gradient(rgba(0, 0, 0, 0.5), rgba(0, 0, 0, 0.5)), url('{{ asset('img/hero-bg.jpg') }}') center/cover no-repeat;
            min-height: 100vh;
            display: flex;
            align-items: center;
            color: white;
        }
        
        .navbar-custom {
            background-color: rgba(255, 255, 255, 0.95);
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }
        
        .feature-card {
            border: none;
            transition: transform 0.3s;
            border-radius: 15px;
            overflow: hidden;
        }
        
        .feature-card:hover {
            transform: translateY(-10px);
        }
        
        .article-card {
            border: none;
            border-radius: 15px;
            overflow: hidden;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }
        
        .footer {
            background-color: #2c3e50;
            color: white;
        }
        
        .contact-form {
            background-color: rgba(255, 255, 255, 0.95);
            border-radius: 15px;
            padding: 30px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

            .article-card {
        transition: all 0.3s ease;
        border-radius: 15px;
        overflow: hidden;
    }
    
    .article-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 25px rgba(0,0,0,0.15) !important;
    }
    
    .article-card .card-img-top {
        transition: transform 0.3s ease;
    }
    
    .article-card:hover .card-img-top {
        transform: scale(1.05);
    }
    
    .article-card .card-body {
        padding: 1.5rem;
    }
    
    .article-card .card-title {
        line-height: 1.3;
        min-height: 2.6rem;
    }
    
    .article-card .card-text {
        line-height: 1.6;
        min-height: 4.8rem;
    }
    
    #articles {
        background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
    }
    </style>
</head>

<body>
    <!-- Navbar -->
    @include('landing-page.partials.navbar')

    <!-- Main Content -->
    @yield('content')

    <!-- Footer -->
    @include('landing-page.partials.footer')

    <!-- Bootstrap JS and dependencies -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>
