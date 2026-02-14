@extends('layouts.app')

@section('content')
    <div class="row justify-content-center">
        <div class="col-sm-10">
            <div class="card shadow-sm border-dark">
                <div class="card-header d-flex justify-content-between">
                    <h4 class="card-title fw-bold">Edit Pengumuman</h4>
                    <a href="{{ route('announcements') }}" class="btn btn-outline-secondary btn-sm rounded-pill">Kembali</a>
                </div>
                <div class="card-body">
                    <form action="{{ route('announcements.update', $announcement->id) }}" method="POST">
                        @csrf
                        @method('PUT') {{-- PENTING UNTUK PROSES UPDATE --}}

                        <div class="form-group mb-3">
                            <label class="form-label fw-bold">Judul Pengumuman</label>
                            <input type="text" class="form-control border-dark" name="title"
                                value="{{ old('title', $announcement->title) }}" required>
                        </div>

                        <div class="form-group mb-4">
                            <label class="form-label fw-bold">Isi Pengumuman</label>
                            <textarea class="form-control border-dark" name="content" rows="8" required>{{ old('content', $announcement->content) }}</textarea>
                        </div>

                        <button type="submit" class="btn btn-warning px-5 rounded-pill shadow-sm text-white">
                            <i class="bi bi-save me-1"></i> Simpan Perubahan
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
