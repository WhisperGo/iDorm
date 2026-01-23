@extends('penghuni.layouts')

@section('content')
    <div class="row">
        <div class="col-sm-12">
            <div class="card shadow-sm">
                <div class="card-header d-flex justify-content-between align-items-center flex-wrap">
                    <div class="header-title">
                        {{-- Judul dinamis sesuai kategori (Dapur, Mesin Cuci, dll) --}}
                        <h4 class="card-title mb-0 fw-bold">Schedule: {{ $title }}</h4>
                    </div>

                    <div class="d-flex gap-3">
                        {{-- Form Pencarian (Jika kamu ingin tetap pakai @include, pastikan filenya ada) --}}
                        <form action="{{ url()->current() }}" method="GET" class="d-flex gap-2">
                            <input type="text" name="search" class="form-control form-control-sm"
                                placeholder="Search name..." value="{{ request('search') }}">
                            <button type="submit" class="btn btn-primary btn-sm">Search</button>
                        </form>
                    </div>
                </div>

                <div class="card-body">
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
                            {{-- Bagian isi tabel di viewSchedule.blade.php --}}
                            <tbody>
                                @forelse($bookings as $index => $b)
                                    <tr>
                                        <td class="text-center">{{ $bookings->firstItem() + $index }}</td>
                                        <td class="text-center">
                                            {{ \Carbon\Carbon::parse($b->booking_date)->format('d M Y') }}</td>
                                        <td class="text-center">
                                            <span class="badge bg-soft-primary text-primary fs-6">
                                                @if ($b->slot)
                                                    {{ substr($b->slot->start_time, 0, 5) }} -
                                                    {{ substr($b->slot->end_time, 0, 5) }}
                                                @else
                                                    {{ substr($b->start_time, 0, 5) }} - {{ substr($b->end_time, 0, 5) }}
                                                @endif
                                            </span>
                                        </td>
                                        <td>
                                            <div class="fw-bold">{{ $b->user->residentDetails->full_name }}</div>
                                            <small class="text-muted">Room:
                                                {{ $b->user->residentDetails->room_number }}</small>
                                        </td>
                                        <td>
                                            <div class="fw-bold">{{ $b->facility->name }}</div>
                                            {{-- Menampilkan Detail Khusus: Judul Film / Area --}}
                                            @if ($b->description)
                                                <small class="text-primary"><i class="bi bi-info-circle"></i>
                                                    {{ $b->description }}</small>
                                            @elseif($b->item_sergun)
                                                <small class="text-primary"><i class="bi bi-geo-alt"></i> Area:
                                                    {{ ucwords(str_replace('_', ' ', $b->item_sergun)) }}</small>
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
                                    {{-- PESAN INFORMATIF SAAT DATA KOSONG --}}
                                    <tr>
                                        <td colspan="6" class="text-center py-5">
                                            <div class="d-flex flex-column align-items-center">
                                                <i class="bi bi-calendar-x text-muted mb-3" style="font-size: 3rem;"></i>
                                                <h5 class="text-muted fw-bold">Belum Ada Jadwal untuk {{ $title }}>
                                                </h5>
                                                <p class="text-muted small mb-3">
                                                    @if (Str::contains(strtolower($title), 'dapur'))
                                                        Dapur saat ini kosong. Kamu bisa memasak tanpa perlu antre!
                                                    @elseif(Str::contains(strtolower($title), 'mesin cuci'))
                                                        Area laundry masih tersedia. Yuk, cuci baju kamu sekarang sebelum
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
                                                <a href="{{ route('booking.create', ['kategori_fasilitas' => $category]) }}"
                                                    class="btn btn-primary btn-sm rounded-pill px-4">
                                                    Booking Sekarang
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                {{-- Pagination Laravel --}}
                <div class="d-flex justify-content-end p-4">
                    {{ $bookings->appends(request()->query())->links() }}
                </div>
            </div>
        </div>
    </div>

    {{-- Script tetap menggunakan asset() agar aman --}}
    @push('scripts')
        <script src="{{ asset('hopeui/js/hope-ui.js') }}" defer></script>
    @endpush
@endsection
