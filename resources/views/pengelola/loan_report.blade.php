{{-- @extends('pengelola.layouts')

@section('content')
<div class="row justify-content-center">
    <div class="col-sm-10">
        <div class="card shadow-sm">
            <div class="card-header">
                <h4 class="card-title mb-0 fw-bold">Print Laporan Peminjaman</h4>
            </div>

            <div class="card-body">
                {{-- Baris 1: Tanggal --}}
                {{-- <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Tanggal Awal Peminjaman</label>
                        <input type="date" class="form-control" name="tanggal_awal" required>
                    </div>

                    <div class="col-md-6 mb-3">
                        <label class="form-label">Tanggal Akhir Peminjaman</label>
                        <input type="date" class="form-control" name="tanggal_akhir" required>
                    </div>
                </div> --}}

                {{-- Baris 2: Fasilitas --}}
                {{-- <div class="mb-4">
                    <label class="form-label">Fasilitas yang dipinjam</label>
                    <select class="form-select" name="fasilitas">
                        <option value="">-- Pilih fasilitas yang ingin di cetak --</option>
                        <option value="all">Seluruh Fasilitas</option>
                        <option value="dapur">Dapur</option>
                        <option value="mesin_cuci">Mesin Cuci</option>
                        <option value="Theatre">Theatre</option>
                        <option value="Sergun">Serba Guna</option>
                        <option value="cws">Co Working Space</option>
                    </select>
                </div> --}}

                {{-- Baris 3: Tombol Aksi dengan Ikon --}}
                {{-- <div class="d-flex gap-2">
                    {{-- Tombol Print --}}
                    <button type="submit" class="btn btn-primary d-inline-flex align-items-center">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-printer-fill me-2" viewBox="0 0 16 16">
                            <path d="M5 1a2 2 0 0 0-2 2v1h10V3a2 2 0 0 0-2-2zm6 8H5a1 1 0 0 0-1 1v3a1 1 0 0 0 1 1h6a1 1 0 0 0 1-1v-3a1 1 0 0 0-1-1"/>
                            <path d="M0 7a2 2 0 0 1 2-2h12a2 2 0 0 1 2 2v3a2 2 0 0 1-2 2h-1v-2a2 2 0 0 0-2-2H5a2 2 0 0 0-2 2v2H2a2 2 0 0 1-2-2zm2.5 1a.5.5 0 1 0 0-1 .5.5 0 0 0 0 1"/>
                        </svg>
                        Print
                    </button> --}}

                    {{-- Tombol Excel --}}
                    {{-- <button type="reset" class="btn btn-success d-inline-flex align-items-center">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-file-earmark-excel me-2" viewBox="0 0 16 16">
                            <path d="M5.884 6.68a.5.5 0 1 0-.768.64L7.349 10l-2.233 2.68a.5.5 0 0 0 .768.64L8 10.781l2.116 2.54a.5.5 0 0 0 .768-.641L8.651 10l2.233-2.68a.5.5 0 0 0-.768-.64L8 9.219l-2.116-2.54z"/>
                            <path d="M14 14V4.5L9.5 0H4a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h8a2 2 0 0 0 2-2M9.5 3A1.5 1.5 0 0 0 11 4.5h2V14a1 1 0 0 1-1 1H4a1 1 0 0 1-1-1V2a1 1 0 0 1 1-1h5.5z"/>
                        </svg>
                        Export Excel
                    </button> --}}

                    {{-- Tombol PDF --}}
                    {{-- <button type="button" class="btn btn-danger d-inline-flex align-items-center">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-file-pdf me-2" viewBox="0 0 16 16">
                            <path d="M4 0a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h8a2 2 0 0 0 2-2V2a2 2 0 0 0-2-2zm0 1h8a1 1 0 0 1 1 1v12a1 1 0 0 1-1 1H4a1 1 0 0 1-1-1V2a1 1 0 0 1 1-1"/>
                            <path d="M4.603 12.087a.8.8 0 0 1-.438-.42c-.195-.388-.13-.776.08-1.102.198-.307.526-.568.897-.787a7.7 7.7 0 0 1 1.482-.645 20 20 0 0 0 1.062-2.227 7.3 7.3 0 0 1-.43-1.295c-.086-.4-.119-.796-.046-1.136.075-.354.274-.672.65-.823.192-.077.4-.12.602-.077a.7.7 0 0 1 .477.365c.088.164.12.356.127.538.007.187-.012.395-.047.614-.084.51-.27 1.134-.52 1.794a11 11 0 0 0 .98 1.686 5.8 5.8 0 0 1 1.334.05c.364.065.734.195.96.465.12.144.193.32.2.518.007.192-.047.382-.138.563a1.04 1.04 0 0 1-.354.416.86.86 0 0 1-.51.138c-.331-.014-.654-.196-.933-.417a5.7 5.7 0 0 1-.911-.95 11.6 11.6 0 0 0-1.997.406 11.3 11.3 0 0 1-1.021 1.51c-.29.35-.608.655-.926.787a.8.8 0 0 1-.58.029m1.379-1.901q-.25.115-.459.238c-.328.194-.541.383-.647.547-.094.145-.096.25-.04.361q.016.032.026.044l.035-.012c.137-.056.355-.235.635-.572a8 8 0 0 0 .45-.606m1.64-1.33a13 13 0 0 1 1.01-.193 12 12 0 0 1-.51-.858 21 21 0 0 1-.5 1.05zm2.446.45q.226.244.435.41c.24.19.407.253.498.256a.1.1 0 0 0 .07-.015.3.3 0 0 0 .094-.125.44.44 0 0 0 .059-.2.1.1 0 0 0-.026-.063c-.052-.062-.2-.152-.518-.209a4 4 0 0 0-.612-.053zM8.078 5.8a7 7 0 0 0 .2-.828q.046-.282.038-.465a.6.6 0 0 0-.032-.198.5.5 0 0 0-.145.04c-.087.035-.158.106-.196.283-.04.192-.03.469.046.822q.036.167.09.346z"/>
                        </svg>
                        PDF
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection --}}

@extends('layouts.app')

@section('content')
<div class="row">
    <div class="col-sm-12">
        <div class="card shadow-sm border-0">
            <div class="card-header d-flex justify-content-between align-items-center bg-white py-3">
                <h4 class="card-title mb-0 fw-bold">Loan Report</h4>
                <button onclick="window.print()" class="btn btn-danger btn-sm rounded-pill">
                    <i class="bi bi-printer me-1"></i> Cetak PDF
                </button>
            </div>
            
            <div class="card-body">
                {{-- Filter Section --}}
                <form action="{{ route('pengelola.loan_report') }}" method="GET" class="row g-3 mb-4">
                    <div class="col-md-3">
                        <label class="form-label small fw-bold">Dari Tanggal</label>
                        <input type="date" name="start_date" class="form-control form-control-sm" value="{{ request('start_date') }}">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label small fw-bold">Sampai Tanggal</label>
                        <input type="date" name="end_date" class="form-control form-control-sm" value="{{ request('end_date') }}">
                    </div>
                    <div class="col-md-2 d-flex align-items-end">
                        <button type="submit" class="btn btn-primary btn-sm w-100">Filter</button>
                    </div>
                </form>

                {{-- Table Section --}}
                <div class="table-responsive">
                    <table class="table table-hover align-middle border">
                        <thead class="table-light">
                            <tr>
                                <th>No</th>
                                <th>Nama Penghuni</th>
                                <th>Fasilitas</th>
                                <th>Tanggal</th>
                                <th>Waktu</th>
                                <th>Status Peminjaman</th>
                                <th>Kebersihan</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($bookings as $booking)
                            <tr>
                                <td>{{ ($bookings->currentPage() - 1) * $bookings->perPage() + $loop->iteration }}</td>
                                <td>
                                    <div class="fw-bold">{{ $booking->user->residentDetails->full_name ?? $booking->user->name }}</div>
                                    <small class="text-muted">ID: {{ $booking->user->id }}</small>
                                </td>
                                <td>{{ $booking->facility->name }}</td>
                                <td>{{ \Carbon\Carbon::parse($booking->booking_date)->format('d/m/Y') }}</td>
                                <td>{{ substr($booking->start_time, 0, 5) }} - {{ substr($booking->end_time, 0, 5) }}</td>
                                <td>
                                    @php
                                        $statusColor = match($booking->status->status_name) {
                                            'Booked' => 'primary',
                                            'Ongoing' => 'info',
                                            'Completed' => 'success',
                                            'Cancelled' => 'danger',
                                            default => 'secondary'
                                        };
                                    @endphp
                                    <span class="badge bg-{{ $statusColor }}">{{ $booking->status->status_name }}</span>
                                </td>
                                <td>
                                    <span class="badge bg-{{ $booking->cleanliness_status == 'verified' ? 'success' : 'warning' }}">
                                        {{ ucfirst($booking->cleanliness_status) }}
                                    </span>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="7" class="text-center py-5">Data peminjaman tidak ditemukan.</td>
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
    </div>
</div>
@endsection