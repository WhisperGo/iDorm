@extends('layouts.app')
@section('content')
<div class="card shadow-sm border-0">
    <div class="card-header d-flex justify-content-between border-0 bg-white">
        <h4 class="fw-bold text-warning">Facility Admin Data</h4>
        <div id="live-filter-container"></div>
    </div>
    <div class="card-body">
        <table id="datatable" class="table table-hover align-middle">
            <thead class="table-dark">
                <tr>
                    <th>No.</th>
                    <th>Nama Admin</th>
                    <th>Fasilitas yang Dikelola</th>
                    <th class="text-center">Status Akun</th>
                    <th class="text-center">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @foreach($admins as $adm)
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td>
                        <div class="fw-bold">{{ $adm->residentDetails?->full_name ?? $adm->name }}</div>
                        <small>{{ $adm->email }}</small>
                    </td>
                    <td>
                        {{-- Menampilkan fasilitas yang dikelola dari relasi managedFacilities --}}
                        @foreach($adm->managedFacilities as $facility)
                            <span class="badge bg-info">{{ $facility->name }}</span>
                        @endforeach
                    </td>
                    <td class="text-center">
                        <span class="badge bg-success">Active</span>
                    </td>
                    <td class="text-center">
                        <a href="{{ route('admin.profile.edit', $adm->id) }}" class="btn btn-sm btn-outline-warning"><i class="bi bi-gear"></i></a>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection