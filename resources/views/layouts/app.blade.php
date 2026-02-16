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

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">

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

        .iq-top-navbar {
            z-index: 1040 !important;
            /* Naikkan jadi 1040 agar setara sidebar */
        }

        /* 2. Pastikan Sidebar tetap di bawah Modal Backdrop (1050) */
        .sidebar,
        .iq-sidebar {
            z-index: 1041 !important;
            /* Sidebar sedikit di atas navbar agar shadow-nya terlihat bagus */
        }

        /* 3. KHUSUS UNTUK MODAL (PENTING) */
        /* Saat modal terbuka, paksa navbar & sidebar turun kelas */
        body.modal-open .iq-top-navbar,
        body.modal-open .sidebar,
        body.modal-open .iq-sidebar {
            z-index: 1000 !important;
            /* Turunkan drastis saat ada modal */
        }

        ::-ms-reveal {
            display: none;
        }

        /* Opsional: Menyembunyikan tombol 'clear' bawaan (tanda silang) */
        ::-ms-clear {
            display: none;
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
    @yield('styles')
</head>

@push('scripts')

    <body class="">
        {{-- <div id="loading">
        <div class="loader simple-loader">
            <div class="loader-body"></div>
        </div>
    </div> --}}

        @include('layouts.partials.sidebar')

        <main class="main-content">

            @include('layouts.partials.navbar')

            <div class="position-relative">
                <div class="iq-banner">
                    @include('layouts.partials.banner')
                </div>

                <div class="container-fluid content-inner mt-5 py-0">
                    @yield('content')
                </div>

                @include('layouts.partials.footer')
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

        @stack('scripts')
    </body>

    <script>
        document.querySelectorAll('.btn-freeze').forEach(button => {
            button.addEventListener('click', function(e) {
                const form = this.closest('.freeze-form');
                const status = this.getAttribute('title'); // Mengambil teks dari title

                Swal.fire({
                    title: 'Konfirmasi Perubahan',
                    text: `Apakah Anda yakin ingin melakukan "${status}"?`,
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Ya, Lanjutkan!',
                    cancelButtonText: 'Batal',
                    background: '#fff',
                    customClass: {
                        popup: 'format-swal'
                    }
                }).then((result) => {
                    if (result.isConfirmed) {
                        form.submit(); // Submit form jika user klik 'Ya'
                    }
                });
            });
        });

        document.addEventListener('DOMContentLoaded', function() {
            const maxLimit = 2; // Batas maksimal
            const checkboxes = document.querySelectorAll('.machine-checkbox');
            const alertBox = document.getElementById('max-selection-alert');

            // Fungsi untuk update tampilan (Solid vs Outline)
            function updateVisuals() {
                checkboxes.forEach(box => {
                    // Cari label temannya
                    const label = document.querySelector(`label[for="${box.id}"]`);

                    // Logika Ganti Warna (FORCE SWAP CLASS)
                    if (box.checked) {
                        // Kalau dicentang: Jadi Solid Biru
                        label.classList.remove('btn-outline-primary');
                        label.classList.add('btn-primary');
                    } else {
                        // Kalau tidak dicentang: Jadi Garis Biru (Outline)
                        label.classList.remove('btn-primary');
                        label.classList.add('btn-outline-primary');
                    }
                });

                // Logika Disable jika sudah 2
                const checkedCount = document.querySelectorAll('.machine-checkbox:checked').length;

                if (checkedCount >= maxLimit) {
                    checkboxes.forEach(box => {
                        if (!box.checked) {
                            box.disabled = true;
                            // Bikin labelnya agak transparan biar kelihatan disabled
                            const label = document.querySelector(`label[for="${box.id}"]`);
                            label.classList.add('opacity-50');
                        }
                    });
                } else {
                    checkboxes.forEach(box => {
                        box.disabled = false;
                        const label = document.querySelector(`label[for="${box.id}"]`);
                        label.classList.remove('opacity-50');
                    });
                }
            }

            checkboxes.forEach(checkbox => {
                checkbox.addEventListener('change', function() {
                    // 1. Cek Limit dulu sebelum update visual
                    const checkedCount = document.querySelectorAll('.machine-checkbox:checked')
                        .length;

                    if (checkedCount > maxLimit) {
                        this.checked = false; // Batalkan centangan terakhir

                        // Tampilkan alert
                        alertBox.classList.remove('d-none');
                        setTimeout(() => {
                            alertBox.classList.add('d-none');
                        }, 3000);
                    }

                    // 2. Update Tampilan (Warna & Disable state)
                    updateVisuals();

                    // 3. Hilangkan focus agar outline biru bawaan browser hilang
                    this.blur();
                });
            });

            // Jalankan sekali saat load (untuk handle old input kalau ada error validasi sebelumnya)
            updateVisuals();
        });
    </script>

    </html>
    <!doctype html>
    <html lang="en" dir="ltr" data-bs-theme="light" data-bs-theme-color="theme-color-default">
