@extends('penghuni.layouts')

@section('content')
    <div class="row">
        <div class="col-sm-12">
            <div class="card shadow-sm">
                <div class="card-header">
                    {{-- BARIS 1: Tombol Add --}}
                    <div class="mb-3">
                        <a href="{{ route('complaint.create') }}"
                            class="btn btn-primary d-inline-flex align-items-center px-4 py-2">
                            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="currentColor"
                                class="bi bi-plus-lg me-2" viewBox="0 0 16 16">
                                <path fill-rule="evenodd"
                                    d="M8 2a.5.5 0 0 1 .5.5v5h5a.5.5 0 0 1 0 1h-5v5a.5.5 0 0 1-1 0v-5h-5a.5.5 0 0 1 0-1h5v-5A.5.5 0 0 1 8 2" />
                            </svg>
                            Add Complaint
                        </a>
                    </div>

                    <div class="d-flex justify-content-between align-items-center flex-wrap">
                        <div class="header-title">
                            <h4 class="card-title mb-0 fw-bold">Complaint</h4>
                        </div>

                        {{-- WADAH LIVE FILTER (Agar Sejajar dengan Judul) --}}
                        <div id="filter-complaint-container"></div>
                    </div>
                </div>

                <div class="card-body">
                    @if ($complaints->isEmpty())
                        {{-- Tampilan kalau GAK ADA DATA --}}
                        <div class="text-center py-5">
                            <i class="bi bi-chat-left-dots text-muted mb-3" style="font-size: 3rem; opacity: 0.5;"></i>
                            <h5 class="text-muted">Belum ada keluhan yang masuk.</h5>
                            <p class="small text-muted">Klik tombol "Add Complaint" untuk membuat keluhan baru.</p>
                        </div>
                    @else
                        <div class="table-responsive">
                            {{-- ID unik agar tidak bentrok dengan script otomatis HopeUI --}}
                            <table id="tabelComplaint" class="table table-bordered align-middle">
                                <thead class="bg-light">
                                    <tr>
                                        <th class="text-center" width="5%">No</th>
                                        <th>Tanggal</th>
                                        <th>Lokasi / Item</th>
                                        <th>Deskripsi</th>
                                        <th>Pelapor (Kamar)</th>
                                        <th>Foto</th>
                                        <th class="text-center">Status</th>
                                        <td>{{ $complaints->firstItem() + $index }}</td>

                                        <td>{{ $item->created_at->format('d-m-Y') }}</td>

                                        <td>
                                            <a href="{{ route('admin.complaint.show', $item->id) }}" class="fw-bold text-primary">
                                                {{ $item->location_item }}
                                            </a>
                                        </td>

                                        <td>
                                            <small class="text-muted">{{ Str::limit($item->description, 50) }}</small>
                                        </td>

                                        <td>
                                            <div>{{ $item->resident->residentDetails->full_name }}</div>
                                            <small class="badge bg-soft-primary text-primary">Kamar:
                                                {{ $item->resident->residentDetails->room_number }}</small>
                                        </td>

                                        <td>
                                            @if ($item->photo_path)
                                                <img src="{{ asset('storage/' . $item->photo_path) }}" class="rounded"
                                                    style="width: 50px; height: 50px; object-fit: cover;">
                                            @else
                                                <span class="text-muted small">No Photo</span>
                                            @endif
                                        </td>

                                        <td class="text-center">
                                            @php
                                                $color = match ($item->status->status_name) {
                                                    'Submitted' => 'warning',
                                                    'On Progress' => 'info',
                                                    'Resolved' => 'success',
                                                    default => 'secondary',
                                                };
                                            @endphp
                                            <span
                                                class="badge bg-{{ $color }}">{{ $item->status->status_name }}</span>
                                        </td>

                                        {{-- @if ($item->)
                                            
                                        @endif --}}
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($complaints as $index => $item)
                                        <tr>
                                            <td class="text-center">{{ $complaints->firstItem() + $index }}</td>
                                            <td>{{ $item->created_at->format('d-m-Y') }}</td>
                                            <td>
                                                <a href="{{ route('admin.complaint.showAdminOnly', $item->id) }}"
                                                    class="fw-bold text-primary">
                                                    {{ $item->location_item }}
                                                </a>
                                            </td>
                                            <td>
                                                <small class="text-muted">{{ Str::limit($item->description, 50) }}</small>
                                            </td>
                                            <td>
                                                <div>{{ $item->resident->residentDetails->full_name }}</div>
                                                <small class="badge bg-soft-primary text-primary">Kamar:
                                                    {{ $item->resident->residentDetails->room_number }}</small>
                                            </td>
                                            <td>
                                                @if ($item->photo_path)
                                                    <img src="{{ asset('storage/' . $item->photo_path) }}"
                                                        style="width: 50px; height: 50px; object-fit: cover;">
                                                @else
                                                    <span class="text-muted small">No Photo</span>
                                                @endif
                                            </td>
                                            <td class="text-center">
                                                @php
                                                    $color = match ($item->status->status_name) {
                                                        'Submitted' => 'warning',
                                                        'On Progress' => 'info',
                                                        'Resolved' => 'success',
                                                        default => 'secondary',
                                                    };
                                                @endphp
                                                <span
                                                    class="badge bg-{{ $color }}">{{ $item->status->status_name }}</span>
                                            </td>
                                        </tr>
                                        {{-- Ganti bagian @empty di dalam <tbody> kamu --}}
                                    @empty
                                        {{-- <tr>
                                            <td colspan="7" class="text-center py-4 text-muted">
                                                <i class="bi bi-info-circle me-1"></i> Belum ada keluhan yang masuk.
                                            </td>
                                        </tr> --}}
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                        <div class="mt-3">
                            {{ $complaints->links() }}
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script src="{{ asset('hopeui/js/hope-ui.js') }}" defer></script>
        <script>
            window.onload = function() {
                // Inisialisasi DataTable pada ID baru
                var table = $('#tabelComplaint').DataTable({
                    "paging": false,
                    "info": false,
                    "searching": true,
                    "ordering": true,
                    "dom": 'rt', // Sembunyikan search box bawaan agar bisa kita buat manual
                });

                // Buat Search Bar Manual yang sejajar
                const customSearchHtml = `
                    <div class="d-flex align-items-center gap-2">
                        <span class="fw-bold small text-dark">Search:</span>
                        <input type="search" id="inputSearchComplaint" class="form-control form-control-sm border-primary" 
                               placeholder="Cari di halaman ini..." style="width: 250px;">
                    </div>
                `;

                // Masukkan ke container di header
                $('#filter-complaint-container').html(customSearchHtml);

                // Jalankan fungsi filter saat mengetik
                $('#inputSearchComplaint').on('keyup', function() {
                    table.search(this.value).draw();
                });
            };
        </script>
    @endpush
@endsection
