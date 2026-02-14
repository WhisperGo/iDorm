<!doctype html>
<html lang="en" dir="ltr" data-bs-theme="light" data-bs-theme-color="theme-color-default">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>iDorm | Verify Email</title>

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

                <div class="col-md-6 p-0 d-flex align-items-center justify-content-center">
                    <div class="card card-transparent auth-card shadow-none d-flex justify-content-center mb-0">
                        <div class="card-body">
                            <a href="{{ url('/') }}" class="navbar-brand d-flex align-items-center mb-3">
                                <div class="logo-main">
                                    <div class="logo-normal d-flex align-items-center">
                                        <img src="{{ asset('hopeui/images/logo/iDorm2.png') }}" class="img-fluid"
                                            style="height: 30px;" alt="iDorm Logo">
                                    </div>
                                </div>
                                <h4 class="logo-title ms-3">iDorm</h4>
                            </a>

                            <img src="{{ asset('assets/images/auth/mail.png') }}" class="img-fluid" width="80"
                                alt="">

                            <h2 class="mt-3 mb-0">Verify Your Email</h2>

                            <p class="cnf-mail mb-3 mt-2">
                                Thanks for signing up! Before getting started, could you verify your email address by
                                clicking on the link we just emailed to you? If you didn't receive the email, we will
                                gladly send you another.
                            </p>

                            @if (session('status') == 'verification-link-sent')
                                <div class="alert alert-success" role="alert">
                                    A new verification link has been sent to the email address you provided during
                                    registration.
                                </div>
                            @endif

                            <div class="d-inline-block w-100">
                                <form method="POST" action="{{ route('verification.send') }}" class="d-inline">
                                    @csrf
                                    <button type="submit" class="btn btn-primary mt-3">Resend Verification
                                        Email</button>
                                </form>

                                <form method="POST" action="{{ route('logout') }}" class="d-inline ms-2">
                                    @csrf
                                    <button type="submit" class="btn btn-secondary mt-3">Log Out</button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-6 d-md-block d-none bg-primary p-0 vh-100 overflow-hidden">
                    <img src="{{ asset('hopeui/images/auth/03.png') }}" class="img-fluid gradient-main animated-scaleX"
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
