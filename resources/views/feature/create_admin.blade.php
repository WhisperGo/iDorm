@extends('layouts.app')

@section('content')
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow-sm border-0 mt-4">
                <div class="card-header bg-white py-3 border-bottom">
                    <h5 class="mb-0 fw-bold text-primary"><i class="bi bi-person-badge me-2"></i>Tambah Admin Fasilitas</h5>
                </div>
                <div class="card-body p-4">
                    <form action="{{ route('manager.admins.store') }}" method="POST">
                        @csrf
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold small">Card ID (4 Digit)</label>
                                <input type="text" name="card_id" class="form-control" maxlength="4"
                                    placeholder="Contoh: 2001" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold small">Nama Lengkap</label>
                                <input type="text" name="full_name" class="form-control" placeholder="Nama Lengkap"
                                    required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold small">Gender</label>
                                <select name="gender" class="form-select" required>
                                    <option value="" selected disabled>-- Pilih --</option>
                                    <option value="Male">Laki-laki</option>
                                    <option value="Female">Perempuan</option>
                                </select>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold small">Tugaskan di Fasilitas</label>
                                <select name="facility_id" class="form-select" required>
                                    <option value="" selected disabled>-- Pilih Fasilitas --</option>
                                    @foreach ($facilities as $fac)
                                        <option value="{{ $fac->id }}">{{ $fac->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold small">Nomor WhatsApp</label>
                                <input type="text" name="phone_number" class="form-control" placeholder="0812xxxx">
                            </div>
                            {{-- Letakkan di dalam baris yang sama dengan Gender atau Fasilitas --}}
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold small">Kelas</label>
                                <input type="text" name="class_name"
                                    class="form-control @error('class_name') is-invalid @enderror"
                                    placeholder="Contoh: PPBP 64" required>
                                @error('class_name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold small">Nomor Kamar</label>
                                <input type="text" name="room_number" class="form-control"
                                    value="{{ old('room_number') }}" placeholder="Contoh: A.101" required>
                            </div>
                        </div>

                        <div class="alert alert-soft-primary d-flex align-items-center mt-3">
                            <i class="bi bi-info-circle-fill me-2"></i>
                            <div class="small">Password otomatis: <strong>password</strong></div>
                        </div>

                        <div class="d-grid gap-2 mt-4">
                            <button type="submit" class="btn btn-primary fw-bold">Simpan Data Admin</button>
                            <a href="{{ route('manager.admins.index') }}" class="btn btn-link text-muted btn-sm">Batal</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
