@extends('penghuni.layouts')

@section('content')
    <div class="row">
        <div class="col-sm-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center flex-wrap">
                    <div class="header-title">
                        <h4 class="card-title mb-0 fw-bold">Booking Mesin Cuci</h4>
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
                                        <button class="d-none btn btn-success">Accept</button>
                                        <button class="d-none btn btn-danger">Decline</button>
                                        <button class="btn btn-warning">Pending</button>
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
    </div>

    <!-- Library Bundle Script -->
    <script src="../assets/js/core/libs.min.js"></script>

    <!-- External Library Bundle Script -->
    <script src="../assets/js/core/external.min.js"></script>

    <!-- Widgetchart Script -->
    <script src="../assets/js/charts/widgetcharts.js"></script>

    <!-- mapchart Script -->
    <script src="../assets/js/charts/vectore-chart.js"></script>
    <script src="../assets/js/charts/dashboard.js"></script>

    <!-- fslightbox Script -->
    <script src="../assets/js/plugins/fslightbox.js"></script>

    <!-- Settings Script -->
    <script src="../assets/js/plugins/setting.js"></script>

    <!-- Slider-tab Script -->
    <script src="../assets/js/plugins/slider-tabs.js"></script>

    <!-- Form Wizard Script -->
    <script src="../assets/js/plugins/form-wizard.js"></script>

    <!-- AOS Animation Plugin-->
    <script src="../assets/vendor/aos/dist/aos.js"></script>

    <!-- App Script -->
    <script src="../assets/js/hope-ui.js" defer></script>


    </body>

    </html>
@endsection
