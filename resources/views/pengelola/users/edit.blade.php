@extends('layouts.app')

@section('content')
    <div class="row">
        <div class="col-xl-9 col-lg-8 mx-auto">
            <div class="card shadow-sm border-0">
                <div class="card-header d-flex justify-content-between">
                    <div class="header-title">
                        <h4 class="card-title fw-bold">Edit User Profile</h4>
                        {{-- <p class="text-muted mb-0">Mengubah informasi akun untuk <strong>{{ $user->name }}</strong></p> --}}
                    </div>
                    <a href="{{ $user->role_id == 2 ? route('manager.admins.index') : route('pengelola.resident') }}" <i
                        class="bi bi-arrow-left me-1"></i> Kembali
                    </a>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.users.update', $user->id) }}" method="POST">
                        @csrf
                        @method('PUT')
                        {{-- Detail Profil --}}
                        <div class="col-md-12 mb-3">
                            <label class="form-label fw-bold">Nama Lengkap</label>
                            <input type="text" name="full_name" class="form-control"
                                value="{{ old('full_name', $user->residentDetails?->full_name ?? $user->adminDetails?->full_name) }}">
                        </div>

                        <div class="col-md-12 mb-3">
                            <label class="form-label fw-bold">Nomor WhatsApp/Telepon</label>
                            <input type="text" name="phone_number" class="form-control"
                                value="{{ old('phone_number', $user->residentDetails?->phone_number ?? $user->adminDetails?->phone_number) }}"
                                placeholder="0812xxxx">
                        </div>

                        <div class="col-md-12 mb-3">
                            <label class="form-label fw-bold">Nomor Kamar</label>
                            <input type="text" name="room_number" class="form-control font-monospace"
                                value="{{ old('room_number', $user->residentDetails?->room_number ?? $user->adminDetails->room_number) }}"
                                placeholder="Contoh: A.102">
                        </div>

                        <div class="col-md-12 mb-3">
                            <label class="form-label fw-bold">Gender</label>
                            <select name="gender" class="form-select" required>
                                <option value="Male"
                                    {{ old('gender', $user->role_id == 2 ? $user->adminDetails?->gender : $user->residentDetails?->gender) == 'Male' ? 'selected' : '' }}>
                                    Laki-laki
                                </option>

                                <option value="Female"
                                    {{ old('gender', $user->role_id == 2 ? $user->adminDetails?->gender : $user->residentDetails?->gender) == 'Female' ? 'selected' : '' }}>
                                    Perempuan
                                </option>
                            </select>
                        </div>

                        <div class="col-md-12 mb-3">
                            <label class="form-label fw-bold">Role Akun</label>
                            <input type="text" class="form-control bg-light"
                                value="{{ strtoupper($user->role->role_name) }}" readonly>
                            <small class="text-muted">Role tidak dapat diubah di halaman ini.</small>
                        </div>

                        <hr class="my-3 text-muted">

                        <div class="mt-4 text-end">
                            <button type="reset" class="btn btn-soft-danger me-2">Reset</button>
                            <button type="submit" class="btn btn-primary px-4">Simpan Perubahan</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
