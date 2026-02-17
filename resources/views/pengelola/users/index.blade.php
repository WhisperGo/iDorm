@extends('layouts.app')

@section('content')
    <div class="row">
        <div class="col-sm-12">
            <div class="card shadow-sm border-0">
                <div class="card-header d-flex justify-content-between align-items-center flex-wrap">
                    <div class="header-title">
                        <h4 class="card-title mb-0 fw-bold">User Management</h4>
                        <small class="text-muted">Kelola data seluruh Admin Fasilitas dan Penghuni Asrama</small>
                    </div>
                    {{-- Tempat input search DataTables --}}
                    <div id="live-filter-container"></div>
                </div>

                <div class="card-body">
                    @if (session('success'))
                        <div class="alert alert-success alert-dismissible fade show mb-3" role="alert">
                            {{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    <div class="table-responsive">
                        <table id="datatable" class="table table-bordered align-middle">
                            <thead class="bg-light">
                                <tr>
                                    <th class="text-center" width="5%">No.</th>
                                    <th>Informasi User</th>
                                    <th class="text-center">Kamar</th>
                                    <th class="text-center">Role</th>
                                    <th class="text-center" width="15%">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($residents as $res)
                                    <tr>
                                        <td class="text-center">
                                            {{ ($residents->currentPage() - 1) * $residents->perPage() + $loop->iteration }}
                                        </td>
                                        <td>
                                            <div class="fw-bold text-dark">
                                                {{ $res->residentDetails?->full_name ?? $res->name }}</div>
                                            <small class="text-muted">Email: {{ $res->email }} | ID:
                                                {{ $res->card_id ?? '-' }}</small>
                                        </td>
                                        <td class="text-center font-monospace">
                                            {{ $res->residentDetails?->room_number ?? '-' }}</td>
                                        <td class="text-center">
                                            @if ($res->role_id == 2)
                                                <span class="badge bg-soft-warning text-warning">Admin Fasilitas</span>
                                            @elseif($res->role_id == 3)
                                                <span class="badge bg-soft-primary text-primary">Penghuni</span>
                                            @endif
                                        </td>
                                        <td class="text-center">
                                            <a href="{{ route('admin.profile.edit', $res->id) }}"
                                                class="btn btn-sm btn-icon btn-soft-primary" title="Edit">
                                                <i class="bi bi-pencil-square"></i>
                                            </a>
                                            {{-- Tombol Suspend (Jika logic modal suspend sudah ada) --}}
                                            <button class="btn btn-sm btn-icon btn-soft-danger" title="Suspend">
                                                <i class="bi bi-slash-circle"></i>
                                            </button>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center py-4 text-muted">Belum ada data user.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    <div class="mt-3">
                        {{ $residents->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            window.onload = function() {
                var table = $('#datatable').DataTable({
                    "paging": false,
                    "info": false,
                    "searching": true,
                    "dom": 'frt',
                    "language": {
                        "search": "",
                        "searchPlaceholder": "Cari nama atau email..."
                    }
                });
                $('.dataTables_filter').appendTo('#live-filter-container');
                $('.dataTables_filter input').addClass('form-control form-control-sm border-primary').css({
                    'width': '250px'
                });
            };
        </script>
    @endpush
@endsection
