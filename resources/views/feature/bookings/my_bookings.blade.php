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
                    @if (session('error'))
                        <div class="alert alert-danger alert-dismissible fade show border-0 shadow-sm" role="alert">
                            <i class="bi bi-exclamation-circle-fill me-2"></i> {{ session('error') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    <div class="table-responsive">
                        <table class="table table-hover align-middle border">
                            <thead class="bg-light">
                                <tr>
                                    <th class="text-center" width="5%">No.</th>
                                    <th width="25%">Fasilitas & Detail</th>
                                    <th class="text-center">Waktu Peminjaman</th>
                                    <th class="text-center">Status Booking</th>
                                    <th class="text-center" width="35%">Aksi / Status Kebersihan</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($groupedBookings as $groupKey => $group)
                                    @php
                                        $b = $group->first();
                                        // Ambil status reservasi (Booked, Approved, Completed, dll)
                                        $bookingStatus = $b->status->status_name;

                                        // Ambil status kebersihan (pending, approved, rejected)
                                        $cleanlinessStatus = $b->cleanliness_status;
                                        $hasPhoto = !empty($b->photo_proof_path);

                                        // Mapping Warna Badge Status Booking
                                        $badgeColor = match ($bookingStatus) {
                                            'Booked' => 'info',
                                            'Approved' => 'primary',
                                            'On Going' => 'warning',
                                            'Completed' => 'success',
                                            'Rejected', 'Canceled' => 'danger',
                                            default => 'secondary',
                                        };
                                    @endphp
                                    <tr>
                                        <td class="text-center">{{ $loop->iteration }}</td>

                                        {{-- KOLOM 1: FASILITAS --}}
                                        <td>
                                            <div class="fw-bold text-dark">{{ $b->facility->name }}</div>
                                            <div class="small text-muted mt-1">
                                                @if (in_array($b->facility->name, ['Mesin Cuci', 'Dapur', 'Serba Guna Hall']))
                                                    @foreach ($group as $item)
                                                        @if ($item->facilityItem)
                                                            <span class="badge bg-light text-dark border me-1">
                                                                {{ $item->facilityItem->name }}
                                                            </span>
                                                        @endif
                                                    @endforeach
                                                @endif
                                            </div>
                                            <div class="small text-muted mt-1">
                                                ID: #{{ $b->id }}
                                            </div>
                                        </td>

                                        {{-- KOLOM 2: WAKTU --}}
                                        <td class="text-center">
                                            <div class="fw-bold">
                                                {{ \Carbon\Carbon::parse($b->booking_date)->format('d M Y') }}
                                            </div>
                                            <div class="badge bg-light text-dark border">
                                                {{ substr($b->start_time, 0, 5) }} - {{ substr($b->end_time, 0, 5) }}
                                            </div>
                                        </td>

                                        {{-- KOLOM 3: STATUS BOOKING --}}
                                        <td class="text-center">
                                            <span
                                                class="badge bg-soft-{{ $badgeColor }} text-{{ $badgeColor }} text-uppercase shadow-xs px-3 py-2">
                                                {{ $bookingStatus }}
                                            </span>

                                            {{-- Jika Approved/On Going tapi belum waktu mulai --}}
                                            @if ($bookingStatus === 'Approved')
                                                <div class="mt-1 small text-muted fst-italic">Silakan datang sesuai jadwal
                                                </div>
                                            @endif
                                        </td>

                                        {{-- KOLOM 4: AKSI / KEBERSIHAN (LOGIKA UTAMA) --}}
                                        <td>
                                            <div class="d-flex flex-column align-items-center gap-2 p-2">

                                                {{-- KASUS 1: SEDANG BERJALAN (ON GOING) --}}
                                                @if ($bookingStatus === 'On Going')
                                                    @if (!$b->is_early_release)
                                                        <form action="{{ route('booking.earlyRelease', $b->id) }}"
                                                            method="POST"
                                                            onsubmit="return confirm('Apakah Anda sudah selesai menggunakan fasilitas?')">
                                                            @csrf
                                                            <button type="submit"
                                                                class="btn btn-sm btn-warning text-dark fw-bold w-100">
                                                                <i class="bi bi-stop-circle me-1"></i> Selesai Sekarang
                                                            </button>
                                                            <small class="d-block text-center text-muted mt-1"
                                                                style="font-size: 0.7rem">
                                                                Klik jika selesai sebelum waktu habis
                                                            </small>
                                                        </form>
                                                    @else
                                                        <span class="badge bg-secondary">Menunggu Waktu Habis</span>
                                                    @endif

                                                    {{-- KASUS 2: SELESAI (COMPLETED) ATAU RELEASE AWAL --}}
                                                @elseif ($bookingStatus === 'Completed' || ($bookingStatus === 'On Going' && $b->is_early_release))
                                                    {{-- A. JIKA ADMIN MENOLAK FOTO (REJECTED) --}}
                                                    @if ($cleanlinessStatus === 'rejected')
                                                        <div class="alert alert-danger p-2 mb-2 w-100 text-center lh-sm"
                                                            style="font-size: 0.85rem;">
                                                            <strong><i class="bi bi-x-circle"></i> Foto
                                                                Ditolak!</strong><br>
                                                            Bukti kebersihan tidak valid/kurang jelas. Silakan upload ulang.
                                                        </div>
                                                        {{-- Form Upload Ulang --}}
                                                        @include('feature.bookings.partials.upload_form', [
                                                            'booking_id' => $b->id,
                                                        ])

                                                        {{-- B. JIKA SUDAH ADA FOTO & STATUS PENDING --}}
                                                    @elseif ($hasPhoto && $cleanlinessStatus === 'pending')
                                                        <div class="text-center w-100 border rounded p-2 bg-soft-warning">
                                                            <span class="text-warning fw-bold small">
                                                                <i class="bi bi-hourglass-split"></i> Menunggu Verifikasi
                                                            </span>
                                                            <div class="mt-1">
                                                                <a href="{{ asset('storage/' . $b->photo_proof_path) }}"
                                                                    target="_blank"
                                                                    class="btn btn-xs btn-link p-0 text-decoration-none small">
                                                                    Lihat Foto Saya
                                                                </a>
                                                            </div>
                                                        </div>

                                                        {{-- C. JIKA SUDAH APPROVED (SELESAI TOTAL) --}}
                                                    @elseif ($cleanlinessStatus === 'approved')
                                                        <div class="text-center w-100 border rounded p-2 bg-soft-success">
                                                            <div class="text-success fw-bold">
                                                                <i class="bi bi-patch-check-fill fs-5"></i>
                                                            </div>
                                                            <div class="small fw-bold text-success">Selesai & Bersih</div>
                                                        </div>

                                                        {{-- D. JIKA BELUM ADA FOTO SAMA SEKALI --}}
                                                    @elseif (!$hasPhoto)
                                                        <div class="card p-2 bg-light border-dashed w-100">
                                                            <div class="text-center mb-1 text-danger small fw-bold">
                                                                Wajib Upload Bukti Kebersihan
                                                            </div>
                                                            {{-- Gunakan partial atau form langsung --}}
                                                            <form action="{{ route('booking.upload', $b->id) }}"
                                                                method="POST" enctype="multipart/form-data">
                                                                @csrf
                                                                <div class="input-group input-group-sm">
                                                                    <input type="file" name="photo"
                                                                        class="form-control" required accept="image/*">
                                                                    <button class="btn btn-primary" type="submit">
                                                                        <i class="bi bi-upload"></i>
                                                                    </button>
                                                                </div>
                                                            </form>
                                                        </div>
                                                    @endif

                                                    {{-- KASUS 3: BOOKING DITOLAK / BATAL --}}
                                                @elseif ($bookingStatus === 'Rejected')
                                                    <span class="text-danger small">Booking ditolak Admin</span>
                                                @elseif ($bookingStatus === 'Canceled')
                                                    <span class="text-muted small">Booking dibatalkan</span>
                                                @elseif ($bookingStatus === 'Booked')
                                                    <span class="text-muted small fst-italic">Menunggu Konfirmasi
                                                        Admin</span>
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

    {{-- CSS Tambahan --}}
    <style>
        .bg-soft-success {
            background-color: #d1e7dd;
            color: #0f5132;
        }

        .bg-soft-primary {
            background-color: #cfe2ff;
            color: #084298;
        }

        .bg-soft-danger {
            background-color: #f8d7da;
            color: #842029;
        }

        .bg-soft-info {
            background-color: #cff4fc;
            color: #055160;
        }

        .bg-soft-warning {
            background-color: #fff3cd;
            color: #664d03;
        }

        .border-dashed {
            border: 1px dashed #6c757d;
        }

        .shadow-xs {
            box-shadow: 0 1px 2px rgba(0, 0, 0, 0.05);
        }
    </style>
@endsection
