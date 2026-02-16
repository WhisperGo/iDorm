@php
    $role = auth()->user()->role->role_name;
    $gender = auth()->user()->residentDetails->gender ?? null;
@endphp

<aside class="sidebar sidebar-default sidebar-white sidebar-base navs-rounded-all">
    <div class="sidebar-header d-flex align-items-center justify-content-start">
        <div class="navbar-brand">
            <div class="logo-main">
                <div class="logo-normal d-flex align-items-center">
                    <img src="{{ asset('hopeui/images/logo/iDorm2.png') }}" class="img-fluid" style="height: 40px;"
                        alt="iDorm Logo">
                    <h4 class="fw-bold logo-title ms-3 mb-0">iDorm</h4>
                </div>
            </div>
        </div>
        <div class="sidebar-toggle" data-toggle="sidebar" data-active="true">
            <i class="icon">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none"
                    xmlns="http://www.w3.org/2000/svg">
                    <path d="M4.25 12.2744L19.25 12.2744" stroke="currentColor" stroke-width="1.5"
                        stroke-linecap="round" stroke-linejoin="round"></path>
                    <path d="M10.2998 18.2988L4.2498 12.2748L10.2998 6.24976" stroke="currentColor" stroke-width="1.5"
                        stroke-linecap="round" stroke-linejoin="round"></path>
                </svg>
            </i>
        </div>
    </div>

    <div class="sidebar-body pt-0 data-scrollbar">
        <div class="sidebar-list">
            <ul class="navbar-nav iq-main-menu" id="sidebar-menu">
                <li class="nav-item static-item">
                    <a class="nav-link static-item disabled" href="#" tabindex="-1">
                        <span class="default-icon">Home</span>
                        <span class="mini-icon">-</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ Route::is('dashboard') ? 'active' : '' }}" href="{{ route('dashboard') }}">
                        <i class="icon">
                            <svg width="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"
                                class="icon-20">
                                <path opacity="0.4"
                                    d="M16.0756 2H19.4616C20.8639 2 22.0001 3.14585 22.0001 4.55996V7.97452C22.0001 9.38864 20.8639 10.5345 19.4616 10.5345H16.0756C14.6734 10.5345 13.5371 9.38864 13.5371 7.97452V4.55996C13.5371 3.14585 14.6734 2 16.0756 2Z"
                                    fill="currentColor"></path>
                                <path fill-rule="evenodd" clip-rule="evenodd"
                                    d="M4.53852 2H7.92449C9.32676 2 10.463 3.14585 10.463 4.55996V7.97452C10.463 9.38864 9.32676 10.5345 7.92449 10.5345H4.53852C3.13626 10.5345 2 9.38864 2 7.97452V4.55996C2 3.14585 3.13626 2 4.53852 2ZM4.53852 13.4655H7.92449C9.32676 13.4655 10.463 14.6114 10.463 16.0255V19.44C10.463 20.8532 9.32676 22 7.92449 22H4.53852C3.13626 22 2 20.8532 2 19.44V16.0255C2 14.6114 3.13626 13.4655 4.53852 13.4655ZM19.4615 13.4655H16.0755C14.6732 13.4655 13.537 14.6114 13.537 16.0255V19.44C13.537 20.8532 14.6732 22 16.0755 22H19.4615C20.8637 22 22 20.8532 22 19.44V16.0255C22 14.6114 20.8637 13.4655 19.4615 13.4655Z"
                                    fill="currentColor"></path>
                            </svg>
                        </i>
                        <span class="item-name">Dashboard</span>
                    </a>
                </li>

                {{-- MASTER DATA: Hanya Muncul untuk Pengelola --}}
                @if ($role === 'Manager')
                    <li>
                        <hr class="hr-horizontal">
                    </li>
                    <li class="nav-item static-item">
                        <a class="nav-link static-item disabled" href="#">
                            <span class="default-icon">Master Data</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ Route::is('pengelola.resident') ? 'active' : '' }}"
                            href="{{ route('pengelola.resident') }}">
                            <i class="bi bi-people-fill"></i>
                            <span class="item-name">User Data</span>
                        </a>
                    </li>
                @elseif ($role === 'Admin')
                    <li>
                        <hr class="hr-horizontal">
                    </li>
                    <li class="nav-item static-item">
                        <a class="nav-link static-item disabled" href="#">
                            <span class="default-icon">Master Data</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ Route::is('admin.resident') ? 'active' : '' }}"
                            href="{{ route('admin.resident') }}">
                            <i class="bi bi-people-fill"></i>
                            <span class="item-name">Resident Data</span>
                        </a>
                    </li>
                @endif

                <li>
                    <hr class="hr-horizontal">
                </li>

                <li class="nav-item static-item">
                    <a class="nav-link static-item disabled" href="#">
                        <span class="default-icon">Pages</span>
                    </a>
                </li>

                {{-- Menu Announcement --}}
                <li class="nav-item">
                    <a class="nav-link {{ Route::is('announcements') ? 'active' : '' }}"
                        href="{{ route('announcements') }}">
                        <i class="bi bi-chat-left-quote-fill"></i>
                        <span class="item-name">Announcement</span>
                    </a>
                </li>

                {{-- Menu Facilities (Dinamis untuk lihat jadwal) --}}
                <li class="nav-item">
                    <a class="nav-link" data-bs-toggle="collapse" href="#sidebar-special">
                        <i class="bi bi-people-fill"></i>
                        <span class="item-name">Facilities Schedule</span>
                    </a>
                    <ul class="sub-nav collapse" id="sidebar-special">
                        {{-- Menggunakan rute facility.schedule yang baru --}}
                        <li class="nav-item">
                            <a class="nav-link {{ Request::is('facility-schedule/dapur') ? 'active' : '' }}"
                                href="{{ route('facility.schedule', 'dapur') }}">
                                <i class="icon">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                                        fill="currentColor" class="bi bi-fork-knife" viewBox="0 0 16 16">
                                        <path
                                            d="M13 .5c0-.276-.226-.506-.498-.465-1.703.257-2.94 2.012-3 8.462a.5.5 0 0 0 .498.5c.56.01 1 .13 1 1.003v5.5a.5.5 0 0 0 .5.5h1a.5.5 0 0 0 .5-.5zM4.25 0a.25.25 0 0 1 .25.25v5.122a.128.128 0 0 0 .256.006l.233-5.14A.25.25 0 0 1 5.24 0h.522a.25.25 0 0 1 .25.238l.233 5.14a.128.128 0 0 0 .256-.006V.25A.25.25 0 0 1 6.75 0h.29a.5.5 0 0 1 .498.458l.423 5.07a1.69 1.69 0 0 1-1.059 1.711l-.053.022a.92.92 0 0 0-.58.884L6.47 15a.971.971 0 1 1-1.942 0l.202-6.855a.92.92 0 0 0-.58-.884l-.053-.022a1.69 1.69 0 0 1-1.059-1.712L3.462.458A.5.5 0 0 1 3.96 0z" />
                                    </svg>
                                </i>
                                <span class="item-name">Dapur</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ Request::is('facility-schedule/mesin-cuci') ? 'active' : '' }}"
                                href="{{ route('facility.schedule', 'mesin-cuci') }}">
                                <i class="icon">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                                        fill="currentColor" class="bi bi-droplet-fill" viewBox="0 0 16 16">
                                        <path
                                            d="M8 16a6 6 0 0 0 6-6c0-1.655-1.122-2.904-2.432-4.362C10.254 4.176 8.75 2.503 8 0c0 0-6 5.686-6 10a6 6 0 0 0 6 6M6.646 4.646l.708.708c-.29.29-1.128 1.311-1.907 2.87l-.894-.448c.82-1.641 1.717-2.753 2.093-3.13" />
                                    </svg>
                                </i>
                                <span class="item-name">Mesin Cuci</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ Request::is('facility-schedule/theater') ? 'active' : '' }}"
                                href="{{ route('facility.schedule', 'theater') }}">
                                <i class="icon">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                                        fill="currentColor" class="bi bi-film" viewBox="0 0 16 16">
                                        <path
                                            d="M0 1a1 1 0 0 1 1-1h14a1 1 0 0 1 1 1v14a1 1 0 0 1-1 1H1a1 1 0 0 1-1-1zm4 0v6h8V1zm8 8H4v6h8zM1 1v2h2V1zm2 3H1v2h2zM1 7v2h2V7zm2 3H1v2h2zm-2 3v2h2v-2zM15 1h-2v2h2zm-2 3v2h2V4zm2 3h-2v2h2zm-2 3v2h2v-2zm2 3h-2v2h2z" />
                                    </svg>
                                </i>
                                <span class="item-name">Theater</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ Request::is('facility-schedule/sergun') ? 'active' : '' }}"
                                href="{{ route('facility.schedule', 'sergun') }}">
                                <i class="icon">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                                        fill="currentColor" class="bi bi-house-door" viewBox="0 0 16 16">
                                        <path
                                            d="M8.354 1.146a.5.5 0 0 0-.708 0l-6 6A.5.5 0 0 0 1.5 7.5v7a.5.5 0 0 0 .5.5h4.5a.5.5 0 0 0 .5-.5v-4h2v4a.5.5 0 0 0 .5.5H14a.5.5 0 0 0 .5-.5v-7a.5.5 0 0 0-.146-.354L13 5.793V2.5a.5.5 0 0 0-.5-.5h-1a.5.5 0 0 0-.5.5v1.293zM2.5 14V7.707l5.5-5.5 5.5 5.5V14H10v-4a.5.5 0 0 0-.5-.5h-3a.5.5 0 0 0-.5.5v4z" />
                                    </svg>
                                </i>
                                <span class="item-name">Serba Guna</span>
                            </a>
                        </li>
                        <li class="nav-item"><a
                                class="nav-link {{ Request::is('facility-schedule/cws') ? 'active' : '' }}"
                                href="{{ route('facility.schedule', 'cws') }}">
                                <i class="icon">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                                        fill="currentColor" class="bi bi-wifi" viewBox="0 0 16 16">
                                        <path
                                            d="M15.384 6.115a.485.485 0 0 0-.047-.736A12.44 12.44 0 0 0 8 3C5.259 3 2.723 3.882.663 5.379a.485.485 0 0 0-.048.736.52.52 0 0 0 .668.05A11.45 11.45 0 0 1 8 4c2.507 0 4.827.802 6.716 2.164.205.148.49.13.668-.049" />
                                        <path
                                            d="M13.229 8.271a.482.482 0 0 0-.063-.745A9.46 9.46 0 0 0 8 6c-1.905 0-3.68.56-5.166 1.526a.48.48 0 0 0-.063.745.525.525 0 0 0 .652.065A8.46 8.46 0 0 1 8 7a8.46 8.46 0 0 1 4.576 1.336c.206.132.48.108.653-.065m-2.183 2.183c.226-.226.185-.605-.1-.75A6.5 6.5 0 0 0 8 9c-1.06 0-2.062.254-2.946.704-.285.145-.326.524-.1.75l.015.015c.16.16.407.19.611.09A5.5 5.5 0 0 1 8 10c.868 0 1.69.201 2.42.56.203.1.45.07.61-.091zM9.06 12.44c.196-.196.198-.52-.04-.66A2 2 0 0 0 8 11.5a2 2 0 0 0-1.02.28c-.238.14-.236.464-.04.66l.706.706a.5.5 0 0 0 .707 0l.707-.707z" />
                                    </svg>
                                </i>
                                <span class="item-name">CWS</span>
                            </a>
                        </li>
                    </ul>
                </li>

                {{-- BOOKING: Hanya untuk Resident --}}
                @if ($role === 'Resident')
                    <li class="nav-item">
                        <a class="nav-link {{ Route::is('booking.create') ? 'active' : '' }}"
                            href="{{ route('booking.create') }}">
                            <i class="bi bi-calendar4-week"></i>
                            <span class="item-name">Make a Booking</span>
                        </a>
                    </li>

                    {{-- MENU BARU: My Bookings --}}
                    <li class="nav-item">
                        <a class="nav-link {{ Route::is('booking.my_bookings') ? 'active' : '' }}"
                            href="{{ route('booking.my_bookings') }}">
                            <i class="bi bi-clock-history"></i>
                            <span class="item-name">My Bookings</span>
                        </a>
                    </li>
                @endif

                {{-- COMPLAINT: Link ke list keluhan (Filtered by Room for Residents) --}}
                @if(auth()->user()->role->role_name != 'Admin')
                    <li class="nav-item">
                        @php
                            // Tentukan nama rute berdasarkan role
                            $complaintRoute =
                                Auth::user()->role->role_name === 'Resident' ? 'complaint.index' : 'admin.complaint';
                        @endphp
                        <a class="nav-link {{ Route::is($complaintRoute) ? 'active' : '' }}"
                            href="{{ route($complaintRoute) }}">
                            <i class="icon">
                                <i class="bi bi-exclamation-octagon"></i>
                            </i>
                            <span class="item-name">Complaints</span>
                        </a>
                    </li>
                @endif

                {{-- TAMBAHKAN MENU KOS PREDICTION DISINI --}}
                <li class="nav-item static-item">
                    <a class="nav-link static-item disabled" href="#">
                        <span class="default-icon">Boarding House Prediction</span>
                    </a>
                </li>

                <li class="nav-item">
                    <a class="nav-link {{ Route::is('prediction.index') ? 'active' : '' }}"
                        href="{{ route('prediction.index') }}">
                        <i class="icon">
                            <i class="bi bi-magic"></i> {{-- Ikon tongkat sihir agar terasa seperti 'Smart/AI' --}}
                        </i>
                        <span class="item-name">Smart Prediction</span>
                    </a>
                </li>

                {{-- REPORT: Hanya untuk Pengelola --}}
                @if ($role === 'Manager')
                    <li>
                        <hr class="hr-horizontal">
                    </li>

                    <li class="nav-item static-item">
                        <a class="nav-link static-item disabled" href="#">
                            <span class="default-icon">Report</span>
                        </a>
                    </li>

                    <li class="nav-item">
                        <a class="nav-link {{ Route::is('pengelola.report') ? 'active' : '' }}"
                            href="{{ route('pengelola.report') }}">
                            <i class="bi bi-file-earmark-arrow-down-fill"></i>
                            <span class="item-name">Loan Report</span>
                        </a>
                    </li>
                @endif
            </ul>
        </div>
    </div>
</aside>
