@extends('penghuni.layouts') {{-- Sesuaikan dengan layout utama Anda --}}

@section('content')
    <div class="row">
        <div class="col-lg-15 mx-auto">
            <div class="card shadow-sm border-0">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="card-title fw-bold mb-0">Detail Keluhan</h4>
                    <a href="{{ route('pengelola.complaint') }}" class="btn btn-sm btn-secondary">
                        <i class="bi bi-arrow-left"></i> Kembali
                    </a>
                </div>

                <div class="card-body">
                    <div class="row g-4">
                        {{-- Bagian Foto --}}
                        <div class="col-md-5">
                            <label class="form-label text-muted small text-uppercase fw-bold">Bukti Foto</label>
                            @if ($complaint->photo_path)
                                <div class="border rounded overflow-hidden">
                                    <img src="{{ asset('storage/' . $complaint->photo_path) }}"
                                        class="img-fluid w-100 cursor-pointer" alt="Foto Keluhan"
                                        style="max-height: 200px; object-fit: contain;" >
                                </div>
                            @else
                                <div class="bg-light rounded d-flex align-items-center justify-content-center border"
                                    style="height: 200px;">
                                    <div class="text-center text-muted">
                                        <i class="bi bi-image" style="font-size: 2rem;"></i>
                                        <p class="mb-0 small">Tidak ada foto terlampir</p>
                                    </div>
                                </div>
                            @endif

                            <hr>

                            <div class="row">
                                <div class="col-6">
                                    <label class="form-label text-muted small text-uppercase fw-bold">Pelapor</label>
                                    <p class="mb-0 fw-medium">{{ $complaint->resident->residentDetails->full_name }}</p>
                                </div>
                                <div class="col-6">
                                    <label class="form-label text-muted small text-uppercase fw-bold">Nomor Kamar</label>
                                    <p class="mb-0 fw-medium">{{ $complaint->resident->residentDetails->room_number }}</p>
                                </div>
                            </div>

                            <div class="mt-4">
                                <label class="form-label text-muted small text-uppercase fw-bold">Tanggal Kirim</label>
                                <p class="text-muted">{{ $complaint->created_at->format('d F Y, H:i') }} WIB</p>
                            </div>
                        </div>

                        {{-- Bagian Detail Data --}}
                        <div class="col-md-7">
                            <div class="mb-3">
                                <label class="form-label text-muted small text-uppercase fw-bold">Lokasi / Item</label>
                                <h5 class="fw-bold">{{ $complaint->location_item }}</h5>
                            </div>

                            <div class="mb-3">
                                <label class="form-label text-muted small text-uppercase fw-bold">Status Saat Ini</label>
                                <div>
                                    @php
                                        $color = match ($complaint->status->status_name) {
                                            'Submitted' => 'warning',
                                            'On Progress' => 'info',
                                            'Resolved' => 'success',
                                            default => 'secondary',
                                        };
                                    @endphp
                                    <span class="badge bg-{{ $color }} px-3 py-2" style="font-size: 0.9rem;">
                                        {{ $complaint->status->status_name }}
                                    </span>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label class="form-label text-muted small text-uppercase fw-bold">Deskripsi Keluhan</label>
                                <p class="text-dark bg-light p-3 rounded border">
                                    {{ $complaint->description }}
                                </p>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Tombol Aksi Khusus Admin/Manager --}}
                @if (auth()->user()->role->role_name !== 'Resident' && auth()->user()->role->role_name !== 'Admin')
                    <div class="card-footer bg-white border-top-0 pb-4 px-4">
                        <div class="d-flex gap-2">
                            @if ($complaint->status->status_name !== 'Resolved')
                                <form action="{{ route('pengelola.complaint.updateStatus', $complaint->id) }}" method="POST"
                                    class="w-100">
                                    @csrf
                                    @method('PATCH')
                                    <input type="hidden" name="status_id" value="3"> {{-- 3 = Resolved --}}
                                    <button type="submit" class="btn btn-success w-100 py-2 fw-bold">
                                        <i class="bi bi-check-circle-fill me-2"></i> Tandai Selesai (Resolve)
                                    </button>
                                </form>
                            @endif
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection
