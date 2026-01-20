@extends('admin.layouts')

@section('content')
    <div class="row justify-content-center">
        <div class="col-sm-10">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title mb-0 fw-bold">Booking Fasilitas</h4>
                </div>

                <div class="card-body">
                    {{-- 1. INPUT PERTAMA (DROPDOWN UTAMA) --}}
                    <form action="" method="GET" class="mb-4">
                        <label class="form-label fw-bold">Pilih Fasilitas yang ingin dipinjam:</label>

                        <select class="form-select" name="kategori_fasilitas" onchange="this.form.submit()">
                            {{-- Opsi Default: Akan terpilih jika user belum memilih apapun --}}
                            <option value="" selected disabled>-- Pilih Fasilitas --</option>

                            <option value="dapur" {{ request('kategori_fasilitas') == 'dapur' ? 'selected' : '' }}>
                                Dapur
                            </option>
                            <option value="cws" {{ request('kategori_fasilitas') == 'cws' ? 'selected' : '' }}>
                                Co-Working Space
                            </option>
                            <option value="mesin_cuci"
                                {{ request('kategori_fasilitas') == 'mesin_cuci' ? 'selected' : '' }}>
                                Mesin Cuci
                            </option>
                            {{-- <option value="komunal" {{ request('kategori_fasilitas') == 'komunal' ? 'selected' : '' }}>
                                Ruang Komunal
                            </option> --}}
                            <option value="sergun" {{ request('kategori_fasilitas') == 'sergun' ? 'selected' : '' }}>
                                Serba Guna
                            </option>
                            <option value="theater" {{ request('kategori_fasilitas') == 'theater' ? 'selected' : '' }}>
                                Theater
                            </option>
                        </select>
                    </form>

                    <hr class="mb-4">

                    {{-- 2. LOGIKA TAMPILAN FORM --}}

                    @if (request('kategori_fasilitas') == 'dapur')
                        {{-- Form Dapur --}}
                        <div class="animate-fade-in">
                            <h5 class="text-primary mb-3">Formulir Peminjaman Dapur</h5>
                            <form action="/booking/store" method="POST">
                                @csrf
                                <input type="hidden" name="kategori" value="dapur">
                                <div class="mb-3">
                                    <label class="form-label">Tanggal Peminjaman</label>
                                    <input type="date" class="form-control" name="tanggal" required>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Alat yang dibutuhkan</label>
                                    <select class="form-select" name="item_dapur">
                                        <option value="">-- Pilih peralatan yang dibutuhkan --</option>
                                        <option value="rice_cooker">Rice Cooker</option>
                                        <option value="kompor">Kompor</option>
                                        <option value="airfryer">Airfryer</option>
                                    </select>
                                </div>
                                <div class="row mb-3">
                                    <div class="col-md-6">
                                        <label class="form-label">Jam Mulai</label>
                                        <input type="time" class="form-control" name="start_time" required>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">Jam Selesai</label>
                                        <input type="time" class="form-control" name="end_time" required>
                                    </div>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Peralatan yang dipinjam</label>
                                    <select class="form-select" name="item_dapur">
                                        <option value="">-- Peralatan pinjam atau pribadi --</option>
                                        <option value="rice_cooker">Pribadi</option>
                                        <option value="kompor">Pinjam</option>
                                    </select>
                                </div>
                                <button type="submit" class="btn btn-primary w-100">Booking Dapur</button>
                            </form>
                        </div>
                    @elseif(request('kategori_fasilitas') == 'cws')
                        <div class="animate-fade-in">
                            <h5 class="text-primary mb-3">Formulir Peminjaman Co-Working Space</h5>
                            <form action="/booking/store" method="POST">
                                @csrf
                                {{-- Pastikan value kategori sesuai --}}
                                <input type="hidden" name="kategori" value="cws">

                                {{-- Input Tanggal --}}
                                <div class="mb-3">
                                    <label class="form-label">Tanggal Peminjaman</label>
                                    <input type="date" class="form-control" name="tanggal" required>
                                </div>

                                {{-- Input Jumlah Orang --}}
                                <div class="mb-3">
                                    <label class="form-label">Jumlah Orang</label>
                                    <input type="number" class="form-control" name="jumlah_orang"
                                        placeholder="Masukkan jumlah orang" min="1" required>
                                </div>

                                {{-- Input Jam Mulai & Selesai --}}
                                <div class="row mb-3">
                                    <div class="col-md-6">
                                        <label class="form-label">Jam Mulai</label>
                                        <input type="time" class="form-control" name="start_time" required>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">Jam Selesai</label>
                                        <input type="time" class="form-control" name="end_time" required>
                                    </div>
                                </div>

                                {{-- Input Keterangan Bebas --}}
                                <div class="mb-4">
                                    <label class="form-label">Keterangan Tambahan</label>
                                    <textarea class="form-control" name="keterangan" rows="3"
                                        placeholder="Contoh: Untuk keperluan kerja kelompok / meeting project"></textarea>
                                </div>

                                <button type="submit" class="btn btn-primary w-100">Booking Co-Working Space</button>
                            </form>
                        </div>
                    @elseif(request('kategori_fasilitas') == 'mesin_cuci')
                        <div class="animate-fade-in">
                            <h5 class="text-primary mb-3">Formulir Booking Mesin Cuci</h5>

                            {{-- Tampilkan Pesan Error dari Controller (Jika ada) --}}
                            @if (session('error'))
                                <div class="alert alert-danger">
                                    {{ session('error') }}
                                </div>
                            @endif

                            {{-- SATU FORM UTAMA --}}
                            <form action="/booking/store" method="POST">
                                @csrf
                                {{-- Input Hidden untuk Kategori --}}
                                <input type="hidden" name="kategori" value="mesin_cuci">

                                {{-- 1. Input Tanggal & Jam --}}
                                <div class="row mb-3">
                                    <div class="col-md-6">
                                        <label class="form-label">Tanggal Penggunaan</label>
                                        <input type="date" class="form-control" name="tanggal"
                                            value="{{ old('tanggal') }}" required>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">Jam Mulai</label>
                                        <input type="time" class="form-control" name="start_time"
                                            value="{{ old('start_time') }}" required>
                                    </div>
                                </div>

                                {{-- 2. Input Jumlah Mesin --}}
                                <div class="mb-3">
                                    <label class="form-label fw-bold">Berapa mesin yang ingin digunakan?</label>
                                    <select class="form-select" name="jumlah_mesin" required>
                                        <option value="" selected disabled>-- Pilih Jumlah --</option>
                                        @for ($i = 1; $i <= 5; $i++)
                                            <option value="{{ $i }}"
                                                {{ old('jumlah_mesin') == $i ? 'selected' : '' }}>
                                                {{ $i }} Mesin
                                            </option>
                                        @endfor
                                    </select>
                                </div>

                                {{-- 3. Pilihan Nomor Mesin (Bebas pilih, divalidasi nanti) --}}
                                <div class="mb-3">
                                    <label class="form-label d-block text-muted">Pilih Nomor Mesin:</label>
                                    <div class="row g-2 text-center">
                                        @for ($j = 1; $j <= 5; $j++)
                                            <div class="col">
                                                <input type="checkbox" class="btn-check" name="mesin_dipilih[]"
                                                    id="mesin{{ $j }}" value="{{ $j }}"
                                                    {{ is_array(old('mesin_dipilih')) && in_array($j, old('mesin_dipilih')) ? 'checked' : '' }}>
                                                <label class="btn btn-outline-primary w-100"
                                                    for="mesin{{ $j }}">M-{{ $j }}</label>
                                            </div>
                                        @endfor
                                    </div>
                                    <small class="form-text text-muted">Pastikan jumlah mesin yang dicentang sama dengan
                                        pilihan jumlah mesin di atas.</small>
                                </div>

                                {{-- Tombol Submit --}}
                                <button type="submit" class="btn btn-primary w-100">
                                    Booking Mesin Cuci Sekarang
                                </button>
                            </form>
                        </div>
                    @elseif(request('kategori_fasilitas') == 'theater')
                        <div class="animate-fade-in">
                            <h5 class="text-primary mb-3">Formulir Peminjaman Theater</h5>
                            <form action="/booking/store" method="POST">
                                @csrf
                                {{-- Pastikan value kategori sesuai --}}
                                <input type="hidden" name="kategori" value="cws">

                                {{-- Input Tanggal --}}
                                <div class="mb-3">
                                    <label class="form-label">Tanggal Peminjaman</label>
                                    <input type="date" class="form-control" name="tanggal" required>
                                </div>

                                {{-- Input Jumlah Orang --}}
                                <div class="mb-3">
                                    <label class="form-label">Jumlah Orang</label>
                                    <input type="number" class="form-control" name="jumlah_orang"
                                        placeholder="Masukkan jumlah orang" min="1" required>
                                </div>

                                {{-- Input Jam Mulai & Selesai --}}
                                <div class="row mb-3">
                                    <div class="col-md-6">
                                        <label class="form-label">Jam Mulai</label>
                                        <input type="time" class="form-control" name="start_time" required step="60">
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">Jam Selesai</label>
                                        <input type="time" class="form-control" name="end_time" required>
                                    </div>
                                </div>

                                {{-- Input Keterangan Bebas --}}
                                <div class="mb-4">
                                    <label class="form-label">Keterangan Tambahan</label>
                                    <textarea class="form-control" name="keterangan" rows="3"
                                        placeholder="Contoh: Untuk keperluan kerja kelompok / meeting project"></textarea>
                                </div>

                                <button type="submit" class="btn btn-primary w-100">Booking Co-Working Space</button>
                            </form>
                        </div>
                    @elseif(request('kategori_fasilitas') == 'cws')
                        {{-- Form Theater --}}
                        <div>
                            <h5 class="text-warning mb-3">Formulir Peminjaman Theater</h5>
                            <form action="/booking/store" method="POST">
                                @csrf
                                <input type="hidden" name="kategori" value="theater">
                                <div class="mb-3">
                                    <label class="form-label">Tanggal Nonton</label>
                                    <input type="date" class="form-control" name="tanggal" required>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Judul Film / Acara</label>
                                    <input type="text" class="form-control" name="judul_acara"
                                        placeholder="Contoh: Nobar Timnas">
                                </div>
                                <button type="submit" class="btn btn-warning w-100">Booking Theater</button>
                            </form>
                        </div>
                        @elseif (request('kategori_fasilitas') == 'sergun')
                            {{-- Form Dapur --}}
                            <div class="animate-fade-in">
                                <h5 class="text-primary mb-3">Formulir Peminjaman Serba Guna</h5>
                                <form action="/booking/store" method="POST">
                                    @csrf
                                    <input type="hidden" name="kategori" value="dapur">
                                    <div class="mb-3">
                                        <label class="form-label">Tanggal Peminjaman</label>
                                        <input type="date" class="form-control" name="tanggal" required>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">Bagian Sergun yang akan di booking</label>
                                        <select class="form-select" name="item_dapur">
                                            <option value="">-- Pilih bagian sergun yang akan di booking --</option>
                                            <option value="area_sergun_A">Area Sergun A (dekat kios dan tangga AG)</option>
                                            <option value="area_sergun_B">Area Sergun B (dekat UKS)</option>
                                            <option value="lainnya">Lainnya</option>
                                        </select>
                                    </div>
                                    <div class="row mb-3">
                                        <div class="col-md-6">
                                            <label class="form-label">Jam Mulai</label>
                                            <input type="time" class="form-control" name="start_time" required>
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label">Jam Selesai</label>
                                            <input type="time" class="form-control" name="end_time" required>
                                        </div>
                                    </div>
                                    <button type="submit" class="btn btn-primary w-100">Booking Sergun</button>
                                </form>
                            </div>
                        @else
                            {{-- TAMPILAN AWAL (KOSONG / INSTRUKSI) --}}
                            <div class="text-center py-5">
                                <h5 class="text-muted">Silakan pilih kategori fasilitas untuk melanjutkan.</h5>
                                <p class="text-muted small">Formulir peminjaman akan muncul setelah Anda memilih salah satu
                                    menu di atas.</p>
                            </div>
                        @endif

                </div>
            </div>
        </div>
    </div>
@endsection
