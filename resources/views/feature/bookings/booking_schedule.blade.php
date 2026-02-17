@extends('layouts.app')

@section('content')
    @php
        $user = Auth::user();
        // Gunakan null coalescing operator (??) untuk mencegah error jika relasi null
        $role = $user->role->role_name ?? '';

        // 1. Ambil ID Admin (gunakan safe navigation operator ?-> untuk PHP 8+)
        // Jika user bukan admin (misal Manager), ini akan null dan tidak error.
        $adminFacilityId = $user->adminDetails?->facility_id;

        if ($category === 'cws') {
            $longName = 'Co-Working Space';
        } elseif ($category === 'sergun') {
            $longName = 'Serba Guna Hall';
        } elseif ($category === 'mesin cuci') {
            $longName = 'Mesin Cuci';
        } elseif ($category === 'dapur') {
            $longName = 'Dapur';
        } elseif ($category === 'theater') {
            $longName = 'Theater Room';
        } elseif ($category === 'mesin-cuci') {
            $longName = 'Mesin Cuci';
        }

        // 2. Cari Facility berdasarkan category/slug yang ada di URL
        $currentFacility = \App\Models\Facility::where('name', $longName)->first();

        // 3. Logika Akses
        $canAccess = false;

        if ($role === 'Manager') {
            // Manager bisa akses semua
            $canAccess = true;
        } elseif ($role === 'Admin') {
            // Admin hanya bisa akses jika facility ditemukan DAN ID-nya cocok
            if ($currentFacility && $currentFacility->id == $adminFacilityId) {
                $canAccess = true;
            }
        }
    @endphp

    <div class="row">
        <div class="col-sm-12">
            <div class="card shadow-sm">
                <div class="card-header d-flex justify-content-between align-items-center flex-wrap">
                    <div class="header-title">
                        <h4 class="card-title mb-0 fw-bold">Schedule: {{ $title }}</h4>
                    </div>

                    <div id="filter-container" class="d-flex align-items-center gap-2">
                        @if ($category == 'dapur' || $category == 'sergun')
                            <form action="{{ url()->current() }}" method="GET" id="manual-filter-form">
                                <select name="item" class="form-select form-select-sm" onchange="this.form.submit()">
                                    <option value="">-- Semua Unit --</option>
                                    @php
                                        // Kita tentukan keyword pencarian yang pas buat database
                                        $dbKeyword = match ($category) {
                                            'sergun' => 'Serba Guna',
                                            'dapur' => 'Dapur',
                                            'cws' => 'Co-Working',
                                            default => str_replace('-', ' ', $category),
                                        };

                                        $filterItems = \App\Models\FacilityItem::whereHas('facility', function (
                                            $q,
                                        ) use ($dbKeyword) {
                                            $q->where('name', 'LIKE', '%' . $dbKeyword . '%');
                                        })->get();
                                    @endphp
                                    @foreach ($filterItems as $fi)
                                        <option value="{{ $fi->id }}"
                                            {{ request('item') == $fi->id ? 'selected' : '' }}>
                                            {{ $fi->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </form>
                        @endif

                        @if (request('item') || request('search'))
                            <a href="{{ url()->current() }}" class="btn btn-secondary btn-sm">Reset</a>
                        @endif
                    </div>
                </div>

                <div class="card-body">
                    {{-- Pesan Sukses/Error --}}
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

                    <div class="table-responsive">
                        <table id="schedule-final-table" class="table table-bordered align-middle">
                            <thead class="bg-light">
                                <tr>
                                    <th class="text-center" width="5%">No.</th>
                                    <th class="text-center">Date</th>
                                    <th class="text-center">Time</th>
                                    <th class="text-center">Resident Name</th>
                                    <th class="text-center">Facility Item</th>
                                    <th class="text-center">Booking Status</th>
                                    @if ($canAccess)
                                        <th class="text-center">Booking Action</th>
                                        {{-- 3 KOLOM BARU (CLEANLINESS) --}}
                                        {{-- <th class="text-center">Cleanliness Status</th> --}}
                                        <th class="text-center" width="10%">Facility Photos</th>
                                        <th class="text-center">Cleanliness Action</th>
                                    @endif
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($bookings->groupBy(fn($item) => $item->user_id . $item->facility_id . $item->booking_date . $item->start_time . $item->end_time) as $group)
                                    @php $b = $group->first(); @endphp
                                    <tr>
                                        <td class="text-center">{{ $loop->iteration }}</td>

                                        <td class="text-center">
                                            {{ \Carbon\Carbon::parse($b->booking_date)->format('d M Y') }}
                                        </td>

                                        <td class="text-center">
                                            <span class="badge bg-soft-primary text-primary fs-6">
                                                {{ substr($b->start_time, 0, 5) }} - {{ substr($b->end_time, 0, 5) }}
                                            </span>
                                        </td>

                                        <td>
                                            <div class="fw-bold">
                                                {{ $b->user->residentDetails->full_name ?? ($b->user->name ?? 'User Tidak Ditemukan') }}
                                            </div>
                                            <small class="text-muted">Room:
                                                {{ $b->user->residentDetails->room_number ?? '-' }}
                                            </small>
                                        </td>

                                        <td>
                                            @php
                                                $facilityName = strtolower($b->facility->name);
                                            @endphp

                                            @if (str_contains($facilityName, 'mesin cuci'))
                                                <div class="fw-bold">Mesin Cuci</div>
                                                <small class="text-success fw-bold">
                                                    No: @foreach ($group as $g)
                                                        M-{{ substr($g->facilityItem->name, -1) }}{{ !$loop->last ? ',' : '' }}
                                                    @endforeach
                                                </small>
                                            @elseif (str_contains($facilityName, 'dapur'))
                                                <div class="fw-bold">Dapur</div>
                                                <small class="text-danger fw-bold">
                                                    Alat: {{ $b->facilityItem->name ?? 'Umum' }}
                                                </small>
                                            @elseif (str_contains($facilityName, 'serba guna') || str_contains($facilityName, 'sergun'))
                                                <div class="fw-bold">Serba Guna</div>
                                                <small class="text-primary fw-bold">
                                                    Area: {{ $b->facilityItem->name ?? 'Umum' }}
                                                </small>
                                            @else
                                                <div class="fw-bold">{{ $b->facility->name }}</div>
                                            @endif
                                        </td>
                                        
                                        <td class="text-center">
                                            <span
                                                class="badge bg-info text-uppercase px-3 py-2">{{ $b->status->status_name }}</span>
                                        </td>

                                        {{-- KOLOM KHUSUS ADMIN / MANAGER --}}
                                        @if ($canAccess)
                                            {{-- 1. Booking Action (Approve/Reject Reservasi) --}}
                                            <td class="text-center">
                                                @if ($b->status->status_name === 'Booked')
                                                    <div class="d-flex justify-content-center align-items-center gap-2">
                                                        {{-- Tombol Accept Booking --}}
                                                        <form
                                                            action="{{ route('admin.booking.action', [$b->id, 'approve']) }}"
                                                            method="POST">
                                                            @csrf @method('PUT')
                                                            <button type="submit"
                                                                class="btn btn-success btn-sm p-1 shadow-sm"
                                                                title="Terima Booking">
                                                                <i class="bi bi-check-lg"></i>
                                                            </button>
                                                        </form>

                                                        {{-- Tombol Reject Booking --}}
                                                        <form
                                                            action="{{ route('admin.booking.action', [$b->id, 'reject']) }}"
                                                            method="POST"
                                                            onsubmit="return confirm('Apakah Anda yakin ingin menolak booking ini?');">
                                                            @csrf @method('PUT')
                                                            <button type="submit"
                                                                class="btn btn-danger btn-sm p-1 shadow-sm"
                                                                title="Tolak Booking">
                                                                <i class="bi bi-x-lg"></i>
                                                            </button>
                                                        </form>
                                                    </div>
                                                @else
                                                    {{-- Tampilan Status Booking Jika Bukan 'Booked' --}}
                                                    @if ($b->status->status_name === 'Approved')
                                                        <i class="bi bi-check-circle-fill text-success"
                                                            title="Booking Approved"></i>
                                                    @elseif($b->status->status_name === 'Rejected')
                                                        <i class="bi bi-x-circle-fill text-danger"
                                                            title="Booking Rejected"></i>
                                                    @else
                                                        -
                                                    @endif
                                                @endif
                                            </td>

                                            {{-- KOLOM: Cleanliness Status
                                            <td class="text-center">
                                                @php
                                                    $statusName = $b->status->status_name;
                                                @endphp --}}

                                                {{-- @if ($statusName === 'Completed') --}}
                                                    {{-- ID 6 --}}
                                                    {{-- <span class="badge bg-success"><i
                                                            class="bi bi-check-circle me-1"></i>Selesai</span>
                                                @elseif($statusName === 'Verifying Cleanliness') --}}
                                                    {{-- ID 5 --}}
                                                    {{-- <span class="badge bg-warning text-dark">
                                                        <i class="bi bi-search me-1"></i>Verifikasi Admin
                                                    </span>
                                                @elseif($statusName === 'Awaiting Cleanliness Photo') --}}
                                                    {{-- ID 7 --}}
                                                    {{-- <span class="badge bg-secondary">Menunggu Foto User</span>
                                                @else
                                                    <span class="text-muted small">-</span>
                                                @endif
                                            </td> --}}

                                            {{-- KOLOM: Facility Photos (Hanya tampilkan gambar) --}}
                                            <td class="text-center">
                                                @if ($b->photo_proof_path)
                                                    <a href="{{ asset('storage/' . $b->photo_proof_path) }}"
                                                        target="_blank">
                                                        <img src="{{ asset('storage/' . $b->photo_proof_path) }}"
                                                            alt="Bukti" class="img-thumbnail"
                                                            style="height: 50px; width: 50px; object-fit: cover;">
                                                    </a>
                                                @else
                                                    <span class="text-muted small fst-italic">Belum upload</span>
                                                @endif
                                            </td>

                                            {{-- KOLOM: Cleanliness Action (Tombol Admin) --}}
                                            <td class="text-center">
                                                {{-- Tampilkan tombol HANYA jika status = Verifying Cleanliness (ID 5) --}}
                                                @if ($b->status->status_name === 'Verifying Cleanliness' && $b->photo_proof_path)
                                                    <div class="d-flex justify-content-center gap-2">

                                                        {{-- Tombol APPROVE (Ubah ke ID 6) --}}
                                                        <form action="{{ route('booking.cleanliness.update', $b->id) }}"
                                                            method="POST">
                                                            @csrf @method('PUT')
                                                            <input type="hidden" name="action" value="approved">
                                                            <button type="submit" class="btn btn-success btn-sm"
                                                                title="Approve & Selesaikan">
                                                                <i class="bi bi-check-lg"></i>
                                                            </button>
                                                        </form>

                                                        {{-- Tombol REJECT (Kembalikan ke ID 7) --}}
                                                        <form action="{{ route('booking.cleanliness.update', $b->id) }}"
                                                            method="POST"
                                                            onsubmit="return confirm('Tolak bukti ini? Status akan kembali ke Awaiting Photo.');">
                                                            @csrf @method('PUT')
                                                            <input type="hidden" name="action" value="rejected">
                                                            <button type="submit" class="btn btn-danger btn-sm"
                                                                title="Tolak & Minta Upload Ulang">
                                                                <i class="bi bi-x-lg"></i>
                                                            </button>
                                                        </form>

                                                    </div>
                                                @elseif($b->status->status_name === 'Completed')
                                                    <i class="bi bi-patch-check-fill text-primary fs-4"
                                                        title="Verified & Completed"></i>
                                                @else
                                                    <small class="text-muted">-</small>
                                                @endif
                                            </td>
                                        @endif
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                        <div class="d-flex justify-content-between align-items-center mt-3 px-3">
                            <div>
                                {{ $bookings->appends(request()->query())->links() }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        $(document).ready(function() {
            const table = $('#schedule-final-table').DataTable({
                "paging": false,
                "info": false,
                "searching": true,
                "ordering": true,
                "dom": 'rt',
                "autoWidth": false,
                "language": {
                    "emptyTable": "Belum ada jadwal yang terdaftar."
                },
                // Tambahkan ini agar kolom action tidak bisa disortir (opsional)
                "columnDefs": [{
                    "orderable": false,
                    "targets": -1
                }]
            });

            // Search manual
            const searchHtml = `
            <div class="dataTables_filter d-flex align-items-center" id="custom-search-input">
                <label class="mb-0 d-flex align-items-center gap-2">
                    <span class="fw-bold small text-dark">Search:</span>
                    <input type="search" class="form-control form-control-sm border-primary" placeholder="Type to filter...">
                </label>
            </div>`;

            $('#filter-container').append(searchHtml);
            $('#custom-search-input input').on('keyup', function() {
                table.search(this.value).draw();
            });
        });
    </script>
@endpush
