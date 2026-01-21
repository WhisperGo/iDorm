@extends('pengelola.layouts')

@section('content')
    <div class="row">
        <div class="col-sm-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center flex-wrap">
                    <div class="header-title">
                        <h4 class="card-title mb-0">Announcement</h4>
                    </div>

                    <div class="d-flex gap-3">
                        @include('search.searchByText')
                        @include('search.searchByDate')
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
                                    <th class="text-center">Time
                                        <div style="float:right;">
                                        </div>
                                    </th>
                                    <th class="text-center">Name
                                        <div style="float:right;">
                                        </div>
                                    </th>
                                    <th class="text-center">Facilities
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
                                    <td class="text-center">
                                        {{ Str::limit('Lorem ipsum dolor sit amet, consectetur adipisicing elit. Blanditiis, debitis?', 50) }}
                                    </td>
                                    <td class="text-center">
                                        <button class="btn btn-success">Accept</button>
                                        <button class="btn btn-danger">Decline</button>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="d-flex justify-content-end p-4">
                    <nav aria-label="Page navigation example">
                        <ul class="pagination">
                            <li class="page-item"><a class="page-link" href="#">Previous</a></li>
                            <li class="page-item"><a class="page-link" href="#">1</a></li>
                            <li class="page-item"><a class="page-link" href="#">2</a></li>
                            <li class="page-item"><a class="page-link" href="#">3</a></li>
                            <li class="page-item"><a class="page-link" href="#">Next</a></li>
                        </ul>
                    </nav>
                </div>
            </div>
        </div>
    </div>
    </div


    </body>

    </html>
@endsection
