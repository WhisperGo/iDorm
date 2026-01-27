@extends('penghuni.layouts')

@section('content')
    <div class="row justify-content-center">
        <div class="col-sm-12">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                    <h4 class="fw-bold mb-0"><i class="bi bi-journal-text me-2"></i>Riwayat Peminjaman Saya</h4>
                    <a href="{{ route('booking.create') }}" class="btn btn-primary">
                        <i class="bi bi-plus-lg"></i> Buat Booking Baru
                    </a>
                </div>
                <div class="card-body">
                    @if (session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            <i class="bi bi-check-circle-fill me-2"></i> {{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    <div class="table-responsive">
                        <table class="table table-bordered table-hover align-middle">
                            <thead class="bg-light">
                                <tr>
                                    <th class="text-center" width="5%">No.</th>
                                    <th>Fasilitas & Detail</th>
                                    <th class="text-center">Tanggal & Waktu</th>
                                    <th class="text-center">Status</th>
                                    <th class="text-center" width="30%">Aksi / Bukti Foto</th>
                                </tr>
                            </thead>
                            <tbody>
                                {{-- LOGIKA GROUPING: Berdasarkan Tanggal dan Jam --}}
                                @forelse($myBookings->groupBy(fn($item) => $item->booking_date . $item->start_time . $item->end_time) as $group)
                                    @php
                                        $b = $group->first();
                                    @endphp
                                    <tr>
                                        <td class="text-center">{{ $loop->iteration }}</td>
                                        <td>
                                            {{-- JIKA MESIN CUCI: Gabungkan nomor mesinnya --}}
                                            @if (Str::contains(strtolower($b->facility->name), 'mesin cuci'))
                                                <div class="fw-bold text-primary">Mesin Cuci</div>
                                                <small class="badge bg-soft-success text-success">
                                                    No. Mesin:
                                                    @foreach ($group as $item)
                                                        M-{{ substr($item->facility->name, -1) }}{{ !$loop->last ? ',' : '' }}
                                                    @endforeach
                                                </small>
                                            @else
                                                <div class="fw-bold text-primary">{{ $b->facility->name }}</div>
                                                @if ($b->item_dapur)
                                                    <small class="badge bg-soft-danger text-danger">Alat:
                                                        {{ ucwords(str_replace('_', ' ', $b->item_dapur)) }}</small>
                                                @elseif($b->item_sergun)
                                                    <small class="badge bg-soft-primary text-primary">Area:
                                                        {{ ucwords(str_replace('_', ' ', $b->item_sergun)) }}</small>
                                                @elseif($b->description)
                                                    <small
                                                        class="text-muted italic">"{{ Str::limit($b->description, 30) }}"</small>
                                                @endif
                                            @endif
                                        </td>
                                        <td class="text-center">
                                            <div class="fw-bold">
                                                {{ \Carbon\Carbon::parse($b->booking_date)->format('d M Y') }}</div>
                                            <span class="badge bg-soft-info text-info">
                                                {{ substr($b->start_time, 0, 5) }} - {{ substr($b->end_time, 0, 5) }}
                                            </span>
                                        </td>
                                        <td class="text-center">
                                            @php
                                                $statusClass =
                                                    [
                                                        'Booked' => 'info',
                                                        'Cancelled' => 'danger',
                                                        'Completed' => 'success',
                                                        'Ongoing' => 'warning',
                                                    ][$b->status->status_name] ?? 'secondary';
                                            @endphp
                                            <span class="badge bg-{{ $statusClass }} text-uppercase px-3 py-2">
                                                {{ $b->status->status_name }}
                                            </span>
                                        </td>
                                        <td>
                                            @if ($b->photo_proof_path)
                                                <div class="text-center text-success fw-bold">
                                                    <i class="bi bi-check-all fs-5"></i><br>
                                                    <small>Bukti Terunggah</small>
                                                </div>
                                            @else
                                                @php
                                                    $endTime = \Carbon\Carbon::parse(
                                                        $b->booking_date . ' ' . $b->end_time,
                                                    );
                                                    $isOverdue = now() > $endTime;
                                                @endphp

                                                @if ($isOverdue)
                                                    {{-- Jika digabung, upload foto cukup satu kali untuk mewakili grup tersebut --}}
                                                    <form action="{{ route('booking.upload', $b->id) }}" method="POST"
                                                        enctype="multipart/form-data">
                                                        @csrf
                                                        <div class="input-group input-group-sm">
                                                            <input type="file" name="photo" class="form-control"
                                                                required>
                                                            <button class="btn btn-warning" type="submit">Upload</button>
                                                        </div>
                                                    </form>
                                                @else
                                                    <div class="text-center">
                                                        <small class="text-muted"><i class="bi bi-hourglass-split"></i>
                                                            Upload tersedia setelah jam selesai</small>
                                                    </div>
                                                @endif
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center py-5 text-muted">
                                            <i class="bi bi-inbox fs-1 d-block mb-3"></i>
                                            Kamu belum memiliki riwayat peminjaman.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
