<!DOCTYPE html>
<html>

<head>
    <title>Laporan iDorm</title>
    <style>
        body {
            font-family: sans-serif;
            font-size: 11px;
            color: #333;
        }

        /* Styling Header menggunakan Table */
        .header-table {
            width: 100%;
            border-collapse: collapse;
            border-bottom: 2px solid #000;
            margin-bottom: 20px;
        }

        .header-table td {
            border: none;
            /* Hilangkan border untuk header */
            vertical-align: middle;
            padding-bottom: 10px;
        }

        .logo-column {
            width: 80px;
            /* Lebar kolom logo */
        }

        .title-column {
            text-align: center;
            padding-right: 80px;
            /* Biar judul bener-bener di tengah kertas */
        }

        /* Styling Tabel Data */
        .data-table {
            width: 100%;
            border-collapse: collapse;
        }

        .data-table th,
        .data-table td {
            border: 1px solid #ccc;
            padding: 8px;
            text-align: left;
        }

        .data-table th {
            background-color: #f8f9fa;
            text-transform: uppercase;
        }

        .footer {
            margin-top: 20px;
            text-align: right;
            font-style: italic;
            font-size: 9px;
        }
    </style>
</head>

<body>
    {{-- Header Layout: Logo Kiri, Teks Tengah --}}
    <table class="header-table">
        <tr>
            <td class="logo-column">
                <img src="{{ public_path('hopeui/images/logo/iDorm2.svg') }}" width="80">
            </td>
            <td class="title-column">
                <h2 style="margin:0;">LAPORAN PEMINJAMAN FASILITAS iDORM</h2>
                <p style="margin:5px 0;">
                    Fasilitas: <strong>{{ $facility_name }}</strong> |
                    Periode: {{ request('start_date') ?? '-' }} s/d {{ request('end_date') ?? '-' }}
                </p>
            </td>
        </tr>
    </table>

    {{-- Tabel Utama --}}
    <table class="data-table">
        <thead>
            <tr>
                <th style="width: 20px;">No</th>
                <th>Nama Penghuni</th>
                <th>Fasilitas</th>
                <th>Tanggal</th>
                <th>Waktu</th>
                <th style="width: 80px;">Status</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($bookings as $booking)
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td>{{ $booking->user->residentDetails->full_name ?? $booking->user->name }}</td>
                    <td>{{ $booking->facility->name }}</td>
                    <td>{{ \Carbon\Carbon::parse($booking->booking_date)->format('d/m/Y') }}</td>
                    <td>{{ substr($booking->start_time, 0, 5) }} - {{ substr($booking->end_time, 0, 5) }}</td>
                    <td>{{ $booking->status->status_name }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="footer">
        Dicetak pada: {{ date('d/m/Y H:i') }} oleh Sistem iDorm
    </div>
</body>

</html>
