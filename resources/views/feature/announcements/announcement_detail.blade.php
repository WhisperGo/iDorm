@extends('layouts.app')

@section('content')
    <div class="row justify-content-center">
        <div class="col-lg-9">
            {{-- Tombol Kembali --}}
            <div class="mb-3">
                <a href="{{ url()->previous() }}" class="btn btn-sm btn-light border shadow-sm">
                    <i class="bi bi-arrow-left me-1"></i> Kembali
                </a>
            </div>

            <div class="card shadow-sm border-0">
                <div class="card-body p-4 p-md-5">
                    {{-- Judul Pengumuman --}}
                    <h1 class="fw-bold mb-3 text-dark">{{ $announcement->title }}</h1>

                    {{-- Metadata (Penulis & Tanggal) --}}
                    <div class="d-flex align-items-center mb-4 pb-3 border-bottom">
                        <div class="me-3">
                            {{-- Inisial Penulis atau Avatar --}}
                            <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center"
                                style="width: 45px; height: 45px;">
                                <i class="bi bi-person-fill"></i>
                            </div>
                        </div>
                        <div>
                            <h6 class="mb-0 fw-bold">
                                {{ $announcement->author->managerDetails->full_name ?? 'Administrator' }}</h6>
                            <small class="text-muted">
                                <i class="bi bi-calendar-event me-1"></i> {{ $announcement->created_at->format('d F Y') }}
                                <span class="mx-2">|</span>
                                <i class="bi bi-clock me-1"></i> {{ $announcement->created_at->format('H:i') }} WIB
                            </small>
                        </div>
                    </div>

                    {{-- Konten Pengumuman --}}
                    <div class="announcement-content text-dark lh-base" style="font-size: 1.1rem;">
                        {!! nl2br(e($announcement->content)) !!}
                    </div>
                </div>

                {{-- Footer Card (Opsional: Info iDorm) --}}
                <div class="card-footer bg-light border-0 py-3 text-center">
                    <small class="text-muted">Pengumuman resmi iDorm Management System</small>
                </div>
            </div>
        </div>
    </div>

    <style>
        .announcement-content {
            text-align: justify;
        }
    </style>
@endsection
