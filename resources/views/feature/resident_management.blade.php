@extends('layouts.app')

@section('content')
    <div class="row">
        <div class="col-sm-12">
            <div class="card shadow-sm">
                <div class="card-header">
                    <div class="d-flex justify-content-between align-items-center flex-wrap">
                        <div class="header-title">
                            <h4 class="card-title mb-0 fw-bold">Resident Management</h4>

                            {{-- Logic Safe Mode Info (Variable dari Controller) --}}
                            @if ($isLaundryAdmin)
                                <small class="text-primary d-block mt-1">
                                    <i class="bi bi-info-circle me-1"></i>
                                    Otoritas: Khusus Penghuni
                                    @if ($adminGender)
                                        <strong>{{ $adminGender == 'Male' ? 'Laki-laki' : 'Perempuan' }}</strong>
                                    @else
                                        <span class="text-danger">(Profil Gender Admin Belum Diatur)</span>
                                    @endif
                                </small>
                            @endif
                        </div>
                        <div id="live-filter-container"></div>
                    </div>
                </div>

                <div class="card-body mt-0">
                    {{-- Alert Messages --}}
                    @if (session('success'))
                        <div class="alert alert-left alert-success alert-dismissible fade show mb-3" role="alert">
                            <span><i class="fas fa-check-circle me-1"></i> {{ session('success') }}</span>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif
                    @if (session('error'))
                        <div class="alert alert-left alert-danger alert-dismissible fade show mb-3" role="alert">
                            <span><i class="fas fa-exclamation-triangle me-1"></i> {{ session('error') }}</span>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    <div class="table-responsive">
                        <table id="datatable" class="table table-bordered align-middle" data-toggle="data-table">
                            <thead class="bg-light">
                                <tr>
                                    <th class="text-center" width="5%">No.</th>
                                    <th>Resident Info</th>
                                    <th class="text-center">Room</th>
                                    
                                    @if ($isManager)
                                        <th class="text-center">Global Status</th>
                                    @endif
                                    
                                    <th class="text-center" width="20%">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php $displayNumber = ($residents->currentPage() - 1) * $residents->perPage() + 1; @endphp

                                @forelse($residents as $res)
                                    @php
                                        // LOGIC SUSPEND DATA (Disederhanakan karena logic filtering sudah di controller)
                                        // 1. Global Suspend (Untuk Manager)
                                        $globalSuspend = $res->activeSuspensions->whereNull('facility_id')->first();

                                        // 2. Local Suspend (Untuk Admin saat ini)
                                        // Kita pakai variable $myFacilityId dari controller
                                        $localSuspend = null;
                                        if($isAdmin && $myFacilityId) {
                                            $localSuspend = $res->activeSuspensions->where('facility_id', $myFacilityId)->first();
                                        }
                                        
                                        $residentDetails = $res->residentDetails;
                                    @endphp

                                    <tr>
                                        <td class="text-center">{{ $displayNumber++ }}</td>

                                        {{-- Info Resident --}}
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <div>
                                                    <div class="fw-bold text-dark">{{ $residentDetails?->full_name ?? 'Unknown User' }}</div>
                                                    <small class="text-muted">ID: {{ $res->card_id }} | {{ $residentDetails?->gender ?? '-' }}</small>
                                                </div>
                                            </div>
                                        </td>

                                        {{-- Info Kamar --}}
                                        <td class="text-center font-monospace">{{ $residentDetails?->room_number ?? '-' }}</td>

                                        {{-- A. MANAGER AREA (Global Status) --}}
                                        @if ($isManager)
                                            <td class="text-center">
                                                @if ($globalSuspend)
                                                    <form action="{{ route('suspensions.destroy', $globalSuspend->id) }}"
                                                        method="POST"
                                                        onsubmit="return confirm('Cabut blokir global?')">
                                                        @csrf @method('DELETE')
                                                        <button type="submit"
                                                            class="btn btn-danger btn-sm w-100 shadow-sm d-flex align-items-center justify-content-center gap-1">
                                                            <i class="bi bi-unlock-fill"></i> BUKA BLOKIR
                                                        </button>
                                                    </form>
                                                    <small class="text-danger fw-bold" style="font-size: 10px;">
                                                        Until: {{ $globalSuspend->end_date ? $globalSuspend->end_date->format('d M Y') : 'Forever' }}
                                                    </small>
                                                @else
                                                    <button type="button" class="btn btn-soft-success btn-sm w-100"
                                                        data-bs-toggle="modal"
                                                        data-bs-target="#modalGlobal{{ $res->id }}">
                                                        <i class="bi bi-check-circle me-1"></i> ACTIVE
                                                    </button>
                                                @endif
                                            </td>
                                        @endif

                                        {{-- B. ACTION AREA --}}
                                        <td class="text-center">
                                            <div class="d-flex align-items-center justify-content-center list-user-action">
                                                
                                                {{-- Manager: Edit Profile --}}
                                                @if ($isManager)
                                                    <a class="btn btn-sm btn-icon btn-soft-primary"
                                                        href="{{ route('admin.profile.edit', $res->id) }}"
                                                        title="Edit Profile">
                                                        <span class="btn-inner">
                                                            {{-- Icon Edit SVG --}}
                                                            <svg width="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                                <path d="M11.4925 2.78906H7.75349C4.67849 2.78906 2.75049 4.96606 2.75049 8.04806V16.3621C2.75049 19.4441 4.66949 21.6211 7.75349 21.6211H16.5775C19.6625 21.6211 21.5815 19.4441 21.5815 16.3621V12.3341" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"></path>
                                                                <path fill-rule="evenodd" clip-rule="evenodd" d="M8.82812 10.921L16.3011 3.44799C17.2321 2.51799 18.7411 2.51799 19.6721 3.44799L20.8891 4.66499C21.8201 5.59599 21.8201 7.10599 20.8891 8.03599L13.3801 15.545C12.9731 15.952 12.4211 16.181 11.8451 16.181H8.09912L8.19312 12.401C8.20712 11.845 8.43412 11.315 8.82812 10.921Z" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"></path>
                                                                <path d="M15.1655 4.60254L19.7315 9.16854" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"></path>
                                                            </svg>
                                                        </span>
                                                    </a>

                                                {{-- Admin: Suspend Controls --}}
                                                @elseif ($isAdmin)
                                                    @if ($localSuspend)
                                                        {{-- Tombol UNSUSPEND --}}
                                                        <span class="badge rounded-pill bg-soft-danger me-2" title="Berakhir Pada">
                                                            {{ $localSuspend->end_date ? $localSuspend->end_date->format('d M') : '∞' }}
                                                        </span>
                                                        <form action="{{ route('suspensions.destroy', $localSuspend->id) }}"
                                                            method="POST"
                                                            onsubmit="return confirm('Cabut sanksi fasilitas?')">
                                                            @csrf @method('DELETE')
                                                            <button type="submit"
                                                                class="btn btn-sm btn-icon btn-soft-danger"
                                                                title="Cabut Suspend">
                                                                <span class="btn-inner">
                                                                    {{-- Icon Unsuspend SVG --}}
                                                                    <svg width="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" stroke="currentColor">
                                                                        <path d="M19.3248 9.46826C19.3248 9.46826 18.7818 16.2033 18.4668 19.0403C18.3168 20.3953 17.4798 21.1893 16.1088 21.2143C13.4998 21.2613 10.8878 21.2643 8.27979 21.2093C6.96079 21.1823 6.13779 20.3783 5.99079 19.0473C5.67379 16.1853 5.13379 9.46826 5.13379 9.46826" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"></path>
                                                                        <path d="M20.708 6.23975H3.75" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"></path>
                                                                        <path d="M17.4406 6.23973C16.6556 6.23973 15.9796 5.68473 15.8256 4.91573L15.5826 3.69973C15.4326 3.13873 14.9246 2.75073 14.3456 2.75073H10.1126C9.53358 2.75073 9.02558 3.13873 8.87558 3.69973L8.63258 4.91573C8.47858 5.68473 7.80258 6.23973 7.01758 6.23973" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"></path>
                                                                    </svg>
                                                                </span>
                                                            </button>
                                                        </form>
                                                    @else
                                                        {{-- Tombol SUSPEND --}}
                                                        <button type="button"
                                                            class="btn btn-sm btn-icon btn-soft-warning"
                                                            data-bs-toggle="modal"
                                                            data-bs-target="#modalLocal{{ $res->id }}"
                                                            title="Beri Sanksi">
                                                            <span class="btn-inner">
                                                                {{-- Icon Suspend SVG --}}
                                                                <svg width="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                                    <path d="M16.5 12V8.5C16.5 6.01 14.49 4 12 4C9.51 4 7.5 6.01 7.5 8.5V12" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"></path>
                                                                    <path fill-rule="evenodd" clip-rule="evenodd" d="M12 21.25C15.176 21.25 17.75 18.676 17.75 15.5C17.75 12.324 15.176 9.75 12 9.75C8.824 9.75 6.25 12.324 6.25 15.5C6.25 18.676 8.824 21.25 12 21.25Z" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"></path>
                                                                    <path d="M12 16.75V14.25" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"></path>
                                                                </svg>
                                                            </span>
                                                        </button>
                                                    @endif
                                                @endif
                                            </div>
                                        </td>
                                    </tr>

                                    {{-- =======================
                                         MODAL AREA
                                         ======================= --}}

                                    {{-- 1. MODAL GLOBAL (MANAGER) --}}
                                    @if ($isManager && !$globalSuspend)
                                        <div class="modal fade" id="modalGlobal{{ $res->id }}" tabindex="-1" aria-hidden="true">
                                            <div class="modal-dialog modal-dialog-centered">
                                                <div class="modal-content border-0 shadow-lg">
                                                    <div class="modal-header bg-soft-danger border-bottom-0">
                                                        <div class="d-flex align-items-center">
                                                            <span class="avatar-40 rounded-pill bg-danger d-flex align-items-center justify-content-center me-2">
                                                                <i class="bi bi-exclamation-triangle-fill text-white"></i>
                                                            </span>
                                                            <div>
                                                                <h5 class="modal-title fw-bold text-danger mb-0">Global Suspend</h5>
                                                                <small class="text-muted">Target: {{ $residentDetails?->full_name }}</small>
                                                            </div>
                                                        </div>
                                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                    </div>
                                                    <form action="{{ route('suspensions.store') }}" method="POST">
                                                        @csrf
                                                        <div class="modal-body">
                                                            <input type="hidden" name="user_id" value="{{ $res->id }}">
                                                            <input type="hidden" name="facility_id" value="">
                                                            <div class="alert alert-soft-danger d-flex align-items-center mb-4" role="alert">
                                                                <i class="bi bi-info-circle me-3 fs-4"></i>
                                                                <div class="small">Akun akan <b>diblokir total</b> dari seluruh fasilitas asrama.</div>
                                                            </div>
                                                            <div class="form-group mb-3">
                                                                <label class="form-label fw-bold">Alasan Hukuman <span class="text-danger">*</span></label>
                                                                <textarea name="reason" class="form-control" rows="3" required placeholder="Contoh: Pelanggaran berat..."></textarea>
                                                            </div>
                                                            <div class="row">
                                                                <div class="col-6">
                                                                    <label class="form-label fw-bold">Mulai</label>
                                                                    <input type="date" name="start_date" class="form-control" value="{{ date('Y-m-d') }}" required>
                                                                </div>
                                                                <div class="col-6">
                                                                    <label class="form-label fw-bold">Selesai (Opsional)</label>
                                                                    <input type="date" name="end_date" class="form-control">
                                                                    <small class="text-muted" style="font-size: 0.75rem;">Kosong = Permanen</small>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="modal-footer border-top-0 pt-0">
                                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                                                            <button type="submit" class="btn btn-danger">Ya, Bekukan Akun</button>
                                                        </div>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    @endif

                                    {{-- 2. MODAL LOCAL (ADMIN) --}}
                                    @if ($isAdmin)
                                        <div class="modal fade" id="modalLocal{{ $res->id }}" tabindex="-1" aria-hidden="true">
                                            <div class="modal-dialog modal-dialog-centered">
                                                <div class="modal-content border-0 shadow-lg">
                                                    <div class="modal-header bg-soft-warning border-bottom-0">
                                                        <div class="d-flex align-items-center">
                                                            <span class="avatar-40 rounded-pill bg-warning d-flex align-items-center justify-content-center me-2">
                                                                <i class="bi bi-lock-fill text-white"></i>
                                                            </span>
                                                            <div>
                                                                <h5 class="modal-title fw-bold text-dark mb-0">Suspend Fasilitas</h5>
                                                                <small class="text-muted">Target: {{ $residentDetails?->full_name }}</small>
                                                            </div>
                                                        </div>
                                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                    </div>
                                                    <form action="{{ route('suspensions.store') }}" method="POST">
                                                        @csrf
                                                        <div class="modal-body">
                                                            <input type="hidden" name="user_id" value="{{ $res->id }}">
                                                            
                                                            <div class="form-group mb-3">
                                                                <label class="form-label fw-bold">Fasilitas Target</label>
                                                                {{-- Logic Input Fasilitas --}}
                                                                @if ($isManager)
                                                                    {{-- Manager: Dropdown semua fasilitas --}}
                                                                    <select name="facility_id" class="form-select" required>
                                                                        <option value="" selected disabled>-- Pilih Fasilitas --</option>
                                                                        @foreach ($facilities as $facility)
                                                                            <option value="{{ $facility->id }}">{{ $facility->name }}</option>
                                                                        @endforeach
                                                                    </select>
                                                                @else
                                                                    {{-- Admin: Readonly Fasilitas Miliknya --}}
                                                                    <div class="input-group">
                                                                        <span class="input-group-text bg-light border-end-0 text-primary"><i class="bi bi-building"></i></span>
                                                                        <input type="text" class="form-control bg-light border-start-0 fw-bold text-dark" 
                                                                               value="{{ ucfirst($myFacilityName ?: 'No Authority') }}" readonly>
                                                                    </div>
                                                                    <input type="hidden" name="facility_id" value="{{ $myFacilityId }}">
                                                                @endif
                                                            </div>

                                                            <div class="form-group mb-3">
                                                                <label class="form-label fw-bold">Alasan Sanksi</label>
                                                                <textarea name="reason" class="form-control" rows="2" required placeholder="Contoh: Merusak alat..."></textarea>
                                                            </div>

                                                            <div class="row">
                                                                <div class="col-6">
                                                                    <label class="form-label fw-bold">Mulai</label>
                                                                    <input type="date" name="start_date" class="form-control" value="{{ date('Y-m-d') }}" required>
                                                                </div>
                                                                <div class="col-6">
                                                                    <label class="form-label fw-bold">Selesai</label>
                                                                    <input type="date" name="end_date" class="form-control">
                                                                    <small class="text-muted" style="font-size: 0.75rem;">Kosong = Permanen</small>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="modal-footer border-top-0 pt-0">
                                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                                                            <button type="submit" class="btn btn-warning text-dark fw-bold">Simpan Sanksi</button>
                                                        </div>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    @endif

                                @empty
                                    <tr>
                                        <td colspan="6" class="text-center py-4 text-muted">Belum ada data resident.</td>
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
                        "search": "Cari:",
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