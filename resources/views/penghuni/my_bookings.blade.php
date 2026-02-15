@extends('layouts.app')

@section('content')
    <div class="row justify-content-center">
        <div class="col-sm-12">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                    <h4 class="fw-bold mb-0 text-primary"><i class="bi bi-calendar-check me-2"></i>Riwayat Peminjaman Saya
                    </h4>
                    <a href="{{ route('booking.create') }}" class="btn btn-primary shadow-sm">
                        <i class="bi bi-plus-lg"></i> Booking Baru
                    </a>
                </div>
                <div class="card-body">
                    {{-- Alert Pesan Sukses/Error --}}
                    @if (session('success'))
                        <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm" role="alert">
                            <i class="bi bi-check-circle-fill me-2"></i> {{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    <div class="table-responsive">
                        <table class="table table-hover align-middle border">
                            <thead class="bg-light">
                                <tr>
                                    <th class="text-center" width="5%">No.</th>
                                    <th>Fasilitas & Detail</th>
                                    <th class="text-center">Waktu Peminjaman</th>
                                    <th class="text-center">Status Sistem</th>
                                    <th class="text-center" width="30%">Aksi / Bukti Kebersihan</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($groupedBookings as $groupKey => $group)
                                    @php
                                        $b = $group->first();
                                        // Mengambil status real-time dari Accessor Model
                                        $status = $b->calculated_status;

                                        // Mapping Warna Badge
                                        $badgeColor =
                                            [
                                                'Booked' => 'info',
                                                'Accepted' => 'primary',
                                                'On Going' => 'warning',
                                                'Canceled' => 'danger',
                                                'Verifying Cleanliness' => 'dark',
                                                'Completed' => 'success',
                                                'Awaiting Cleanliness Photo' => 'secondary',
                                            ][$status] ?? 'light';
                                    @endphp
                                    <tr>
                                        <td class="text-center">{{ $loop->iteration }}</td>
                                        <td>
                                            <div class="fw-bold text-dark">{{ $b->facility->name }}</div>
                                            <div class="small text-muted mt-1">
                                                {{-- Loop item dalam grup (contoh: Mesin Cuci 1, Mesin Cuci 2) --}}
                                                @if ($b->facility->name === "Mesin Cuci" or $b->facility->name === "Dapur")
                                                    @foreach ($group as $item)
                                                        @if ($item->facilityItem)
                                                            <span class="badge bg-light text-dark border me-1">
                                                                {{ $item->facilityItem->name }}
                                                            </span>
                                                        @else
                                                            
                                                        @endif
                                                    @endforeach
                                                @endif
                                            </div>
                                        </td>
                                        <td class="text-center">
                                            <div class="small fw-bold">
                                                {{ \Carbon\Carbon::parse($b->booking_date)->format('d M Y') }}</div>
                                            <div class="badge bg-light text-dark border">
                                                {{ substr($b->start_time, 0, 5) }} - {{ substr($b->end_time, 0, 5) }}
                                            </div>
                                        </td>
                                        <td class="text-center">
                                            <span class="badge bg-{{ $badgeColor }} text-uppercase shadow-xs"
                                                style="font-size: 0.75rem;">
                                                {{ $status }}
                                            </span>
                                            @if ($b->is_early_release)
                                                <div class="mt-1"><span class="badge bg-soft-warning text-warning"
                                                        style="font-size: 0.65rem;">Selesai Lebih Awal</span></div>
                                            @endif
                                        </td>
                                        <td>
                                            <div class="d-flex flex-column align-items-center gap-2">

                                                {{-- 1. LOGIKA TOMBOL SELESAI AWAL --}}
                                                @if ($status === 'On Going' && !$b->is_early_release)
                                                    <form action="{{ route('booking.earlyRelease', $b->id) }}"
                                                        method="POST"
                                                        onsubmit="return confirm('Apakah Anda sudah selesai menggunakan fasilitas?')">
                                                        @csrf
                                                        <button type="submit"
                                                            class="btn btn-sm btn-outline-warning fw-bold">
                                                            <i class="bi bi-stop-circle me-1"></i> Selesai Sekarang
                                                        </button>
                                                    </form>

                                                    {{-- 2. LOGIKA UPLOAD FOTO (Waktu Habis ATAU Selesai Awal, dan belum ada foto) --}}
                                                @elseif(($status === 'Awaiting Cleanliness Photo' || $b->is_early_release) && !$b->photo_proof_path)
                                                    <div class="card p-2 bg-light border-dashed w-100">
                                                        <form action="{{ route('booking.upload', $b->id) }}" method="POST"
                                                            enctype="multipart/form-data">
                                                            @csrf
                                                            <label class="form-label small fw-bold mb-1">Unggah Foto
                                                                Kebersihan:</label>
                                                            <div class="input-group input-group-sm">
                                                                <input type="file" name="photo" class="form-control"
                                                                    required>
                                                                <button class="btn btn-primary"
                                                                    type="submit">Kirim</button>
                                                            </div>
                                                        </form>
                                                    </div>

                                                    {{-- 3. LOGIKA MENUNGGU VERIFIKASI ADMIN --}}
                                                @elseif($status === 'Verifying Cleanliness')
                                                    <div class="text-center">
                                                        <span class="text-muted small"><i
                                                                class="bi bi-hourglass-split me-1"></i> Menunggu Verifikasi
                                                            Kebersihan oleh Admin</span>
                                                        <br>
                                                        <a href="{{ asset('storage/' . $b->photo_proof_path) }}"
                                                            target="_blank" class="badge bg-info text-decoration-none">Lihat
                                                            Foto Anda</a>
                                                    </div>

                                                    {{-- 4. LOGIKA SELESAI (COMPLETED) --}}
                                                @elseif($status === 'Completed')
                                                    <div class="text-success text-center">
                                                        <i class="bi bi-patch-check-fill fs-4"></i>
                                                        <div class="small fw-bold">Selesai & Bersih</div>
                                                    </div>

                                                    {{-- 5. LOGIKA BOOKED (BELUM MULAI) --}}
                                                @elseif($status === 'Booked')
                                                    <small class="text-muted italic">Menunggu persetujuan admin...</small>
                                                @elseif($status === 'Accepted')
                                                    <small class="text-primary fw-bold"><i
                                                            class="bi bi-info-circle me-1"></i> Silakan datang saat jam
                                                        mulai</small>
                                                @endif

                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center py-5 text-muted">
                                            <i class="bi bi-inbox fs-1 d-block mb-3 opacity-25"></i>
                                            Belum ada riwayat peminjaman.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <style>
        .bg-soft-success {
            background-color: #e8fadf;
        }

        .bg-soft-primary {
            background-color: #e7f1ff;
        }

        .bg-soft-danger {
            background-color: #fce8e8;
        }

        .bg-soft-info {
            background-color: #e1f5fe;
        }

        .bg-soft-warning {
            background-color: #fff9db;
        }

        .border-dashed {
            border: 1px dashed #dee2e6;
        }
    </style>
@endsection
