@extends('pengelola.layouts')

@section('content')
    <div class="row">
        <div class="col-sm-12">
            <div class="card shadow-sm">
                {{-- Header dengan padding yang pas --}}
                <div class="card-header">

                    {{-- BARIS 2: Paket Sejajar (Announcement di Kiri, Search di Kanan) --}}
                    <div class="d-flex justify-content-between align-items-center flex-wrap">

                        {{-- Kiri --}}
                        <div class="header-title">
                            <h4 class="card-title mb-0 fw-bold">Resident Data</h4>
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
                                    <th class="text-center">Nama Lengkap</th>
                                    <th class="text-center">Tanggal Lahir</th>
                                    <th class="text-center" width="2%">Jenis Kelamin</th>
                                    <th class="text-center" width="2%">Nomor Kamar</th>
                                    <th class="text-center" width="2%">Peran</th>
                                    <th class="text-center" width="2%">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td class="text-center">1</td>
                                    <td class="text-center">Jason Wijaya</td>
                                    <td class="text-center">1 Januari 1910</td>
                                    <td class="text-center">L</td>
                                    <td class="text-center">B332</td>
                                    <td class="text-center">Penghuni</td>
                                    <td>
                                        <div class="d-flex flex-column gap-1">
                                            <button type="button" class="btn btn-primary">Freeze Account</button>
                                            <button type="button" class="btn btn-info">Un-Freeze Account</button>
                                        </div>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
