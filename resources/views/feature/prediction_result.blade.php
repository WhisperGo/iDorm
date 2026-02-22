@extends('layouts.app')

@section('content')
    <style>
        .hover-elevate {
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }

        .hover-elevate:hover {
            transform: translateY(-5px);
            box-shadow: 0 .5rem 1rem rgba(0, 0, 0, .15) !important;
        }

        .icon-shape {
            width: 48px;
            height: 48px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 50%;
        }

        .icon-shape-lg {
            width: 80px;
            height: 80px;
        }
    </style>

    <div class="container py-4">
        <div class="row justify-content-center">
            <!-- Increased column width to prevent clipping -->
            <div class="col-xl-10 col-lg-11">
                <!-- Header -->
                <div class="card mb-4 shadow-sm border-0 rounded-4">
                    <div class="card-body py-4 px-4 text-center">
                        <h4 class="fw-bold text-dark mb-1" style="letter-spacing: -0.5px;">iDorm AI Analysis Result</h4>
                        <p class="text-muted mb-0 small">
                            Laporan Prediksi Harga Wajar Wilayah
                            <span class="text-primary fw-bold text-uppercase ms-1">
                                {{ str_replace('_', ' ', $res['metadata']['region']) }}
                            </span>
                        </p>
                    </div>
                </div>

                <div class="row g-4 mb-4">
                    <!-- Status Kewajaran Harga -->
                    <div class="col-md-5">
                        <div
                            class="card h-100 shadow-sm border-0 rounded-4 hover-elevate overflow-hidden position-relative">
                            <!-- Top decorative bar matching color -->
                            <div class="bg-{{ $res['result']['analysis']['color_code'] }} w-100" style="height: 6px;"></div>
                            <div
                                class="card-body d-flex flex-column align-items-center justify-content-center text-center p-4">
                                <div
                                    class="bg-{{ $res['result']['analysis']['color_code'] }}-subtle p-4 rounded-circle mb-4 shadow-sm">
                                    <div class="bg-{{ $res['result']['analysis']['color_code'] }} text-white rounded-circle icon-shape-lg d-flex align-items-center justify-content-center shadow"
                                        style="width: 100px; height: 100px;">
                                        <i class="bi bi-shield-check" style="font-size: 3.5rem;"></i>
                                    </div>
                                </div>
                                <h1 class="text-{{ $res['result']['analysis']['color_code'] }} fw-bold mb-2">
                                    {{ $res['result']['analysis']['verdict'] }}
                                </h1>
                                <p class="text-muted mb-0 text-uppercase fw-medium" style="letter-spacing: 0.5px;">Status
                                    Kewajaran Harga</p>
                            </div>
                        </div>
                    </div>

                    <!-- Statistik Harga -->
                    <div class="col-md-7">
                        <div class="row g-4 h-100">
                            <!-- Prediksi AI -->
                            <div class="col-12">
                                <div
                                    class="card shadow-sm border-0 rounded-4 hover-elevate h-100 bg-primary-subtle text-primary">
                                    <div class="card-body p-4 p-lg-5 d-flex align-items-center justify-content-between">
                                        <div>
                                            <p class="text-primary mb-2 text-uppercase fw-bold"
                                                style="letter-spacing: 0.5px;">
                                                PREDIKSI AI
                                            </p>
                                            <h2 class="fw-bold mb-0 text-primary">Rp
                                                {{ number_format($res['result']['base_prediction'], 0, ',', '.') }}</h2>
                                        </div>
                                        <div class="bg-primary text-white rounded-3 p-3 d-flex align-items-center justify-content-center shadow-sm"
                                            style="width: 72px; height: 72px;">
                                            <i class="bi bi-robot fs-1"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Batas Bawah -->
                            <div class="col-lg-4 col-md-6">
                                <div class="card h-100 shadow-sm border-0 rounded-4 hover-elevate overflow-hidden">
                                    <div
                                        class="card-body p-4 text-center position-relative h-100 d-flex flex-column justify-content-center">
                                        <div class="mb-4 d-flex justify-content-center">
                                            <div class="bg-info-subtle text-info rounded-circle icon-shape shadow-sm mx-auto"
                                                style="width: 64px; height: 64px;">
                                                <i class="bi bi-graph-down-arrow fs-3"></i>
                                            </div>
                                        </div>
                                        <p class="text-muted mb-2 text-uppercase fw-bold"
                                            style="letter-spacing: 0.8px; font-size: 0.75rem;">
                                            Batas Bawah</p>
                                        <h5 class="fw-bold mb-0 text-dark text-nowrap fs-6">Rp
                                            {{ number_format($res['result']['fair_range']['min'], 0, ',', '.') }}</h5>
                                        <div class="position-absolute bottom-0 start-0 w-100 bg-info opacity-75"
                                            style="height: 6px;"></div>
                                    </div>
                                </div>
                            </div>

                            <!-- Budget Kamu -->
                            <div class="col-lg-4 col-md-12 order-lg-0 order-first">
                                <div class="card h-100 shadow-sm border-0 rounded-4 hover-elevate overflow-hidden">
                                    <div
                                        class="card-body p-4 text-center position-relative h-100 d-flex flex-column justify-content-center">
                                        <div class="mb-4 d-flex justify-content-center">
                                            <div class="bg-primary-subtle text-primary rounded-circle icon-shape shadow-sm mx-auto"
                                                style="width: 64px; height: 64px;">
                                                <i class="bi bi-tag-fill fs-3"></i>
                                            </div>
                                        </div>
                                        <p class="text-muted mb-2 text-uppercase fw-bold"
                                            style="letter-spacing: 0.8px; font-size: 0.75rem;">
                                            Budget Kamu</p>
                                        <h5 class="fw-bold mb-0 text-dark text-nowrap fs-6">Rp
                                            {{ number_format($res['result']['offered_price'], 0, ',', '.') }}</h5>
                                        <div class="position-absolute bottom-0 start-0 w-100 bg-secondary opacity-25"
                                            style="height: 6px;"></div>
                                    </div>
                                </div>
                            </div>

                            <!-- Batas Atas -->
                            <div class="col-lg-4 col-md-6">
                                <div class="card h-100 shadow-sm border-0 rounded-4 hover-elevate overflow-hidden">
                                    <div
                                        class="card-body p-4 text-center position-relative h-100 d-flex flex-column justify-content-center">
                                        <div class="mb-4 d-flex justify-content-center">
                                            <div class="bg-warning-subtle text-warning rounded-circle icon-shape shadow-sm mx-auto"
                                                style="width: 64px; height: 64px;">
                                                <i class="bi bi-graph-up-arrow fs-3"></i>
                                            </div>
                                        </div>
                                        <p class="text-muted mb-2 text-uppercase fw-bold"
                                            style="letter-spacing: 0.8px; font-size: 0.75rem;">
                                            Batas Atas</p>
                                        <h5 class="fw-bold mb-0 text-dark text-nowrap fs-6">Rp
                                            {{ number_format($res['result']['fair_range']['max'], 0, ',', '.') }}</h5>
                                        <div class="position-absolute bottom-0 start-0 w-100 bg-warning opacity-75"
                                            style="height: 6px;"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Tombol Aksi -->
                <div class="card shadow-sm border-0 rounded-4 mb-3 mt-2">
                    <div class="card-body p-4">
                        <div class="d-flex flex-column flex-sm-row justify-content-center align-items-center gap-3">
                            <a href="{{ route('prediction.index') }}"
                                class="btn btn-light border rounded-pill px-5 py-2 fw-medium hover-elevate text-dark">
                                <i class="bi bi-arrow-left me-2"></i> Check Again
                            </a>
                            <a href="{{ route('prediction.download') }}"
                                class="btn btn-primary rounded-pill px-5 py-2 shadow-sm fw-medium hover-elevate">
                                <i class="bi bi-file-earmark-pdf-fill me-2"></i> Download PDF Report
                            </a>
                        </div>
                    </div>
                </div>

                {{-- Info Tambahan --}}
                <div class="text-center opacity-75 mt-4">
                    <p class="text-muted small mb-0">
                        <i class="bi bi-info-circle me-1"></i> This data is automatically generated by iDorm AI based on
                        market trends in
                        <span class="fw-bold">{{ str_replace('_', ' ', $res['metadata']['region']) }}</span>.
                    </p>
                </div>
            </div>
        </div>
    </div>
@endsection
