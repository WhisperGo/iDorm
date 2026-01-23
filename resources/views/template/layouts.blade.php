<!doctype html>
<html lang="en" dir="ltr" data-bs-theme="light" data-bs-theme-color="theme-color-default">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>iDorm</title>

    <link rel="shortcut icon" href="{{ asset('hopeui/images/favicon.ico') }}" />
    <link rel="stylesheet" href="{{ asset('hopeui/css/core/libs.min.css') }}" />
    <link rel="stylesheet" href="{{ asset('hopeui/css/hope-ui.min.css') }}" />
    <link rel="stylesheet" href="{{ asset('hopeui/css/custom.min.css') }}" />
    <link rel="stylesheet" href="{{ asset('hopeui/css/customizer.min.css') }}" />
    <link rel="stylesheet" href="{{ asset('hopeui/css/rtl.min.css') }}" />

    <style>
        /* 1. SIDEBAR PALING DEPAN */
        /* Kita paksa Sidebar punya z-index tertinggi */
        aside.sidebar {
            z-index: 1060 !important;
            /* Di atas Navbar (1050) & Modal */
        }

        /* 2. NAVBAR DI BELAKANG SIDEBAR TAPI DI DEPAN KONTEN */
        /* Ini style default untuk navbar sticky kita */
        .navbar-sticky {
            position: sticky;
            top: 0;
            z-index: 1050;
            /* Di bawah Sidebar (1060), di atas Konten (1) */
            width: 100%;
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border-bottom: 1px solid #eee;
        }
    </style>

    <script>
        const storedTheme = localStorage.getItem('theme');
        if (storedTheme) {
            document.getElementById('main-html').setAttribute('data-bs-theme', storedTheme);
        } else if (window.matchMedia('(prefers-color-scheme: dark)').matches) {
            document.getElementById('main-html').setAttribute('data-bs-theme', 'dark');
        }
    </script>
</head>

<body class="">
    <div id="loading">
        <div class="loader simple-loader">
            <div class="loader-body"></div>
        </div>
    </div>

    @include('template.sidebar')

    <main>
        @yield('content')
    </main>

    <main class="main-content">

        @include('template.navbar')

        <div class="position-relative">
            <div class="iq-banner">
                @include('penghuni.banner')
            </div>

            <div class="container-fluid content-inner mt-5 py-0">
                @yield('content')
            </div>

            @include('template.footer')
        </div>

    </main>

    <script src="{{ asset('hopeui/js/core/libs.min.js') }}"></script>
    <script src="{{ asset('hopeui/js/core/external.min.js') }}"></script>
    <script src="{{ asset('hopeui/js/charts/widgetcharts.js') }}"></script>
    <script src="{{ asset('hopeui/js/charts/vectore-chart.js') }}"></script>
    <script src="{{ asset('hopeui/js/charts/dashboard.js') }}"></script>
    <script src="{{ asset('hopeui/js/plugins/fslightbox.js') }}"></script>
    <script src="{{ asset('hopeui/js/plugins/setting.js') }}"></script>
    <script src="{{ asset('hopeui/js/plugins/slider-tabs.js') }}"></script>
    <script src="{{ asset('hopeui/js/plugins/form-wizard.js') }}"></script>
    <script src="{{ asset('hopeui/vendor/aos/dist/aos.js') }}"></script>
    <script src="{{ asset('hopeui/js/hope-ui.js') }}" defer></script>
</body>

</html>
