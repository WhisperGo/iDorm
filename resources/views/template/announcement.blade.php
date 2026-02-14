@extends(auth()->user()->role->role_name === 'Resident' ? 'penghuni.layouts' : (auth()->user()->role->role_name === 'Admin' ? 'admin.layouts' : 'pengelola.layouts'))

@section('content')
    <div class="row justify-content-center">
        <div class="col-sm-12">
            {{-- --- CARD BACKGROUND UTAMA --- --}}
            <div class="card shadow-sm border-0">
                <div class="card-header d-flex justify-content-between align-items-center flex-wrap bg-white py-3">
                    <div class="header-title d-flex align-items-center gap-3">
                        <h4 class="card-title mb-0 fw-bold text-dark">Announcement List</h4>

                        {{-- TOMBOL TAMBAH: Hanya muncul untuk Admin & Pengelola --}}
                        @if (auth()->user()->role->role_name !== 'Resident')
                            <a href="{{ route('announcements.create') }}"
                                class="btn btn-primary btn-sm shadow-sm d-inline-flex align-items-center rounded-pill px-3">
                                <i class="bi bi-plus-lg me-1"></i>
                                <span>Tambah Pengumuman</span>
                            </a>
                        @endif
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

                <div class="card-body bg-light-subtle">
                    <div class="row">
                        @forelse($announcements as $announcement)
                            <div class="col-12 mb-3">
                                <div class="card border-0 shadow-sm animate__animated animate__fadeInUp">
                                    <div class="card-body">
                                        <div class="d-flex justify-content-between align-items-start mb-2">
                                            <div>
                                                <h3 class="fw-bold text-primary mb-0">{{ $announcement->title }}</h3>
                                                <div class="mt-1">
                                                    <span class="badge bg-soft-info text-info rounded-pill me-2">
                                                        <i class="bi bi-calendar3 me-1"></i>
                                                        {{ $announcement->created_at->format('d M Y') }}
                                                    </span>

                                                    {{-- Info Nama dengan Background Berwarna sesuai Role --}}
                                                    <small class="text-muted">
                                                        <i class="bi bi-person-circle me-1"></i>
                                                        Posted by
                                                        @php
                                                            $role = $announcement->author->role->role_name ?? '';

                                                            // 1. Tentukan Warna Badge
                                                            $bgClass = match ($role) {
                                                                'Admin' => 'bg-primary',
                                                                'Manager', 'Pengelola' => 'bg-success',
                                                                default => 'bg-secondary',
                                                            };

                                                            // 2. Tentukan Nama (Biar nggak dobel)
                                                            $authorName =
                                                                $announcement->author->managerDetails->full_name ??
                                                                ($announcement->author->adminDetails->full_name ??
                                                                    'Staff');
                                                        @endphp

                                                        <span
                                                            class="badge {{ $bgClass }} rounded-pill text-white fw-bold shadow-sm px-2">
                                                            {{ $authorName }}
                                                        </span>
                                                    </small>
                                                </div>
                                            </div>
                                            <span class="badge rounded-pill bg-label-primary px-3">
                                                {{ $announcement->created_at->diffForHumans() }}
                                            </span>
                                        </div>
                                        <hr class="my-3 opacity-25">
                                        <p class="card-text text-secondary mb-4" style="line-height: 1.7;">
                                            {{ $announcement->content }}
                                        </p>

                                        {{-- FITUR EDIT & HAPUS: Hanya untuk Admin/Pengelola --}}
                                        @if (auth()->user()->role->role_name !== 'Resident')
                                            <div class="d-flex justify-content-end gap-2 pt-3 border-top">
                                                <a href="{{ route('announcements.edit', $announcement->id) }}"
                                                    class="btn btn-sm btn-soft-warning rounded-pill px-3">
                                                    <i class="bi bi-pencil-square me-1"></i> Edit
                                                </a>

                                                <form action="{{ route('announcements.destroy', $announcement->id) }}"
                                                    method="POST"
                                                    onsubmit="return confirm('Apakah Anda yakin ingin menghapus pengumuman ini?')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit"
                                                        class="btn btn-sm btn-soft-danger rounded-pill px-3">
                                                        <i class="bi bi-trash3 me-1"></i> Hapus
                                                    </button>
                                                </form>
                                            </div>
                                        @endif
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

                    {{-- --- PAGINATION --- --}}
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
