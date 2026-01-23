@extends(
    auth()->user()->role->role_name === 'Resident' ? 'penghuni.layouts' : 
    (auth()->user()->role->role_name === 'Admin' ? 'admin.layouts' : 'pengelola.layouts')
)

@section('content')
    <div class="row">
        <div class="col-sm-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center flex-wrap">
                    <div class="header-title">
                        <h4 class="card-title mb-0 fw-bold">Announcement</h4>
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
                                @forelse($announcements as $announcement)
                                    <tr>
                                        <td class="text-center">
                                            {{ ($announcements->currentPage() - 1) * $announcements->perPage() + $loop->iteration }}
                                        </td>

                                        <td class="text-center">
                                            {{ $announcement->created_at->format('d M Y') }}
                                        </td>

                                        <td class="text-center">
                                            {{ $announcement->title }}
                                        </td>

                                        <td class="text-center">
                                            {{ Str::limit($announcement->description, 50) }}
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="text-center">Belum ada pengumuman untuk saat ini.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                        <div class="d-flex justify-content-between align-items-center mt-3">
                            <div>
                                Showing {{ $announcements->firstItem() }} to {{ $announcements->lastItem() }} of
                                {{ $announcements->total() }} entries
                            </div>
                            <div>
                                {{ $announcements->links() }}
                            </div>
                        </div>
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
