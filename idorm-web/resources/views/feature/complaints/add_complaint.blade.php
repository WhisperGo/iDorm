@extends('layouts.app')

@section('content')
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card shadow-sm">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="card-title mb-0 fw-bold">Buat Keluhan Baru</h4>
                    <a href="{{ route('admin.complaint') }}" class="btn btn-sm btn-secondary">Kembali</a>
                </div>

                <div class="card-body">
                    {{-- Box Info Identitas Pelapor (Read Only) --}}
                    <div class="alert alert-soft-primary d-flex align-items-center mb-4" role="alert">
                        <svg class="bi shrink-0 me-2" width="24" height="24" fill="currentColor" viewBox="0 0 16 16">
                            <path
                                d="M8 8a3 3 0 1 0 0-6 3 3 0 0 0 0 6m2-3a2 2 0 1 1-4 0 2 2 0 0 1 4 0m4 8c0 1-1 1-1 1H3s-1 0-1-1 1-4 6-4 6 3 6 4m-1-.004c-.001-.246-.154-.986-.832-1.664C11.516 10.68 10.289 10 8 10s-3.516.68-4.168 1.332c-.678.678-.83 1.418-.832 1.664z" />
                        </svg>
                        <div>
                            <strong>Identitas Pelapor:</strong>
                            {{ Auth::user()->residentDetails->full_name ?? Auth::user()->adminDetails->full_name }}
                            (Kamar:
                            {{ Auth::user()->residentDetails->room_number ?? Auth::user()->adminDetails->room_number }})
                        </div>
                    </div>

                    {{-- Alert Sukses/Error --}}
                    @if ($errors->any())
                        <svg xmlns="http://www.w3.org/2000/svg" style="display: none;">
                            <symbol id="exclamation-triangle-fill" fill="currentColor" viewBox="0 0 16 16">
                                <path
                                    d="M8.982 1.566a1.13 1.13 0 0 0-1.96 0L.165 13.233c-.457.778.091 1.767.98 1.767h13.713c.889 0 1.438-.99.98-1.767L8.982 1.566zM8 5c.535 0 .954.462.9.995l-.35 3.507a.552.552 0 0 1-1.1 0L7.1 5.995A.905.905 0 0 1 8 5zm.002 6a1 1 0 1 1 0 2 1 1 0 0 1 0-2z" />
                            </symbol>
                        </svg>
                        <div class="alert alert-danger d-flex align-items-center mb-4" role="alert">
                            <svg class="flex-shrink-0 bi me-2 icon-24" width="24" height="24">
                                <use xlink:href="#exclamation-triangle-fill" />
                            </svg>
                            <div>
                                <ul class="mb-0 list-unstyled">
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        </div>
                    @endif

                    @if (session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            {{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif

                    @if (session('error'))
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            {{ session('error') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif

                    <form action="{{ route('complaint.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf

                        {{-- Lokasi / Item yang Rusak --}}
                        <div class="form-group mb-3">
                            <label class="form-label fw-bold">Lokasi atau Item yang Bermasalah</label>
                            <input type="text" name="location_item"
                                class="form-control @error('location_item') is-invalid @enderror"
                                placeholder="Contoh: AC Kamar A101, Keran Dapur, Lampu CWS"
                                value="{{ old('location_item') }}" required>
                            @error('location_item')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Deskripsi Keluhan --}}
                        <div class="form-group mb-3">
                            <label class="form-label fw-bold">Deskripsi Kerusakan / Keluhan</label>
                            <textarea name="description" class="form-control @error('description') is-invalid @enderror" rows="5"
                                placeholder="Jelaskan secara detail kendala yang dialami..." required>{{ old('description') }}</textarea>
                            @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Upload Foto Bukti --}}
                        <div class="form-group mb-4">
                            <label class="form-label fw-bold">Foto Bukti (Opsional)</label>
                            <input type="file" name="photo" class="form-control @error('photo') is-invalid @enderror"
                                accept="image/*">
                            <small class="text-muted">Format: JPG, PNG, JPEG. Maksimal 2MB.</small>
                            @error('photo')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="d-grid">
                            <button type="submit" class="btn btn-primary py-2 fw-bold">Kirim Keluhan</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
