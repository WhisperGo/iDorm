{{-- Sama dengan view index sebelumnya, tapi fokus ke kolom kamar --}}
@extends('layouts.app')
@section('content')
    <div class="card shadow-sm border-0">
        <div class="card-header d-flex justify-content-between">
            <h4 class="fw-bold text-primary">Resident Data</h4>
            <div id="live-filter-container"></div>
        </div>
        <div class="card-body">
            <table id="datatable" class="table table-bordered align-middle">
                <thead class="bg-light">
                    <tr>
                        <th>No.</th>
                        <th>Nama Penghuni</th>
                        <th class="text-center">Kamar</th>
                        <th class="text-center">Gender</th>
                        <th class="text-center">No. Telepon</th>
                        <th class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($residents as $res)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>
                                <div class="fw-bold">{{ $res->residentDetails?->full_name ?? $res->name }}</div>
                                <small class="text-muted">{{ $res->email }}</small>
                            </td>
                            <td class="text-center">{{ $res->residentDetails?->room_number ?? '-' }}</td>
                            <td class="text-center">{{ $res->residentDetails?->gender ?? '-' }}</td>
                            <td class="text-center">{{ $res->residentDetails?->phone_number ?? '-' }}</td>
                            <td class="text-center">
                                <a href="{{ route('admin.profile.edit', $res->id) }}" class="btn btn-sm btn-soft-primary"><i
                                        class="bi bi-pencil"></i></a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
@endsection
