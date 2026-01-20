@extends('pengelola.layouts')

@section('content')
    <div class="row">
        <div class="col-sm-12">
            <div class="card shadow-sm">
                {{-- Header dengan padding yang pas --}}
                <div class="card-header">

                    {{-- BARIS 1: Tombol Add Sendirian di Atas --}}
                    <div class="mb-3">
                        <a href="#" class="btn btn-primary d-inline-flex align-items-center px-4 py-2">
                            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="currentColor"
                                class="bi bi-plus-lg me-2" viewBox="0 0 16 16">
                                <path fill-rule="evenodd"
                                    d="M8 2a.5.5 0 0 1 .5.5v5h5a.5.5 0 0 1 0 1h-5v5a.5.5 0 0 1-1 0v-5h-5a.5.5 0 0 1 0-1h5v-5A.5.5 0 0 1 8 2" />
                            </svg>
                            Add
                        </a>
                    </div>

                    {{-- BARIS 2: Paket Sejajar (Announcement di Kiri, Search di Kanan) --}}
                    <div class="d-flex justify-content-between align-items-center flex-wrap">

                        {{-- Kiri --}}
                        <div class="header-title">
                            <h4 class="card-title mb-0 fw-bold">Announcement</h4>
                        </div>

                        {{-- Kanan --}}
                        <div class="d-flex align-items-center gap-2">
                            <span class="text-secondary fw-medium">Search:</span>
                            <input type="text" class="form-control" style="width: 250px;" placeholder="Type here..."
                                aria-label="Search">
                        </div>
                    </div>
                </div>

                <div class="card-body mt-0">
                    <div class="table-responsive">
                        <table class="table table-bordered align-middle">
                            <thead>
                                <tr>
                                    <th class="text-center" width="5%">No.</th>
                                    <th class="text-center">Announcement Date</th>
                                    <th class="text-center">Announcement Title</th>
                                    <th class="text-center">Description</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td class="text-center">1</td>
                                    <td class="text-center">30 June 2025</td>
                                    <td class="text-center fw-bold">General Cleaning</td>
                                    <td class="text-wrap">General Cleaning will be held on September 1, 2025...</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
