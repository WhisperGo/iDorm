@extends('layouts.app')

@section('content')
    <div class="row">
        <div class="col-sm-12">
            <div class="card shadow-sm">
                <div class="card-header d-flex justify-content-between align-items-center flex-wrap gap-2">
                    <div class="header-title">
                        <h4 class="card-title mb-0 fw-bold">Resident Management</h4>
                        <small class="text-muted">Mengelola data seluruh penghuni asrama</small>
                    </div>
                    <div class="d-flex align-items-center gap-3">
                        <div id="live-filter-container"></div>
                        @if (Auth::user()->role->role_name === 'Manager')
                            <a href="{{ route('manager.residents.create') }}"
                                class="btn btn-primary btn-sm shadow-sm d-inline-flex align-items-center rounded-pill px-3 text-nowrap">
                                <i class="bi bi-person-plus-fill me-1"></i> Add Resident
                            </a>
                        @endif
                    </div>
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
                                    @if (Auth::user()->role->role_name === 'Manager')
                                        <th class="text-center">Global Status</th>
                                        <th class="text-center" width="15%">Action</th>
                                    @elseif (Auth::user()->role->role_name === 'Admin')
                                        <th class="text-center" style="width: 10px;">Local Status</th>
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

                                        @if (Auth::user()->role->role_name === 'Manager')
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
                                                        data-bs-toggle="modal"
                                                        data-bs-target="#modalGlobal{{ $res->id }}">
                                                        <i class="bi bi-check-circle me-1"></i> ACTIVE
                                                    </button>
                                                @endif
                                            </td>
                                        @endif

                                        @if (Auth::user()->role->role_name === 'Admin')
                                            {{-- Local Suspend Button --}}
                                            <td class="text-center">
                                                @if ($localSuspend)
                                                    <form action="{{ route('suspensions.destroy', $localSuspend->id) }}"
                                                        method="POST" class="d-inline-block m-0"
                                                        onsubmit="return confirm('Cabut sanksi?')">
                                                        @csrf @method('DELETE')
                                                        <button type="submit" class="btn btn-sm btn-icon btn-soft-danger"
                                                            data-bs-toggle="tooltip" data-bs-placement="top"
                                                            title="Cabut Sanksi">
                                                            <span class="btn-inner">
                                                                <svg class="icon-20" width="20" viewBox="0 0 24 24"
                                                                    fill="none" stroke="currentColor" stroke-width="1.5"
                                                                    stroke-linecap="round" stroke-linejoin="round">
                                                                    <rect x="3" y="11" width="18" height="11"
                                                                        rx="2" ry="2"></rect>
                                                                    <path d="M7 11V7a5 5 0 0 1 9.9-1"></path>
                                                                </svg>
                                                            </span>
                                                        </button>
                                                    </form>
                                                @else
                                                    <span class="d-inline-block" tabindex="0" data-bs-toggle="tooltip"
                                                        data-bs-placement="top">
                                                        <button type="button" class="btn btn-sm btn-icon btn-soft-warning"
                                                            data-bs-toggle="modal"
                                                            data-bs-target="#modalLocal{{ $res->id }}">
                                                            <span class="btn-inner">
                                                                <svg class="icon-20" width="20" viewBox="0 0 24 24"
                                                                    fill="none" stroke="currentColor" stroke-width="1.5"
                                                                    stroke-linecap="round" stroke-linejoin="round">
                                                                    <rect x="3" y="11" width="18" height="11"
                                                                        rx="2" ry="2"></rect>
                                                                    <path d="M7 11V7a5 5 0 0 1 10 0v4"></path>
                                                                </svg>
                                                            </span>
                                                        </button>
                                                    </span>
                                                @endif
                                            </td>
                                        @endif

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
                                                </div>
                                            </td>
                                        @endif
                                    </tr>

                                    {{-- Modal Global & Local di-render di luar tabel --}}
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center py-5">
                                            <i class="bi bi-people text-muted mb-3"
                                                style="font-size: 3rem; opacity: 0.5;"></i>
                                            <h5 class="text-muted">Belum ada data resident.</h5>
                                        </td>
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

    {{-- MODALS DIRENDER DI LUAR TABEL --}}
    @foreach ($residents as $res)
        @php
            $globalSuspend = $res->activeSuspensions->whereNull('facility_id')->first();
        @endphp

        {{-- MODAL GLOBAL SUSPEND (MANAGER) --}}
        @if (auth()->user()->role->role_name == 'Manager' && !$globalSuspend)
            <div class="modal fade" id="modalGlobal{{ $res->id }}" tabindex="-1" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content border-0 shadow-lg form-modal">
                        {{-- Header --}}
                        <div class="modal-header border-bottom-0 pt-4 pb-0 px-4">
                            <div class="d-flex align-items-center">
                                <div class="bg-danger rounded-circle d-flex align-items-center justify-content-center me-3"
                                    style="width: 50px; height: 50px;">
                                    <i class="bi bi-exclamation-triangle-fill text-white fs-4"></i>
                                </div>
                                <div>
                                    <h5 class="modal-title fw-bold text-danger mb-1 fs-5">Global Suspend</h5>
                                    <small class="text-muted" style="font-size: 0.9rem;">Target:
                                        {{ $res->residentDetails->full_name ?? $res->name }}</small>
                                </div>
                            </div>
                            <button type="button" class="btn-close mb-auto mt-1" data-bs-dismiss="modal"
                                aria-label="Close"></button>
                        </div>

                        <form action="{{ route('suspensions.store') }}" method="POST">
                            @csrf
                            <div class="modal-body px-4 pt-4 pb-2">
                                <input type="hidden" name="user_id" value="{{ $res->id }}">
                                <input type="hidden" name="facility_id" value="">
                                {{-- NULL untuk Global --}}

                                {{-- Info Text --}}
                                <div class="d-flex align-items-center text-muted mb-4 px-1" style="font-size: 0.95rem;">
                                    <i class="bi bi-info-circle me-3 fs-5"></i>
                                    <span>Akun akan <span class="fw-bold">diblokir total</span> dari seluruh fasilitas
                                        asrama.</span>
                                </div>

                                <div class="form-group mb-4">
                                    <label class="form-label fw-bold text-muted">Alasan Hukuman <span
                                            class="text-danger">*</span></label>
                                    <textarea name="reason" class="form-control" rows="3" required
                                        placeholder="Contoh: Menunggak pembayaran, pelanggaran berat..."></textarea>
                                </div>

                                <div class="row mb-2">
                                    <div class="col-6">
                                        <label class="form-label fw-bold text-muted">Mulai</label>
                                        <input type="date" name="start_date" class="form-control"
                                            value="{{ date('Y-m-d') }}" required>
                                    </div>
                                    <div class="col-6">
                                        <label class="form-label fw-bold text-muted">Selesai (Opsional)</label>
                                        <input type="date" name="end_date" class="form-control">
                                        <small class="text-muted d-block mt-1" style="font-size: 0.8rem;">Kosong =
                                            Permanen</small>
                                    </div>
                                </div>
                            </div>
                            <div class="modal-footer border-top-0 pt-0 pb-4 px-4">
                                <button type="button" class="btn btn-dark text-white w-auto px-4"
                                    data-bs-dismiss="modal"
                                    style="background-color: #00205b; border-color: #00205b;">Batal</button>
                                <button type="submit" class="btn btn-danger w-auto px-4">Ya, Bekukan Akun</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        @endif

        {{-- MODAL LOCAL SUSPEND (ADMIN) --}}
        @if (auth()->user()->role->role_name != 'Resident')
            <div class="modal fade" id="modalLocal{{ $res->id }}" tabindex="-1" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content border-0 shadow-lg form-modal">
                        {{-- Header --}}
                        <div class="modal-header border-bottom-0 pt-4 pb-0 px-4">
                            <div class="d-flex align-items-center">
                                <div class="rounded-circle d-flex align-items-center justify-content-center me-3"
                                    style="width: 50px; height: 50px; background-color: #fd7e14;">
                                    <i class="bi bi-lock-fill text-white fs-4"></i>
                                </div>
                                <div>
                                    <h5 class="modal-title fw-bold text-dark mb-1 fs-5">Suspend Fasilitas</h5>
                                    <small class="text-muted" style="font-size: 0.9rem;">Penghuni:
                                        {{ $res->residentDetails->full_name ?? $res->name }}</small>
                                </div>
                            </div>
                            <button type="button" class="btn-close mb-auto mt-1" data-bs-dismiss="modal"
                                aria-label="Close"></button>
                        </div>

                        <form action="{{ route('suspensions.store') }}" method="POST">
                            @csrf
                            <div class="modal-body px-4 pt-4 pb-2">
                                <input type="hidden" name="user_id" value="{{ $res->id }}">

                                {{-- LOGIC OTORITAS ADMIN --}}
                                <div class="form-group mb-4">
                                    <label class="form-label fw-bold text-muted">Fasilitas</label>
                                    @php
                                        $user = auth()->user();
                                        $isManager = $user->role->role_name === 'Manager';
                                        $adminFacilityId = $user->adminDetails->facility_id ?? null;
                                    @endphp

                                    @if ($isManager)
                                        {{-- Manager: Select Option --}}
                                        <select name="facility_id" class="form-select bg-light fw-bold text-dark"
                                            required>
                                            <option value="" selected disabled>-- Pilih Fasilitas --</option>
                                            @foreach ($facilities as $facility)
                                                <option value="{{ $facility->id }}">{{ $facility->name }}</option>
                                            @endforeach
                                        </select>
                                    @else
                                        {{-- Admin: Readonly Input --}}
                                        @php
                                            $myFacility = $facilities->firstWhere('id', $adminFacilityId);
                                            $facilityName = $myFacility ? $myFacility->name : 'Tidak Ada Otoritas';
                                        @endphp
                                        <div class="input-group">
                                            <span class="input-group-text bg-light border-end-0 text-primary">
                                                <i class="bi bi-building"></i>
                                            </span>
                                            <input type="text"
                                                class="form-control bg-light border-start-0 fw-bold text-dark"
                                                value="{{ $facilityName }}" readonly>
                                        </div>
                                        <input type="hidden" name="facility_id" value="{{ $adminFacilityId }}">
                                        <small class="mt-1 d-block" style="color: #fd7e14; font-size: 0.85rem;">*Sesuai
                                            otoritas akun Anda</small>
                                    @endif
                                </div>

                                <div class="form-group mb-4">
                                    <label class="form-label fw-bold text-muted">Pelanggaran / Alasan</label>
                                    <textarea name="reason" class="form-control" rows="2" required
                                        placeholder="Contoh: Merusak alat, tidak membersihkan..."></textarea>
                                </div>

                                <div class="row mb-2">
                                    <div class="col-6">
                                        <label class="form-label fw-bold text-muted">Mulai</label>
                                        <input type="date" name="start_date" class="form-control"
                                            value="{{ date('Y-m-d') }}" required>
                                    </div>
                                    <div class="col-6">
                                        <label class="form-label fw-bold text-muted">Selesai</label>
                                        <input type="date" name="end_date" class="form-control">
                                    </div>
                                </div>
                            </div>
                            <div class="modal-footer border-top-0 pt-0 pb-4 px-4">
                                <button type="button" class="btn btn-dark text-white w-auto px-4"
                                    data-bs-dismiss="modal"
                                    style="background-color: #00205b; border-color: #00205b;">Batal</button>
                                <button type="submit" class="btn text-dark fw-bold w-auto px-4"
                                    style="background-color: #fd7e14; border-color: #fd7e14;">Simpan Hukuman</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        @endif
    @endforeach
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
                            <i class="bi bi-people text-muted mb-3" style="font-size: 3rem; opacity: 0.5;"></i>
                            <h5 class="text-muted">Belum ada data resident.</h5>
                        </div>
                    `,
                    "zeroRecords": `
                        <div class="text-center py-5">
                            <i class="bi bi-search text-muted mb-3" style="font-size: 3rem; opacity: 0.5;"></i>
                            <h5 class="text-muted">Pencarian tidak ditemukan.</h5>
                            <p class="small text-muted">Tidak ada data resident yang cocok dengan kata kunci Anda.</p>
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

            $('#live-filter-container').html(searchHtml);
            $('#custom-search-input input').on('keyup', function() {
                table.search(this.value).draw();
            });

            // Remove focus from the button after modal is closed
            $('.modal').on('hidden.bs.modal', function() {
                if (document.activeElement) {
                    document.activeElement.blur();
                }
            });
        });
    </script>
@endpush
