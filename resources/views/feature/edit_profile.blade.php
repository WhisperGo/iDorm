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
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between">
                    <div class="header-title">
                        <h4 class="card-title">
                            Edit Profil
                            {{ Auth::user()->residentDetails?->full_name ?? (Auth::user()->adminDetails?->full_name ?? (Auth::user()->managerDetails?->full_name ?? 'User')) }}
                        </h4>
                    </div>
                </div>
                <div class="card-body">
                    @if (session('success'))
                        <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm" role="alert">
                            <i class="bi bi-check-circle-fill me-2"></i> {{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif

                    <div class="acc-edit">
                        <form action="{{ route('profile.update', $user->id) }}" method="POST"
                            enctype="multipart/form-data">
                            @csrf
                            @method('PUT')

                            {{-- Section Foto Profil --}}
                            <div class="text-center mb-4">
                                <input type="file" name="photo" id="photo" accept="image/*"
                                    {{ $isRestricted ? 'disabled' : '' }}>
                                @if ($isRestricted)
                                    <p class="text-muted small mt-2"><i class="bi bi-info-circle"></i> Foto profil hanya
                                        bisa
                                        diubah oleh Pengelola.</p>
                                @else
                                    <p class="text-muted small mt-2">Klik atau seret foto untuk mengganti profil</p>
                                @endif
                            </div>

                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group mb-3">
                                        <label for="full_name" class="form-label">Nama Lengkap:</label>
                                        <input type="text" id="full_name" name="full_name"
                                            class="form-control {{ $isRestricted ? 'bg-light text-muted' : '' }}"
                                            value="{{ old('full_name', $user->residentDetails?->full_name ?? ($user->adminDetails?->full_name ?? $user->managerDetails?->full_name)) }}"
                                            {{ $isRestricted ? 'readonly' : 'required' }}>

                                        @if ($isRestricted)
                                            <div class="form-text text-danger mt-1" style="font-size: 11px;">
                                                <i class="bi bi-lock-fill"></i> Data ini hanya bisa diubah oleh Pengelola.
                                            </div>
                                        @endif
                                    </div>
                                </div>

                                {{-- Email & Kamar selalu readonly untuk semua kecuali SuperAdmin --}}
                                <div class="col-md-6 mb-3">
                                    <div class="form-group">
                                        <label for="email" class="form-label">Card ID:</label>
                                        <input type="text" id="email" class="form-control bg-light"
                                            value="{{ $user->card_id }}" readonly>
                                    </div>
                                </div>

                                <div class="col-md-6 mb-3">
                                    <div class="form-group">
                                        <label for="room_number" class="form-label">Nomor Kamar:</label>
                                        <input type="text" id="room_number" class="form-control bg-light"
                                            value="{{ $user->residentDetails?->room_number ?? 'N/A' }}" readonly>
                                    </div>
                                </div>

                                {{-- Detail Tambahan - Readonly jika Restricted --}}
                                <div class="col-md-6 mb-3">
                                    <div class="form-group">
                                        <label for="class_name" class="form-label">Kelas:</label>
                                        <input type="text" id="class_name" name="class_name"
                                            class="form-control {{ $isRestricted ? 'bg-light' : '' }}"
                                            value="{{ old('class_name', $user->residentDetails?->class_name) }}"
                                            {{ $isRestricted ? 'readonly' : '' }}>
                                    </div>
                                </div>

                                <div class="col-md-6 mb-4">
                                    <div class="form-group">
                                        <label for="phone_number" class="form-label">Nomor Telepon:</label>
                                        <div class="input-group">
                                            <span
                                                class="input-group-text border-0 {{ $isRestricted ? 'bg-light' : '' }}">(+62)</span>
                                            <input type="text" id="phone_number" name="phone_number"
                                                class="form-control {{ $isRestricted ? 'bg-light' : '' }}"
                                                value="{{ old('phone_number', $user->residentDetails?->phone_number ?? ($user->adminDetails?->phone_number ?? $user->managerDetails?->phone_number)) }}"
                                                {{ $isRestricted ? 'readonly' : '' }} placeholder="812xxxx">
                                        </div>
                                    </div>
                                </div>

                                {{-- Khusus untuk Admin (Role 2) --}}
                                @if ($user->role_id == 2)
                                    <div class="col-md-12 mb-3">
                                        <div class="form-group">
                                            <label for="facility_id" class="form-label text-primary">Tugaskan di
                                                Fasilitas:</label>
                                            <select id="facility_id" name="facility_id"
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
                                                <div class="form-text text-danger mt-1" style="font-size: 11px;">
                                                    <i class="bi bi-info-circle"></i> Otoritas fasilitas hanya bisa diubah
                                                    oleh
                                                    Manager.
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                @endif
                            </div>

                            {{-- Section Password - SELALU TERBUKA UNTUK SIAPAPUN --}}
                            <hr class="mb-4">
                            <h5 class="mb-3 text-primary"><i class="bi bi-shield-lock me-2"></i>Keamanan & Reset
                                Password</h5>
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <div class="form-group">
                                        <label for="password" class="form-label text-danger">Password Baru:</label>
                                        <input type="password" name="password" id="password"
                                            class="form-control border-danger border-opacity-25" onpaste="return false;"
                                            oncopy="return false;" placeholder="Isi hanya jika ingin ganti">
                                        @error('password')
                                            <div class="invalid-feedback d-block">Password yang diberikan berbeda dengan
                                                password yang di
                                                konfirmasi</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <div class="form-group">
                                        <label for="password_confirmation" class="form-label text-danger">Konfirmasi
                                            Password:</label>
                                        <input type="password" name="password_confirmation" id="password_confirmation"
                                            class="form-control border-danger border-opacity-25" onpaste="return false;"
                                            oncopy="return false;" placeholder="Ketik ulang password">
                                    </div>
                                </div>
                            </div>

                            <div class="mt-4">
                                <button type="submit" class="btn btn-primary">
                                    Simpan Perubahan
                                </button>

                                @if (Auth::user()->role_name == 'Resident')
                                    <a href="{{ url('/') }}" class="btn btn-soft-danger ms-2">Kembali ke
                                        Beranda</a>
                                @else
                                    <a href="{{ url()->previous() }}" class="btn btn-soft-danger ms-2">Kembali</a>
                                @endif
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
            // LOGIKA DISABLE FILEPOND
            disabled: {{ $isRestricted ? 'true' : 'false' }},
            allowBrowse: {{ $isRestricted ? 'false' : 'true' }},
            allowDrop: {{ $isRestricted ? 'false' : 'true' }},

            @if ($user->role_id == 2 && $user->adminDetails?->photo_path)
                files: [{
                    source: "{{ asset('storage/' . $user->adminDetails->photo_path) }}"
                }],
            @elseif ($user->role_id == 3 && $user->residentDetails?->photo_path)
                files: [{
                        source: "{{ asset('storage/' . $user->residentDetails->photo_path) }}"
                    }],
            @elseif ($user->role_id == 1 && $user->managerDetails?->photo_path)
                files: [{
                        source: "{{ asset('storage/' . $user->managerDetails->photo_path) }}"
                    }],
            @endif
        });
    </script>
@endpush
