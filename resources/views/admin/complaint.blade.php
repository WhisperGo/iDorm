@extends('penghuni.layouts')

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
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th class="text-center">No.
                                        <div style="float:right;">
                                        </div>
                                    </th>
                                    <th class="text-center">Date
                                        <div style="float:right;">
                                        </div>
                                    </th>
                                    <th class="text-center">Facilities
                                        <div style="float:right;">
                                        </div>
                                    </th>
                                    <th class="text-center">Description
                                        <div style="float:right;">
                                        </div>
                                    </th>
                                    <th class="text-center">Status
                                        <div style="float:right;">
                                        </div>
                                    </th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr class="">
                                    <td class="text-center"> 1
                                    </td>
                                    <td class="text-center">
                                        {{ Str::limit('Lorem ipsum dolor sit amet consectetur adipisicing elit. Officia, maxime.', 50) }}
                                    </td>
                                    <td class="text-center">
                                        {{ Str::limit('Lorem ipsum dolor sit amet consectetur, adipisicing elit. Obcaecati, dolore.', 50) }}
                                    </td>
                                    <td class="text-center">
                                        {{ Str::limit('Lorem ipsum dolor sit, amet consectetur adipisicing elit. Repellat, sit!', 50) }}
                                    </td>
                                    <td>
                                        <span class="badge bg-primary">Done</span>
                                        <span class="badge bg-success">Accepted</span>
                                        <span class="badge bg-danger">Not Accepted</span>
                                        <span class="badge bg-warning text-dark">Pending</span>
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
