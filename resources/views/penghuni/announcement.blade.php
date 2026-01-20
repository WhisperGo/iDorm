@extends('penghuni.layouts')

@section('content')
    <div class="row">
        <div class="col-sm-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center flex-wrap">
                    <div class="header-title">
                        <h4 class="card-title mb-0">Announcement</h4>
                    </div>

                    <div class="d-flex align-items-center">
                        <span class="me-2">Search:</span>

                        <input type="text" class="form-control" style="width: 250px;" placeholder="Type Here"
                            aria-label="Search">
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
                                    <th class="text-center">Announcement Date
                                        <div style="float:right;">
                                        </div>
                                    </th>
                                    <th class="text-center">Announcement Title
                                        <div style="float:right;">
                                        </div>
                                    </th>
                                    <th class="text-center">Description
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
                                </tr>
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
                                </tr>
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
                                </tr>
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
                                </tr>
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
                                </tr>
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
                                </tr>
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
                                </tr>
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
                                </tr>
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
                                </tr>
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
                                </tr>
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
                                </tr>
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
                                </tr>
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
                                </tr>
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
                                </tr>
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
                                </tr>
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
                                </tr>
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
                                </tr>
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
                                </tr>
                            </tbody>
                        </table>
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
