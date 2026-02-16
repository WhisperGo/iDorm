@extends('layouts.app')

@section('content')
    <div class="row justify-content-center">
        <div class="col-sm-10">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title mb-0 fw-bold">Booking Fasilitas</h4>
                </div>

                <div class="card-body">
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
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif

                    {{-- 1. INPUT PERTAMA (DROPDOWN UTAMA) --}}
                    <form action="" method="GET" class="mb-4">
                        <label class="form-label fw-bold">Pilih Fasilitas yang ingin dipinjam: <span
                                class="text-danger">*</span></label>

                        <select class="form-select" name="kategori_fasilitas" onchange="this.form.submit()">
                            <option value="" selected disabled>-- Pilih Fasilitas --</option>
                            <option value="dapur" {{ request('kategori_fasilitas') == 'dapur' ? 'selected' : '' }}>Dapur
                            </option>
                            <option value="cws" {{ request('kategori_fasilitas') == 'cws' ? 'selected' : '' }}>Co-Working
                                Space</option>
                            <option value="mesin_cuci"
                                {{ request('kategori_fasilitas') == 'mesin_cuci' ? 'selected' : '' }}>Mesin Cuci</option>
                            <option value="sergun" {{ request('kategori_fasilitas') == 'sergun' ? 'selected' : '' }}>Serba
                                Guna</option>
                            <option value="theater" {{ request('kategori_fasilitas') == 'theater' ? 'selected' : '' }}>
                                Theater</option>
                        </select>
                    </form>

                    <hr class="mb-4">

                    {{-- 2. LOGIKA TAMPILAN FORM --}}

                    {{-- --- FORM DAPUR --- --}}
                    @if (request('kategori_fasilitas') == 'dapur')
                        <div class="animate-fade-in">
                            <h5 class="text-primary mb-3">Formulir Peminjaman Dapur</h5>
                            <form action="{{ route('booking.store') }}" method="POST">
                                @csrf
                                <input type="hidden" name="facility_id" value="{{ $facilities->first()->id ?? '' }}">
                                <input type="hidden" name="kategori" value="dapur">
                                <div class="mb-3">
                                    <label class="form-label">Tanggal Peminjaman <span class="text-danger">*</span></label>
                                    <input type="date" class="form-control" name="booking_date" required
                                        min="{{ date('Y-m-d') }}">
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Alat yang dibutuhkan</label>
                                    <select class="form-select" name="facility_item_id" required>
                                        <option value="" selected disabled>
                                            -- Pilih peralatan yang dibutuhkan --
                                        </option>
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
                                        <x-time-dropdown name="start_time" label="Jam Mulai" required="true" />
                                    </div>
                                    <div class="col-md-6">
                                        <x-time-dropdown name="end_time" label="Jam Selesai" required="true" />
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
                                <div class="alert alert-danger">
                                    ⚠️ Fasilitas Theater tidak ditemukan! Pastikan nama di database mengandung kata
                                    "Theater" atau "Theatre".
                                </div>
                            @else
                                <div class="alert alert-info py-2">
                                    <small>Booking untuk: <strong>{{ $facilities->first()->name }}</strong> (ID:
                                        {{ $facilities->first()->id }})</small>
                                </div>
                                <form action="{{ route('booking.store') }}" method="POST">
                                    @csrf
                                    <input type="hidden" name="facility_id" value="{{ $facilities->first()->id ?? '' }}">
                                    <input type="hidden" name="kategori" value="cws">

                                    @if ($items->isNotEmpty())
                                        <input type="hidden" name="facility_item_id" value="{{ $items->first()->id }}">
                                    @else
                                        <div class="alert alert-danger">Error: Admin belum setup item ruangan CWS!</div>
                                    @endif

                                    <div class="mb-3">
                                        <label class="form-label">Tanggal Peminjaman <span
                                                class="text-danger">*</span></label>
                                        <input type="date" class="form-control" name="booking_date" required
                                            min="{{ date('Y-m-d') }}">
                                        <small class="text-muted text-danger fw-bold">Note: CWS tidak tersedia di hari
                                            Kamis.</small>
                                    </div>

                                    <div class="mb-3">
                                        <label class="form-label">Jumlah Orang <span class="text-danger">*</span></label>
                                        <input type="number" class="form-control" name="jumlah_orang"
                                            placeholder="Masukkan jumlah orang" min="1" required>
                                        <small class="text-muted">Minimal peminjaman: <strong>20 Orang</strong>.</small>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <x-time-dropdown name="start_time" label="Jam Mulai *" required="true" />
                                        </div>
                                        <div class="col-md-6">
                                            <x-time-dropdown name="end_time" label="Jam Selesai *" required="true" />
                                        </div>
                                    </div>
                                    <button type="submit" class="btn btn-primary w-100">Booking Co-Working Space</button>
                                </form>
                            @endif
                        </div>

                    {{-- --- FORM MESIN CUCI (SESI 2 JAM) --- --}}
                    @elseif(request('kategori_fasilitas') == 'mesin_cuci')
                        <div class="animate-fade-in">
                            <h5 class="text-primary mb-3">Formulir Booking Mesin Cuci</h5>
                            <form action="{{ route('booking.store') }}" method="POST">
                                @csrf
                                <input type="hidden" name="facility_id" value="{{ $facilities->first()->id }}">
                                <input type="hidden" name="kategori" value="mesin_cuci">

                                <div class="row mb-3">
                                    <div class="col-md-6">
                                        <label class="form-label">Tanggal Penggunaan <span
                                                class="text-danger">*</span></label>
                                        <input type="date" class="form-control" name="booking_date"
                                            value="{{ old('booking_date') }}" required min="{{ date('Y-m-d') }}">
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
                                    <label class="form-label fw-bold">
                                        Pilih Nomor Mesin:
                                        <span class="text-danger">*</span>
                                    </label>
                                    <div class="row g-2 text-center mb-3 justify-content-between" id="washing-machine-group" data-max="2">
                                        @forelse ($items as $item)
                                            <div class="col-md-2 col-4">
                                                {{-- Checkbox Input --}}
                                                <input type="checkbox"
                                                    class="btn-check machine-checkbox"
                                                    name="facility_item_id[]"
                                                    id="mesin{{ $item->id }}"
                                                    value="{{ $item->id }}"
                                                    autocomplete="off">

                                                {{-- Label sebagai Tombol --}}
                                                <label class="btn btn-outline-primary w-100 d-flex flex-column align-items-center justify-content-center py-3" 
                                                    for="mesin{{ $item->id }}">
                                                    <span class="small fw-bold">M-{{ substr($item->name, -1) }}</span>
                                                </label>
                                            </div>
                                        @empty
                                            <div class="col-12">
                                                <p class="text-danger small">Data mesin tidak ditemukan untuk gender {{ $gender }}.</p>
                                            </div>
                                        @endforelse
                                    </div>

                                    {{-- Pesan Error jika memilih lebih dari 2 --}}
                                    <div id="max-selection-alert" class="alert alert-warning d-none">
                                        <small><i class="bi bi-exclamation-triangle"></i> Maksimal hanya boleh memilih <strong>2 mesin cuci</strong> sekaligus.</small>
                                    </div>

                                    <button type="submit" class="btn btn-primary w-100">
                                        Booking Mesin Cuci Sekarang
                                    </button>
                            </form>
                        </div>

                        {{-- --- FORM THEATER --- --}}
                    @elseif(request('kategori_fasilitas') == 'theater')
                        <div class="animate-fade-in">
                            <h5 class="text-primary mb-3">Formulir Peminjaman Theater</h5>
                            @if ($facilities->isEmpty())
                                <div class="alert alert-danger">
                                    ⚠️ Fasilitas Theater tidak ditemukan! Pastikan nama di database mengandung kata
                                    "Theater" atau "Theatre".
                                </div>
                            @else
                                <div class="alert alert-info py-2">
                                    <small>Booking untuk: <strong>{{ $facilities->first()->name }}</strong> (ID:
                                        {{ $facilities->first()->id }})</small>
                                </div>
                                <form action="{{ route('booking.store') }}" method="POST">
                                    @csrf
                                    <input type="hidden" name="facility_id"
                                        value="{{ $facilities->first()->id ?? '' }}">
                                    <input type="hidden" name="kategori" value="theater">

                                    @if ($items->isNotEmpty())
                                        <input type="hidden" name="facility_item_id" value="{{ $items->first()->id }}">
                                    @else
                                        <div class="alert alert-danger">Error: Admin belum setup item ruangan Theater!
                                        </div>
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
                                        <input type="number" class="form-control" name="jumlah_orang" min="1"
                                            placeholder="Berapa orang yang ikut nonton?" required>
                                        <small class="text-muted">Maksimal peminjaman: <strong>50 Orang</strong>.</small>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <x-time-dropdown name="start_time" label="Jam Mulai *" required="true" />
                                        </div>
                                        <div class="col-md-6">
                                            <x-time-dropdown name="end_time" label="Jam Selesai *" required="true" />
                                        </div>
                                    </div>
                                    <button type="submit" class="btn btn-primary w-100">Booking Theater</button>
                                </form>
                            @endif
                        </div>

                        {{-- --- FORM SERGUN --- --}}
                    @elseif (request('kategori_fasilitas') == 'sergun')
                        <div class="animate-fade-in">
                            <h5 class="text-primary mb-3">Formulir Peminjaman Serba Guna</h5>
                            @if ($facilities->isEmpty())
                                <div class="alert alert-danger">
                                    ⚠️ Fasilitas Serba Guna tidak ditemukan! Pastikan nama di database mengandung kata
                                    "Theater" atau "Theatre".
                                </div>
                            @else
                                <div class="alert alert-info py-2">
                                    <small>Booking untuk: <strong>{{ $facilities->first()->name }}</strong> (ID:
                                        {{ $facilities->first()->id }})</small>
                                </div>
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

                                    {{-- Ganti bagian select di FORM SERGUN --}}
                                    <div class="mb-3">
                                        <label class="form-label">Bagian Sergun yang akan di booking</label>
                                        <select class="form-select" name="facility_item_id" required>
                                            <option value="" selected disabled>-- Pilih bagian sergun --</option>
                                            @foreach ($items as $item)
                                                <option value="{{ $item->id }}">{{ $item->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-6">
                                            <x-time-dropdown name="start_time" label="Jam Mulai *" required="true" />
                                        </div>
                                        <div class="col-md-6">
                                            <x-time-dropdown name="end_time" label="Jam Selesai *" required="true" />
                                        </div>
                                    </div>

                                    <button type="submit" class="btn btn-primary w-100">Booking Sergun</button>
                                </form>
                            @endif
                        </div>
                    @else
                        <div class="text-center py-5">
                            <h5 class="text-muted">Silakan pilih kategori fasilitas untuk melanjutkan.</h5>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection
