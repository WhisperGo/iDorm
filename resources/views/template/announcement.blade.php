@extends(auth()->user()->role->role_name === 'Resident' ? 'penghuni.layouts' : (auth()->user()->role->role_name === 'Admin' ? 'admin.layouts' : 'pengelola.layouts'))

@section('content')
    <div class="row justify-content-center">
        <div class="col-sm-12">
            {{-- --- CARD BACKGROUND UTAMA --- --}}
            <div class="card shadow-sm border-0">
                <div class="card-header d-flex justify-content-between align-items-center flex-wrap bg-white py-3">
                    <div class="header-title">
                        <h4 class="card-title mb-0 fw-bold text-dark">Announcement List</h4>
                    </div>

                    <div class="d-flex align-items-center gap-2">
                        <form action="{{ url()->current() }}" method="GET" class="d-flex gap-2">
                            <input type="text" name="search" class="form-control form-control-sm" style="width: 250px;"
                                placeholder="Cari pengumuman..." value="{{ request('search') }}">
                            <button type="submit" class="btn btn-primary btn-sm">
                                <i class="bi bi-search"></i>
                            </button>
                        </form>
                    </div>
                </div>

                <div class="card-body bg-light-subtle"> {{-- Warna background agak beda biar card dalamnya 'pop' --}}

                    <div class="row">
                        @forelse($announcements as $announcement)
                            {{-- --- LOOPING CARD PENGUMUMAN (DI DALAM) --- --}}
                            <div class="col-12 mb-3">
                                <div class="card border-0 shadow-sm animate__animated animate__fadeInUp">
                                    <div class="card-body">
                                        <div class="d-flex justify-content-between align-items-start mb-2">
                                            <div>
                                                {{-- <h4 class="fw-bold text-primary mb-0">{{ $announcement->title }}</h4> --}}
                                                <h3 class="fw-bold text-primary mb-0">{{ $announcement->title }}</h3>
                                                <span class="badge bg-soft-info text-info rounded-pill">
                                                    <i class="bi bi-calendar3 me-1"></i>
                                                    {{ $announcement->created_at->format('d M Y') }}
                                                </span>
                                                <small class="text-muted">
                                                    <i class="bi bi-person-circle me-1"></i> Posted by Admin
                                                </small>
                                            </div>
                                            <span class="badge rounded-pill bg-label-primary px-3">
                                                {{ $announcement->created_at->format('d M Y') }}
                                            </span>
                                        </div>
                                        <hr class="my-3 opacity-25">
                                        <p class="card-text text-secondary" style="line-height: 1.7;">
                                            {{ $announcement->content }}
                                        </p>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="col-12 text-center py-5">
                                <img src="{{ asset('assets/images/no-data.svg') }}" alt="No Data" class="img-fluid mb-3"
                                    style="width: 150px;">
                                <h5 class="text-muted">Belum ada pengumuman hari ini.</h5>
                            </div>
                        @endforelse
                    </div>

                    {{-- --- PAGINATION DI DALAM CARD BACKGROUND --- --}}
                    <div class="d-flex justify-content-between align-items-center mt-3 px-2">
                        <div class="text-muted small">
                            Showing {{ $announcements->firstItem() }} to {{ $announcements->lastItem() }} of
                            {{ $announcements->total() }} entries
                        </div>
                        <div>
                            {{ $announcements->links() }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Script tetap dipertahankan --}}
    <script src="../assets/js/core/libs.min.js"></script>
    <script src="../assets/js/core/external.min.js"></script>
    <script src="../assets/js/hope-ui.js" defer></script>
@endsection
