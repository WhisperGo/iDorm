@extends('penghuni.layouts')

@section('content')
    <style>
        /* Styling untuk Custom Radio Tipe Kos */
        .tipe-kos-option {
            cursor: pointer;
            transition: all 0.3s ease;
            border: 2px solid #eee;
            border-radius: 12px;
            text-align: center;
            padding: 15px;
            display: block;
        }

        .btn-check:checked+.tipe-kos-option {
            border-color: #3a57e8;
            background-color: rgba(58, 87, 232, 0.1);
            transform: translateY(-5px);
            box-shadow: 0 5px 15px rgba(58, 87, 232, 0.2);
        }

        /* CUSTOM BORDER UNTUK CHECKBOX/SWITCH */
        .form-check-input {
            border: 2px solid #3a57e8 !important;
            /* Border biru primer */
            cursor: pointer;
        }

        #selectWilayah {
            text-align: center;
            text-align-last: center;
            /* Ini kunci buat nengahin teks di dalam select */
            moz-text-align-last: center;
            /* Untuk Firefox */
        }

        /* Opsional: Biar opsi di dalamnya juga ikutan tengah (hanya jalan di beberapa browser) */
        #selectWilayah option {
            text-align: center;
        }

        .form-check-input:checked {
            background-color: #3a57e8 !important;
            border-color: #3a57e8 !important;
        }

        /* Styling List Fasilitas */
        .facility-item {
            padding: 15px;
            border-radius: 12px;
            border: 1px solid transparent;
            transition: all 0.2s;
        }

        .facility-item:hover {
            background: #fdfdfd;
            border-color: #eee;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
        }

        .facility-icon {
            font-size: 1.2rem;
            color: #3a57e8;
            margin-right: 10px;
        }
    </style>

    <div class="container-fluid py-4">
        <div class="row justify-content-center">
            <div class="col-lg-10">
                <div class="card border-0 shadow-lg">
                    <div class="card-header bg-primary py-3">
                        <h5 class="text-white mb-0 text-center"></i>Smart Prediction Kos</h5>
                    </div>
                    <div class="card-body p-4">
                        <form action="{{ route('prediction.store') }}" method="POST">
                            @csrf

                            {{-- Step 1: Pilih Wilayah --}}
                            <div class="mb-5 text-center">
                                <label class="form-label h6 fw-bold mb-3">Mau Cari Kos di Daerah Mana?</label>
                                <div class="col-md-6 mx-auto">
                                    <select name="region" id="selectWilayah"
                                        class="form-select form-select-lg border-primary shadow-none justify-content-center text-align-last: center">
                                        <option value="" selected disabled>-- Pilih Wilayah --</option>
                                        <option value="Jakarta Pusat">Jakarta Pusat</option>
                                        <option value="Jakarta Selatan">Jakarta Selatan</option>
                                        <option value="Jakarta Utara">Jakarta Utara</option>
                                        <option value="Yogyakarta">Yogyakarta</option>
                                    </select>
                                </div>
                            </div>

                            <div id="formSection" style="display: none;">
                                <div class="row g-4">
                                    {{-- Tipe Kos --}}
                                    <div class="col-12">
                                        <label class="form-label fw-bold d-block text-center mb-3">Tipe Penghuni Kos</label>
                                        <div class="row g-3">
                                            <div class="col-md-4">
                                                <input type="radio" class="btn-check" name="tipe_kos" value="cowo"
                                                    id="r1" required>
                                                <label class="tipe-kos-option shadow-sm" for="r1">
                                                    <i class="bi bi-gender-male text-primary"></i>
                                                    <span class="fw-bold">Putra</span>
                                                </label>
                                            </div>
                                            <div class="col-md-4">
                                                <input type="radio" class="btn-check" name="tipe_kos" value="cewe"
                                                    id="r2">
                                                <label class="tipe-kos-option shadow-sm" for="r2">
                                                    <i class="bi bi-gender-female text-danger"></i>
                                                    <span class="fw-bold">Putri</span>
                                                </label>
                                            </div>
                                            <div class="col-md-4">
                                                <input type="radio" class="btn-check" name="tipe_kos" value="campur"
                                                    id="r3">
                                                <label class="tipe-kos-option shadow-sm" for="r3">
                                                    <i class="bi bi-gender-ambiguous text-warning"></i>
                                                    <span class="fw-bold">Campur</span>
                                                </label>
                                            </div>
                                        </div>
                                    </div>

                                    {{-- Budget & Luas --}}
                                    <div class="col-md-6">
                                        <label class="form-label fw-bold">Maksimal Budget (Rp)</label>
                                        <div class="input-group">
                                            <span class="input-group-text bg-white border-primary border-end-0">Rp</span>
                                            <input type="text" id="inputHarga"
                                                class="form-control border-primary border-start-0 shadow-none"
                                                placeholder="Contoh: 2.000.000" required>
                                            <input type="hidden" name="harga" id="hargaMurni">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label fw-bold">Luas Kamar (m²)</label>
                                        <div class="input-group">
                                            <input type="number" name="luas_kamar"
                                                class="form-control border-primary shadow-none" placeholder="Contoh: 12"
                                                required>
                                            <span class="input-group-text bg-white border-primary">m²</span>
                                        </div>
                                    </div>

                                    {{-- Geolokasi --}}
                                    <div
                                        class="col-12 bg-soft-primary p-4 rounded-3 border border-primary border-opacity-25">
                                        <label class="form-label fw-bold mb-1"><i
                                                class="bi bi-geo-alt-fill text-primary"></i> Titik Lokasi Acuan</label>
                                        <p class="text-muted small mb-3">Gunakan lokasi kamu saat ini untuk menghitung jarak
                                            ke BCA atau tempat kerja.</p>
                                        <div class="row g-2">
                                            <div class="col-md-5"><input type="text" id="lat" name="latitude"
                                                    class="form-control border-0" placeholder="Latitude" readonly></div>
                                            <div class="col-md-5"><input type="text" id="lng" name="longitude"
                                                    class="form-control border-0" placeholder="Longitude" readonly></div>
                                            <div class="col-md-2"><button type="button" onclick="getLocation()"
                                                    class="btn btn-primary w-100 fw-bold">CEK</button></div>
                                        </div>
                                    </div>

                                    {{-- Checklist Fasilitas Bordered --}}
                                    <div class="col-12">
                                        <label class="form-label fw-bold text-dark h5 mb-3">Fasilitas & Layanan
                                            Tambahan</label>
                                        <div class="row">
                                            {{-- KM Dalam --}}
                                            <div class="col-md-6 facility-item d-flex align-items-start">
                                                <div class="form-check form-switch me-3">
                                                    <input class="form-check-input" type="checkbox" name="is_km_dalam">
                                                </div>
                                                <div>
                                                    <label class="fw-bold mb-0">Kamar Mandi Dalam</label>
                                                    <small class="d-block text-muted">Kamar mandi pribadi di dalam kamar
                                                        (Bukan KM Luar/Bersama).</small>
                                                </div>
                                            </div>
                                            {{-- Water Heater --}}
                                            <div class="col-md-6 facility-item d-flex align-items-start">
                                                <div class="form-check form-switch me-3">
                                                    <input class="form-check-input" type="checkbox"
                                                        name="is_water_heater">
                                                </div>
                                                <div>
                                                    <label class="fw-bold mb-0">Water Heater</label>
                                                    <small class="d-block text-muted">Tersedia pemanas air mandi
                                                        elektrik/gas di dalam kamar mandi.</small>
                                                </div>
                                            </div>
                                            {{-- Furnished --}}
                                            <div class="col-md-6 facility-item d-flex align-items-start">
                                                <div class="form-check form-switch me-3">
                                                    <input class="form-check-input" type="checkbox" name="is_furnished">
                                                </div>
                                                <div>
                                                    <label class="fw-bold mb-0">Fully Furnished</label>
                                                    <small class="d-block text-muted">Sudah termasuk Kasur, Lemari, Meja,
                                                        dan Kursi minimalis.</small>
                                                </div>
                                            </div>
                                            {{-- Listrik --}}
                                            <div class="col-md-6 facility-item d-flex align-items-start">
                                                <div class="form-check form-switch me-3">
                                                    <input class="form-check-input" type="checkbox"
                                                        name="is_listrik_free">
                                                </div>
                                                <div>
                                                    <label class="fw-bold mb-0">Gratis
                                                        Listrik</label>
                                                    <small class="d-block text-muted">Biaya sewa sudah termasuk pemakaian
                                                        listrik standar (Tanpa Token).</small>
                                                </div>
                                            </div>
                                            {{-- Parkir --}}
                                            <div class="col-md-6 facility-item d-flex align-items-start">
                                                <div class="form-check form-switch me-3">
                                                    <input class="form-check-input" type="checkbox"
                                                        name="is_parkir_mobil">
                                                </div>
                                                <div>
                                                    <label class="fw-bold mb-0">Parkir Mobil</label>
                                                    <small class="d-block text-muted">Tersedia slot parkir khusus mobil dan
                                                        akses jalan yang lebar.</small>
                                                </div>
                                            </div>
                                            {{-- Mesin Cuci --}}
                                            <div class="col-md-6 facility-item d-flex align-items-start">
                                                <div class="form-check form-switch me-3">
                                                    <input class="form-check-input" type="checkbox" name="is_mesin_cuci">
                                                </div>
                                                <div>
                                                    <label class="fw-bold mb-0">Fasilitas Cuci</label>
                                                    <small class="d-block text-muted">Tersedia Mesin Cuci bersama atau jasa
                                                        Laundry internal kos.</small>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-12 mt-5 text-center">
                                        <button type="submit"
                                            class="btn btn-primary btn-lg px-5 shadow rounded-pill fw-bold mb-3">
                                            MULAI PREDIKSI SEKARANG
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        // JS Logic tetap sama, saya hanya rapikan variabelnya
        document.getElementById('selectWilayah').addEventListener('change', function() {
            document.getElementById('formSection').style.display = 'block';
            window.scrollBy({
                top: 300,
                behavior: 'smooth'
            });
        });

        function getLocation() {
            if (navigator.geolocation) {
                navigator.geolocation.getCurrentPosition(pos => {
                    document.getElementById('lat').value = pos.coords.latitude;
                    document.getElementById('lng').value = pos.coords.longitude;
                });
            }
        }

        const inputHarga = document.getElementById('inputHarga');
        const hargaMurni = document.getElementById('hargaMurni');

        inputHarga.addEventListener('keyup', function() {
            let val = this.value.replace(/[^,\d]/g, '').toString();
            let split = val.split(',');
            let sisa = split[0].length % 3;
            let rupiah = split[0].substr(0, sisa);
            let ribuan = split[0].substr(sisa).match(/\d{3}/gi);

            if (ribuan) {
                let separator = sisa ? '.' : '';
                rupiah += separator + ribuan.join('.');
            }

            this.value = rupiah;
            hargaMurni.value = val.replace(/\./g, '');
        });
    </script>
@endsection
