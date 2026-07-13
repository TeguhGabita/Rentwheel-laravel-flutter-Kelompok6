<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cetak Laporan Booking - RentWheel</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: 'Arial', sans-serif;
            color: #333;
            background: #f5f5f5;
            padding: 20px;
        }
        .container {
            max-width: 1000px;
            margin: 0 auto;
            background: white;
            padding: 40px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        .header {
            text-align: center;
            border-bottom: 3px solid #f59e0b;
            padding-bottom: 20px;
            margin-bottom: 30px;
        }
        .header h1 {
            font-size: 28px;
            color: #0a0a0f;
            margin-bottom: 5px;
        }
        .header p {
            color: #666;
            font-size: 14px;
        }
        .print-date {
            text-align: right;
            font-size: 12px;
            color: #999;
            margin-bottom: 20px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 30px;
        }
        table thead {
            background-color: #0a0a0f;
            color: white;
        }
        table th {
            padding: 12px;
            text-align: left;
            font-weight: bold;
            border: 1px solid #ddd;
        }
        table td {
            padding: 10px 12px;
            border: 1px solid #ddd;
        }
        table tbody tr:nth-child(even) {
            background-color: #f9fafb;
        }
        .status-badge {
            display: inline-block;
            padding: 4px 12px;
            border-radius: 12px;
            font-size: 12px;
            font-weight: bold;
        }
        .status-dipesan {
            background-color: #fef3c7;
            color: #92400e;
        }
        .status-berjalan {
            background-color: #dbeafe;
            color: #0c4a6e;
        }
        .status-selesai {
            background-color: #dcfce7;
            color: #166534;
        }
        .status-batal {
            background-color: #fee2e2;
            color: #991b1b;
        }
        .summary {
            background-color: #f9fafb;
            border: 2px solid #f59e0b;
            border-radius: 8px;
            padding: 20px;
            text-align: right;
        }
        .summary h3 {
            font-size: 16px;
            color: #666;
            margin-bottom: 10px;
        }
        .total-amount {
            font-size: 24px;
            font-weight: bold;
            color: #0a0a0f;
        }
        .page-break {
            page-break-after: always;
            margin-top: 50px;
            margin-bottom: 50px;
        }
        @media print {
            body {
                background: white;
                padding: 0;
            }
            .container {
                box-shadow: none;
                padding: 20px;
            }
            .no-print {
                display: none;
            }
        }
        .print-buttons {
            text-align: center;
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid #ddd;
        }
        .btn {
            display: inline-block;
            padding: 10px 20px;
            margin: 0 5px;
            border-radius: 6px;
            text-decoration: none;
            font-size: 14px;
            font-weight: bold;
            cursor: pointer;
            border: none;
        }
        .btn-print {
            background-color: #f59e0b;
            color: white;
        }
        .btn-back {
            background-color: #e5e7eb;
            color: #333;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>RentWheel</h1>
            <p>Laporan Pemesanan Mobil</p>
        </div>

        <div class="print-date">
            Dicetak pada: {{ now()->format('d/m/Y H:i') }}
        </div>

        <table>
            <thead>
                <tr>
                    <th>No</th>
                    <th>Mobil</th>
                    <th>Pelanggan</th>
                    <th>Tanggal Mulai</th>
                    <th>Tanggal Selesai</th>
                    <th>Total Harga</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($bookings as $index => $booking)
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td>
                            <strong>{{ $booking->mobil->nama_mobil ?? '-' }}</strong><br>
                            <small>{{ $booking->mobil->plat_nomor ?? '-' }}</small>
                        </td>
                        <td>
                            <strong>{{ $booking->user->name ?? '-' }}</strong><br>
                            <small>{{ $booking->user->email ?? '-' }}</small>
                        </td>
                        <td>{{ \Carbon\Carbon::parse($booking->tanggal_mulai)->format('d/m/Y') }}</td>
                        <td>{{ \Carbon\Carbon::parse($booking->tanggal_selesai)->format('d/m/Y') }}</td>
                        <td>Rp{{ number_format($booking->total_harga, 0, ',', '.') }}</td>
                        <td>
                            <span class="status-badge status-{{ $booking->status }}">
                                {{ ucfirst($booking->status) }}
                            </span>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" style="text-align: center; color: #999;">Tidak ada data</td>
                    </tr>
                @endempty
            </tbody>
        </table>

        <div class="summary">
            <h3>Total Keseluruhan:</h3>
            <div class="total-amount">Rp{{ number_format($totalHarga, 0, ',', '.') }}</div>
        </div>

        <div class="print-buttons no-print">
            <button class="btn btn-print" onclick="window.print()">
                <span>🖨️ Cetak Laporan</span>
            </button>
            <a href="{{ route('admin.laporan.index') }}" class="btn btn-back">
                ← Kembali
            </a>
        </div>
    </div>

    <script>
        // Auto print when page loads (optional)
        // window.print();
    </script>
</body>
</html>
