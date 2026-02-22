<!doctype html>
<html lang="en" dir="ltr" data-bs-theme="light" data-bs-theme-color="theme-color-default">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>iDorm | Sign Up</title>

    <link rel="shortcut icon" href="{{ asset('hopeui/images/favicon.ico') }}">
    <link rel="stylesheet" href="{{ asset('hopeui/vendor/aos/dist/aos.css') }}">
    <link rel="stylesheet" href="{{ asset('hopeui/css/hope-ui.min.css') }}">
</head>

<body class=" " data-bs-spy="scroll" data-bs-target="#elements-section" data-bs-offset="0" tabindex="0">
    <div id="loading">
        <div class="loader simple-loader">
            <div class="loader-body">
            </div>
        </div>
    </div>
    <div class="offcanvas offcanvas-end" tabindex="-1" id="offcanvasExample" data-bs-scroll="true"
        data-bs-backdrop="true" aria-labelledby="offcanvasExampleLabel">
        <div class="offcanvas-header">
            <div class="d-flex align-items-center">
                <h3 class="offcanvas-title me-3" id="offcanvasExampleLabel">Settings</h3>
            </div>
            <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
        </div>
        <div class="offcanvas-body data-scrollbar">
            <div class="row">
                <div class="col-lg-12">
                    <h5 class="mb-3">Scheme</h5>
                </div>
            </div>
        </div>
    </div>

    <div class="wrapper">
        <section class="login-content">
            <div class="row m-0 bg-white vh-100">

                <div class="col-md-6 d-md-block d-none bg-primary p-0 vh-100 overflow-hidden">
                    <img src="{{ asset('hopeui/images/auth/05.png') }}" class="img-fluid gradient-main animated-scaleX"
                        alt="images">
                </div>

                <div class="col-md-6 d-flex align-items-center">
                    <div class="row justify-content-center w-100">
                        <div class="col-md-10">
                            <div class="card card-transparent auth-card shadow-none d-flex justify-content-center mb-0">
                                <div class="card-body">
                                    <a href="{{ url('/') }}" class="navbar-brand d-flex align-items-center mb-3">
                                        <div class="logo-main">
                                            <div class="logo-normal d-flex align-items-center">
                                                <img src="{{ asset('hopeui/images/logo/iDorm2.png') }}"
                                                    class="img-fluid" style="height: 30px;" alt="iDorm Logo">
                                                <h4 class="fw-bold logo-title ms-3 mb-0">iDorm</h4>
                                            </div>
                                        </div>
                                    </a>
                                    <h2 class="mb-2 text-center">Sign Up</h2>
                                    <p class="text-center">Create your iDorm account.</p>

                                    <form method="POST" action="{{ route('register') }}">
                                        @csrf

                                        <div class="row">
                                            <div class="col-lg-12">
                                                <div class="form-group">
                                                    <label for="name" class="form-label">Full Name</label>
                                                    <input type="text"
                                                        class="form-control @error('name') is-invalid @enderror"
                                                        id="name" name="name" value="{{ old('name') }}"
                                                        placeholder=" " required autofocus autocomplete="name">

                                                    @error('name')
                                                        <div class="invalid-feedback">
                                                            {{ $message }}
                                                        </div>
                                                    @enderror
                                                </div>
                                            </div>

                                            <div class="col-lg-12">
                                                <div class="form-group">
                                                    <label for="email" class="form-label">Email</label>
                                                    <input type="email"
                                                        class="form-control @error('email') is-invalid @enderror"
                                                        id="email" name="email" value="{{ old('email') }}"
                                                        placeholder=" " required autocomplete="username">

                                                    @error('email')
                                                        <div class="invalid-feedback">
                                                            {{ $message }}
                                                        </div>
                                                    @enderror
                                                </div>
                                            </div>

                                            <div class="col-lg-6">
                                                <div class="form-group">
                                                    <label for="password" class="form-label">Password</label>
                                                    <input type="password"
                                                        class="form-control @error('password') is-invalid @enderror"
                                                        id="password" name="password" placeholder=" " required
                                                        autocomplete="new-password">

                                                    @error('password')
                                                        <div class="invalid-feedback">
                                                            {{ $message }}
                                                        </div>
                                                    @enderror
                                                </div>
                                            </div>

                                            <div class="col-lg-6">
                                                <div class="form-group">
                                                    <label for="password_confirmation" class="form-label">Confirm
                                                        Password</label>
                                                    <input type="password"
                                                        class="form-control @error('password_confirmation') is-invalid @enderror"
                                                        id="password_confirmation" name="password_confirmation"
                                                        placeholder=" " required autocomplete="new-password">

                                                    @error('password_confirmation')
                                                        <div class="invalid-feedback">
                                                            {{ $message }}
                                                        </div>
                                                    @enderror
                                                </div>
                                            </div>

                                            <div class="col-lg-12 d-flex justify-content-center">
                                                <div class="form-check mb-3">
                                                    <input type="checkbox" class="form-check-input" id="customCheck1"
                                                        required>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="d-flex justify-content-center">
                                            <button type="submit" class="btn btn-primary">Sign Up</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
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
