@extends('admin.layouts')

@section('content')
    <div class="row">
        <div class="col-sm-12">
            <div class="card shadow-sm">
                <div class="card-header">
                    <div class="d-flex justify-content-between align-items-center flex-wrap">
                        <div class="header-title">
                            <h4 class="card-title mb-0 fw-bold">Resident Data</h4>
                        </div>

                        {{-- Update Search Form --}}
                        <form action="{{ route('admin.resident') }}" method="GET" class="d-flex align-items-center gap-2">
                            <span class="text-secondary fw-medium">Search:</span>
                            <input type="text" name="search" class="form-control" style="width: 250px;"
                                placeholder="Name or Room..." value="{{ request('search') }}">
                        </form>
                    </div>
                </div>

                <div class="card-body mt-0">
                    <div class="table-responsive">
                        <table class="table table-bordered align-middle">
                            <thead class="bg-light">
                                <tr>
                                    <th class="text-center" width="5%">No.</th>
                                    <th class="text-center">Full Name</th>
                                    <th class="text-center">Gender</th>
                                    <th class="text-center" width="10%">Room Number</th>
                                    <th class="text-center">Account Status</th>
                                    <th class="text-center" width="15%">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($residents as $index => $res)
                                    <tr>
                                        <td class="text-center">{{ $residents->firstItem() + $index }}</td>
                                        <td>
                                            <div class="fw-bold">{{ $res->residentDetails->full_name }}</div>
                                            <small class="text-muted">ID: {{ $res->card_id }}</small>
                                        </td>
                                        <td class="text-center">{{ $res->residentDetails->gender == 'Male' ? 'L' : 'P' }}
                                        </td>
                                        <td class="text-center font-monospace">{{ $res->residentDetails->room_number }}</td>
                                        <td class="text-center">
                                            <span
                                                class="badge {{ $res->account_status == 'active' ? 'bg-success' : 'bg-danger' }}">
                                                {{ strtoupper($res->account_status) }}
                                            </span>
                                        </td>
                                        <td class="text-center">
                                            <div class="d-flex align-items-center gap-2">
                                                {{-- Tombol Freeze/Unfreeze --}}
                                                <form action="{{ route('admin.resident.freeze', $res->id) }}" method="POST"
                                                    class="m-0" {{-- Menghilangkan margin bawaan form --}}
                                                    onsubmit="return confirm('Apakah Anda yakin ingin mengubah status akun ini?')">
                                                    @csrf
                                                    <button type="submit"
                                                        class="btn btn-sm {{ $res->account_status == 'active' ? 'btn-outline-danger' : 'btn-outline-success' }} d-flex align-items-center justify-content-center"
                                                        style="width: 75px; height: 45px;" {{-- Lebar dan tinggi dikunci agar sama --}}
                                                        title="{{ $res->account_status == 'active' ? 'Freeze Account' : 'Unfreeze Account' }}">
                                                        <div class="d-flex flex-column align-items-center lh-1"
                                                            style="font-size: 11px; font-weight:700">
                                                            <span>{{ $res->account_status == 'active' ? 'Freeze' : 'Unfreeze' }}</span>
                                                            <span>Account</span>
                                                        </div>
                                                    </button>
                                                </form>

                                                {{-- Tombol Edit --}}
                                                <a href="{{ route('admin.profile.edit', $res->id) }}"
                                                    class="btn btn-sm btn-outline-primary d-flex align-items-center justify-content-center"
                                                    style="width: 75px; height: 45px;" {{-- Disamakan dengan tombol sebelah --}}
                                                    title="Edit Data">
                                                    <div class="d-flex flex-column align-items-center lh-1"
                                                        style="font-size: 11px; font-weight:700">
                                                        <span>Edit</span>
                                                        <span>Account</span>
                                                    </div>
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="text-center py-4 text-muted">No resident data found.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    {{-- Pagination --}}
                    <div class="mt-3">
                        {{ $residents->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
