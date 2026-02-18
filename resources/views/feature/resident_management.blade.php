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

                                    {{-- @unless ($globalSuspend) --}}
                                    @if (auth()->user()->role->role_name == 'Manager' && !$globalSuspend)
                                        <div class="modal fade" id="modalGlobal{{ $res->id }}" tabindex="-1"
                                            aria-hidden="true">
                                            <div class="modal-dialog modal-dialog-centered">
                                                <div class="modal-content border-0 shadow-lg">
                                                    {{-- Header dengan Soft Background --}}
                                                    <div class="modal-header bg-soft-danger border-bottom-0">
                                                        <div class="d-flex align-items-center">
                                                            <span
                                                                class="avatar-40 rounded-pill bg-danger d-flex align-items-center justify-content-center me-2">
                                                                <i class="bi bi-exclamation-triangle-fill text-white"></i>
                                                            </span>
                                                            <div>
                                                                <h5 class="modal-title fw-bold text-danger mb-0">Global
                                                                    Suspend</h5>
                                                                <small class="text-muted">Target:
                                                                    {{ $res->residentDetails->full_name }}</small>
                                                            </div>
                                                        </div>
                                                        <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                            aria-label="Close"></button>
                                                    </div>

                                                    <form action="{{ route('suspensions.store') }}" method="POST">
                                                        @csrf
                                                        <div class="modal-body">
                                                            <input type="hidden" name="user_id"
                                                                value="{{ $res->id }}">
                                                            <input type="hidden" name="facility_id" value="">
                                                            {{-- NULL untuk Global --}}

                                                            {{-- Alert Info --}}
                                                            <div class="alert alert-soft-danger d-flex align-items-center mb-4"
                                                                role="alert">
                                                                <i class="bi bi-info-circle me-3"></i>
                                                                <div class="small">
                                                                    Akun akan <b>diblokir total</b> dari seluruh fasilitas
                                                                    asrama.
                                                                </div>
                                                            </div>

                                                            <div class="form-group mb-3">
                                                                <label class="form-label fw-bold">Alasan Hukuman <span
                                                                        class="text-danger">*</span></label>
                                                                <textarea name="reason" class="form-control" rows="3" required
                                                                    placeholder="Contoh: Menunggak pembayaran, pelanggaran berat..."></textarea>
                                                            </div>

                                                            <div class="row">
                                                                <div class="col-6">
                                                                    <label class="form-label fw-bold">Mulai</label>
                                                                    <input type="date" name="start_date"
                                                                        class="form-control" value="{{ date('Y-m-d') }}"
                                                                        required>
                                                                </div>
                                                                <div class="col-6">
                                                                    <label class="form-label fw-bold">Selesai
                                                                        (Opsional)
                                                                    </label>
                                                                    <input type="date" name="end_date"
                                                                        class="form-control">
                                                                    <small class="text-muted"
                                                                        style="font-size: 0.75rem;">Kosong =
                                                                        Permanen</small>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="modal-footer border-top-0 pt-0">
                                                            <button type="button" class="btn btn-secondary"
                                                                data-bs-dismiss="modal">Batal</button>
                                                            <button type="submit" class="btn btn-danger">Ya, Bekukan
                                                                Akun</button>
                                                        </div>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    @endif

                                    {{-- MODAL LOCAL SUSPEND (ADMIN)
                                        Style: Hope UI Warning Theme (Soft Background) --}}
                                    @if (auth()->user()->role->role_name != 'Resident')
                                        <div class="modal fade" id="modalLocal{{ $res->id }}" tabindex="-1"
                                            aria-hidden="true">
                                            <div class="modal-dialog modal-dialog-centered">
                                                <div class="modal-content border-0 shadow-lg">
                                                    {{-- Header dengan Soft Background --}}
                                                    <div class="modal-header bg-soft-warning border-bottom-0">
                                                        <div class="d-flex align-items-center">
                                                            <span
                                                                class="avatar-40 rounded-pill bg-warning d-flex align-items-center justify-content-center me-2">
                                                                <i class="bi bi-lock-fill text-white"></i>
                                                            </span>
                                                            <div>
                                                                <h5 class="modal-title fw-bold text-dark mb-0">Suspend
                                                                    Fasilitas</h5>
                                                                <small class="text-muted">Penghuni:
                                                                    {{ $res->residentDetails->full_name }}</small>
                                                            </div>
                                                        </div>
                                                        <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                            aria-label="Close"></button>
                                                    </div>

                                                    <form action="{{ route('suspensions.store') }}" method="POST">
                                                        @csrf
                                                        <div class="modal-body">
                                                            <input type="hidden" name="user_id"
                                                                value="{{ $res->id }}">

                                                            {{-- LOGIC OTORITAS ADMIN --}}
                                                            <div class="form-group mb-3">
                                                                <label class="form-label fw-bold">Fasilitas</label>
                                                                @php
                                                                    $user = auth()->user();
                                                                    $isManager = $user->role->role_name === 'Manager';
                                                                    $adminFacilityId =
                                                                        $user->adminDetails->facility_id ?? null;
                                                                @endphp

                                                                @if ($isManager)
                                                                    {{-- Manager: Select Option --}}
                                                                    <select name="facility_id" class="form-select"
                                                                        required>
                                                                        <option value="" selected disabled>-- Pilih
                                                                            Fasilitas --</option>
                                                                        @foreach ($facilities as $facility)
                                                                            <option value="{{ $facility->id }}">
                                                                                {{ $facility->name }}</option>
                                                                        @endforeach
                                                                    </select>
                                                                @else
                                                                    {{-- Admin: Readonly Input (Hope UI Style) --}}
                                                                    @php
                                                                        $myFacility = $facilities->firstWhere(
                                                                            'id',
                                                                            $adminFacilityId,
                                                                        );
                                                                        $facilityName = $myFacility
                                                                            ? $myFacility->name
                                                                            : 'Tidak Ada Otoritas';
                                                                    @endphp
                                                                    <div class="input-group">
                                                                        <span
                                                                            class="input-group-text bg-light border-end-0 text-primary">
                                                                            <i class="bi bi-building"></i>
                                                                        </span>
                                                                        <input type="text"
                                                                            class="form-control bg-light border-start-0 fw-bold text-dark"
                                                                            value="{{ $facilityName }}" readonly>
                                                                    </div>
                                                                    <input type="hidden" name="facility_id"
                                                                        value="{{ $adminFacilityId }}">
                                                                    <small class="text-warning mt-1 d-block"
                                                                        style="font-size: 0.75rem;">*Sesuai otoritas akun
                                                                        Anda</small>
                                                                @endif
                                                            </div>

                                                            <div class="form-group mb-3">
                                                                <label class="form-label fw-bold">Pelanggaran /
                                                                    Alasan</label>
                                                                <textarea name="reason" class="form-control" rows="2" required
                                                                    placeholder="Contoh: Merusak alat, tidak membersihkan..."></textarea>
                                                            </div>

                                                            <div class="row">
                                                                <div class="col-6">
                                                                    <label class="form-label fw-bold">Mulai</label>
                                                                    <input type="date" name="start_date"
                                                                        class="form-control" value="{{ date('Y-m-d') }}"
                                                                        required>
                                                                </div>
                                                                <div class="col-6">
                                                                    <label class="form-label fw-bold">Selesai</label>
                                                                    <input type="date" name="end_date"
                                                                        class="form-control">
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="modal-footer border-top-0 pt-0">
                                                            <button type="button" class="btn btn-secondary"
                                                                data-bs-dismiss="modal">Batal</button>
                                                            <button type="submit"
                                                                class="btn btn-warning text-dark fw-bold">Simpan
                                                                Hukuman</button>
                                                        </div>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    @endif
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
