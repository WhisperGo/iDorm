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
                            <h4 class="card-title mb-0 fw-bold">Keluhan</h4>
                        </div>

                        {{-- Form Search Aktif --}}
                        <form action="{{ route('pengelola.complaint') }}" method="GET"
                            class="d-flex align-items-center gap-2">
                            <span class="text-secondary fw-medium">Search:</span>
                            <input type="text" name="search" class="form-control" style="width: 250px;"
                                placeholder="Cari lokasi atau nama..." value="{{ request('search') }}">
                        </form>
                    </div>
                </div>

                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Tanggal</th>
                                    <th>Lokasi / Item</th>
                                    <th>Deskripsi</th>
                                    <th>Pelapor (Kamar)</th>
                                    <th>Foto</th>
                                    <th class="text-center">Status</th>
                                    <th class="text-center">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($complaints as $index => $item)
                                    <tr>
                                        <td>{{ $complaints->firstItem() + $index }}</td>

                                        <td>{{ $item->created_at->format('d-m-Y') }}</td>

                                        <td>
                                            <a href="{{ route('pengelola.complaint.showPengelolaOnly', $item->id) }}"
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

                                        {{-- TOMBOL ACTION (Hanya Admin/Manager) --}}
                                        <td class="text-center">
                                            @if (auth()->user()->role->role_name !== 'Resident')
                                                <form action="{{ route('complaint.updateStatus', $item->id) }}"
                                                    method="POST">
                                                    @csrf
                                                    @method('PATCH')
                                                    @if ($item->status->status_name !== 'Resolved')
                                                        <input type="hidden" name="status_id" value="3">
                                                        {{-- Asumsi 3 = Resolved --}}
                                                        <button type="submit" class="btn btn-sm btn-outline-success">Mark
                                                            Resolved</button>
                                                    @else
                                                        <button type="button"
                                                            class="btn btn-sm btn-success disabled">Closed</button>
                                                    @endif
                                                </form>
                                            @else
                                                -
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="text-center py-4">Belum ada keluhan yang masuk.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    {{-- Pagination --}}
                    <div class="mt-3">
                        {{ $complaints->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
