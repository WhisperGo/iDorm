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
        .filepond--item-panel {
            background-color: transparent !important;
        }
    </style>
@endsection

@section('content')
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow-sm border-0 mt-4">
                <div class="card-header bg-white py-3">
                    {{-- Judul Dinamis berdasarkan nama penghuni --}}
                    <h5 class="mb-0 fw-bold text-primary">
                        Edit Profil {{ Auth::user()->residentDetails?->full_name ?? 'Penghuni' }}
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
                    <form action="{{ route('admin.profile.update', $user->id) }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')

                        {{-- Section Foto Profil --}}
                        <div class="text-center mb-4">
                            <input type="file" name="photo" id="photo" accept="image/*">
                            <p class="text-muted small mt-2">Seret foto untuk mengganti foto profil {{ $user->name }}</p>
                            @error('photo')
                                <small class="text-danger">{{ $message }}</small>
                            @enderror
                        </div>

                        {{-- Nama Lengkap (Menggunakan $user, bukan Auth::user) --}}
                        <div class="mb-3">
                            <label class="form-label fw-bold small">Nama Lengkap</label>
                            <input type="text" name="full_name"
                                class="form-control @error('full_name') is-invalid @enderror"
                                value="{{ old('full_name', $user->residentDetails?->full_name) }}" required>
                            @error('full_name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold small">Email (Login)</label>
                                <input type="text" class="form-control bg-light" value="{{ $user->email }}" disabled>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold small">Nomor Kamar</label>
                                <input type="text" class="form-control bg-light"
                                    value="{{ $user->residentDetails?->room_number ?? 'N/A' }}" disabled>
                                <small class="text-muted" style="font-size: 10px;">Ganti di manajemen kamar untuk ubah nomor.</small>
                            </div>
                        </div>

                        {{-- Kelas (Wajib) --}}
                        <div class="mb-3">
                            <label class="form-label fw-bold small text-danger">Kelas (Wajib untuk Otomasi) *</label>
                            <input type="text" name="class_name"
                                class="form-control @error('class_name') is-invalid @enderror"
                                value="{{ old('class_name', $user->residentDetails?->class_name) }}"
                                placeholder="Contoh: TI-2A" required>
                            @error('class_name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Nomor WhatsApp --}}
                        <div class="mb-4">
                            <label class="form-label fw-bold small">Nomor WhatsApp/HP</label>
                            <input type="text" name="phone"
                                class="form-control @error('phone') is-invalid @enderror"
                                value="{{ old('phone', $user->residentDetails?->phone) }}"
                                placeholder="Contoh: 0812xxx">
                            @error('phone')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-primary fw-bold py-2 shadow-sm">
                                <i class="bi bi-save me-1"></i> Simpan Perubahan Profil
                            </button>
                            <a href="{{ route('admin.resident.index') }}" class="btn btn-link text-muted btn-sm">Kembali ke Daftar</a>
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
        FilePond.registerPlugin(
            FilePondPluginImagePreview, 
            FilePondPluginFileValidateType,
            FilePondPluginImageCrop
        );

        const inputElement = document.querySelector('#photo');
        const pond = FilePond.create(inputElement, {
            labelIdle: '<i class="bi bi-camera" style="font-size: 2rem;"></i>',
            imagePreviewHeight: 150,
            imageCropAspectRatio: '1:1', // Memastikan hasil crop kotak agar pas di lingkaran
            stylePanelLayout: 'compact circle',
            styleLoadIndicatorPosition: 'center bottom',
            styleButtonRemoveItemPosition: 'center bottom',
            storeAsFile: true,
            acceptedFileTypes: ['image/*'],
            @if ($user->residentDetails?->photo_path)
                files: [{
                    source: "{{ asset('storage/' . $user->residentDetails->photo_path) }}",
                }],
            @endif
        });
    </script>
@endpush