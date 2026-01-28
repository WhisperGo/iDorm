@extends('penghuni.layouts')

@section('content')
    <div class="row">
        <div class="col-sm-12">
            <div class="card shadow-sm">
                <div class="card-header d-flex justify-content-between align-items-center flex-wrap">
                    <div class="header-title">
                        <h4 class="card-title mb-0 fw-bold">Schedule: {{ $title }}</h4>
                    </div>

                    <div class="d-flex gap-2">
                        <form action="{{ url()->current() }}" method="GET" class="d-flex gap-2">
                            @if ($category == 'dapur')
                                <select name="item" class="form-select form-select-sm color-black table-bordered border-"
                                    onchange="this.form.submit()">
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
                                        {{ request('item') == 'airfryer_halal' ? 'selected' : '' }}>Airfryer Halal</option>
                                    <option value="airfryer_non_halal"
                                        {{ request('item') == 'airfryer_non_halal' ? 'selected' : '' }}>Airfryer Non-Halal
                                    </option>
                                </select>
                            @endif

                            @if ($category == 'sergun')
                                <select name="item" class="form-select form-select-sm" onchange="this.form.submit()">
                                    <option value="">-- Semua Area --</option>
                                    <option value="area_sergun_A"
                                        {{ request('item') == 'area_sergun_A' ? 'selected' : '' }}>Area A</option>
                                    <option value="area_sergun_B"
                                        {{ request('item') == 'area_sergun_B' ? 'selected' : '' }}>Area B</option>
                                </select>
                            @endif

                            <input type="text" name="search" class="form-control form-control-sm"
                                placeholder="Search name..." value="{{ request('search') }}">

                            <button type="submit" class="btn btn-primary btn-sm">Search</button>

                            @if (request('item') || request('search'))
                                <a href="{{ url()->current() }}" class="btn btn-secondary btn-sm">Reset</a>
                            @endif
                        </form>
                    </div>
                </div>

                <div class="card-body">
                    @if (session('error'))
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <i class="bi bi-exclamation-triangle-fill me-2"></i>
                            {!! session('error') !!}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif

                    <div class="table-responsive">
                        <table class="table table-bordered align-middle">
                            <thead class="bg-light">
                                <tr>
                                    <th class="text-center" width="5%">No.</th>
                                    <th class="text-center">Date</th>
                                    <th class="text-center">Time</th>
                                    <th class="text-center">Resident Name</th>
                                    <th class="text-center">Facility Item</th>
                                    <th class="text-center">Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                {{-- LOGIKA PENGGABUNGAN CERDAS --}}
                                {{-- Kita tambah item_dapur & item_sergun di kunci grouping agar alat yang berbeda tidak sengaja tergabung --}}
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
                                            {{-- LOGIKA TAMPILAN: MESIN CUCI vs ALAT LAIN --}}
                                            @if (Str::contains(strtolower($b->facility->name), 'mesin cuci'))
                                                <div class="fw-bold">Mesin Cuci</div>
                                                <small class="text-success fw-bold">
                                                    No. Mesin:
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
                                                    <small class="text-primary">
                                                        Area:
                                                        {{ ucwords(str_replace('_', ' ', $b->item_sergun)) }}</small>
                                                @elseif ($b->description)
                                                    <small class="text-muted"><i class="bi bi-info-circle"></i>
                                                        {{ $b->description }}</small>
                                                @endif
                                            @endif
                                        </td>
                                        <td class="text-center">
                                            @php
                                                $statusClass =
                                                    [
                                                        'Booked' => 'info',
                                                        'Cancelled' => 'danger',
                                                        'Completed' => 'success',
                                                        'Ongoing' => 'warning',
                                                    ][$b->status->status_name] ?? 'secondary';
                                            @endphp
                                            <span class="badge bg-{{ $statusClass }} text-uppercase px-3 py-2">
                                                {{ $b->status->status_name }}
                                            </span>
                                        </td>
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
    @endpush
@endsection
