@extends('layouts.app')

@section('content')
    <div class="row">
        <div class="col-sm-12">
            <div class="card shadow-sm">
                <div class="card-header d-flex justify-content-between align-items-center flex-wrap gap-2">
                    <div class="header-title">
                        <h4 class="card-title mb-0 fw-bold">Facility Admin Management</h4>
                        <small class="text-muted">Mengelola data para pengelola fasilitas asrama</small>
                    </div>

                    <div class="d-flex align-items-center gap-3">
                        <div id="filter-container"></div>
                        @if (Auth::user()->role->role_name === 'Manager')
                            {{-- Tombol Tambah Admin --}}
                            <a href="{{ route('manager.admins.create') }}"
                                class="btn btn-primary btn-sm shadow-sm d-inline-flex align-items-center rounded-pill px-3 text-nowrap">
                                <i class="bi bi-person-plus-fill me-1"></i> Add Admin
                            </a>
                        @endif
                    </div>
                </div>

                <div class="card-body mt-0">
                    {{-- Alert Success --}}
                    @if (session('success'))
                        <div class="alert alert-success alert-dismissible fade show mb-3" role="alert">
                            <span><i class="fas fa-check-circle me-1"></i> {{ session('success') }}</span>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    <div class="table-responsive">
                        <table id="datatable" class="table table-bordered align-middle">
                            <thead class="bg-light">
                                <tr>
                                    <th class="text-center" width="5%">No.</th>
                                    <th>Admin Info</th>
                                    <th>Managed Facility</th>
                                    @if (Auth::user()->role->role_name === 'Manager')
                                        <th class="text-center" width="15%">Action</th>
                                    @endif
                                </tr>
                            </thead>
                            <tbody>
                                @php $displayNumber = ($admins->currentPage() - 1) * $admins->perPage() + 1; @endphp
                                @forelse ($admins as $adm)
                                    @php $details = $adm->adminDetails; @endphp
                                    <tr>
                                        <td class="text-center">{{ $displayNumber++ }}</td>
                                        <td>
                                            <div class="fw-bold text-dark">
                                                {{ $details?->full_name ?? $adm->name }}
                                            </div>
                                            <small class="text-muted">
                                                ID: {{ $adm->card_id ?? '-' }} | Gender: {{ $details?->gender ?? '-' }}
                                            </small>
                                        </td>
                                        <td>
                                            @if ($details && $details->facility)
                                                <span class="text-info px-3 fw-bold">
                                                    {{ Str::replace([' Hall', ' Room'], '', $details->facility->name) }}
                                                    {{-- {{ $details->facility->name }} --}}
                                                </span>
                                            @else
                                                <span class="badge bg-soft-secondary text-secondary px-3">Belum
                                                    Ditugaskan</span>
                                            @endif
                                        </td>

                                        @if (Auth::user()->role->role_name === 'Manager')
                                            <td class="text-center">
                                                <div class="d-flex align-items-center justify-content-center gap-2">
                                                    {{-- Edit Button --}}
                                                    <a class="btn btn-sm btn-icon btn-soft-primary"
                                                        href="{{ route('admin.profile.edit', $adm->id) }}"
                                                        title="Edit Admin">
                                                        <i class="bi bi-pencil-square"></i>
                                                    </a>

                                                    {{-- Delete Button (Soft Delete) --}}
                                                    <form action="{{ route('manager.admins.destroy', $adm->id) }}"
                                                        method="POST"
                                                        onsubmit="return confirm('Apakah Anda yakin ingin menghapus/menonaktifkan admin ini?')">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit"
                                                            class="btn btn-sm btn-icon btn-soft-danger btn-outline-danger">
                                                            <i class="bi bi-trash"></i>
                                                        </button>
                                                    </form>
                                                </div>
                                            </td>
                                        @endif
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="text-center py-5">
                                            <i class="bi bi-person-badge text-muted mb-3"
                                                style="font-size: 3rem; opacity: 0.5;"></i>
                                            <h5 class="text-muted">Belum ada data admin fasilitas.</h5>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    {{-- Pagination Links --}}
                    <div class="mt-3">
                        {{ $admins->links() }}
                    </div>
                </div>
            </div>
        </div>
    @endsection

    @push('scripts')
        <script>
            $(document).ready(function() {
                if ($.fn.DataTable.isDataTable('#datatable')) {
                    $('#datatable').DataTable().destroy();
                }

                var table = $('#datatable').DataTable({
                    "paging": false,
                    "info": false,
                    "searching": true,
                    "ordering": true,
                    "autoWidth": false,
                    "dom": 'rt',
                    "language": {
                        "emptyTable": `
                        <div class="text-center py-5">
                            <i class="bi bi-person-badge text-muted mb-3" style="font-size: 3rem; opacity: 0.5;"></i>
                            <h5 class="text-muted">Belum ada data admin fasilitas.</h5>
                        </div>
                    `,
                        "zeroRecords": `
                        <div class="text-center py-5">
                            <i class="bi bi-search text-muted mb-3" style="font-size: 3rem; opacity: 0.5;"></i>
                            <h5 class="text-muted">Pencarian tidak ditemukan.</h5>
                            <p class="small text-muted">Tidak ada data admin yang cocok dengan kata kunci Anda.</p>
                        </div>
                    `
                    }
                });

                // Custom search input styling
                const searchHtml = `
            <div class="dataTables_filter d-flex align-items-center justify-content-end" id="custom-search-input">
                <label class="mb-0 d-flex align-items-center gap-2">
                    <span>Search:</span>
                    <input type="search" class="form-control form-control-sm" style="width: 250px;">
                </label>
            </div>`;

                $('#filter-container').html(searchHtml);
                $('#custom-search-input input').on('keyup', function() {
                    table.search(this.value).draw();
                });
            });
        </script>
    @endpush
