@extends('layouts.app')

@section('content')
    <div class="row">
        <div class="col-sm-12">
            <div class="card shadow-sm">
                <div class="card-header">
                    <div class="d-flex justify-content-between align-items-center flex-wrap">
                        <div class="header-title">
                            <h4 class="card-title mb-0 fw-bold">Resident Management</h4>
                        </div>
                        <div id="live-filter-container"></div>
                    </div>
                </div>

                <div class="card-body mt-0">
                    @if (session('success'))
                        <div class="alert alert-success">{{ session('success') }}</div>
                    @endif
                    @if (session('error'))
                        <div class="alert alert-danger">{{ session('error') }}</div>
                    @endif

                    <div class="table-responsive">
                        <table id="datatable" class="table table-bordered align-middle" data-toggle="data-table">
                            <thead class="bg-light">
                                <tr>
                                    <th class="text-center" width="5%">No.</th>
                                    <th>Resident Info</th>
                                    <th class="text-center">Room</th>
                                    @if (auth()->user()->role->role_name == 'Manager')
                                        <th class="text-center">Global Status</th>
                                    @endif
                                    @if (auth()->user()->role->role_name != 'Resident')
                                        <th class="text-center" width="20%">Action</th>
                                    @endif
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($residents as $index => $res)
                                    @php
                                        // Cek Suspend Global (Manager)
                                        $globalSuspend = $res->activeSuspensions->whereNull('facility_id')->first();

                                        // Cek Suspend Admin (Cek apakah user ini punya suspend di fasilitas yang dikelola admin ini)
                                        // Note: Logic filter fasilitas spesifik lebih baik di handle di Controller/Model untuk performa,
                                        // tapi untuk view ini kita cek sekilas saja.

                                    @endphp

                                    <tr>
                                        {{-- NO. --}}
                                        <td class="text-center">{{ $residents->firstItem() + $index }}</td>

                                        {{-- Resident Info --}}
                                        <td>
                                            <div class="fw-bold">{{ $res->residentDetails->full_name }}</div>
                                            <small class="text-muted">ID: {{ $res->card_id }} |
                                                {{ $res->residentDetails->gender }}</small>
                                        </td>

                                        {{-- Room Number --}}
                                        <td class="text-center font-monospace">{{ $res->residentDetails->room_number }}</td>

                                        {{-- 1. MANAGER BUTTONS (Global Suspend) --}}
                                        @if (auth()->user()->role->role_name == 'Manager')
                                            <td class="text-center">
                                                @if ($globalSuspend)
                                                    {{-- Tombol UNSUSPEND --}}
                                                    <form action="{{ route('suspensions.destroy', $globalSuspend->id) }}"
                                                        method="POST"
                                                        onsubmit="return confirm('Cabut hukuman global untuk user ini?')">
                                                        @csrf @method('DELETE')
                                                        <button
                                                            class="btn btn-danger btn-sm w-100 d-flex align-items-center justify-content-center gap-1">
                                                            <svg width="16" viewBox="0 0 24 24" fill="none"
                                                                xmlns="http://www.w3.org/2000/svg">
                                                                <path d="M16 12H8" stroke="currentColor" stroke-width="1.5"
                                                                    stroke-linecap="round" stroke-linejoin="round" />
                                                            </svg>
                                                            SUSPENDED
                                                        </button>
                                                    </form>
                                                    <small class="text-danger fw-bold" style="font-size: 10px;">
                                                        Until:
                                                        {{ $globalSuspend->end_date ? $globalSuspend->end_date->format('d M Y') : 'Forever' }}
                                                    </small>
                                                @else
                                                    {{-- Tombol OPEN MODAL (Trigger Bootstrap) --}}
                                                    <button type="button" class="btn btn-success btn-sm w-100"
                                                        data-bs-toggle="modal"
                                                        data-bs-target="#modalGlobal{{ $res->id }}">
                                                        ACTIVE
                                                    </button>
                                                @endif
                                            </td>
                                        @endif

                                        {{-- 2. ACTION BUTTONS (ADMIN & MANAGER) --}}
                                        {{-- 2. ACTION BUTTONS (ADMIN & MANAGER) --}}
                                        @if (auth()->user()->role->role_name != 'Resident')
                                            <td class="text-center">
                                                <div
                                                    class="d-flex align-items-center justify-content-center list-user-action">

                                                    {{-- LOGIC MANAGER: Edit Profile --}}
                                                    @if (auth()->user()->role->role_name == 'Manager')
                                                        <a class="btn btn-sm btn-icon btn-soft-primary"
                                                            data-bs-toggle="tooltip" data-bs-placement="top"
                                                            title="Edit Profile"
                                                            href="{{ route('admin.profile.edit', $res->id) }}">
                                                            <span class="btn-inner">
                                                                <svg width="20" viewBox="0 0 24 24" fill="none"
                                                                    xmlns="http://www.w3.org/2000/svg">
                                                                    <path
                                                                        d="M11.4925 2.78906H7.75349C4.67849 2.78906 2.75049 4.96606 2.75049 8.04806V16.3621C2.75049 19.4441 4.66949 21.6211 7.75349 21.6211H16.5775C19.6625 21.6211 21.5815 19.4441 21.5815 16.3621V12.3341"
                                                                        stroke="currentColor" stroke-width="1.5"
                                                                        stroke-linecap="round" stroke-linejoin="round">
                                                                    </path>
                                                                    <path fill-rule="evenodd" clip-rule="evenodd"
                                                                        d="M8.82812 10.921L16.3011 3.44799C17.2321 2.51799 18.7411 2.51799 19.6721 3.44799L20.8891 4.66499C21.8201 5.59599 21.8201 7.10599 20.8891 8.03599L13.3801 15.545C12.9731 15.952 12.4211 16.181 11.8451 16.181H8.09912L8.19312 12.401C8.20712 11.845 8.43412 11.315 8.82812 10.921Z"
                                                                        stroke="currentColor" stroke-width="1.5"
                                                                        stroke-linecap="round" stroke-linejoin="round">
                                                                    </path>
                                                                    <path d="M15.1655 4.60254L19.7315 9.16854"
                                                                        stroke="currentColor" stroke-width="1.5"
                                                                        stroke-linecap="round" stroke-linejoin="round">
                                                                    </path>
                                                                </svg>
                                                            </span>
                                                        </a>
                                                    @else
                                                        {{-- LOGIC ADMIN --}}
                                                        @php
                                                            $myFacilityId =
                                                                auth()->user()->adminDetails->facility_id ?? 0;
                                                            $localSuspend = $res->activeSuspensions->firstWhere(
                                                                'facility_id',
                                                                $myFacilityId,
                                                            );
                                                        @endphp

                                                        @if ($localSuspend)
                                                            {{-- KONDISI A: SEDANG DIHUKUM --}}
                                                            {{-- 1. Info Tanggal Berakhir (Badge Kecil) --}}
                                                            {{-- <span class="badge rounded-pill bg-soft-danger me-2"
                                                                data-bs-toggle="tooltip" title="Berakhir Pada">
                                                                <i class="bi bi-calendar-event me-1"></i>
                                                                {{ $localSuspend->end_date ? $localSuspend->end_date->format('d M') : '∞' }}
                                                            </span> --}}

                                                            {{-- 2. Tombol Hapus/Batalkan (Soft Danger Button) --}}
                                                            <form
                                                                action="{{ route('suspensions.destroy', $localSuspend->id) }}"
                                                                method="POST"
                                                                onsubmit="return confirm('Cabut hukuman fasilitas untuk user ini?')">
                                                                @csrf @method('DELETE')
                                                                <button type="submit"
                                                                    class="btn btn-sm btn-icon btn-soft-danger"
                                                                    data-bs-toggle="tooltip" data-bs-placement="top"
                                                                    title="Cabut Suspend">
                                                                    <span class="btn-inner">
                                                                        <svg width="20" viewBox="0 0 24 24"
                                                                            fill="none"
                                                                            xmlns="http://www.w3.org/2000/svg"
                                                                            stroke="currentColor">
                                                                            <path
                                                                                d="M19.3248 9.46826C19.3248 9.46826 18.7818 16.2033 18.4668 19.0403C18.3168 20.3953 17.4798 21.1893 16.1088 21.2143C13.4998 21.2613 10.8878 21.2643 8.27979 21.2093C6.96079 21.1823 6.13779 20.3783 5.99079 19.0473C5.67379 16.1853 5.13379 9.46826 5.13379 9.46826"
                                                                                stroke-width="1.5" stroke-linecap="round"
                                                                                stroke-linejoin="round"></path>
                                                                            <path d="M20.708 6.23975H3.75"
                                                                                stroke-width="1.5" stroke-linecap="round"
                                                                                stroke-linejoin="round"></path>
                                                                            <path
                                                                                d="M17.4406 6.23973C16.6556 6.23973 15.9796 5.68473 15.8256 4.91573L15.5826 3.69973C15.4326 3.13873 14.9246 2.75073 14.3456 2.75073H10.1126C9.53358 2.75073 9.02558 3.13873 8.87558 3.69973L8.63258 4.91573C8.47858 5.68473 7.80258 6.23973 7.01758 6.23973"
                                                                                stroke-width="1.5" stroke-linecap="round"
                                                                                stroke-linejoin="round"></path>
                                                                        </svg>
                                                                    </span>
                                                                </button>
                                                            </form>
                                                        @else
                                                            {{-- KONDISI B: AMAN -> TOMBOL SUSPEND (Soft Warning Button) --}}
                                                            <button type="button"
                                                                class="btn btn-sm btn-icon btn-soft-warning"
                                                                data-bs-toggle="modal"
                                                                data-bs-target="#modalLocal{{ $res->id }}"
                                                                title="Suspend Fasilitas">
                                                                <span class="btn-inner">
                                                                    <svg width="20" viewBox="0 0 24 24"
                                                                        fill="none" xmlns="http://www.w3.org/2000/svg">
                                                                        <path
                                                                            d="M16.5 12V8.5C16.5 6.01 14.49 4 12 4C9.51 4 7.5 6.01 7.5 8.5V12"
                                                                            stroke="currentColor" stroke-width="1.5"
                                                                            stroke-linecap="round"
                                                                            stroke-linejoin="round"></path>
                                                                        <path fill-rule="evenodd" clip-rule="evenodd"
                                                                            d="M12 21.25C15.176 21.25 17.75 18.676 17.75 15.5C17.75 12.324 15.176 9.75 12 9.75C8.824 9.75 6.25 12.324 6.25 15.5C6.25 18.676 8.824 21.25 12 21.25Z"
                                                                            stroke="currentColor" stroke-width="1.5"
                                                                            stroke-linecap="round"
                                                                            stroke-linejoin="round"></path>
                                                                        <path d="M12 16.75V14.25" stroke="currentColor"
                                                                            stroke-width="1.5" stroke-linecap="round"
                                                                            stroke-linejoin="round"></path>
                                                                    </svg>
                                                                </span>
                                                            </button>
                                                        @endif
                                                    @endif
                                                </div>
                                            </td>
                                        @endif
                                    </tr>

                                    {{-- =======================
                                        MODAL AREA (Di dalam Loop)
                                        ======================= --}}

                                    {{-- =======================================================
                                        MODAL GLOBAL SUSPEND (MANAGER)
                                        Style: Hope UI Danger Theme (Soft Background)
                                        ======================================================= --}}
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

                                @empty
                                    <tr>
                                        <td colspan="6" class="text-center py-4 text-muted">Data resident tidak
                                            ditemukan.</td>
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
        <script src="{{ asset('hopeui/js/hope-ui.js') }}" defer></script>
        {{-- Script DataTable Filter Kamu yang Lama --}}
        <script>
            window.onload = function() {
                if ($.fn.DataTable.isDataTable('#datatable')) {
                    $('#datatable').DataTable().destroy();
                }
                var table = $('#datatable').DataTable({
                    "paging": false,
                    "lengthChange": false,
                    "info": false,
                    "searching": true,
                    "ordering": true,
                    "dom": 'frt',
                    "language": {
                        "search": "Live Search:",
                        "searchPlaceholder": "Ketik nama..."
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
