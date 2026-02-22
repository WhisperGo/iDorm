@extends('layouts.app')

@section('content')
    @php
        $iconMap = [
            'Dapur' => ['icon' => 'bi-fire', 'label' => 'Dapur'],
            'Mesin Cuci' => ['icon' => 'bi-droplet-fill', 'label' => 'Mesin Cuci'],
            'Theater Room' => ['icon' => 'bi-film', 'label' => 'Theater'],
            'Serba Guna Hall' => ['icon' => 'bi-house-door', 'label' => 'Serba Guna'],
            'Co-Working Space' => ['icon' => 'bi-wifi', 'label' => 'Co-Working Space'],
        ];
    @endphp
    <div class="row">
        <div class="col-sm-12">

            {{-- SECTION 1: KARTU PILIHAN FASILITAS --}}
            <div class="card shadow-sm border-0 mb-4">
                <div class="card-header bg-white py-3">
                    <h5 class="fw-bold mb-0 text-primary">Pilih Fasilitas yang Ingin Dilihat</h5>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        {{-- Tombol Semua --}}
                        <div class="col-md-2">
                            {{-- Kita beri parameter facility_id = all --}}
                            <a href="{{ route('pengelola.loan_report', ['facility_id' => 'all']) }}"
                                class="text-decoration-none">
                                <div
                                    class="card h-100 border text-center p-3 {{ request('facility_id') == 'all' ? 'bg-primary text-white' : 'bg-light text-dark' }} hover-shadow">
                                    <i class="bi bi-collection-fill fs-3 mb-2"></i>
                                    <div class="small fw-bold">Seluruh Fasilitas</div>
                                </div>
                            </a>
                        </div>

                        @foreach ($facilities as $fac)
                            <div class="col-md-2">
                                <a href="{{ route('pengelola.loan_report', ['facility_id' => $fac->id]) }}"
                                    class="text-decoration-none">
                                    <div
                                        class="card h-100 border text-center p-3 {{ request('facility_id') == $fac->id ? 'bg-primary text-white' : 'bg-light text-dark' }} hover-shadow">
                                        {{-- PEMANGGILAN IKON OTOMATIS --}}
                                        <i class="bi {{ $iconMap[$fac->name]['icon'] ?? 'bi-building' }} fs-3 mb-2"></i>
                                        <div class="small fw-bold">{{ $iconMap[$fac->name]['label'] }}</div>
                                    </div>
                                </a>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>

            {{-- SECTION 2: TABEL DATA (Hanya muncul jika $bookings ada) --}}
            @if ($bookings)
                {{-- Ganti tombol Cetak PDF lama dengan grup tombol ini --}}
                <div class="d-flex gap-2 mb-3">
                    <a href="{{ route('pengelola.loan_report.excel', request()->all()) }}"
                        class="btn btn-success btn-sm rounded-pill">
                        <i class="bi bi-file-earmark-excel"></i> Excel
                    </a>
                    <a href="{{ route('pengelola.loan_report.pdf', request()->all()) }}"
                        class="btn btn-danger btn-sm rounded-pill">
                        <i class="bi bi-file-earmark-pdf"></i> PDF
                    </a>
                </div>

                <div class="card-body">
                    {{-- Form Filter Tanggal --}}
                    <form action="{{ route('pengelola.loan_report') }}" method="GET" class="row g-2 mb-4">
                        <input type="hidden" name="facility_id" value="{{ request('facility_id') }}">
                        <div class="col-md-3">
                            <input type="date" name="start_date" class="form-control form-control-sm"
                                value="{{ request('start_date') }}">
                        </div>
                        <div class="col-md-3">
                            <input type="date" name="end_date" class="form-control form-control-sm"
                                value="{{ request('end_date') }}">
                        </div>
                        <div class="col-md-2">
                            <button type="submit" class="btn btn-primary btn-sm w-100">Filter Tanggal</button>
                        </div>
                    </form>

                    <div class="table-responsive">
                        <table class="table table-hover align-middle border">
                            <thead class="table-light">
                                <tr>
                                    <th>No</th>
                                    <th>Nama Penghuni</th>
                                    <th>Fasilitas</th>
                                    <th>Tanggal</th>
                                    <th>Waktu</th>
                                    <th>Status</th>
                                    <th>Kebersihan</th>
                                </tr>
                            </thead>
                            <tbody>
                                {{-- PERBAIKAN: Gunakan $bookings, bukan $totalBookings --}}
                                @forelse($bookings as $booking)
                                    <tr>
                                        <td>{{ ($bookings->currentPage() - 1) * $bookings->perPage() + $loop->iteration }}
                                        </td>
                                        <td>
                                            <div class="fw-bold">
                                                {{ $booking->user->residentDetails->full_name ?? $booking->user->name }}
                                            </div>
                                            <small class="text-muted">ID: {{ $booking->user->id }}</small>
                                        </td>
                                        <td>{{ $booking->facility->name }}</td>
                                        <td>{{ \Carbon\Carbon::parse($booking->booking_date)->format('d/m/Y') }}</td>
                                        <td>{{ substr($booking->start_time, 0, 5) }} -
                                            {{ substr($booking->end_time, 0, 5) }}</td>
                                        <td>
                                            @php
                                                $statusColor = match ($booking->status->status_name) {
                                                    'Accepted', 'Booked' => 'primary',
                                                    'On Going' => 'info',
                                                    'Completed' => 'success',
                                                    'Canceled' => 'danger',
                                                    default => 'secondary',
                                                };
                                            @endphp
                                            <span
                                                class="badge bg-{{ $statusColor }}">{{ $booking->status->status_name }}</span>
                                        </td>
                                        <td>
                                            <span
                                                class="badge bg-{{ $booking->cleanliness_status == 'approved' ? 'success' : 'warning' }}">
                                                {{ ucfirst($booking->cleanliness_status) }}
                                            </span>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="text-center py-5">Data peminjaman tidak ditemukan.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-3">
                        {{ $bookings->links() }}
                    </div>
                </div>
        </div>
    @else
        <div class="alert alert-info border-0 shadow-sm">
            <i class="bi bi-info-circle me-2"></i> Silakan pilih salah satu kartu fasilitas di atas untuk melihat
            detail laporan.
        </div>
        @endif

    </div>
    </div>
@endsection
