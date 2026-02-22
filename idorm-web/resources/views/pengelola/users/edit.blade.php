@extends('layouts.app')

@section('styles')
    {{-- FilePond CSS --}}
    <link href="https://unpkg.com/filepond/dist/filepond.css" rel="stylesheet">
    <link href="https://unpkg.com/filepond-plugin-image-preview/dist/filepond-plugin-image-preview.css" rel="stylesheet">
    <style>
        .filepond--root {
            width: 150px;
            height: 150px;
            margin: 0 auto;
        }

        .filepond--panel-root {
            background-color: transparent;
            border: 2px dashed #dbdbdb;
            border-radius: 50% !important;
        }

        .filepond--image-preview-wrapper {
            -webkit-mask-image: -webkit-radial-gradient(white, black);
            mask-image: radial-gradient(white, black);
            border-radius: 50% !important;
        }
    </style>
@endsection
@section('content')
    <div class="row">
        <div class="col-xl-9 col-lg-8 mx-auto">
            <div class="card shadow-sm border-0">
                <div class="card-header d-flex justify-content-between">
                    <div class="header-title">
                        <h4 class="card-title fw-bold">Edit User Profile</h4>
                        {{-- <p class="text-muted mb-0">Mengubah informasi akun untuk <strong>{{ $user->name }}</strong></p> --}}
                    </div>
                    <a href="{{ $user->role_id == 2 ? route('manager.admins.index') : route('pengelola.resident') }}"
                        class="btn btn-sm btn-soft-danger">
                        <i class="bi bi-arrow-left me-1"></i> Kembali
                    </a>
                </div>
                <div class="card-body">
                    <div class="acc-edit">
                        <form action="{{ route('admin.users.update', $user->id) }}" method="POST"
                            enctype="multipart/form-data">
                            @csrf
                            @method('PUT')
                            {{-- Section Foto Profil --}}
                            <div class="text-center mb-4">
                                <input type="file" name="photo" id="photo" accept="image/*">
                                <p class="text-muted small mt-2">Klik atau seret foto untuk mengganti profil user</p>
                            </div>

                            {{-- Detail Profil --}}
                            <div class="row">
                                <div class="col-md-12 mb-3">
                                    <div class="form-group">
                                        <label class="form-label fw-bold">Nama Lengkap</label>
                                        <input type="text" name="full_name" class="form-control"
                                            value="{{ old('full_name', $user->residentDetails?->full_name ?? $user->adminDetails?->full_name) }}">
                                    </div>
                                </div>

                                <div class="col-md-6 mb-3">
                                    <div class="form-group">
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
                                </div>

                                <div class="col-md-6 mb-3">
                                    <div class="form-group">
                                        <label class="form-label fw-bold">Nomor Kamar</label>
                                        <input type="text" name="room_number" class="form-control font-monospace"
                                            value="{{ old('room_number', $user->residentDetails?->room_number ?? $user->adminDetails?->room_number) }}"
                                            placeholder="Contoh: A.102">
                                    </div>
                                </div>

                                @if ($user->role_id == 2)
                                    <div class="col-md-6 mb-3">
                                        <div class="form-group">
                                            <label class="form-label fw-bold">Kewenangan Fasilitas</label>
                                            <select name="facility_id" class="form-select" required>
                                                <option value="" disabled>-- Pilih Fasilitas --</option>
                                                @foreach ($facilities as $fac)
                                                    <option value="{{ $fac->id }}"
                                                        {{ old('facility_id', $user->adminDetails?->facility_id) == $fac->id ? 'selected' : '' }}>
                                                        {{ $fac->name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                @else
                                    <div class="col-md-6 mb-3">
                                        <div class="form-group">
                                            <label class="form-label fw-bold">Nomor Telepon</label>
                                            <div class="input-group">
                                                <span class="input-group-text border-0">(+62)</span>
                                                <input type="text" name="phone_number" class="form-control"
                                                    value="{{ old('phone_number', $user->residentDetails?->phone_number ?? $user->adminDetails?->phone_number) }}"
                                                    placeholder="812xxxx">
                                            </div>
                                        </div>
                                    </div>
                                @endif

                                <div class="col-md-6 mb-3">
                                    <div class="form-group">
                                        <label class="form-label fw-bold">Role Akun</label>
                                        <input type="text" class="form-control bg-light"
                                            value="{{ strtoupper($user->role->role_name) }}" readonly>
                                        <small class="text-muted">Role tidak dapat diubah di sini.</small>
                                    </div>
                                </div>
                            </div>

                            <hr class="my-4">

                            <div class="mt-4 text-end">
                                <button type="reset" class="btn btn-soft-danger me-2">Reset</button>
                                <button type="submit" class="btn btn-primary px-4">Simpan Perubahan</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    {{-- FilePond JS & Plugins --}}
    <script src="https://unpkg.com/filepond-plugin-image-preview/dist/filepond-plugin-image-preview.js"></script>
    <script src="https://unpkg.com/filepond-plugin-image-crop/dist/filepond-plugin-image-crop.js"></script>
    <script src="https://unpkg.com/filepond-plugin-file-validate-type/dist/filepond-plugin-file-validate-type.js"></script>
    <script src="https://unpkg.com/filepond/dist/filepond.js"></script>

    <script>
        FilePond.registerPlugin(FilePondPluginImagePreview, FilePondPluginFileValidateType);

        const pond = FilePond.create(document.querySelector('#photo'), {
            labelIdle: '<i class="bi bi-camera" style="font-size: 2rem;"></i>',
            imagePreviewHeight: 150,
            stylePanelLayout: 'compact circle',
            storeAsFile: true,

            @if ($user->role_id == 2 && $user->adminDetails?->photo_path)
                files: [{
                    source: "{{ asset('storage/' . $user->adminDetails->photo_path) }}"
                }],
            @elseif ($user->role_id == 3 && $user->residentDetails?->photo_path)
                files: [{
                        source: "{{ asset('storage/' . $user->residentDetails->photo_path) }}"
                    }],
            @endif
        });
    </script>
@endpush
