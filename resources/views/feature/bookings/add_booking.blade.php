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
                        {{-- ... (Bagian Global Suspend: Tetap Sama) ... --}}
                        <div class="text-center py-4 animate-fade-in">
                            <div class="alert alert-soft-danger border-0 p-5 rounded-4 shadow-sm" role="alert">
                                <div class="avatar-80 rounded-pill bg-danger d-inline-flex align-items-center justify-content-center mb-4">
                                    <i class="bi bi-shield-slash-fill text-white fs-1"></i>
                                </div>
                                <h3 class="fw-bold text-danger mb-2">AKUN DIBEKUKAN</h3>
                                <p class="text-secondary mb-4">Pengelola telah menonaktifkan akses Anda ke <strong>seluruh fasilitas</strong> asrama.</p>
                                <div class="bg-white rounded-3 p-3 mb-4 shadow-sm text-start">
                                    <div class="row">
                                        <div class="col-sm-6 border-end">
                                            <small class="text-muted d-block uppercase fw-bold" style="font-size: 10px;">ALASAN SANKSI</small>
                                            <span class="text-dark">{{ $globalSuspend->reason }}</span>
                                        </div>
                                        <div class="col-sm-6 ps-md-4">
                                            <small class="text-muted d-block uppercase fw-bold" style="font-size: 10px;">ISSUER (PEMBERI)</small>
                                            <span class="text-dark">{{ $globalSuspend->issuer->residentDetails->full_name ?? 'Manager iDorm' }}</span>
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
                            <svg xmlns="http://www.w3.org/2000/svg" style="display: none;">
                                <symbol id="exclamation-triangle-fill" fill="currentColor" viewBox="0 0 16 16">
                                    <path d="M8.982 1.566a1.13 1.13 0 0 0-1.96 0L.165 13.233c-.457.778.091 1.767.98 1.767h13.713c.889 0 1.438-.99.98-1.767L8.982 1.566zM8 5c.535 0 .954.462.9.995l-.35 3.507a.552.552 0 0 1-1.1 0L7.1 5.995A.905.905 0 0 1 8 5zm.002 6a1 1 0 1 1 0 2 1 1 0 0 1 0-2z" />
                                </symbol>
                            </svg>
                            <div class="alert alert-danger d-flex align-items-center mb-4" role="alert">
                                <svg class="flex-shrink-0 bi me-2 icon-24" width="24" height="24">
                                    <use xlink:href="#exclamation-triangle-fill" />
                                </svg>
                                <div>
                                    <ul class="mb-0 list-unstyled">
                                        @foreach ($errors->all() as $error)
                                            <li>{{ $error }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                            </div>
                        @endif

                        @if (session('error'))
                            <svg xmlns="http://www.w3.org/2000/svg" style="display: none;">
                                <symbol id="exclamation-triangle-fill" fill="currentColor" viewBox="0 0 16 16">
                                    <path d="M8.982 1.566a1.13 1.13 0 0 0-1.96 0L.165 13.233c-.457.778.091 1.767.98 1.767h13.713c.889 0 1.438-.99.98-1.767L8.982 1.566zM8 5c.535 0 .954.462.9.995l-.35 3.507a.552.552 0 0 1-1.1 0L7.1 5.995A.905.905 0 0 1 8 5zm.002 6a1 1 0 1 1 0 2 1 1 0 0 1 0-2z" />
                                </symbol>
                            </svg>
                            <div class="alert alert-danger d-flex align-items-center mb-4 alert-dismissible fade show" role="alert">
                                <svg class="flex-shrink-0 bi me-2 icon-24" width="24" height="24">
                                    <use xlink:href="#exclamation-triangle-fill" />
                                </svg>
                                <div>{!! session('error') !!}</div>
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                            </div>
                        @endif

                        {{-- 1. INPUT PERTAMA (DROPDOWN UTAMA) --}}
                        <form action="" method="GET" class="mb-4">
                            <label class="form-label fw-bold">Pilih Fasilitas yang ingin dipinjam: <span class="text-danger">*</span></label>
                            {{-- TAMBAHAN: Default value kategori dari Chatbot --}}
                            <select class="form-select" name="kategori_fasilitas" onchange="this.form.submit()">
                                <option value="" selected disabled>-- Pilih Fasilitas --</option>
                                <option value="dapur" {{ request('kategori_fasilitas', $kategori ?? '') == 'dapur' ? 'selected' : '' }}>Dapur</option>
                                <option value="cws" {{ request('kategori_fasilitas', $kategori ?? '') == 'cws' ? 'selected' : '' }}>Co-Working Space</option>
                                <option value="mesin_cuci" {{ request('kategori_fasilitas', $kategori ?? '') == 'mesin_cuci' ? 'selected' : '' }}>Mesin Cuci</option>
                                <option value="sergun" {{ request('kategori_fasilitas', $kategori ?? '') == 'sergun' ? 'selected' : '' }}>Serba Guna</option>
                                <option value="theater" {{ request('kategori_fasilitas', $kategori ?? '') == 'theater' ? 'selected' : '' }}>Theater</option>
                            </select>
                        </form>

                        <hr class="mb-4">

                        {{-- 2. LOGIKA TAMPILAN FORM --}}
                        @if (request('kategori_fasilitas', $kategori ?? ''))
                            @if (isset($localSuspend))
                                {{-- ... (Tampilan Local Suspend: Tetap Sama) ... --}}
                                <div class="animate-fade-in">
                                    <div class="alert alert-soft-danger border-0 p-4 rounded-4 shadow-sm" role="alert">
                                        {{-- Isi suspend tetap sama --}}
                                        <div class="d-flex align-items-center mb-3">
                                            <div class="avatar-50 rounded-pill bg-danger d-flex align-items-center justify-content-center me-3">
                                                <i class="bi bi-shield-lock-fill text-white fs-4"></i>
                                            </div>
                                            <div>
                                                <h5 class="alert-heading fw-bold text-danger mb-0">Akses Diblokir (Suspended)</h5>
                                                <small class="text-secondary">Anda dilarang menggunakan fasilitas ini untuk sementara waktu.</small>
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
                                    </div>
                                    <div class="text-center mt-4">
                                        <a href="{{ route('dashboard') }}" class="btn btn-soft-primary rounded-pill px-4">
                                            <i class="bi bi-arrow-left me-1"></i> Kembali ke Dashboard
                                        </a>
                                    </div>
                                </div>
                            @else
                                {{-- JIKA TIDAK KENA SUSPEND LOKAL --}}

                                {{-- --- FORM DAPUR --- --}}
                                @if (request('kategori_fasilitas', $kategori ?? '') == 'dapur')
                                    <div class="animate-fade-in">
                                        <h5 class="text-primary mb-3">Formulir Peminjaman Dapur</h5>
                                        <form action="{{ route('booking.store') }}" method="POST">
                                            @csrf
                                            <input type="hidden" name="facility_id" value="{{ $facilities->first()->id ?? '' }}">
                                            <input type="hidden" name="kategori" value="dapur">
                                            <div class="mb-3">
                                                <label class="form-label">Tanggal Peminjaman <span class="text-danger">*</span></label>
                                                {{-- TAMBAHAN: value dari chatbot --}}
                                                <input type="date" class="form-control" name="booking_date" required
                                                    min="{{ date('Y-m-d') }}" value="{{ $bookingDate ?? old('booking_date') }}">
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label">Alat yang dibutuhkan</label>
                                                <select class="form-select" name="facility_item_id" required>
                                                    <option value="" selected disabled>-- Pilih peralatan --</option>
                                                    @foreach ($items as $item)
                                                        {{-- TAMBAHAN: logic selected otomatis --}}
                                                        <option value="{{ $item->id }}"
                                                            {{ (isset($bookingDate) && str_contains(strtolower($item->name), 'dapur')) ? 'selected' : '' }}
                                                            {{ old('facility_item_id') == $item->id ? 'selected' : '' }}>
                                                            {{ $item->name }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            <div class="row">
                                                <div class="col-md-6">
                                                    {{-- TAMBAHAN: passing value ke component --}}
                                                    <x-time-dropdown name="start_time" label="Jam Mulai" required="true" 
                                                        :value="isset($startTime) ? str_replace('.', ':', $startTime) : null" />
                                                </div>
                                                <div class="col-md-6">
                                                    {{-- TAMBAHAN: passing value ke component --}}
                                                    <x-time-dropdown name="end_time" label="Jam Selesai" required="true" 
                                                        :value="isset($endTime) ? str_replace('.', ':', $endTime) : null" />
                                                </div>
                                            </div>

                                            {{-- TAMBAHAN: Checklist Alat Masak (Khusus Dapur) --}}
                                            @if(isset($tools) && $tools->isNotEmpty())
                                            <div class="mt-3 mb-3 p-3 border rounded bg-light text-start">
                                                <label class="form-label fw-bold">Peralatan Masak Tambahan:</label>
                                                <div class="row">
                                                    @foreach($tools as $tool)
                                                    <div class="col-6">
                                                        <div class="form-check">
                                                            <input class="form-check-input" type="checkbox" name="tools[]" value="{{ $tool->name }}" id="tool-{{ $tool->id }}">
                                                            <label class="form-check-label small" for="tool-{{ $tool->id }}">{{ $tool->name }}</label>
                                                        </div>
                                                    </div>
                                                    @endforeach
                                                </div>
                                            </div>
                                            @endif

                                            <button type="submit" class="btn btn-primary w-100">Booking Dapur</button>
                                        </form>
                                    </div>

                                {{-- --- FORM CWS --- --}}
                                @elseif(request('kategori_fasilitas', $kategori ?? '') == 'cws')
                                    <div class="animate-fade-in">
                                        <h5 class="text-primary mb-3">Formulir Peminjaman Co-Working Space</h5>
                                        <form action="{{ route('booking.store') }}" method="POST">
                                            @csrf
                                            <input type="hidden" name="facility_id" value="{{ $facilities->first()->id ?? '' }}">
                                            <input type="hidden" name="kategori" value="cws">
                                            @if ($items->isNotEmpty())
                                                <input type="hidden" name="facility_item_id" value="{{ $items->first()->id }}">
                                            @endif
                                            <div class="mb-3">
                                                <label class="form-label">Tanggal Peminjaman <span class="text-danger">*</span></label>
                                                {{-- TAMBAHAN: value dari chatbot --}}
                                                <input type="date" class="form-control" name="booking_date" required 
                                                       min="{{ date('Y-m-d') }}" value="{{ $bookingDate ?? old('booking_date') }}">
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label">Jumlah Orang <span class="text-danger">*</span></label>
                                                <input type="number" class="form-control" name="jumlah_orang" placeholder="Masukkan jumlah orang" min="1" required>
                                            </div>
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <x-time-dropdown name="start_time" label="Jam Mulai *" required="true" 
                                                       :value="isset($startTime) ? str_replace('.', ':', $startTime) : null" />
                                                </div>
                                                <div class="col-md-6">
                                                    <x-time-dropdown name="end_time" label="Jam Selesai *" required="true" 
                                                       :value="isset($endTime) ? str_replace('.', ':', $endTime) : null" />
                                                </div>
                                            </div>
                                            <button type="submit" class="btn btn-primary w-100">Booking CWS</button>
                                        </form>
                                    </div>

                                {{-- --- FORM MESIN CUCI --- --}}
                                @elseif(request('kategori_fasilitas', $kategori ?? '') == 'mesin_cuci')
                                    <div class="animate-fade-in">
                                        <h5 class="text-primary mb-3">Formulir Booking Mesin Cuci</h5>
                                        <form action="{{ route('booking.store') }}" method="POST">
                                            @csrf
                                            <input type="hidden" name="facility_id" value="{{ $facilities->first()->id }}">
                                            <input type="hidden" name="kategori" value="mesin_cuci">
                                            <div class="row mb-3">
                                                <div class="col-md-6">
                                                    <label class="form-label">Tanggal Penggunaan <span class="text-danger">*</span></label>
                                                    {{-- TAMBAHAN: value dari chatbot --}}
                                                    <input type="date" class="form-control" name="booking_date" 
                                                        value="{{ $bookingDate ?? old('booking_date') }}" required min="{{ date('Y-m-d') }}">
                                                </div>
                                                <div class="col-md-6">
                                                    <label class="form-label">Pilih Sesi Waktu <span class="text-danger">*</span></label>
                                                    <select class="form-select" name="slot_id" required>
                                                        <option value="" selected disabled>-- Pilih Sesi --</option>
                                                        @foreach ($timeSlots as $slot)
                                                            <option value="{{ $slot->id }}" {{ old('slot_id') == $slot->id ? 'selected' : '' }}>
                                                                {{ $slot->full_slot }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>
                                            {{-- ... (Isian mesin cuci tetap sama) ... --}}
                                            <div class="mb-3">
                                                <label class="form-label fw-bold">Pilih Nomor Mesin: <span class="text-danger">*</span></label>
                                                <div class="row g-2 text-center mb-3 justify-content-between" id="washing-machine-group" data-max="2">
                                                    @forelse ($items as $item)
                                                        <div class="col-md-2 col-4">
                                                            <input type="checkbox" class="btn-check machine-checkbox" name="facility_item_id[]" id="mesin{{ $item->id }}" value="{{ $item->id }}">
                                                            <label class="btn btn-outline-primary w-100 d-flex flex-column align-items-center justify-content-center py-3" for="mesin{{ $item->id }}">
                                                                <span class="small fw-bold">M-{{ substr($item->name, -1) }}</span>
                                                            </label>
                                                        </div>
                                                    @empty
                                                        <p class="text-danger small">Data mesin tidak ditemukan.</p>
                                                    @endforelse
                                                </div>
                                                <button type="submit" class="btn btn-primary w-100">Booking Mesin Cuci</button>
                                            </div>
                                        </form>
                                    </div>

                                {{-- --- FORM THEATER --- --}}
                                @elseif(request('kategori_fasilitas', $kategori ?? '') == 'theater')
                                    <div class="animate-fade-in">
                                        <h5 class="text-primary mb-3">Formulir Peminjaman Theater</h5>
                                        <form action="{{ route('booking.store') }}" method="POST">
                                            @csrf
                                            <input type="hidden" name="facility_id" value="{{ $facilities->first()->id ?? '' }}">
                                            <input type="hidden" name="kategori" value="theater">
                                            @if ($items->isNotEmpty())
                                                <input type="hidden" name="facility_item_id" value="{{ $items->first()->id }}">
                                            @endif
                                            <div class="mb-3">
                                                <label class="form-label">Tanggal Nonton <span class="text-danger">*</span></label>
                                                {{-- TAMBAHAN: value dari chatbot --}}
                                                <input type="date" class="form-control" name="booking_date" required 
                                                       min="{{ date('Y-m-d') }}" value="{{ $bookingDate ?? old('booking_date') }}">
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label">Judul Film / Acara <span class="text-danger">*</span></label>
                                                <input type="text" class="form-control" name="description" placeholder="Contoh: Acara Kelas" required>
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label fw-bold">Jumlah Orang <span class="text-danger">*</span></label>
                                                <input type="number" class="form-control" name="jumlah_orang" min="1" required>
                                            </div>
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <x-time-dropdown name="start_time" label="Jam Mulai *" required="true" 
                                                       :value="isset($startTime) ? str_replace('.', ':', $startTime) : null" />
                                                </div>
                                                <div class="col-md-6">
                                                    <x-time-dropdown name="end_time" label="Jam Selesai *" required="true" 
                                                       :value="isset($endTime) ? str_replace('.', ':', $endTime) : null" />
                                                </div>
                                            </div>
                                            <button type="submit" class="btn btn-primary w-100">Booking Theater</button>
                                        </form>
                                    </div>

                                {{-- --- FORM SERGUN --- --}}
                                @elseif (request('kategori_fasilitas', $kategori ?? '') == 'sergun')
                                    <div class="animate-fade-in">
                                        <h5 class="text-primary mb-3">Formulir Peminjaman Serba Guna</h5>
                                        <form action="{{ route('booking.store') }}" method="POST">
                                            @csrf
                                            <input type="hidden" name="facility_id" value="{{ $facilities->first()->id ?? '' }}">
                                            <input type="hidden" name="kategori" value="sergun">
                                            <div class="mb-3">
                                                <label class="form-label">Tanggal Peminjaman <span class="text-danger">*</span></label>
                                                {{-- TAMBAHAN: value dari chatbot --}}
                                                <input type="date" class="form-control" name="booking_date" required 
                                                       min="{{ date('Y-m-d') }}" value="{{ $bookingDate ?? old('booking_date') }}">
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label">Bagian Sergun yang akan di booking</label>
                                                <select class="form-select" name="facility_item_id" required>
                                                    <option value="" selected disabled>-- Pilih bagian sergun --</option>
                                                    @foreach ($items as $item)
                                                        <option value="{{ $item->id }}" 
                                                            {{ (isset($bookingDate) && str_contains(strtolower($item->name), strtolower($room ?? ''))) ? 'selected' : '' }}>
                                                            {{ $item->name }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <x-time-dropdown name="start_time" label="Jam Mulai *" required="true" 
                                                       :value="isset($startTime) ? str_replace('.', ':', $startTime) : null" />
                                                </div>
                                                <div class="col-md-6">
                                                    <x-time-dropdown name="end_time" label="Jam Selesai *" required="true" 
                                                       :value="isset($endTime) ? str_replace('.', ':', $endTime) : null" />
                                                </div>
                                            </div>
                                            <button type="submit" class="btn btn-primary w-100">Booking Sergun</button>
                                        </form>
                                    </div>
                                @endif
                            @endif
                        @else
                            <div class="text-center py-5">
                                <img src="{{ asset('hopeui/images/error/01.png') }}" alt="Select" class="img-fluid mb-4" style="max-height: 150px; opacity: 0.6;">
                                <h5 class="text-muted">Silakan pilih kategori fasilitas untuk melanjutkan.</h5>
                            </div>
                        @endif
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection