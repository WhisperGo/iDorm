@extends('penghuni.layouts')

@section('content')
    @php
        $user = Auth::user();
        $role = $user->role->role_name;
        $normalizedURL = str_replace(['-', '_'], '', strtolower($category));
        $normalizedAdmin = str_replace(['-', '_'], '', strtolower($user->assigned_category ?? ''));

        // Cek akses sekali saja
        $canAccess = $role === 'Manager' || ($role === 'Admin' && $normalizedURL === $normalizedAdmin);
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
                    <div class="table-responsive">
                        {{-- PERBAIKAN: ID BARU & TANPA CLASS dataTable --}}
                        {{-- Hapus baris ini kalau sudah muncul datanya --}}
                        {{-- <div class="alert alert-info">
                            DEBUG: Jumlah data ditemukan = {{ $bookings->count() }}
                            (Kategori: {{ $category }}, Item Filter: {{ request('item') ?? 'Kosong' }})
                        </div> --}}
                        <table id="schedule-final-table" class="table table-bordered align-middle">
                            <thead class="bg-light">
                                <tr>
                                    <th class="text-center" width="5%">No.</th>
                                    <th class="text-center">Date</th>
                                    <th class="text-center">Time</th>
                                    <th class="text-center">Resident Name</th>
                                    <th class="text-center">Facility Item</th>
                                    <th class="text-center">Status</th>
                                    @if ($canAccess)
                                        <th class="text-center">Admin Action</th>
                                    @endif
                                </tr>
                            </thead>
                            <tbody>
                                {{-- PERBAIKAN: Gunakan foreach biasa --}}
                                @foreach ($bookings->groupBy(fn($item) => $item->user_id . $item->booking_date . $item->start_time . $item->end_time) as $group)
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
                                            {{-- Pakai ?-> untuk mencegah crash jika data null --}}
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
                                                {{-- Kasus 1: Mesin Cuci (Tampilkan No. Mesin) --}}
                                                <div class="fw-bold">Mesin Cuci</div>
                                                <small class="text-success fw-bold">
                                                    No: @foreach ($group as $g)
                                                        M-{{ substr($g->facilityItem->name, -1) }}{{ !$loop->last ? ',' : '' }}
                                                    @endforeach
                                                </small>
                                            @elseif (str_contains($facilityName, 'dapur'))
                                                {{-- Kasus 2: Dapur (Tampilkan Alat) --}}
                                                <div class="fw-bold">Dapur</div>
                                                <small class="text-danger fw-bold">
                                                    Alat: {{ $b->facilityItem->name ?? 'Umum' }}
                                                </small>
                                            @elseif (str_contains($facilityName, 'serba guna') || str_contains($facilityName, 'sergun'))
                                                {{-- Kasus 3: Serba Guna (Tampilkan Area) --}}
                                                <div class="fw-bold">Serba Guna</div>
                                                <small class="text-primary fw-bold">
                                                    Area: {{ $b->facilityItem->name ?? 'Umum' }}
                                                </small>
                                            @else
                                                {{-- Kasus 4: Sisanya (Theater, CWS, dll) --}}
                                                <div class="fw-bold">{{ $b->facility->name }}</div>
                                                {{-- Bagian bawahnya dikosongkan sesuai request --}}
                                            @endif
                                        </td>
                                        <td class="text-center">
                                            <span
                                                class="badge bg-info text-uppercase px-3 py-2">{{ $b->status->status_name }}</span>
                                        </td>
                                        @if ($canAccess)
                                            <td class="text-center">
                                                @if ($b->status->status_name === 'Booked')
                                                    <form action="{{ route('admin.booking.action', [$b->id, 'accept']) }}"
                                                        method="POST">
                                                        @csrf @method('PUT')
                                                        <button type="submit" class="btn btn-success btn-sm p-1"><i
                                                                class="bi bi-check-lg"></i></button>
                                                    </form>
                                                @else
                                                    <small class="text-muted italic">Processed</small>
                                                @endif
                                            </td>
                                        @endif
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        $(document).ready(function() {
            // PERBAIKAN: Inisialisasi pada ID unik
            const table = $('#schedule-final-table').DataTable({
                "paging": false,
                "info": false,
                "searching": true,
                "ordering": true,
                "dom": 'rt',
                "autoWidth": false,
                "language": {
                    "emptyTable": "Belum ada jadwal yang terdaftar."
                }
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
