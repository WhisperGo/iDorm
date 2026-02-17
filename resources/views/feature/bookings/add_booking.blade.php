@extends('layouts.app')

@section('content')
    <div class="row justify-content-center">
        <div class="col-sm-10">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title mb-0 fw-bold">Booking Fasilitas</h4>
                </div>

                <div class="card-body">
                    {{-- CEK GLOBAL SUSPEND --}}
                    @if (isset($globalSuspend))
                        <div class="text-center py-4 animate-fade-in">
                            <div class="alert alert-soft-danger border-0 p-5 rounded-4 shadow-sm" role="alert">
                                <div
                                    class="avatar-80 rounded-pill bg-danger d-inline-flex align-items-center justify-content-center mb-4">
                                    <i class="bi bi-shield-slash-fill text-white fs-1"></i>
                                </div>
                                <h3 class="fw-bold text-danger mb-2">AKUN DIBEKUKAN</h3>
                                <p class="text-secondary mb-4">Pengelola telah menonaktifkan akses Anda ke <strong>seluruh
                                        fasilitas</strong> asrama.</p>

                                <div class="bg-white rounded-3 p-3 mb-4 shadow-sm text-start">
                                    <div class="row">
                                        <div class="col-sm-6 border-end">
                                            <small class="text-muted d-block uppercase fw-bold"
                                                style="font-size: 10px;">ALASAN SANKSI</small>
                                            <span class="text-dark">{{ $globalSuspend->reason }}</span>
                                        </div>
                                        <div class="col-sm-6 ps-md-4">
                                            <small class="text-muted d-block uppercase fw-bold"
                                                style="font-size: 10px;">ISSUER (PEMBERI)</small>
                                            <span
                                                class="text-dark">{{ $globalSuspend->issuer->residentDetails->full_name ?? 'Manager iDorm' }}</span>
                                        </div>
                                    </div>
                                </div>

                                <a href="{{ route('dashboard') }}" class="btn btn-danger rounded-pill px-5 fw-bold shadow">
                                    <i class="bi bi-house-door me-2"></i> Kembali ke Dashboard
                                </a>
                            </div>
                        </div>
                    @else
                        {{-- JIKA TIDAK ADA GLOBAL SUSPEND --}}

                        @if ($errors->any())
                            <div class="alert alert-danger">
                                <ul class="mb-0">
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        {{-- PESAN ERROR BENTROK DINAMIS --}}
                        @if (session('error'))
                            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                <div class="d-flex align-items-center">
                                    <i class="bi bi-exclamation-triangle-fill me-2"></i>
                                    <div>
                                        {!! session('error') !!}
                                    </div>
                                </div>
                                <button type="button" class="btn-close" data-bs-dismiss="alert"
                                    aria-label="Close"></button>
                            </div>
                        @endif

                        {{-- 1. INPUT PERTAMA (DROPDOWN UTAMA) --}}
                        <form action="" method="GET" class="mb-4">
                            <label class="form-label fw-bold">Pilih Fasilitas yang ingin dipinjam: <span
                                    class="text-danger">*</span></label>

                            <select class="form-select" name="kategori_fasilitas" onchange="this.form.submit()">
                                <option value="" selected disabled>-- Pilih Fasilitas --</option>
                                <option value="dapur" {{ request('kategori_fasilitas') == 'dapur' ? 'selected' : '' }}>
                                    Dapur</option>
                                <option value="cws" {{ request('kategori_fasilitas') == 'cws' ? 'selected' : '' }}>
                                    Co-Working Space</option>
                                <option value="mesin_cuci"
                                    {{ request('kategori_fasilitas') == 'mesin_cuci' ? 'selected' : '' }}>Mesin Cuci
                                </option>
                                <option value="sergun" {{ request('kategori_fasilitas') == 'sergun' ? 'selected' : '' }}>
                                    Serba Guna</option>
                                <option value="theater" {{ request('kategori_fasilitas') == 'theater' ? 'selected' : '' }}>
                                    Theater</option>
                            </select>
                        </form>

                        <hr class="mb-4">

                        {{-- 2. LOGIKA TAMPILAN FORM --}}
                        @if (request('kategori_fasilitas'))
                            {{-- CEK LOCAL SUSPEND (Per Fasilitas) --}}
                            @if (isset($localSuspend))
                                {{-- TAMPILAN JIKA TERKENA SUSPEND LOKAL --}}
                                <div class="animate-fade-in">
                                    <div class="alert alert-soft-danger border-0 p-4 rounded-4 shadow-sm" role="alert">
                                        <div class="d-flex align-items-center mb-3">
                                            <div
                                                class="avatar-50 rounded-pill bg-danger d-flex align-items-center justify-content-center me-3">
                                                <i class="bi bi-shield-lock-fill text-white fs-4"></i>
                                            </div>
                                            <div>
                                                <h5 class="alert-heading fw-bold text-danger mb-0">Akses Diblokir
                                                    (Suspended)</h5>
                                                <small class="text-secondary">Anda dilarang menggunakan fasilitas ini untuk
                                                    sementara waktu.</small>
                                            </div>
                                        </div>
                                        <hr class="border-danger opacity-25">
                                        <div class="row g-3">
                                            <div class="col-md-8">
                                                <p class="mb-1 fw-bold text-dark">Alasan Sanksi:</p>
                                                <p class="mb-0 text-muted italic">"{{ $localSuspend->reason }}"</p>
                                            </div>
                                            <div class="col-md-4 text-md-end text-start">
                                                <p class="mb-1 fw-bold text-dark">Berlaku Hingga:</p>
                                                <span class="badge bg-danger rounded-pill">
                                                    {{ $localSuspend->end_date ? $localSuspend->end_date->format('d M Y') : 'Waktu Tidak Ditentukan' }}
                                                </span>
                                            </div>
                                        </div>
                                        <div class="mt-4">
                                            <p class="small text-muted mb-0">
                                                <i class="bi bi-info-circle me-1"></i>
                                                Silakan hubungi
                                                <strong>{{ $localSuspend->issuer->residentDetails->full_name ?? 'Pengelola' }}</strong>
                                                untuk informasi lebih lanjut mengenai pencabutan sanksi.
                                            </p>
                                        </div>
                                    </div>

                                    <div class="text-center mt-4">
                                        <a href="{{ route('dashboard') }}" class="btn btn-soft-primary rounded-pill px-4">
                                            <i class="bi bi-arrow-left me-1"></i> Kembali ke Dashboard
                                        </a>
                                    </div>
                                </div>
                            @else
                                {{-- JIKA TIDAK KENA SUSPEND LOKAL, TAMPILKAN FORM SESUAI KATEGORI --}}

                                {{-- --- FORM DAPUR --- --}}
                                @if (request('kategori_fasilitas') == 'dapur')
                                    <div class="animate-fade-in">
                                        <h5 class="text-primary mb-3">Formulir Peminjaman Dapur</h5>
                                        <form action="{{ route('booking.store') }}" method="POST">
                                            @csrf
                                            <input type="hidden" name="facility_id"
                                                value="{{ $facilities->first()->id ?? '' }}">
                                            <input type="hidden" name="kategori" value="dapur">
                                            <div class="mb-3">
                                                <label class="form-label">Tanggal Peminjaman <span
                                                        class="text-danger">*</span></label>
                                                <input type="date" class="form-control" name="booking_date" required
                                                    min="{{ date('Y-m-d') }}">
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label">Alat yang dibutuhkan</label>
                                                <select class="form-select" name="facility_item_id" required>
                                                    <option value="" selected disabled>-- Pilih peralatan yang
                                                        dibutuhkan --</option>
                                                    @foreach ($items as $item)
                                                        <option value="{{ $item->id }}"
                                                            {{ old('facility_item_id') == $item->id ? 'selected' : '' }}>
                                                            {{ $item->name }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <x-time-dropdown name="start_time" label="Jam Mulai"
                                                        required="true" />
                                                </div>
                                                <div class="col-md-6">
                                                    <x-time-dropdown name="end_time" label="Jam Selesai"
                                                        required="true" />
                                                </div>
                                            </div>
                                            <button type="submit" class="btn btn-primary w-100">Booking Dapur</button>
                                        </form>
                                    </div>

                                    {{-- --- FORM CWS --- --}}
                                @elseif(request('kategori_fasilitas') == 'cws')
                                    <div class="animate-fade-in">
                                        <h5 class="text-primary mb-3">Formulir Peminjaman Co-Working Space</h5>
                                        @if ($facilities->isEmpty())
                                            {{-- Error Handling if empty --}}
                                        @else
                                            <form action="{{ route('booking.store') }}" method="POST">
                                                @csrf
                                                <input type="hidden" name="facility_id"
                                                    value="{{ $facilities->first()->id ?? '' }}">
                                                <input type="hidden" name="kategori" value="cws">

                                                @if ($items->isNotEmpty())
                                                    <input type="hidden" name="facility_item_id"
                                                        value="{{ $items->first()->id }}">
                                                @else
                                                    <div class="alert alert-danger">Error: Admin belum setup item ruangan
                                                        CWS!</div>
                                                @endif

                                                <div class="mb-3">
                                                    <label class="form-label">Tanggal Peminjaman <span
                                                            class="text-danger">*</span></label>
                                                    <input type="date" class="form-control" name="booking_date"
                                                        required min="{{ date('Y-m-d') }}">
                                                    <small class="text-muted text-danger fw-bold">Note: CWS tidak tersedia
                                                        di hari Kamis.</small>
                                                </div>

                                                <div class="mb-3">
                                                    <label class="form-label">Jumlah Orang <span
                                                            class="text-danger">*</span></label>
                                                    <input type="number" class="form-control" name="jumlah_orang"
                                                        placeholder="Masukkan jumlah orang" min="1" required>
                                                    <small class="text-muted">Minimal peminjaman: <strong>20
                                                            Orang</strong>.</small>
                                                </div>
                                                <div class="row">
                                                    <div class="col-md-6">
                                                        <x-time-dropdown name="start_time" label="Jam Mulai *"
                                                            required="true" />
                                                    </div>
                                                    <div class="col-md-6">
                                                        <x-time-dropdown name="end_time" label="Jam Selesai *"
                                                            required="true" />
                                                    </div>
                                                </div>
                                                <button type="submit" class="btn btn-primary w-100">Booking Co-Working
                                                    Space</button>
                                            </form>
                                        @endif
                                    </div>

                                    {{-- --- FORM MESIN CUCI --- --}}
                                @elseif(request('kategori_fasilitas') == 'mesin_cuci')
                                    <div class="animate-fade-in">
                                        <h5 class="text-primary mb-3">Formulir Booking Mesin Cuci</h5>
                                        <form action="{{ route('booking.store') }}" method="POST">
                                            @csrf
                                            <input type="hidden" name="facility_id"
                                                value="{{ $facilities->first()->id }}">
                                            <input type="hidden" name="kategori" value="mesin_cuci">

                                            <div class="row mb-3">
                                                <div class="col-md-6">
                                                    <label class="form-label">Tanggal Penggunaan <span
                                                            class="text-danger">*</span></label>
                                                    <input type="date" class="form-control" name="booking_date"
                                                        value="{{ old('booking_date') }}" required
                                                        min="{{ date('Y-m-d') }}">
                                                </div>
                                                <div class="col-md-6">
                                                    <label class="form-label">Pilih Sesi Waktu (Per 2 Jam) <span
                                                            class="text-danger">*</span></label>
                                                    <select class="form-select" name="slot_id" required>
                                                        <option value="" selected disabled>-- Pilih Sesi --</option>
                                                        @foreach ($timeSlots as $slot)
                                                            <option value="{{ $slot->id }}"
                                                                {{ old('slot_id') == $slot->id ? 'selected' : '' }}>
                                                                {{ $slot->full_slot }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>

                                            <div class="mb-3">
                                                <label class="form-label fw-bold">Pilih Nomor Mesin: <span
                                                        class="text-danger">*</span></label>
                                                <div class="row g-2 text-center mb-3 justify-content-between"
                                                    id="washing-machine-group" data-max="2">
                                                    @forelse ($items as $item)
                                                        <div class="col-md-2 col-4">
                                                            <input type="checkbox" class="btn-check machine-checkbox"
                                                                name="facility_item_id[]" id="mesin{{ $item->id }}"
                                                                value="{{ $item->id }}" autocomplete="off">
                                                            <label
                                                                class="btn btn-outline-primary w-100 d-flex flex-column align-items-center justify-content-center py-3"
                                                                for="mesin{{ $item->id }}">
                                                                <span
                                                                    class="small fw-bold">M-{{ substr($item->name, -1) }}</span>
                                                            </label>
                                                        </div>
                                                    @empty
                                                        <div class="col-12">
                                                            <p class="text-danger small">Data mesin tidak ditemukan untuk
                                                                gender {{ $gender }}.</p>
                                                        </div>
                                                    @endforelse
                                                </div>
                                                <div id="max-selection-alert" class="alert alert-warning d-none">
                                                    <small><i class="bi bi-exclamation-triangle"></i> Maksimal hanya boleh
                                                        memilih <strong>2 mesin cuci</strong> sekaligus.</small>
                                                </div>
                                                <button type="submit" class="btn btn-primary w-100">Booking Mesin Cuci
                                                    Sekarang</button>
                                            </div>
                                        </form>
                                    </div>

                                    {{-- --- FORM THEATER --- --}}
                                @elseif(request('kategori_fasilitas') == 'theater')
                                    <div class="animate-fade-in">
                                        <h5 class="text-primary mb-3">Formulir Peminjaman Theater</h5>
                                        {{-- 
                                            PERBAIKAN: @if yang dikomentari di sini sebelumnya meninggalkan @endif yatim piatu.
                                            Saya sudah menghapus @endif yatim piatu tersebut.
                                        --}}
                                        <form action="{{ route('booking.store') }}" method="POST">
                                            @csrf
                                            <input type="hidden" name="facility_id"
                                                value="{{ $facilities->first()->id ?? '' }}">
                                            <input type="hidden" name="kategori" value="theater">

                                            @if ($items->isNotEmpty())
                                                <input type="hidden" name="facility_item_id"
                                                    value="{{ $items->first()->id }}">
                                            @else
                                                <div class="alert alert-danger">Error: Admin belum setup item ruangan
                                                    Theater!</div>
                                            @endif

                                            <div class="mb-3">
                                                <label class="form-label">Tanggal Nonton <span
                                                        class="text-danger">*</span></label>
                                                <input type="date" class="form-control" name="booking_date" required
                                                    min="{{ date('Y-m-d') }}">
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label">Judul Film / Acara <span
                                                        class="text-danger">*</span></label>
                                                <input type="text" class="form-control" name="description"
                                                    placeholder="Contoh: Acara Kelas" required>
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label fw-bold">Jumlah Orang <span
                                                        class="text-danger">*</span></label>
                                                <input type="number" class="form-control" name="jumlah_orang"
                                                    min="1" placeholder="Berapa orang yang ikut nonton?" required>
                                                <small class="text-muted">Maksimal peminjaman: <strong>50
                                                        Orang</strong>.</small>
                                            </div>
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <x-time-dropdown name="start_time" label="Jam Mulai *"
                                                        required="true" />
                                                </div>
                                                <div class="col-md-6">
                                                    <x-time-dropdown name="end_time" label="Jam Selesai *"
                                                        required="true" />
                                                </div>
                                            </div>
                                            <button type="submit" class="btn btn-primary w-100">Booking Theater</button>
                                        </form>
                                    </div>

                                    {{-- --- FORM SERGUN --- --}}
                                @elseif (request('kategori_fasilitas') == 'sergun')
                                    <div class="animate-fade-in">
                                        <h5 class="text-primary mb-3">Formulir Peminjaman Serba Guna</h5>
                                        {{-- 
                                            PERBAIKAN: Di sini sebelumnya ada @endif yang tertinggal dari komentar @if ($facilities->isEmpty()).
                                            Sudah dihapus agar struktur tidak error.
                                        --}}
                                        <form action="{{ route('booking.store') }}" method="POST">
                                            @csrf
                                            <input type="hidden" name="facility_id"
                                                value="{{ $facilities->first()->id ?? '' }}">
                                            <input type="hidden" name="kategori" value="sergun">

                                            <div class="mb-3">
                                                <label class="form-label">Tanggal Peminjaman <span
                                                        class="text-danger">*</span></label>
                                                <input type="date" class="form-control" name="booking_date" required
                                                    min="{{ date('Y-m-d') }}">
                                            </div>

                                            <div class="mb-3">
                                                <label class="form-label">Bagian Sergun yang akan di booking</label>
                                                <select class="form-select" name="facility_item_id" required>
                                                    <option value="" selected disabled>-- Pilih bagian sergun --
                                                    </option>
                                                    @foreach ($items as $item)
                                                        <option value="{{ $item->id }}">{{ $item->name }}</option>
                                                    @endforeach
                                                </select>
                                            </div>

                                            <div class="row">
                                                <div class="col-md-6">
                                                    <x-time-dropdown name="start_time" label="Jam Mulai *"
                                                        required="true" />
                                                </div>
                                                <div class="col-md-6">
                                                    <x-time-dropdown name="end_time" label="Jam Selesai *"
                                                        required="true" />
                                                </div>
                                            </div>
                                            <button type="submit" class="btn btn-primary w-100">Booking Sergun</button>
                                        </form>
                                    </div>
                                @endif
                                {{-- ENDIF untuk fasilitas check (dapur/cws/dll) --}}

                            @endif
                            {{-- ENDIF untuk Local Suspend check --}}
                        @else
                            {{-- ELSE jika belum pilih kategori (tampilkan placeholder) --}}
                            <div class="text-center py-5">
                                <img src="{{ asset('hopeui/images/error/01.png') }}" alt="Select"
                                    class="img-fluid mb-4" style="max-height: 150px; opacity: 0.6;">
                                <h5 class="text-muted">Silakan pilih kategori fasilitas untuk melanjutkan.</h5>
                            </div>
                        @endif
                        {{-- ENDIF untuk request('kategori_fasilitas') --}}

                    @endif
                    {{-- ENDIF untuk Global Suspend --}}
                </div>
            </div>
        </div>
    </div>
@endsection
