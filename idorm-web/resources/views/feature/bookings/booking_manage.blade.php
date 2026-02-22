@extends('layouts.app')

@section('content')
    <div class="row">
        <div class="col-sm-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center flex-wrap">
                    <div class="header-title">
                        <h4 class="card-title mb-0">{{ $title }}</h4>
                    </div>

                    <div class="d-flex gap-3">
                        {{-- Pencarian --}}
                        <form action="{{ url()->current() }}" method="GET" class="d-flex gap-2">
                            <input type="text" name="search" class="form-control" placeholder="Search name/facility..."
                                value="{{ request('search') }}">
                            <button type="submit" class="btn btn-primary btn-sm">Search</button>
                        </form>
                    </div>
                </div>

                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered align-middle">
                            <thead class="bg-light">
                                <tr>
                                    <th class="text-center">No.</th>
                                    <th class="text-center">Date</th>
                                    <th class="text-center">Time Slot</th>
                                    <th class="text-center">Resident Name</th>
                                    <th class="text-center">Facility</th>
                                    <th class="text-center">Status & Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($bookings as $index => $b)
                                    <tr>
                                        <td class="text-center">{{ $bookings->firstItem() + $index }}</td>
                                        <td class="text-center">
                                            {{ \Carbon\Carbon::parse($b->booking_date)->format('d M Y') }}</td>
                                        <td class="text-center">
                                            <span class="badge bg-soft-info text-info">
                                                {{ substr($b->start_time, 0, 5) }} - {{ substr($b->end_time, 0, 5) }}
                                            </span>
                                        </td>
                                        <td>
                                            <div class="fw-bold">{{ $b->user->residentDetails->full_name }}</div>
                                            <small class="text-muted">Room:
                                                {{ $b->user->residentDetails->room_number }}</small>
                                        </td>
                                        <td class="text-center fw-bold">{{ $b->facility->name }}</td>
                                        <td class="text-center">
                                            {{-- LOGIKA AKSES ADMIN PIC --}}
                                            @php
                                                $roleName = Auth::user()->role->role_name;
                                                $statusColor = match (strtoupper($b->status->status_name)) {
                                                    'BOOKED', 'SCHEDULED' => 'primary',
                                                    'APPROVED', 'COMPLETED', 'ACCEPTED' => 'success',
                                                    'REJECTED', 'CANCELED', 'CANCELLED' => 'danger',
                                                    'VERIFYING CLEANLINESS', 'PENDING' => 'warning',
                                                    'AWAITING CLEANLINESS PHOTO' => 'secondary',
                                                    default => 'info',
                                                };
                                            @endphp

                                            {{-- 1. JIKA USER ADALAH PENGHUNI (RESIDENT) --}}
                                            @if ($roleName === 'Resident')
                                                <span class="badge bg-{{ $statusColor }} text-uppercase px-3 py-2">
                                                    {{ $b->status->status_name }}
                                                </span>

                                                {{-- 2. JIKA USER ADALAH MANAGER ATAU ADMIN PIC YANG BERWENANG --}}
                                            @elseif ($roleName === 'Manager' || in_array($b->facility_id, $managedIds))
                                                @if ($b->status->status_name === 'Booked')
                                                    <div class="d-flex gap-2 justify-content-center">
                                                        <form
                                                            action="{{ route('admin.booking.update', [$b->id, 'accept']) }}"
                                                            method="POST">
                                                            @csrf
                                                            <button type="submit"
                                                                class="btn btn-sm btn-success">Accept</button>
                                                        </form>
                                                        <form
                                                            action="{{ route('admin.booking.update', [$b->id, 'decline']) }}"
                                                            method="POST">
                                                            @csrf
                                                            <button type="submit"
                                                                class="btn btn-sm btn-danger">Decline</button>
                                                        </form>
                                                    </div>
                                                @else
                                                    <span class="badge bg-{{ $statusColor }} text-uppercase px-3 py-2">
                                                        {{ $b->status->status_name }}
                                                    </span>
                                                @endif

                                                {{-- 3. JIKA USER ADALAH ADMIN TAPI BUKAN PIC FASILITAS INI --}}
                                            @else
                                                <span class="badge bg-light text-dark">Read Only</span>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="text-center py-4">No booking records found.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                {{-- Pagination Dinamis --}}
                <div class="d-flex justify-content-end p-4">
                    {{ $bookings->links() }}
                </div>
            </div>
        </div>
    </div>
@endsection
