<!doctype html>
<html lang="en" dir="ltr" data-bs-theme="light" data-bs-theme-color="theme-color-default">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>iDorm | Sign In</title>

    <link rel="shortcut icon" href="{{ asset('hopeui/images/favicon.ico') }}">
    <link rel="stylesheet" href="{{ asset('hopeui/vendor/aos/dist/aos.css') }}">
    <link rel="stylesheet" href="{{ asset('hopeui/css/hope-ui.min.css') }}">
    <style>
        /* Fix missing hover color variable specifically for this page */
        .btn-primary {
            --bs-btn-hover-bg: #2e46ba;
            --bs-btn-hover-border-color: #293da2;
        }

        /* Essential for the right-side cover image to scale properly on zoom */
        .gradient-main {
            height: 100%;
            width: 100%;
            object-fit: cover;
        }
    </style>
</head>

<body class=" " data-bs-spy="scroll" data-bs-target="#elements-section" data-bs-offset="0" tabindex="0">
    <!-- <div id="loading">
        <div class="loader simple-loader">
            <div class="loader-body">
            </div>
        </div>
    </div> -->
    <div class="wrapper">
        <section class="login-content">
            <div class="row m-0 align-items-center bg-white vh-100">
                <div class="col-md-6">
                    <div class="row justify-content-center">
                        <div class="col-md-10">
                            <div class="card card-transparent shadow-none d-flex justify-content-center mb-0 auth-card">
                                <div class="card-body z-3 px-md-0 px-lg-4">
                                    <a href="#" class="navbar-brand d-flex align-items-center mb-4">
                                        <div class="logo-main">
                                            <div class="logo-normal d-flex align-items-center">
                                                <img src="{{ asset('hopeui/images/logo/iDorm2.png') }}" class="img-fluid"
                                                    style="height: 30px;" alt="iDorm Logo">
                                            </div>
                                        </div>
                                        <h4 class="fw-bold logo-title ms-3 mb-0">iDorm</h4>
                                    </a>
                                    <h2 class="mb-2 text-center">Sign In</h2>
                                    <p class="text-center">Login to stay connected.</p>

                                    @if (session('status'))
                                    <div class="alert alert-success mb-4" role="alert">
                                        {{ session('status') }}
                                    </div>
                                    @endif

                                    @if ($errors->any())
                                    <svg xmlns="http://www.w3.org/2000/svg" style="display: none;">
                                        <symbol id="exclamation-triangle-fill" fill="currentColor" viewBox="0 0 16 16">
                                            <path d="M8.982 1.566a1.13 1.13 0 0 0-1.96 0L.165 13.233c-.457.778.091 1.767.98 1.767h13.713c.889 0 1.438-.99.98-1.767L8.982 1.566zM8 5c.535 0 .954.462.9.995l-.35 3.507a.552.552 0 0 1-1.1 0L7.1 5.995A.905.905 0 0 1 8 5zm.002 6a1 1 0 1 1 0 2 1 1 0 0 1 0-2z" />
                                        </symbol>
                                    </svg>
                                    <div class="alert alert-danger d-flex align-items-center mb-4" role="alert">
                                        <svg class="flex-shrink-0 bi me-2 icon-24" width="24" height="24">
                                            <use xlink:href="#exclamation-triangle-fill" />
                                        </svg>
                                        <div>
                                            {{ $errors->first() }}
                                        </div>
                                    </div>
                                    @endif

                                    <form method="POST" action="{{ route('login') }}">
                                        @csrf

                                        <div class="row">
                                            <div class="col-lg-12">
                                                <div class="form-group">
                                                    <label for="card_id" class="form-label">ID Card (4 Digits)</label>
                                                    <input type="text" maxlength="4"
                                                        class="form-control @error('card_id') is-invalid @enderror"
                                                        id="card_id" name="card_id" value="{{ old('card_id') }}"
                                                        placeholder="Enter your 4-digit ID" required autofocus>

                                                    @error('card_id')
                                                    <div class="invalid-feedback">
                                                        {{ $message }}
                                                    </div>
                                                    @enderror
                                                </div>
                                            </div>

                                            <div class="col-lg-12">
                                                <div class="form-group">
                                                    <label for="password" class="form-label">Password</label>
                                                    <input type="password"
                                                        class="form-control @error('password') is-invalid @enderror"
                                                        id="password" name="password" placeholder="Enter password"
                                                        required autocomplete="current-password">

                                                    @error('password')
                                                    <div class="invalid-feedback">
                                                        {{ $message }}
                                                    </div>
                                                    @enderror
                                                </div>
                                            </div>

                                            {{-- Letakkan tepat setelah div password --}}
                                            <div class="d-flex justify-content-between align-items-center mb-4">
                                                <div class="form-check">
                                                    <input type="checkbox" class="form-check-input" id="remember_me"
                                                        name="remember">
                                                    <label class="form-check-label" for="remember_me">Remember
                                                        Me</label>
                                                </div>
                                                {{-- Jika butuh fitur lupa password, aktifkan ini --}}
                                                {{-- <a href="#">Forgot Password?</a> --}}
                                            </div>

                                            <div class="d-flex justify-content-center">
                                                <button type="submit" class="btn btn-primary w-100 py-2 fw-bold">Sign
                                                    In</button>
                                            </div>
                                            {{-- Penutup form ada di sini --}}
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 d-md-block d-none bg-primary p-0 mt-n1 vh-100 overflow-hidden">
                    <img src="{{ asset('hopeui/images/auth/01.png') }}" class="img-fluid gradient-main animated-scaleX"
                        alt="images">
                </div>
            </div>
        </section>
    </div>

    <script src="{{ asset('hopeui/js/core/libs.min.js') }}"></script>
    <script src="{{ asset('hopeui/js/core/external.min.js') }}"></script>
    <script src="{{ asset('hopeui/js/charts/widgetcharts.js') }}"></script>
    <script src="{{ asset('hopeui/js/hope-ui.js') }}" defer></script>
    <script src="{{ asset('hopeui/js/charts/dashboard.js') }}"></script>
    <script src="{{ asset('hopeui/js/plugins/setting.js') }}"></script>
    <script src="{{ asset('hopeui/js/plugins/form-wizard.js') }}"></script>
    <script src="{{ asset('hopeui/vendor/aos/dist/aos.js') }}"></script>
</body>

</html>