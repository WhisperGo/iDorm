@extends('penghuni.layouts')

@section('content')
    <div class="card">
        <div class="card-body">
            <h5 class="fw-bold mb-3">Data Diri (Read Only)</h5>
            <div class="row mb-4">
                {{-- Bagian Data Diri Tetap Sama --}}
                <div class="col-md-6 mb-3">
                    <label>Nama Lengkap</label>
                    <input type="text" class="form-control bg-light" value="{{ $user->residentDetails->full_name }}"
                        readonly>
                </div>
                <div class="col-md-6 mb-3">
                    <label>Gender</label>
                    <input type="text" class="form-control bg-light" value="{{ $user->residentDetails->gender }}"
                        readonly>
                </div>
                <div class="col-md-6 mb-3">
                    <label>Kelas</label>
                    <input type="text" class="form-control bg-light" value="{{ $user->residentDetails->class_name }}"
                        readonly>
                </div>
                <div class="col-md-6 mb-3">
                    <label>Nomor Kamar</label>
                    <input type="text" class="form-control bg-light" value="{{ $user->residentDetails->room_number }}"
                        readonly>
                </div>
                <div class="col-md-6 mb-3">
                    <label>Nomor Telepon</label>
                    <input type="text" class="form-control bg-light" value="{{ $user->residentDetails->phone_number }}"
                        readonly>
                </div>
                {{-- ... kolom lainnya ... --}}
            </div>

            <hr>

            <h5 class="fw-bold mb-3 text-primary">Ganti Password</h5>
            <form action="{{ route('profile.password.update') }}" method="POST">
                @csrf
                @method('PUT')

                <div class=" mb-3">
                    <label>Password Saat Ini</label>
                    <div class="input-group">
                        {{-- PASTIKAN atribut value KOSONG atau DIHAPUS --}}
                        <input type="password" name="current_password" id="current_password" class="form-control"
                            placeholder="Masukkan password lama kamu" required>
                        <span class="input-group-text" style="cursor: pointer;"
                            onclick="togglePassword('current_password', this)">
                            <i class="bi bi-eye"></i>
                        </span>
                    </div>
                </div>

                <div class="mb-3">
                    <label>Password Baru</label>
                    <div class="input-group">
                        <input type="password" name="password" id="password" class="form-control" oncopy="return false"
                            oncut="return false" onpaste="return false" required>
                        <span class="input-group-text" style="cursor: pointer;" onclick="togglePassword('password', this)">
                            <i class="bi bi-eye"></i>
                        </span>
                    </div>
                </div>

                <div class="mb-3">
                    <label>Konfirmasi Password Baru</label>
                    <div class="input-group">
                        <input type="password" name="password_confirmation" id="password_confirmation" class="form-control"
                            oncopy="return false" oncut="return false" onpaste="return false" required>
                        <span class="input-group-text" style="cursor: pointer;"
                            onclick="togglePassword('password_confirmation', this)">
                            <i class="bi bi-eye"></i>
                        </span>
                    </div>
                </div>

                <button type="submit" class="btn btn-primary w-100">Simpan Password Baru</button>
            </form>
        </div>
    @endsection

    @push('scripts')
        <script>
            function togglePassword(inputId, el) {
                const passwordInput = document.getElementById(inputId);
                const icon = el.querySelector('i');

                if (passwordInput.type === "password") {
                    passwordInput.type = "text";
                    icon.classList.replace('bi-eye', 'bi-eye-slash');
                } else {
                    passwordInput.type = "password";
                    icon.classList.replace('bi-eye-slash', 'bi-eye');
                }
            }
        </script>
    @endpush
