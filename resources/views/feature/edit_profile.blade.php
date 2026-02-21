@php
$roleName = Auth::user()->role->role_name ?? '';

$isRestricted = in_array($roleName, ['Resident', 'Admin']);

if (!isset($facilities)) {
$facilities = \App\Models\Facility::all();
}
@endphp

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

    .no-select {
        user-select: none;
    }
</style>
@endsection

@section('content')
@if ($errors->any())
<div class="alert alert-danger">
    <ul>
        @foreach ($errors->all() as $error)
        <li>{{ $error }}</li>
        @endforeach
    </ul>
</div>
@endif
<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card shadow-sm border-0 mt-4">
            <div class="card-header bg-white py-3 border-bottom d-flex justify-content-between align-items-center">
                {{-- Judul Dinamis berdasarkan nama penghuni --}}
                <h5 class="mb-0 fw-bold text-primary">
                    Edit Profil
                    {{ Auth::user()->residentDetails?->full_name ?? (Auth::user()->adminDetails?->full_name ?? (Auth::user()->managerDetails?->full_name ?? 'User')) }}
                </h5>
            </div>
            <div class="card-body p-4">

                @if (session('success'))
                <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm" role="alert">
                    <i class="bi bi-check-circle-fill me-2"></i> {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
                @endif

                {{-- Form mengarah ke route update dengan ID user --}}
                <form action="{{ route('profile.update', $user->id) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')

                    {{-- Section Foto Profil --}}
                    <div class="text-center mb-4">
                        <input type="file" name="photo" id="photo" accept="image/*"
                            {{ $isRestricted ? 'disabled' : '' }}>
                        @if ($isRestricted)
                        <p class="text-muted small mt-2"><i class="bi bi-info-circle"></i> Foto profil hanya bisa
                            diubah oleh Pengelola.</p>
                        @else
                        <p class="text-muted small mt-2">Klik atau seret foto untuk mengganti profil</p>
                        @endif
                    </div>

                    <div class="row">
                        <div class="mb-3">
                            <label class="form-label fw-bold small">Nama Lengkap</label>
                            <input type="text" name="full_name"
                                class="form-control {{ $isRestricted ? 'bg-light text-muted' : '' }}"
                                value="{{ old('full_name', $user->residentDetails?->full_name ?? ($user->adminDetails?->full_name ?? $user->managerDetails?->full_name)) }}"
                                {{ $isRestricted ? 'readonly' : 'required' }}>

                            @if ($isRestricted)
                            <div class="form-text text-danger" style="font-size: 11px;">
                                <i class="bi bi-lock-fill"></i> Data ini hanya bisa diubah oleh Pengelola.
                            </div>
                            @endif
                        </div>

                        {{-- Email & Kamar selalu readonly untuk semua kecuali SuperAdmin --}}
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold small">Email (Login)</label>
                            <input type="text" class="form-control bg-light" value="{{ $user->email }}" readonly>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold small">Nomor Kamar</label>
                            <input type="text" class="form-control bg-light"
                                value="{{ $user->residentDetails?->room_number ?? 'N/A' }}" readonly>
                        </div>

                        {{-- TAMBAHKAN DI SINI: Khusus untuk Admin (Role 2) --}}
                        @if ($user->role_id == 2)
                        <div class="col-md-12 mb-3">
                            <label class="form-label fw-bold small text-primary">Tugaskan di Fasilitas</label>
                            <select name="facility_id"
                                class="form-select {{ $isRestricted ? 'bg-light text-muted' : '' }}"
                                {{ $isRestricted ? 'disabled' : 'required' }}>
                                <option value="" disabled>-- Pilih Fasilitas --</option>
                                @foreach ($facilities as $fac)
                                <option value="{{ $fac->id }}"
                                    {{ old('facility_id', $user->adminDetails?->facility_id) == $fac->id ? 'selected' : '' }}>
                                    {{ $fac->name }}
                                </option>
                                @endforeach
                            </select>
                            @if ($isRestricted)
                            <input type="hidden" name="facility_id"
                                value="{{ $user->adminDetails?->facility_id }}">
                            <div class="form-text text-danger" style="font-size: 11px;">
                                <i class="bi bi-info-circle"></i> Otoritas fasilitas hanya bisa diubah oleh
                                Manager.
                            </div>
                            @endif
                        </div>
                        @endif

                        {{-- Detail Tambahan - Readonly jika Restricted --}}
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold small">Kelas</label>
                            <input type="text" name="class_name"
                                class="form-control {{ $isRestricted ? 'bg-light' : '' }}"
                                value="{{ old('class_name', $user->residentDetails?->class_name) }}"
                                {{ $isRestricted ? 'readonly' : '' }}>
                        </div>

                        <div class="col-md-6 mb-4">
                            <label class="form-label fw-bold small">Nomor WhatsApp</label>
                            <input type="text" name="phone"
                                class="form-control {{ $isRestricted ? 'bg-light' : '' }}"
                                value="{{ old('phone', $user->residentDetails?->phone) }}"
                                {{ $isRestricted ? 'readonly' : '' }}>
                        </div>

                        {{-- Section Password - SELALU TERBUKA UNTUK SIAPAPUN --}}
                        <hr class="my-4">
                        <h6 class="fw-bold mb-3 text-primary"><i class="bi bi-shield-lock me-2"></i>Keamanan & Reset
                            Password</h6>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold small text-danger">Password Baru</label>
                                <input type="password" name="password" id="password"
                                    class="form-control border-danger border-opacity-25" onpaste="return false;"
                                    oncopy="return false;" placeholder="Isi hanya jika ingin ganti">
                                @error('password')
                                <div class="invalid-feedback">Password yang diberikan berbeda dengan password yang di
                                    konfirmasi</div>
                                @enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold small text-danger">Konfirmasi Password</label>
                                <input type="password" name="password_confirmation" id="password_confirmation"
                                    class="form-control border-danger border-opacity-25" onpaste="return false;"
                                    oncopy="return false;" placeholder="Ketik ulang password">
                            </div>
                        </div>

                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-primary fw-bold py-2 shadow-sm">
                                <i class="bi bi-save me-1"></i> Simpan Perubahan Profil
                            </button>

                            @if (Auth::user()->role_name == 'Resident')
                            {{-- Jika Resident, arahkan ke Dashboard atau Home --}}
                            <a href="{{ url('/') }}" class="btn btn-link text-muted btn-sm">Kembali ke
                                Beranda</a>
                            @else
                            {{-- Jika Admin/Manager, arahkan ke daftar, tapi pastikan routenya ada --}}
                            {{-- Ganti 'admin.resident.index' dengan nama route list penghuni kamu yang benar --}}
                            <a href="{{ url()->previous() }}" class="btn btn-link text-muted btn-sm">Kembali</a>
                            @endif
                        </div>
                </form>
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
        // LOGIKA DISABLE FILEPOND
        disabled: {
            {
                $isRestricted ? 'true' : 'false'
            }
        },
        allowBrowse: {
            {
                $isRestricted ? 'false' : 'true'
            }
        },
        allowDrop: {
            {
                $isRestricted ? 'false' : 'true'
            }
        },

        @if($user - > residentDetails ? - > photo_path)
        files: [{
            source: "{{ asset('storage/' . $user->residentDetails->photo_path) }}"
        }],
        @endif
    });
</script>
@endpush