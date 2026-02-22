@extends('layouts.app')

@section('content')
    <style>
        /* Styling untuk Custom Radio Tipe Kos */
        .tipe-kos-option {
            cursor: pointer;
            transition: all 0.3s ease;
            border: 2px solid #e9ecef;
            border-radius: 12px;
            text-align: center;
            padding: 20px 15px;
            display: block;
            background-color: #fff;
        }

        .btn-check:checked+.tipe-kos-option {
            border-color: var(--bs-primary);
            background-color: rgba(58, 87, 232, 0.05);
            transform: translateY(-3px);
            box-shadow: 0 .5rem 1rem rgba(0, 0, 0, .08) !important;
        }

        /* CUSTOM BORDER UNTUK CHECKBOX/SWITCH */
        .form-check-input {
            border: 2px solid var(--bs-primary) !important;
            cursor: pointer;
        }

        .form-check-input:checked {
            background-color: var(--bs-primary) !important;
            border-color: var(--bs-primary) !important;
        }

        /* Styling List Fasilitas */
        .facility-item {
            padding: 16px;
            border-radius: 16px;
            border: 1px solid #e9ecef;
            transition: all 0.2s ease;
            background-color: #fff;
        }

        .facility-item:hover {
            border-color: #ced4da;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.04);
            transform: translateY(-2px);
        }

        #selectWilayah {
            text-align: center;
            text-align-last: center;
            -moz-text-align-last: center;
        }

        #selectWilayah option {
            text-align: center;
        }

        #pac-input {
            background-color: #fff;
            font-family: Roboto;
            font-size: 15px;
            font-weight: 300;
            padding: 0 11px 0 13px;
            text-overflow: ellipsis;
            width: 400px;
            position: absolute;
            top: 10px;
            left: 10px !important;
            z-index: 5;
            height: 48px;
            border: none;
            border-radius: 8px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
        }

        @media (max-width: 576px) {
            #pac-input {
                width: 80%;
                left: 10% !important;
            }
        }
    </style>

    <div class="container-fluid py-4">
        <div class="row justify-content-center">
            <div class="col-xl-10 col-lg-11">
                <div class="card shadow-sm border-0 rounded-4">
                    <div
                        class="card-header bg-white border-bottom py-4 px-4 px-md-5 d-flex justify-content-between align-items-center rounded-top-4">
                        <div class="header-title">
                            <h4 class="card-title fw-bold text-dark mb-0" style="letter-spacing: -0.5px;">Smart Prediction Kos
                            </h4>
                        </div>
                    </div>

                    <div class="card-body p-4 p-md-5">
                        <form action="{{ route('prediction.store') }}" method="POST" id="predictionForm" novalidate>
                            @csrf

                            {{-- Step 1: Pilih Wilayah --}}
                            <div class="mb-5 text-center">
                                <label class="form-label text-uppercase fw-bold text-muted mb-3"
                                    style="letter-spacing: 0.5px;">Mau Cari Kos di Daerah Mana?</label>
                                <div class="col-md-6 col-lg-5 mx-auto">
                                    <select name="region" id="selectWilayah"
                                        class="form-select form-select-lg shadow-sm border-2 rounded-3 bg-light"
                                        style="cursor: pointer; text-align: center; text-align: center;">
                                        <option value="" selected disabled>-- Pilih Wilayah --</option>
                                        <option value="Jakarta Pusat">Jakarta Pusat</option>
                                        <option value="Jakarta Selatan">Jakarta Selatan</option>
                                        <option value="Jakarta Utara">Jakarta Utara</option>
                                        <option value="Yogyakarta">Yogyakarta</option>
                                    </select>
                                    <div class="invalid-feedback text-start mt-2 px-2">Wilayah wajib dipilih.</div>
                                </div>
                            </div>

                            <div id="formSection" style="display: none;">
                                <hr class="mb-5 border-light">

                                <div class="row g-5">
                                    {{-- Tipe Kos --}}
                                    <div class="col-12">
                                        <label class="form-label fw-bold d-block mb-3 text-dark">Tipe Penghuni Kos</label>
                                        <div class="row g-3">
                                            <div class="col-md-4">
                                                <input type="radio" class="btn-check" name="tipe_kos" value="cowo"
                                                    id="r1" required>
                                                <label class="tipe-kos-option shadow-none" for="r1">
                                                    <div class="bg-primary-subtle rounded-circle d-inline-flex align-items-center justify-content-center mb-2"
                                                        style="width: 50px; height: 50px;">
                                                        <i class="bi bi-gender-male text-primary fs-4"></i>
                                                    </div>
                                                    <span class="fw-bold d-block text-dark mt-1">Putra</span>
                                                </label>
                                            </div>
                                            <div class="col-md-4">
                                                <input type="radio" class="btn-check" name="tipe_kos" value="cewe"
                                                    id="r2">
                                                <label class="tipe-kos-option shadow-none" for="r2">
                                                    <div class="bg-danger-subtle rounded-circle d-inline-flex align-items-center justify-content-center mb-2"
                                                        style="width: 50px; height: 50px;">
                                                        <i class="bi bi-gender-female text-danger fs-4"></i>
                                                    </div>
                                                    <span class="fw-bold d-block text-dark mt-1">Putri</span>
                                                </label>
                                            </div>
                                            <div class="col-md-4">
                                                <input type="radio" class="btn-check" name="tipe_kos" value="campur"
                                                    id="r3">
                                                <label class="tipe-kos-option shadow-none" for="r3">
                                                    <div class="bg-warning-subtle rounded-circle d-inline-flex align-items-center justify-content-center mb-2"
                                                        style="width: 50px; height: 50px;">
                                                        <i class="bi bi-gender-ambiguous text-warning fs-4"></i>
                                                    </div>
                                                    <span class="fw-bold d-block text-dark mt-1">Campur</span>
                                                </label>
                                            </div>
                                        </div>
                                        <div class="invalid-feedback mt-3 px-2" id="tipeKosError">Tipe penghuni kos wajib
                                            dipilih.</div>
                                    </div>

                                    <div class="col-12">
                                        <hr class="border-light m-0">
                                    </div>

                                    {{-- Budget & Luas --}}
                                    <div class="col-md-6">
                                        <label class="form-label fw-bold text-dark">Maksimal Budget (Rp)</label>
                                        <div class="input-group input-group-lg shadow-sm rounded-3">
                                            <span
                                                class="input-group-text bg-white border-2 border-end-0 text-muted">Rp</span>
                                            <input type="text" id="inputHarga"
                                                class="form-control border-2 border-start-0 shadow-none px-2"
                                                placeholder="Contoh: 2.000.000" required>
                                            <div class="invalid-feedback text-start px-2">Maksimal budget wajib diisi.</div>
                                            <input type="hidden" name="harga" id="hargaMurni">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label fw-bold text-dark">Luas Kamar (m²)</label>
                                        <div class="input-group input-group-lg shadow-sm rounded-3">
                                            <input type="number" name="luas_kamar" id="inputLuas"
                                                class="form-control border-2 border-end-0 shadow-none"
                                                placeholder="Contoh: 12" required>
                                            <span
                                                class="input-group-text bg-white border-2 border-start-0 text-muted">m²</span>
                                            <div class="invalid-feedback text-start px-2">Luas kamar wajib diisi.</div>
                                        </div>
                                    </div>

                                    <div class="col-12">
                                        <hr class="border-light m-0">
                                    </div>

                                    {{-- Map & Geolocation --}}
                                    <div class="col-12">
                                        <label class="form-label fw-bold text-dark d-flex align-items-center mb-3">
                                            <i class="bi bi-geo-alt-fill text-primary me-2"></i> Titik Lokasi Acuan
                                        </label>
                                        <p class="text-muted small mb-3">Ketik alamat pada kotak pencarian di dalam peta,
                                            atau geser pin merah secara manual ke lokasi yang Anda tuju.</p>

                                        <div
                                            class="card border-0 shadow-sm rounded-4 overflow-hidden mb-4 position-relative">
                                            <input id="pac-input" class="form-control" type="text"
                                                placeholder="Cari alamat atau nama tempat...">
                                            <div id="map" style="height: 450px; width: 100%;"></div>
                                            <input type="hidden" name="latitude" id="latitude">
                                            <input type="hidden" name="longitude" id="longitude">
                                        </div>
                                        <div class="invalid-feedback text-start px-2 mb-3" id="mapError">Titik lokasi
                                            acuan (Peta GPS) wajib dipilih.</div>

                                        <div
                                            class="bg-light p-4 rounded-4 border-0 d-flex flex-column flex-md-row gap-3 align-items-md-center justify-content-between">
                                            <div class="row g-3 flex-grow-1">
                                                <div class="col-sm-6">
                                                    <label class="text-muted small fw-medium mb-1">Latitude</label>
                                                    <input type="text" id="lat" name="latitude"
                                                        class="form-control border-0 bg-white shadow-sm font-monospace text-muted"
                                                        placeholder="Latitude" readonly required>
                                                </div>
                                                <div class="col-sm-6">
                                                    <label class="text-muted small fw-medium mb-1">Longitude</label>
                                                    <input type="text" id="lng" name="longitude"
                                                        class="form-control border-0 bg-white shadow-sm font-monospace text-muted"
                                                        placeholder="Longitude" readonly required>
                                                </div>
                                            </div>
                                            <div
                                                class="mt-3 mt-md-0 d-flex flex-column align-items-start align-items-md-end">
                                                <label class="d-none d-md-block text-transparent small mb-1">&nbsp;</label>
                                                <button type="button" onclick="getLocation()"
                                                    class="btn btn-outline-primary fw-medium px-4 d-flex align-items-center shadow-sm">
                                                    <i class="bi bi-crosshair me-2"></i> Gunakan GPS Saya
                                                </button>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-12">
                                        <hr class="border-light m-0">
                                    </div>

                                    {{-- Checklist Fasilitas --}}
                                    <div class="col-12">
                                        <label class="form-label fw-bold text-dark h5 mb-4">Fasilitas & Layanan
                                            Tambahan</label>
                                        <div class="row g-3">
                                            {{-- KM Dalam --}}
                                            <div class="col-md-6 col-lg-4">
                                                <div class="facility-item h-100 d-flex align-items-start">
                                                    <div class="form-check form-switch me-3 mt-1">
                                                        <input class="form-check-input fs-5 shadow-none" type="checkbox"
                                                            name="is_km_dalam">
                                                    </div>
                                                    <div>
                                                        <label class="fw-bold mb-1 text-dark">Kamar Mandi Dalam</label>
                                                        <small class="d-block text-muted lh-sm">Kamar mandi pribadi di
                                                            dalam kamar.</small>
                                                    </div>
                                                </div>
                                            </div>
                                            {{-- Water Heater --}}
                                            <div class="col-md-6 col-lg-4">
                                                <div class="facility-item h-100 d-flex align-items-start">
                                                    <div class="form-check form-switch me-3 mt-1">
                                                        <input class="form-check-input fs-5 shadow-none" type="checkbox"
                                                            name="is_water_heater">
                                                    </div>
                                                    <div>
                                                        <label class="fw-bold mb-1 text-dark">Water Heater</label>
                                                        <small class="d-block text-muted lh-sm">Tersedia pemanas air
                                                            mandi.</small>
                                                    </div>
                                                </div>
                                            </div>
                                            {{-- Furnished --}}
                                            <div class="col-md-6 col-lg-4">
                                                <div class="facility-item h-100 d-flex align-items-start">
                                                    <div class="form-check form-switch me-3 mt-1">
                                                        <input class="form-check-input fs-5 shadow-none" type="checkbox"
                                                            name="is_furnished">
                                                    </div>
                                                    <div>
                                                        <label class="fw-bold mb-1 text-dark">Fully Furnished</label>
                                                        <small class="d-block text-muted lh-sm">Termasuk kasur, lemari, &
                                                            meja.</small>
                                                    </div>
                                                </div>
                                            </div>
                                            {{-- Listrik --}}
                                            <div class="col-md-6 col-lg-4">
                                                <div class="facility-item h-100 d-flex align-items-start">
                                                    <div class="form-check form-switch me-3 mt-1">
                                                        <input class="form-check-input fs-5 shadow-none" type="checkbox"
                                                            name="is_listrik_free">
                                                    </div>
                                                    <div>
                                                        <label class="fw-bold mb-1 text-dark">Gratis Listrik</label>
                                                        <small class="d-block text-muted lh-sm">Biaya sewa termasuk
                                                            pemakaian listrik.</small>
                                                    </div>
                                                </div>
                                            </div>
                                            {{-- Parkir --}}
                                            <div class="col-md-6 col-lg-4">
                                                <div class="facility-item h-100 d-flex align-items-start">
                                                    <div class="form-check form-switch me-3 mt-1">
                                                        <input class="form-check-input fs-5 shadow-none" type="checkbox"
                                                            name="is_parkir_mobil">
                                                    </div>
                                                    <div>
                                                        <label class="fw-bold mb-1 text-dark">Parkir Mobil</label>
                                                        <small class="d-block text-muted lh-sm">Tersedia slot parkir khusus
                                                            mobil.</small>
                                                    </div>
                                                </div>
                                            </div>
                                            {{-- Mesin Cuci --}}
                                            <div class="col-md-6 col-lg-4">
                                                <div class="facility-item h-100 d-flex align-items-start">
                                                    <div class="form-check form-switch me-3 mt-1">
                                                        <input class="form-check-input fs-5 shadow-none" type="checkbox"
                                                            name="is_mesin_cuci">
                                                    </div>
                                                    <div>
                                                        <label class="fw-bold mb-1 text-dark">Fasilitas Cuci</label>
                                                        <small class="d-block text-muted lh-sm">Mesin cuci bersama atau
                                                            jasa laundry.</small>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-12 mt-5 mb-2 text-center">
                                        <button type="submit"
                                            class="btn btn-primary btn-lg px-5 py-3 shadow-lg rounded-pill fw-bold"
                                            style="letter-spacing: 0.5px; transition: transform 0.2s;">
                                            MULAI PREDIKSI SEKARANG <i class="bi bi-arrow-right ms-2"></i>
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

    @push('scripts')
        <script async
            src="https://maps.googleapis.com/maps/api/js?key={{ config('services.google_maps.api_key') }}&libraries=places&callback=initMap">
        </script>

        <script>
            let map, marker, autocomplete;

            function initMap() {
                const defaultLocation = {
                    lat: -6.200000,
                    lng: 106.816666
                };

                map = new google.maps.Map(document.getElementById("map"), {
                    center: defaultLocation,
                    zoom: 13,
                    disableDefaultUI: true, // Turn off default UI for a cleaner look
                    zoomControl: true,
                    mapTypeControl: false,
                    scaleControl: true,
                    streetViewControl: false,
                    rotateControl: false,
                    fullscreenControl: true
                });

                marker = new google.maps.Marker({
                    position: defaultLocation,
                    map: map,
                    draggable: true,
                    animation: google.maps.Animation.DROP
                });

                updateInputs(defaultLocation.lat, defaultLocation.lng);

                marker.addListener("dragend", function() {
                    const pos = marker.getPosition();
                    updateInputs(pos.lat(), pos.lng());
                });

                const input = document.getElementById("pac-input");
                autocomplete = new google.maps.places.Autocomplete(input);

                // Bind the map's bounds (viewport) property to the autocomplete object,
                // so that the autocomplete requests use the current map bounds for the bounds option in the request.
                autocomplete.bindTo("bounds", map);

                autocomplete.addListener("place_changed", () => {
                    const place = autocomplete.getPlace();
                    if (!place.geometry) return;

                    if (place.geometry.viewport) {
                        map.fitBounds(place.geometry.viewport);
                    } else {
                        map.setCenter(place.geometry.location);
                        map.setZoom(17);
                    }
                    marker.setPosition(place.geometry.location);
                    updateInputs(place.geometry.location.lat(), place.geometry.location.lng());
                });
            }

            function getLocation() {
                if (navigator.geolocation) {
                    console.log("Mencari lokasi...");
                    navigator.geolocation.getCurrentPosition(pos => {
                        const myLoc = {
                            lat: pos.coords.latitude,
                            lng: pos.coords.longitude
                        };

                        map.setCenter(myLoc);
                        map.setZoom(17);
                        marker.setPosition(myLoc);

                        updateInputs(myLoc.lat, myLoc.lng);
                    }, (error) => {
                        alert("Gagal ambil GPS: " + error.message);
                    });
                } else {
                    alert("Browser kamu nggak support GPS bos!");
                }
            }

            function updateInputs(lat, lng) {
                document.getElementById('lat').value = lat;
                document.getElementById('lng').value = lng;
            }
        </script>
    @endpush

    <script>
        document.getElementById('selectWilayah').addEventListener('change', function() {
            const formSection = document.getElementById('formSection');
            if (formSection.style.display === 'none') {
                formSection.style.display = 'block';
                window.scrollBy({
                    top: 300,
                    behavior: 'smooth'
                });
            }
        });

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

        document.getElementById('predictionForm').addEventListener('submit', function(e) {
            let isValid = true;
            let firstInvalidEl = null;

            // Region Validation
            const selectWilayah = document.getElementById('selectWilayah');
            if (!selectWilayah.value) {
                selectWilayah.classList.add('is-invalid');
                isValid = false;
                if (!firstInvalidEl) firstInvalidEl = selectWilayah;
            } else {
                selectWilayah.classList.remove('is-invalid');
            }

            // Tipe Kos Validation
            const tipeKosChecked = document.querySelector('input[name="tipe_kos"]:checked');
            const tipeKosError = document.getElementById('tipeKosError');
            if (!tipeKosChecked) {
                tipeKosError.classList.add('d-block');
                isValid = false;
                if (!firstInvalidEl) firstInvalidEl = tipeKosError.parentElement;
            } else {
                tipeKosError.classList.remove('d-block');
            }

            // Harga Validation
            const inputHarga = document.getElementById('inputHarga');
            const hargaMurni = document.getElementById('hargaMurni').value;
            if (!hargaMurni || hargaMurni == '0') {
                inputHarga.classList.add('is-invalid');
                isValid = false;
                if (!firstInvalidEl) firstInvalidEl = inputHarga;
            } else {
                inputHarga.classList.remove('is-invalid');
            }

            // Luas Kamar Validation
            const inputLuas = document.getElementById('inputLuas');
            if (!inputLuas.value) {
                inputLuas.classList.add('is-invalid');
                isValid = false;
                if (!firstInvalidEl) firstInvalidEl = inputLuas;
            } else {
                inputLuas.classList.remove('is-invalid');
            }

            // Map/Lat Lng Validation
            const lat = document.getElementById('lat');
            const mapError = document.getElementById('mapError');
            if (!lat.value) {
                mapError.classList.add('d-block');
                lat.classList.add('is-invalid');
                document.getElementById('lng').classList.add('is-invalid');
                isValid = false;
                if (!firstInvalidEl) firstInvalidEl = document.getElementById('pac-input');
            } else {
                mapError.classList.remove('d-block');
                lat.classList.remove('is-invalid');
                document.getElementById('lng').classList.remove('is-invalid');
            }

            if (!isValid) {
                e.preventDefault();

                // Scroll to the first element that failed validation
                if (firstInvalidEl) {
                    firstInvalidEl.scrollIntoView({
                        behavior: 'smooth',
                        block: 'center'
                    });
                }
            }
        });

        // Add input listeners to quickly remove the red borders when the user types
        document.getElementById('selectWilayah').addEventListener('change', function() {
            this.classList.remove('is-invalid');
        });

        document.querySelectorAll('input[name="tipe_kos"]').forEach(elm => {
            elm.addEventListener('change', function() {
                document.getElementById('tipeKosError').classList.remove('d-block');
            });
        });

        document.getElementById('inputHarga').addEventListener('input', function() {
            this.classList.remove('is-invalid');
        });

        document.getElementById('inputLuas').addEventListener('input', function() {
            this.classList.remove('is-invalid');
        });
    </script>
@endsection
