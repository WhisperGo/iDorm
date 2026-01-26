<div class="navbar-sticky">
    <nav class="nav navbar navbar-expand-xl navbar-light iq-navbar">
        <div class="container-fluid navbar-inner">
            <a href="{{ url('/dashboard') }}" class="navbar-brand">
                <div class="logo-main">
                    <div class="logo-normal d-flex align-items-center">
                        <img src="{{ asset('hopeui/images/logo/iDorm2.png') }}" class="img-fluid" style="height: 30px;"
                            alt="iDorm Logo">
                        <h4 class="fw-bold logo-title ms-3 mb-0">iDorm</h4>
                    </div>
                </div>
            </a>

            <div class="input-group search-input">
                <span class="input-group-text" id="search-input">
                    <svg class="icon-18" width="18" viewBox="0 0 24 24" fill="none"
                        xmlns="http://www.w3.org/2000/svg">
                        <circle cx="11.7669" cy="11.7666" r="8.98856" stroke="currentColor" stroke-width="1.5"
                            stroke-linecap="round" stroke-linejoin="round"></circle>
                        <path d="M18.0186 18.4851L21.5426 22" stroke="currentColor" stroke-width="1.5"
                            stroke-linecap="round" stroke-linejoin="round"></path>
                    </svg>
                </span>
                <input type="search" class="form-control" placeholder="Search...">
            </div>

            <div class="collapse navbar-collapse" id="navbarSupportedContent">
                <ul class="mb-2 navbar-nav ms-auto align-items-center navbar-list mb-lg-0">
                    <li class="nav-item dropdown custom-drop">
                        <a class="py-0 nav-link d-flex align-items-center" href="#" id="navbarDropdown"
                            role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            <img src="{{ asset('hopeui/images/avatars/01.png') }}" alt="User-Profile"
                                class="theme-color-default-img img-fluid avatar avatar-50 avatar-rounded">

                            {{-- BAGIAN NAMA & ROLE --}}
                            <div class="caption ms-3 d-none d-md-block ">
                                @if (Auth::user()->role->role_name == 'Resident')
                                    <h6 class="mb-0 caption-title fw-bold">
                                        {{ Auth::user()->residentDetails->full_name ?? Auth::user()->name }}
                                    </h6>
                                @elseif (Auth::user()->role->role_name == 'Admin')
                                    <h6 class="mb-0 caption-title fw-bold">
                                        {{ Auth::user()->adminDetails->full_name ?? Auth::user()->name }}
                                    </h6>
                                @elseif (Auth::user()->role->role_name == 'Manager')
                                    <h6 class="mb-0 caption-title fw-bold">
                                        {{ Auth::user()->headResidentDetails->full_name ?? Auth::user()->name }}
                                    </h6>
                                @else
                                    <h6 class="mb-0 caption-title fw-bold">
                                        {{ Auth::user()->name }}
                                    </h6>
                                @endif
                                {{-- BAGIAN NAMA & ROLE --}}

                                {{-- Role Name Display --}}
                                @if (Auth::user()->role->role_name == 'Resident')
                                    <p class="mb-0 caption-sub-title text-muted" style="font-size: 0.75rem;">
                                        {{ Auth::user()->role->role_name }}
                                        {{-- kalo penghuni rolenya penghuni, kalo admin rolenya admin, kalau penhelola rolenya pengelola --}}
                                    </p>
                                @elseif (Auth::user()->role->role_name == 'Admin')
                                    <p class="mb-0 caption-sub-title text-muted" style="font-size: 0.75rem;">
                                        {{ Auth::user()->role->role_name }}
                                    </p>
                                @elseif (Auth::user()->role->role_name == 'Manager')
                                    <p class="mb-0 caption-sub-title text-muted"
                                        style="font-size:
                                        0.75rem;">
                                        {{ Auth::user()->role->role_name }}
                                    </p>
                                @endif
                                {{-- Role Name Display --}}
                            </div>
                        </a>

                        <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdown">
                            <li>
                                <a href="{{ route('profile.edit') }}" class="dropdown-item d-flex align-items-center">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                                        fill="currentColor" class="bi bi-person me-2" viewBox="0 0 16 16">
                                        <path
                                            d="M8 8a3 3 0 1 0 0-6 3 3 0 0 0 0 6m2-3a2 2 0 1 1-4 0 2 2 0 0 1 4 0m4 8c0 1-1 1-1 1H3s-1 0-1-1 1-4 6-4 6 3 6 4m-1-.004c-.001-.246-.154-.986-.832-1.664C11.516 10.68 10.289 10 8 10s-3.516.68-4.168 1.332c-.678.678-.83 1.418-.832 1.664z" />
                                    </svg>
                                    Profile
                                </a>
                            </li>
                            <li>
                                <hr class="dropdown-divider">
                            </li>
                            <li>
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <a href="{{ route('logout') }}"
                                        class="dropdown-item d-flex align-items-center text-danger"
                                        onclick="event.preventDefault(); this.closest('form').submit();">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                                            fill="currentColor" class="bi bi-power me-2" viewBox="0 0 16 16">
                                            <path d="M7.5 1v7h1V1z" />
                                            <path
                                                d="M3 8.812a5 5 0 0 1 2.578-4.375l-.485-.874A6 6 0 1 0 11 3.616l-.501.865A5 5 0 1 1 3 8.812" />
                                        </svg>
                                        Log Out
                                    </a>
                                </form>
                            </li>
                        </ul>
                    </li>
                </ul>
            </div>
        </div>
    </nav>
</div>
