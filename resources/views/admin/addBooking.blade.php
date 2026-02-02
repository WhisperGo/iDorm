@extends('penghuni.layouts')

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

                    {{-- 1. INPUT PERTAMA (DROPDOWN UTAMA) --}}
                    <form action="" method="GET" class="mb-4">
                        <label class="form-label fw-bold">Pilih Fasilitas yang ingin dipinjam:</label>

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
                                    <label class="form-label">Tanggal Peminjaman</label>
                                    <input type="date" class="form-control" name="booking_date" required
                                        min="{{ date('Y-m-d') }}">
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Alat yang dibutuhkan</label>
                                    <select class="form-select" name="item_dapur">
                                        <option value="">-- Pilih peralatan yang dibutuhkan --</option>
                                        <option value="kompor">Kompor</option>
                                        <option value="rice_cooker">Rice Cooker Kecil</option>
                                        <option value="rice_cooker">Rice Cooker Besar</option>
                                        <option value="airfryer">Airfryer Halal</option>
                                        <option value="airfryer">Airfryer Non Halal</option>
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
                                <div class="mb-3">
                                    <label class="form-label">Tanggal Peminjaman</label>
                                    <input type="date" class="form-control" name="booking_date" required
                                        min="{{ date('Y-m-d') }}">
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Jumlah Orang</label>
                                    <input type="number" class="form-control" name="jumlah_orang"
                                        placeholder="Masukkan jumlah orang" min="1" required>
                                </div>
                                <div class="row">
                                    <div class="col-md-6">
                                        <x-time-dropdown name="start_time" label="Jam Mulai" required="true" />
                                    </div>
                                    <div class="col-md-6">
                                        <x-time-dropdown name="end_time" label="Jam Selesai" required="true" />
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
                                <input type="hidden" name="kategori" value="mesin_cuci">

                                <div class="row mb-3">
                                    <div class="col-md-6">
                                        <label class="form-label">Tanggal Penggunaan</label>
                                        <input type="date" class="form-control" name="booking_date"
                                            value="{{ old('booking_date') }}" required min="{{ date('Y-m-d') }}">
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">Pilih Sesi Waktu (Per 2 Jam)</label>
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
                                    <label class="form-label fw-bold">Pilih Nomor Mesin:</label>
                                    <div class="row g-2 text-center">
                                        @foreach ($facilities as $f)
                                            <div class="col">
                                                <input type="checkbox" class="btn-check" name="facility_id[]"
                                                    id="mesin{{ $f->id }}" value="{{ $f->id }}"
                                                    autocomplete="off">
                                                <label class="btn btn-outline-primary w-100"
                                                    for="mesin{{ $f->id }}">
                                                    M-{{ substr($f->name, -1) }}
                                                </label>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>

                                <button type="submit" class="btn btn-primary w-100">Booking Mesin Cuci Sekarang</button>
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
                                    <div class="mb-3">
                                        <label class="form-label">Tanggal Nonton</label>
                                        <input type="date" class="form-control" name="booking_date" required
                                            min="{{ date('Y-m-d') }}">
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">Judul Film / Acara</label>
                                        <input type="text" class="form-control" name="description"
                                            placeholder="Contoh: Nobar Timnas">
                                    </div>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <x-time-dropdown name="start_time" label="Jam Mulai" required="true" />
                                        </div>
                                        <div class="col-md-6">
                                            <x-time-dropdown name="end_time" label="Jam Selesai" required="true" />
                                        </div>
                                    </div>
                                    <button type="submit" class="btn btn-warning w-100">Booking Theater</button>
                                </form>
                            @endif
                        </div>

                        {{-- --- FORM SERGUN --- --}}
                    @elseif (request('kategori_fasilitas') == 'sergun')
                        <div class="animate-fade-in">
                            <h5 class="text-primary mb-3">Formulir Peminjaman Serba Guna</h5>
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
                                <input type="hidden" name="kategori" value="sergun">
                                <div class="mb-3">
                                    <label class="form-label">Tanggal Peminjaman</label>
                                    <input type="date" class="form-control" name="booking_date" required
                                        min="{{ date('Y-m-d') }}">
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Bagian Sergun yang akan di booking</label>
                                    <select class="form-select" name="item_sergun">
                                        <option value="">-- Pilih bagian sergun --</option>
                                        <option value="area_sergun_A">Area Sergun A</option>
                                        <option value="area_sergun_B">Area Sergun B</option>
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

    {{-- 3. TABEL STATUS RIWAYAT --}}
    @if (in_array(request('kategori_fasilitas'), ['dapur', 'mesin_cuci', 'cws', 'sergun', 'theater']))
        <div class="row justify-content-center mt-4">
            <div class="col-sm-10">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title mb-0 fw-bold">Status Peminjaman & Kebersihan</h4>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Fasilitas</th>
                                        <th>Waktu Selesai</th>
                                        <th>Status Foto</th>
                                        <th width="30%">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($myBookings as $booking)
                                        <tr>
                                            <td>
                                                @if ($booking->slot_id && $booking->slot)
                                                    {{ \Carbon\Carbon::parse($booking->slot->start_time)->format('H:i') }}
                                                    -
                                                    {{ \Carbon\Carbon::parse($booking->slot->end_time)->format('H:i') }}
                                                @else
                                                    {{ \Carbon\Carbon::parse($booking->start_time)->format('H:i') }} -
                                                    {{ \Carbon\Carbon::parse($booking->end_time)->format('H:i') }}
                                                @endif
                                            </td>
                                            <td>
                                                @if ($booking->photo_proof_path)
                                                    <span class="badge bg-success">Sudah Diunggah</span>
                                                @else
                                                    <span class="badge bg-danger">Belum Ada Foto</span>
                                                @endif
                                            </td>
                                            <td>
                                                @if (!$booking->photo_proof_path)
                                                    @php
                                                        $endTime = \Carbon\Carbon::parse(
                                                            $booking->booking_date . ' ' . $booking->end_time,
                                                        );
                                                        $isOverdue = now() > $endTime;
                                                    @endphp

                                                    @if ()
                                                        
                                                    @endif
                                                    @if ($isOverdue)
                                                        <form action="{{ route('booking.upload', $booking->id) }}"
                                                            method="POST" enctype="multipart/form-data">
                                                            @csrf
                                                            <div class="input-group input-group-sm">
                                                                <input type="file" name="photo" class="form-control"
                                                                    required>
                                                                <button class="btn btn-warning"
                                                                    type="submit">Upload</button>
                                                            </div>
                                                        </form>
                                                        <small
                                                            class="text-danger fw-bold d-block mt-1 animate__animated animate__flash animate__infinite">⚠️
                                                            Waktu habis! Segera upload foto.</small>
                                                    @else
                                                        <small class="text-muted">Upload tersedia setelah waktu
                                                            selesai.</small>
                                                    @endif
                                                @else
                                                    <button class="btn btn-sm btn-outline-primary"
                                                        disabled>Terverifikasi</button>
                                                @endif
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="4" class="text-center text-muted">Belum ada aktivitas
                                                peminjaman hari ini.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif
@endsection