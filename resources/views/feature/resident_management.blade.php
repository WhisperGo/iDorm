@extends('layouts.app')

@section('content')
    <div class="row">
        <div class="col-sm-12">
            <div class="card shadow-sm">
                <div class="card-header d-flex justify-content-between align-items-center flex-wrap">
                    <div class="d-flex justify-content-between align-items-center flex-wrap">
                        <div class="header-title">
                            <h4 class="card-title mb-0 fw-bold">Resident Management</h4>
                            <small class="text-muted">Mengelola data seluruh penghuni asrama</small>
                        </div>
                        <div id="live-filter-container"></div>
                    </div>

                    @if (Auth::user()->role->role_name === 'Manager')
                        {{-- Letakkan di dalam card-header atau area atas tabel --}}
                        <a href="{{ route('manager.residents.create') }}" class="btn btn-primary btn-sm shadow-sm">
                            <i class="bi bi-plus-circle me-1"></i> Add Resident
                        </a>
                    @endif
                </div>

                <div class="card-body mt-0">
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
                                    <th>Resident Info</th>
                                    <th class="text-center">Room</th>
                                    <th class="text-center">Global Status</th>
                                    @if (Auth::user()->role->role_name === 'Manager')
                                        <th class="text-center" width="15%">Action</th>
                                    @endif
                                </tr>
                            </thead>
                            <tbody>
                                @php $displayNumber = ($residents->currentPage() - 1) * $residents->perPage() + 1; @endphp
                                @forelse($residents as $res)
                                    @php
                                        $globalSuspend = $res->activeSuspensions->whereNull('facility_id')->first();
                                        $localSuspend =
                                            $isAdmin && $myFacilityId
                                                ? $res->activeSuspensions->where('facility_id', $myFacilityId)->first()
                                                : null;
                                        $residentDetails = $res->residentDetails;
                                    @endphp
                                    <tr>
                                        <td class="text-center">{{ $displayNumber++ }}</td>
                                        <td>
                                            <div class="fw-bold text-dark">{{ $residentDetails?->full_name ?? $res->name }}
                                            </div>
                                            <small class="text-muted">ID: {{ $res->card_id ?? '-' }} |
                                                {{ $residentDetails?->gender ?? '-' }}</small>
                                        </td>
                                        <td class="text-center font-monospace">{{ $residentDetails?->room_number ?? '-' }}
                                        </td>

                                        <td class="text-center">
                                            @if ($globalSuspend)
                                                <form action="{{ route('suspensions.destroy', $globalSuspend->id) }}"
                                                    method="POST" onsubmit="return confirm('Buka blokir global?')">
                                                    @csrf @method('DELETE')
                                                    <button type="submit" class="btn btn-danger btn-sm w-100">
                                                        <i class="bi bi-unlock-fill"></i> TERBLOKIR
                                                    </button>
                                                </form>
                                            @else
                                                <button type="button" class="btn btn-soft-success btn-sm w-100"
                                                    data-bs-toggle="modal" data-bs-target="#modalGlobal{{ $res->id }}">
                                                    <i class="bi bi-check-circle me-1"></i> ACTIVE
                                                </button>
                                            @endif
                                        </td>

                                        @if (Auth::user()->role->role_name === 'Manager')
                                            <td class="text-center">
                                                <div class="d-flex align-items-center justify-content-center gap-2">
                                                    {{-- Edit Profile --}}
                                                    <a class="btn btn-sm btn-icon btn-soft-primary"
                                                        href="{{ route('admin.profile.edit', $res->id) }}"
                                                        title="Edit Profile">
                                                        <i class="bi bi-pencil-square"></i>
                                                    </a>

                                                    {{-- Tombol Hapus --}}
                                                    <form action="{{ route('manager.residents.destroy', $res->id) }}"
                                                        method="POST"
                                                        onsubmit="return confirm('Apakah Anda yakin ingin menghapus resident ini? Semua data terkait akan hilang!')">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit"
                                                            class="btn btn-sm btn-icon btn-soft-danger btn-outline-danger">
                                                            <i class="bi bi-trash"></i>
                                                        </button>
                                                    </form>

                                                    {{-- Local Suspend Button
                                                @if ($localSuspend)
                                                    <form action="{{ route('suspensions.destroy', $localSuspend->id) }}" method="POST" onsubmit="return confirm('Cabut sanksi?')">
                                                        @csrf @method('DELETE')
                                                        <button type="submit" class="btn btn-sm btn-icon btn-soft-danger" title="Cabut Sanksi">
                                                            <i class="bi bi-unlock"></i>
                                                        </button>
                                                    </form>
                                                @else
                                                    <button type="button" class="btn btn-sm btn-icon btn-soft-warning" data-bs-toggle="modal" data-bs-target="#modalLocal{{ $res->id }}" title="Beri Sanksi">
                                                        <i class="bi bi-lock-fill"></i>
                                                    </button>
                                                @endif --}}
                                                </div>
                                            </td>
                                        @endif
                                    </tr>
                                    {{-- Modal Global & Local Include disini --}}
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center py-4">Belum ada data resident.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    <div class="mt-3">{{ $residents->links() }}</div>
                </div>
            </div>
        </div>
    </div>
@endsection
