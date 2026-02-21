@extends('layouts.app')

@section('content')
    <div class="container-fluid py-5">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="card border-0 shadow-sm rounded-4 mb-4">
                    <div class="card-body text-center py-4 px-5">
                        <h2 class="fw-bold text-dark mb-2" style="letter-spacing: -0.5px;">
                            Hasil Analisis iDorm AI
                        </h2>
                        <p class="text-secondary mb-0 fw-medium">
                            Laporan Prediksi Harga Wajar Wilayah
                            <span class="text-primary text-uppercase">
                                {{ str_replace('_', ' ', $res['metadata']['region']) }}
                            </span>
                        </p>
                    </div>
                </div>

                <div class="card border-0 shadow-lg overflow-hidden" style="border-radius: 20px;">
                    <div class="card-body p-0">
                        <div class="row g-0">
                            {{-- Sisi Kiri: Status Besar --}}
                            <div
                                class="col-md-5 bg-{{ $res['result']['analysis']['color_code'] }} p-5 text-center text-white d-flex align-items-center justify-content-center">
                                <div>
                                    <i class="bi bi-shield-check display-1 mb-3"></i>
                                    <h1 class="fw-bold display-5">{{ $res['result']['analysis']['verdict'] }}</h1>
                                    <p class="lead opacity-75">Status Kewajaran Harga</p>
                                </div>
                            </div>

                            {{-- Sisi Kanan: Detail Statistik --}}
                            <div class="col-md-7 p-5 bg-white">
                                <h4 class="fw-bold mb-4 text-dark">Ringkasan Properti</h4>

                                <div class="mb-4">
                                    <label class="text-muted small d-block">Harga yang Ditawarkan</label>
                                    <h2 class="fw-bold text-dark">Rp
                                        {{ number_format($res['result']['offered_price'], 0, ',', '.') }}</h2>
                                </div>

                                <div class="row g-4 mb-4">
                                    <div class="col-6">
                                        <div class="p-3 bg-light rounded-3">
                                            <small class="text-muted d-block italic">Batas Bawah (Min)</small>
                                            <span class="fw-bold">Rp
                                                {{ number_format($res['result']['fair_range']['min'], 0, ',', '.') }}</span>
                                        </div>
                                    </div>
                                    <div class="col-6">
                                        <div class="p-3 bg-light rounded-3">
                                            <small class="text-muted d-block">Batas Atas (Max)</small>
                                            <span class="fw-bold">Rp
                                                {{ number_format($res['result']['fair_range']['max'], 0, ',', '.') }}</span>
                                        </div>
                                    </div>
                                </div>

                                <hr class="my-4">

                                <div class="d-flex justify-content-start align-items-center gap-3 flex-column">
                                    {{-- Tombol Cek Lagi --}}
                                    <a href="{{ route('prediction.index') }}"
                                        class="btn btn-outline-secondary rounded-pill px-4">
                                        <i class="bi bi-arrow-left me-2"></i>Cek Lagi
                                    </a>

                                    {{-- TOMBOL DOWNLOAD PDF --}}
                                    <a href="{{ route('prediction.download') }}"
                                        class="btn btn-danger rounded-pill px-4 shadow">
                                        <i class="bi bi-file-earmark-pdf-fill me-2"></i>Download Laporan PDF
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Info Tambahan --}}
                <div class="mt-4 text-center">
                    <p class="text-muted small">
                        Data ini dihasilkan secara otomatis oleh Artificial Intelligence iDorm berdasarkan tren pasar di
                        {{ str_replace('_', ' ', $res['metadata']['region']) }}.
                    </p>
                </div>
            </div>
        </div>
    </div>
@endsection
