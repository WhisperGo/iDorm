@extends('layouts.app')

@section('content')
    <div class="row justify-content-center">
        <div class="col-sm-12 col-lg-10">
            <div class="card shadow-sm border-dark">
                <div class="card-header d-flex justify-content-between">
                    <div class="header-title">
                        <h4 class="card-title fw-bold">Buat Pengumuman Baru</h4>
                    </div>
                    <a href="{{ route('announcements') }}" class="btn btn-outline-secondary btn-sm rounded-pill">
                        <i class="bi bi-arrow-left me-1"></i> Kembali
                    </a>
                </div>
                <div class="card-body">
                    <p class="text-muted small">Isi formulir di bawah ini untuk menyebarkan informasi kepada seluruh
                        penghuni iDorm.</p>
                    <hr class="border-dark opacity-25">

                    <form action="{{ route('announcements.store') }}" method="POST">
                        @csrf

                        {{-- Judul Pengumuman --}}
                        <div class="form-group mb-3">
                            <label class="form-label fw-bold" for="title">Judul Pengumuman <span
                                    class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('title') is-invalid @enderror"
                                id="title" name="title" value="{{ old('title') }}"
                                placeholder="Contoh: Jadwal Pembersihan Dapur Mingguan" required>
                            @error('title')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Isi Pengumuman --}}
                        <div class="form-group mb-4">
                            <label class="form-label fw-bold" for="content">Isi Pengumuman <span
                                    class="text-danger">*</span></label>
                            <textarea class="form-control @error('content') is-invalid @enderror" id="content" name="content"
                                rows="8" placeholder="Tuliskan detail pengumuman di sini..." required>{{ old('content') }}</textarea>
                            @error('content')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="text-muted">Gunakan bahasa yang jelas dan mudah dimengerti oleh seluruh
                                penghuni.</small>
                        </div>

                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary px-5 rounded-pill shadow-sm">
                                <i class="bi bi-megaphone-fill me-1"></i> Publikasikan Sekarang
                            </button>
                            <button type="reset" class="btn btn-soft-danger px-4 rounded-pill">
                                Batalkan
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection