@extends('penghuni.layouts')

@section('content')
    <div class="row">
        <div class="col-sm-12">
            <div class="card shadow-sm">
                <div class="card-header d-flex justify-content-between align-items-center flex-wrap">
                    <div class="header-title">
                        <h4 class="card-title mb-0 fw-bold">Schedule: {{ $title }}</h4>
                    </div>

                    {{-- Kita buat container kosong untuk menampung Dropdown & Live Filter nantinya --}}
                    <div id="filter-container" class="d-flex align-items-center gap-2">
                        @if ($category == 'dapur' || $category == 'sergun')
                            <form action="{{ url()->current() }}" method="GET" id="manual-filter-form">
                                <select name="item" class="form-select form-select-sm"
                                    style="width: auto; min-width: 150px;" onchange="this.form.submit()">
                                    @if ($category == 'dapur')
                                        <option value="">-- Semua Alat --</option>
                                        <option value="kompor" {{ request('item') == 'kompor' ? 'selected' : '' }}>Kompor
                                        </option>
                                        <option value="rice_cooker_kecil"
                                            {{ request('item') == 'rice_cooker_kecil' ? 'selected' : '' }}>Rice Cooker Kecil
                                        </option>
                                        <option value="rice_cooker_besar"
                                            {{ request('item') == 'rice_cooker_besar' ? 'selected' : '' }}>Rice Cooker Besar
                                        </option>
                                        <option value="airfryer_halal"
                                            {{ request('item') == 'airfryer_halal' ? 'selected' : '' }}>Airfryer Halal
                                        </option>
                                        <option value="airfryer_non_halal"
                                            {{ request('item') == 'airfryer_non_halal' ? 'selected' : '' }}>Airfryer
                                            Non-Halal</option>
                                    @else
                                        <option value="">-- Semua Area --</option>
                                        <option value="area_sergun_A"
                                            {{ request('item') == 'area_sergun_A' ? 'selected' : '' }}>Area A</option>
                                        <option value="area_sergun_B"
                                            {{ request('item') == 'area_sergun_B' ? 'selected' : '' }}>Area B</option>
                                    @endif
                                </select>
                            </form>
                        @endif

                        @if (request('item') || request('search'))
                            <a href="{{ url()->current() }}" class="btn btn-secondary btn-sm">Reset</a>
                        @endif
                    </div>
                </div>

                <div class="card-body">
                    {{-- Alert Session --}}
                    @if (session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            {{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    @if (session('error'))
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <i class="bi bi-x-circle-fill me-2"></i>
                            {{ session('error') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif

                    <div class="table-responsive">
                        <table id="datatable" class="table table-bordered align-middle dataTable" data-toggle="data-table">
                            <thead class="bg-light">
                                <tr>
                                    <th class="text-center" width="5%">No.</th>
                                    <th class="text-center">Date</th>
                                    <th class="text-center">Time</th>
                                    <th class="text-center">Resident Name</th>
                                    <th class="text-center">Facility Item</th>
                                    <th class="text-center">Status</th>
                                    {{-- <th class="text-center">Action</th> --}}

                                    {{-- 1. HEADER AKSI KHUSUS ADMIN --}}
                                    @php
                                        $user = Auth::user();
                                        $role = $user->role->role_name;

                                        // Normalisasi keduanya: buang '-' dan '_' agar jadi 'mesincuci'
                                        $normalizedURL = str_replace(['-', '_'], '', strtolower($category));
                                        $normalizedAdmin = str_replace(
                                            ['-', '_'],
                                            '',
                                            strtolower($user->assigned_category ?? ''),
                                        );

                                        // Tentukan akses
                                        $canAccess = false;
                                        if ($role === 'Manager') {
                                            $canAccess = true; // Manager bebas akses apa saja
                                        } elseif ($role === 'Admin') {
                                            // Admin hanya bisa akses jika kategorinya cocok
                                            $canAccess = $normalizedURL === $normalizedAdmin;
                                        }
                                    @endphp
                                    @if ($canAccess)
                                        <th class="text-center">Admin Action</th>
                                    @endif
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($bookings->groupBy(fn($item) => $item->user_id . $item->booking_date . $item->start_time . $item->end_time . $item->item_dapur . $item->item_sergun) as $group)
                                    @php $b = $group->first(); @endphp
                                    <tr>
                                        <td class="text-center">{{ $loop->iteration }}</td>
                                        <td class="text-center">
                                            {{ \Carbon\Carbon::parse($b->booking_date)->format('d M Y') }}</td>
                                        <td class="text-center">
                                            <span class="badge bg-soft-primary text-primary fs-6">
                                                {{ substr($b->start_time, 0, 5) }} - {{ substr($b->end_time, 0, 5) }}
                                            </span>
                                        </td>
                                        <td>
                                            <div class="fw-bold">{{ $b->user->residentDetails->full_name }}</div>
                                            <small class="text-muted">Room:
                                                {{ $b->user->residentDetails->room_number }}</small>
                                        </td>
                                        <td>
                                            @if (Str::contains(strtolower($b->facility->name), 'mesin cuci'))
                                                <div class="fw-bold">Mesin Cuci</div>
                                                <small class="text-success fw-bold">No. Mesin:
                                                    @foreach ($group as $item)
                                                        M-{{ substr($item->facility->name, -1) }}{{ !$loop->last ? ',' : '' }}
                                                    @endforeach
                                                </small>
                                            @else
                                                <div class="fw-bold">{{ $b->facility->name }}</div>
                                                @if ($b->item_dapur)
                                                    <small class="text-danger fw-bold"> Alat:
                                                        {{ ucwords(str_replace('_', ' ', $b->item_dapur)) }}</small>
                                                @elseif ($b->item_sergun)
                                                    <small class="text-primary"> Area:
                                                        {{ ucwords(str_replace('_', ' ', $b->item_sergun)) }}</small>
                                                @endif
                                            @endif
                                        </td>
                                        <td class="text-center">
                                            @php
                                                $statusClass =
                                                    [
                                                        'Booked' => 'info',
                                                        'Accepted' => 'primary',
                                                        'Cancelled' => 'danger',
                                                        'Completed' => 'success',
                                                        'Ongoing' => 'warning',
                                                    ][$b->status->status_name] ?? 'secondary';
                                            @endphp
                                            <span class="badge bg-{{ $statusClass }} text-uppercase px-3 py-2">
                                                {{ $b->status->status_name }}
                                            </span>
                                        </td>

                                        {{-- 2. TOMBOL AKSI KHUSUS ADMIN --}}
                                        @if ($canAccess)
                                            <td class="text-center">
                                                @if ($b->status->status_name === 'Booked')
                                                    <div class="d-flex gap-1 justify-content-center">
                                                        {{-- Karena grouping, kita kirimkan ID pertama dari grup tersebut --}}
                                                        <form
                                                            action="{{ route('admin.booking.action', [$b->id, 'accept']) }}"
                                                            method="POST">
                                                            @csrf @method('PUT')
                                                            <button type="submit" class="btn btn-success btn-sm p-1"
                                                                title="Accept"><i class="bi bi-check-lg"></i></button>
                                                        </form>
                                                        <form
                                                            action="{{ route('admin.booking.action', [$b->id, 'cancel']) }}"
                                                            method="POST">
                                                            @csrf @method('PUT')
                                                            <button type="submit" class="btn btn-danger btn-sm p-1"
                                                                title="Reject"><i class="bi bi-x-lg"></i></button>
                                                        </form>
                                                    </div>
                                                @elseif ($b->status->status_name === 'Verifying')
                                                    {{-- TOMBOL BARU: Untuk Menyetujui Foto Kebersihan --}}
                                                    <div class="d-flex flex-column gap-1 align-items-center">
                                                        {{-- Tombol Lihat Foto (Opsional, agar Admin bisa cek dulu) --}}
                                                        @if ($b->upload_photo)
                                                            <a href="{{ asset('storage/' . $b->upload_photo) }}"
                                                                target="_blank" class="btn btn-info btn-xs mb-1 py-0 px-2"
                                                                style="font-size: 10px;">
                                                                <i class="bi bi-eye"></i> Lihat Foto
                                                            </a>
                                                        @endif
                                                        <form
                                                            action="{{ route('admin.booking.action', [$b->id, 'complete']) }}"
                                                            method="POST">
                                                            @csrf @method('PUT')
                                                            <button type="submit" class="btn btn-primary btn-sm px-2 py-1"
                                                                style="font-size: 11px;">
                                                                <i class="bi bi-stars"></i> Setujui Kebersihan
                                                            </button>
                                                        </form>
                                                    </div>
                                                @elseif ($b->status->status_name === 'Accepted' || $b->status->status_name === 'Ongoing')
                                                    <small class="text-primary fw-bold italic">Waiting for
                                                        Resident...</small>
                                                @else
                                                    <small class="text-muted italic">Processed</small>
                                                @endif
                                            </td>
                                        @endif
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="text-center py-5">
                                            <div class="d-flex flex-column align-items-center">
                                                <i class="bi bi-calendar-x text-muted mb-3" style="font-size: 3rem;"></i>
                                                <h5 class="text-muted fw-bold">Belum Ada Jadwal untuk {{ $title }}
                                                </h5>
                                                <p class="text-muted small mb-3">
                                                    @if (Str::contains(strtolower($title), 'dapur'))
                                                        Dapur saat ini kosong. Kamu bisa memasak tanpa perlu antre!
                                                    @elseif(Str::contains(strtolower($title), 'mesin cuci'))
                                                        Area mesin cuci masih tersedia. Yuk, cuci baju kamu sekarang sebelum
                                                        penuh.
                                                    @elseif(Str::contains(strtolower($title), 'theater'))
                                                        Theater Room masih sepi. Siapkan film favoritmu dan buat jadwal
                                                        nobar!
                                                    @elseif(Str::contains(strtolower($title), 'cws') || Str::contains(strtolower($title), 'working'))
                                                        Area belajar masih sangat tenang. Cocok banget buat kamu yang mau
                                                        fokus!
                                                    @else
                                                        Fasilitas ini belum ada yang memesan. Jadilah yang pertama
                                                        menggunakan fasilitas hari ini!
                                                    @endif
                                                </p>
                                                {{-- kalo manager gausa kasi akses booking --}}
                                                @if (auth()->user()->role->role_name !== 'Manager')
                                                    <a href="{{ route('booking.create', ['kategori_fasilitas' => $category]) }}"
                                                        class="btn btn-primary btn-sm rounded-pill px-4 mt-3">
                                                        Booking Sekarang
                                                    </a>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="d-flex justify-content-end p-4">
                    {{ $bookings->appends(request()->query())->links() }}
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script src="{{ asset('hopeui/js/hope-ui.js') }}" defer></script>
        <script>
            window.onload = function() {
                if ($.fn.DataTable.isDataTable('#datatable')) {
                    $('#datatable').DataTable().destroy();
                }

                const table = $('#datatable').DataTable({
                    "paging": false,
                    "lengthChange": false,
                    "info": false,
                    "searching": true,
                    "ordering": true,
                    "dom": 'rt', // Kita sembunyikan 'f' bawaan karena akan kita pindah posisinya
                    "language": {
                        "search": "<span class='ms-2 fw-bold small text-dark'>Search:</span>",
                        "searchPlaceholder": "Type to filter..."
                    }
                });

                // 1. Buat elemen search secara manual agar bisa kita atur posisinya
                const searchHtml = `
                <div class="dataTables_filter d-flex align-items-center" id="custom-search-input">
                    <label class="mb-0 d-flex align-items-center gap-3">
                        <span class="fw-bold small text-dark">Search:</span>
                        <input type="search" class="form-control form-control-sm border-primary" 
                               placeholder="Type to filter..." aria-controls="datatable" 
                               style="width: 200px; border-radius: 5px;">
                    </label>
                </div>
            `;

                // 2. Masukkan search ke dalam container di header (biar sejajar sama dropdown)
                $('#filter-container').append(searchHtml);

                // 3. Hubungkan input manual tadi dengan fungsi pencarian DataTable
                $('#custom-search-input input').on('keyup', function() {
                    table.search(this.value).draw();
                });
            };
        </script>
    @endpush
@endsection
