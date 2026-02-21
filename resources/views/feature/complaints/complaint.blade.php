@extends('layouts.app')

@section('content')
    <div class="row">
        <div class="col-sm-12">
            <div class="card shadow-sm">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <div class="header-title">
                        <h4 class="card-title mb-0 fw-bold">Complaint</h4>
                    </div>
                    
                    @if(auth()->user()->role->role_name != 'Manager')
                        <a href="{{ route('complaint.create') }}" class="btn btn-primary shadow-sm">
                            <i class="bi bi-plus-lg"></i> Add Complaint
                        </a>
                    @endif
                </div>

                <div class="card-body">

                        <div class="row align-items-center mb-4">
                            <div class="col-md-12 d-flex justify-content-end">
                                <div id="filter-complaint-container"></div>
                            </div>
                        </div>
                        <div class="table-responsive">
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
                                        @if (auth()->user()->role->role_name == 'Manager')
                                            <th class="text-center">Action</th>
                                        @endif
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($complaints as $index => $item)
                                        <tr>
                                            <td class="text-center">{{ $complaints->firstItem() + $index }}</td>
                                            <td>{{ $item->created_at->format('d-m-Y') }}</td>

                                            <td>
                                                @if (auth()->user()->role->role_name == 'Manager')
                                                    <a href="{{ route('admin.complaint.show', $item->id) }}"
                                                        class="fw-bold text-primary">
                                                        {{ $item->location_item }}
                                                    </a>
                                                @else
                                                    {{ $item->location_item }}
                                                @endif
                                            </td>

                                            <td>
                                                <small class="text-muted">{{ Str::limit($item->description, 50) }}</small>
                                            </td>
                                            <td>
                                                <div>{{ $item->resident->residentDetails->full_name ?? $item->resident->adminDetails->full_name}}</div>
                                                <small class="badge bg-soft-primary text-primary">Kamar:
                                                    {{ $item->resident->residentDetails->room_number ?? $item->resident->adminDetails->room_number}}</small>
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
                                                <span class="badge bg-{{ $color }}">{{ $item->status->status_name }}
                                                </span>
                                            </td>

                                            @if (auth()->user()->role->role_name == 'Manager')
                                                <td class="text-center">
                                                    <form action="{{ route('pengelola.updateStatus', $item->id) }}"
                                                        method="POST">
                                                        @csrf
                                                        @method('PATCH')
                                                        @if ($item->status->status_name !== 'Resolved')
                                                            <input type="hidden" name="status_id" value="3">
                                                            {{-- Asumsi 3 = Resolved --}}
                                                            <button type="submit"
                                                                class="btn btn-sm btn-success">Mark
                                                                Resolved</button>
                                                        @else
                                                            <button type="button"
                                                                class="btn btn-sm btn-success-border disabled">Done
                                                            </button>
                                                        @endif
                                                    </form>
                                                </td>
                                            @endif
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        <div class="mt-4 px-3">
                            @if ($complaints->hasPages())
                                {{ $complaints->appends(request()->query())->links() }}
                            @endif
                        </div>
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
                    "language": {
                        "emptyTable": `
                            <div class="text-center py-5">
                                <i class="bi bi-chat-left-dots text-muted mb-3" style="font-size: 3rem; opacity: 0.5;"></i>
                                <h5 class="text-muted">Belum ada keluhan yang masuk.</h5>
                                <p class="small text-muted">Klik tombol "Add Complaint" untuk membuat keluhan baru.</p>
                            </div>
                        `,
                        "zeroRecords": `
                            <div class="text-center py-5">
                                <i class="bi bi-search text-muted mb-3" style="font-size: 3rem; opacity: 0.5;"></i>
                                <h5 class="text-muted">Pencarian tidak ditemukan.</h5>
                                <p class="small text-muted">Tidak ada keluhan yang cocok dengan kata kunci Anda.</p>
                            </div>
                        `
                    }
                });

                // Buat Search Bar Manual yang sejajar
                const customSearchHtml = `
                    <div class="dataTables_filter d-flex align-items-center justify-content-end" id="custom-search-input">
                        <label class="mb-0 d-flex align-items-center gap-2">
                            <span>Search:</span>
                            <input type="search" id="inputSearchComplaint" class="form-control form-control-sm" placeholder="">
                        </label>
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
