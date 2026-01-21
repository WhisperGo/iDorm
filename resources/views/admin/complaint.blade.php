@extends('admin.layouts')

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
                            <h4 class="card-title mb-0 fw-bold">Keluhan</h4>
                        </div>

                        {{-- Kanan --}}
                        <div class="d-flex align-items-center gap-2">
                            <span class="text-secondary fw-medium">Search:</span>
                            <input type="text" class="form-control" style="width: 250px;" placeholder="Type here..."
                                aria-label="Search">
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="custom-datatable-entries">
                        <table id="datatable" class="table table-striped" data-toggle="data-table">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Date</th>
                                    <th>Facilities</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>1</td>
                                    <td>19-01-2025</td>
                                    <td>AC Panas</td>
                                    <td>Imanuel Yusuf Setio Budi</td>
                                </tr>
                                <tr>
                                    <td>2</td>
                                    <td>31-01-2025</td>
                                    <td>13.00 - 14.00</td>
                                    <td>Ade Reynaldi</td>
                                    <td>Theatre</td>
                                </tr>
                                <tr>
                                    <td>3</td>
                                    <td>12-01-2025</td>
                                    <td>10.00 - 18.00</td>
                                    <td>Hendry Wijaya</td>
                                    <td>Serba Guna</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
