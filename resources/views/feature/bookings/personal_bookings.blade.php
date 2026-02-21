@extends('layouts.app')

@section('content')
    <div class="row justify-content-center">
        <div class="col-sm-12">
            <div class="card shadow-sm border-0">
                <div class="card-header d-flex justify-content-between align-items-center flex-wrap gap-2">
                    <div class="header-title">
                        <h4 class="card-title mb-0 fw-bold">Riwayat Peminjaman Saya</h4>
                    </div>
                    <div class="d-flex align-items-center gap-3">
                        <div id="filter-container"></div>
                        <a href="{{ route('booking.create') }}" class="btn btn-primary shadow-sm text-nowrap">
                            <i class="bi bi-plus-lg"></i> Booking Baru
                        </a>
                    </div>
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
                        <table id="tabelPersonalBookings" class="table table-bordered align-middle">
                            <thead class="bg-light">
                                <tr>
                                    <th class="text-center" width="5%">No.</th>
                                    <th>Fasilitas & Detail</th>
                                    <th class="text-center">Waktu Pfeminjaman</th>
                                    <th class="text-center">Status Sistem</th>
                                    <th class="text-center" width="30%">Aksi / Bukti Kebersihan</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($groupedBookings as $groupKey => $group)
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
                                        </td>

                                        <td class="text-center">
                                            <div class="small fw-bold">
                                                {{ \Carbon\Carbon::parse($b->booking_date)->format('d M Y') }}
                                            </div>
                                            <div class="badge bg-light text-dark border">
                                                {{ substr($b->start_time, 0, 5) }} - {{ substr($b->end_time, 0, 5) }}
                                            </div>
                                        </td>

                                        <td class="text-center">
                                            <span class="badge bg-{{ $badgeColor }} text-uppercase shadow-xs"
                                                style="font-size: 0.75rem;">
                                                {{ $status }}
                                            </span>
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
                                                @elseif($status === 'Awaiting Cleanliness Photo' && !$b->photo_proof_path)
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
                                                        <span class="text-muted small">
                                                            <i class="bi bi-hourglass-split me-1"></i> Menunggu Verifikasi
                                                            Kebersihan oleh Admin
                                                        </span>
                                                        <br>
                                                        <a href="{{ asset('storage/' . $b->photo_proof_path) }}"
                                                            target="_blank" class="badge bg-info text-decoration-none">
                                                            Lihat Foto Anda
                                                        </a>
                                                    </div>

                                                    {{-- 4. LOGIKA SELESAI (COMPLETED) --}}
                                                @elseif($status === 'Completed')
                                                    <div class="text-success text-center">
                                                        <i class="bi bi-patch-check-fill fs-4"></i>
                                                        <div class="small fw-bold">Selesai & Bersih</div>
                                                    </div>

                                                    {{-- 5. LOGIKA BOOKED (BELUM MULAI) --}}
                                                @elseif($status === 'Booked')
                                                    <small class="text-muted fst-italic">Menunggu persetujuan
                                                        admin...</small>
                                                @elseif($status === 'Accepted')
                                                    <small class="text-primary fw-bold">
                                                        <i class="bi bi-info-circle me-1"></i> Silakan datang saat jam mulai
                                                    </small>
                                                @endif

                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
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

@push('scripts')
    <script>
        $(document).ready(function() {
            const table = $('#tabelPersonalBookings').DataTable({
                "paging": false,
                "info": false,
                "searching": true,
                "ordering": true,
                "dom": 'rt',
                "autoWidth": false,
                "language": {
                    "emptyTable": `
                        <div class="text-center py-5">
                            <i class="bi bi-calendar-x text-muted mb-3" style="font-size: 3rem; opacity: 0.5;"></i>
                            <h5 class="text-muted">Belum ada riwayat peminjaman.</h5>
                            <p class="small text-muted">Klik tombol "Booking Baru" untuk membuat reservasi.</p>
                        </div>
                    `,
                    "zeroRecords": `
                        <div class="text-center py-5">
                            <i class="bi bi-search text-muted mb-3" style="font-size: 3rem; opacity: 0.5;"></i>
                            <h5 class="text-muted">Pencarian tidak ditemukan.</h5>
                            <p class="small text-muted">Tidak ada riwayat peminjaman yang cocok dengan pencarian Anda.</p>
                        </div>
                    `
                },
                "columnDefs": [{
                    "orderable": false,
                    "targets": -1
                }]
            });

            // Search manual
            const searchHtml = `
            <div class="dataTables_filter d-flex align-items-center justify-content-end" id="custom-search-input">
                <label class="mb-0 d-flex align-items-center gap-2">
                    <span>Search:</span>
                    <input type="search" class="form-control form-control-sm" placeholder="">
                </label>
            </div>`;

            $('#filter-container').append(searchHtml);
            $('#custom-search-input input').on('keyup', function() {
                table.search(this.value).draw();
            });
        });
    </script>
@endpush
