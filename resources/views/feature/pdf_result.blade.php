<!DOCTYPE html>
<html>

<head>
    <title>Laporan Analisis iDorm AI</title>
    <style>
        body {
            font-family: sans-serif;
            color: #333;
        }

        .header {
            text-align: center;
            border-bottom: 2px solid #3a57e8;
            padding-bottom: 10px;
        }

        .verdict-box {
            padding: 20px;
            text-align: center;
            background-color: #f8f9fa;
            border: 1px solid #ddd;
            margin-top: 20px;
        }

        .text-success {
            color: #198754;
        }

        .text-primary {
            color: #3a57e8;
        }

        .text-danger {
            color: #dc3545;
        }

        table {
            width: 100%;
            margin-top: 30px;
            border-collapse: collapse;
        }

        table td {
            padding: 10px;
            border-bottom: 1px solid #eee;
        }

        .footer {
            margin-top: 50px;
            font-size: 10px;
            text-align: center;
            color: #999;
        }
    </style>
</head>

<body>
    <div class="header">
        <h1 style="color: #3a57e8; margin-bottom: 5px;">iDorm Smart Analysis</h1>
        <p>Laporan Estimasi Harga Wajar Kos</p>
    </div>

    <div class="verdict-box">
        <h3 style="margin-top: 0;">Status Analisis AI:</h3>
        <h1 class="text-{{ $res['result']['analysis']['color_code'] }}">
            {{ $res['result']['analysis']['verdict'] }}
        </h1>
        <p>{{ $res['result']['analysis']['description'] }}</p>
    </div>

    <table>
        <tr>
            <td><strong>Wilayah</strong></td>
            <td>{{ strtoupper(str_replace('_', ' ', $res['metadata']['region'])) }}</td>
        </tr>
        <tr>
            <td><strong>Harga Penawaran</strong></td>
            <td>Rp {{ number_format($res['result']['offered_price'], 0, ',', '.') }}</td>
        </tr>
        <tr>
            <td><strong>Range Harga Wajar</strong></td>
            <td class="text-primary">
                Rp {{ number_format($res['result']['fair_range']['min'], 0, ',', '.') }} -
                {{ number_format($res['result']['fair_range']['max'], 0, ',', '.') }}
            </td>
        </tr>
        <tr>
            <td><strong>Margin Error (MAE)</strong></td>
            <td>± Rp {{ number_format($res['metadata']['mae_margin'], 0, ',', '.') }}</td>
        </tr>
    </table>

    <div class="footer">
        Dicetak otomatis oleh iDorm AI pada {{ now()->format('d M Y H:i:s') }}.<br>
        *Laporan ini bersifat referensi berdasarkan data pasar.
    </div>
</body>

</html>
