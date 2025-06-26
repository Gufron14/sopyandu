<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta content="width=device-width, initial-scale=1, maximum-scale=1, shrink-to-fit=no" name="viewport">
    <title>SIDU | @yield('title')</title>

    <!-- Favicon -->
    <link rel="shortcut icon" href="{{ asset('img/logo.png') }}" type="image/png">
    <link rel="apple-touch-icon" href="{{ asset('img/logo.png') }}">

    <!-- General CSS Files -->
    <link rel="stylesheet" href="{{ asset('modules/bootstrap/css/bootstrap.min.css') }}">

    <!-- Template CSS -->
    <link rel="stylesheet" href="{{ asset('css/style.min.css') }}">
    <link rel="stylesheet" href="{{ asset('css/components.css') }}">
    <style>
        @media print {
            @page {
                size: A4 landscape;
            }
        }

        html,
        body {
            font-family: Arial, Helvetica, sans-serif;
        }

        .table {
            font-size: 10px !important;
        }

        .table tr>* {
            vertical-align: middle !important;
        }
    </style>
</head>

<body>
    <div id="app">
        <div class="main-wrapper">
            <div class="container-fluid p-4">
                <header class="d-flex justify-content-between align-items-center">
                    <div class="d-flex align-items-center" style="gap: .5rem">
                        <img src="{{ asset('img/logo.png') }}" alt="Logo" class="rounded" height="40">
                        <h5 class="font-weight-bold mb-0" style="color: #6777ef;">SIDU</h5>
                    </div>
                    <span class="text-small">
                        Dicetak: {{ \Carbon\Carbon::now()->locale('id')->isoFormat('D MMMM YYYY [|] HH:mm') }}, oleh:
                        {{ Auth::check() && Auth::user()->officers?->fullname ? Auth::user()->officers->fullname : 'N/A' }}
                    </span>
                </header>

                <hr class="mt-3 mb-4">

                <!-- Content -->
                @yield('main')
                <!-- End content -->
            </div>
        </div>
    </div>

    <!-- General JS Scripts -->
    <script src="{{ asset('modules/jquery.min.js') }}"></script>
    <script src="{{ asset('modules/popper.js') }}"></script>
    <script src="{{ asset('modules/bootstrap/js/bootstrap.min.js') }}"></script>
    <script src="{{ asset('js/stisla.js') }}"></script>

    <!-- Template JS File -->
    <script src="{{ asset('js/scripts.js') }}"></script>
    <script src="{{ asset('js/custom.js') }}"></script>
</body>

</html>
