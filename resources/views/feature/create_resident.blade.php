@extends('layouts.app')

@section('content')
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow-sm border-0 mt-4">
                <div class="card-header bg-white py-3 border-bottom">
                    <h5 class="mb-0 fw-bold text-primary"><i class="bi bi-person-plus-fill me-2"></i>Tambah Resident Baru</h5>
                </div>
                <div class="card-body p-4">
                    <form action="{{ route('manager.residents.store') }}" method="POST">
                        @csrf

                        <div class="row">
                            {{-- Card ID --}}
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold small">Card ID (4 Digit)</label>
                                <input type="text" name="card_id"
                                    class="form-control @error('card_id') is-invalid @enderror" value="{{ old('card_id') }}"
                                    maxlength="4" placeholder="Contoh: 1001" required>
                                @error('card_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            {{-- Nama Lengkap --}}
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold small">Nama Lengkap</label>
                                <input type="text" name="full_name" class="form-control" value="{{ old('full_name') }}"
                                    placeholder="Masukkan Nama Lengkap" required>
                            </div>

                            {{-- Gender --}}
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold small">Gender</label>
                                <select name="gender" class="form-select" required>
                                    <option value="" selected disabled>-- Pilih Gender --</option>
                                    <option value="Male" {{ old('gender') == 'Male' ? 'selected' : '' }}>Laki-laki
                                    </option>
                                    <option value="Female" {{ old('gender') == 'Female' ? 'selected' : '' }}>Perempuan
                                    </option>
                                </select>
                            </div>

                            {{-- Nomor Kamar --}}
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold small">Nomor Kamar</label>
                                <input type="text" name="room_number" class="form-control"
                                    value="{{ old('room_number') }}" placeholder="Contoh: A.101" required>
                            </div>

                            {{-- Kelas / Class Name --}}
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold small">Kelas</label>
                                <input type="text" name="class_name" class="form-control" value="{{ old('class_name') }}"
                                    placeholder="Contoh: PPTI 35" required>
                            </div>

                            {{-- WhatsApp --}}
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold small">Nomor WhatsApp</label>
                                <input type="text" name="phone_number" class="form-control"
                                    value="{{ old('phone_number') }}" placeholder="0812xxxx">
                            </div>
                        </div>

                        <div class="alert alert-soft-primary d-flex align-items-center mt-3" role="alert">
                            <i class="bi bi-info-circle-fill me-2"></i>
                            <div class="small">
                                Password akun akan otomatis disetel menjadi: <strong>password</strong>
                            </div>
                        </div>

                        <div class="d-grid gap-2 mt-4">
                            <button type="submit" class="btn btn-primary fw-bold py-2 shadow-sm">
                                <i class="bi bi-save me-1"></i> Simpan Data Resident
                            </button>
                            <a href="{{ route('pengelola.resident') }}" class="btn btn-link text-muted btn-sm">Batal</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
