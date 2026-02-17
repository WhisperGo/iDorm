@extends('layouts.app')

@section('content')
    <div class="row">
        <div class="col-sm-12">
            <div class="card shadow-sm">
                <div class="card-header">
                    <div class="d-flex justify-content-between align-items-center flex-wrap">
                        <div class="header-title">
                            <h4 class="card-title mb-0 fw-bold text-primary">Facility Admin Management</h4>
                            <small class="text-muted">Mengelola data para pengelola fasilitas asrama</small>
                        </div>
                        <div id="live-filter-container"></div>
                    </div>
                </div>

                <div class="card-body mt-0">
                    <div class="table-responsive">
                        <table id="datatable" class="table table-bordered align-middle">
                            <thead class="bg-primary text-white">
                                <tr>
                                    <th class="text-center" width="5%">No.</th>
                                    <th>Admin Info</th>
                                    <th>Managed Facility</th>
                                    <th class="text-center">Gender</th>
                                    <th class="text-center" width="10%">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($admins as $adm)
                                    <tr>
                                        <td class="text-center">{{ $loop->iteration }}</td>
                                        <td>
                                            <div class="fw-bold text-dark">
                                                {{ $adm->adminDetails?->full_name ?? $adm->name }}</div>
                                            <small class="text-muted">{{ $adm->email }}</small>
                                        </td>
                                        <td>
                                            {{-- Kita cek lewat adminDetails --}}
                                            @if ($adm->adminDetails && $adm->adminDetails->facility)
                                                <span class="badge bg-info">
                                                    {{ $adm->adminDetails->facility->name }}
                                                </span>
                                            @else
                                                <span class="badge bg-secondary">Belum Ditugaskan</span>
                                            @endif
                                        </td>
                                        <td class="text-center">{{ $adm->adminDetails?->gender ?? '-' }}</td>
                                        <td class="text-center">
                                            <a class="btn btn-sm btn-icon btn-soft-primary"
                                                href="{{ route('admin.profile.edit', $adm->id) }}" title="Edit Admin">
                                                <i class="bi bi-gear-fill"></i>
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
