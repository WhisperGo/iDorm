@extends('layouts.app')

@section('content')
    <div class="row">
        <div class="col-sm-12">
            <div class="card shadow-sm">
                <div class="card-header d-flex justify-content-between align-items-center flex-wrap">
                    <div class="header-title">
                        <h4 class="card-title mb-0 fw-bold">Facility Admin Management</h4>
                        <small class="text-muted">Mengelola data para pengelola fasilitas asrama</small>
                    </div>

                    @if (Auth::user()->role->role_name === 'Manager')
                        {{-- Tombol Tambah Admin --}}
                        <a href="{{ route('manager.admins.create') }}" class="btn btn-primary btn-sm shadow-sm">
                            <i class="bi bi-person-plus-fill me-1"></i> Add Admin
                        </a>
                    @endif
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
                                                    {{-- <form action="{{ route('manager.admins.destroy', $adm->id) }}"
                                                        method="POST"
                                                        onsubmit="return confirm('Apakah Anda yakin ingin menonaktifkan admin ini?')">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit"
                                                            class="btn btn-sm btn-icon btn-soft-danger btn-outline-danger">
                                                            <i class="bi bi-trash"></i>
                                                        </button>
                                                    </form> --}}
                                                </div>
                                            </td>
                                        @endif
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="text-center py-4 text-muted">Belum ada data admin
                                            fasilitas.</td>
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
    </div>
@endsection
