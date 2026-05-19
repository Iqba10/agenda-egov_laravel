@php
    $namaBulan = [
        '1' => 'Januari', '2' => 'Februari', '3' => 'Maret',
        '4' => 'April', '5' => 'Mei', '6' => 'Juni',
        '7' => 'Juli', '8' => 'Agustus', '9' => 'September',
        '10' => 'Oktober', '11' => 'November', '12' => 'Desember',
    ];
    $bulan = $filters['month'] ?? '';
    $tahun = $filters['year'] ?? '';
    if ($bulan && $tahun) {
        $periodeTeks = ($namaBulan[(string)(int)$bulan] ?? $bulan) . ' ' . $tahun;
    } elseif ($tahun) {
        $periodeTeks = 'Tahun ' . $tahun;
    } else {
        $periodeTeks = 'Semua Periode';
    }
@endphp
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Cetak Daftar Agenda - Agenda eGov</title>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            font-size: 12px;
            color: #333;
        }

        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 2px solid #333;
            padding-bottom: 10px;
        }

        .header h1 {
            margin: 0;
            font-size: 20px;
            text-transform: uppercase;
        }

        .header p {
            margin: 5px 0 0;
            font-size: 14px;
        }

        .meta {
            margin-bottom: 20px;
            display: flex;
            justify-content: space-between;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }

        th, td {
            border: 1px solid #333;
            padding: 8px;
            text-align: left;
        }

        th {
            background-color: #f2f2f2;
            font-weight: bold;
        }

        .status {
            font-weight: bold;
            text-transform: uppercase;
            font-size: 10px;
        }

        .footer {
            margin-top: 50px;
            text-align: right;
        }

        @media print {
            .no-print {
                display: none;
            }

            body {
                padding: 20px;
            }
        }
    </style>
</head>
<body onload="window.print()">
    <div class="header">
        <h1>DAFTAR AGENDA KEGIATAN</h1>
        <p>Dinas Komunikasi dan Informatika - Agenda eGov</p>
        <p style="font-size:12px; margin-top:4px;">Periode: {{ $periodeTeks }}</p>
    </div>

    <div class="meta">
        <span>Dicetak pada: {{ now()->format('d/m/Y H:i') }}</span>
        <span>Total Agenda: {{ $agendas->count() }}</span>
    </div>

    <table>
        <thead>
            <tr>
                <th width="30">No</th>
                <th width="200">Perihal Kegiatan</th>
                <th width="150">Waktu</th>
                <th>Tempat</th>
                <th width="80">Status</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($agendas as $index => $agenda)
                <tr>
                    <td style="text-align:center">{{ $index + 1 }}</td>
                    <td><strong>{{ $agenda->perihal_kegiatan }}</strong></td>
                    <td>
                        {{ optional($agenda->waktu_mulai)->format('d/m/Y') }}<br>
                        <small>{{ optional($agenda->waktu_mulai)->format('H:i') }} - {{ optional($agenda->waktu_selesai)->format('H:i') }} WIB</small>
                    </td>
                    <td>{{ $agenda->tempat }}</td>
                    <td style="text-align:center">
                        <span class="status">{{ $agenda->effective_status }}</span>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="5" style="text-align:center">Data tidak ditemukan.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div class="footer">
        <p>Sambas, {{ now()->translatedFormat('d F Y') }}</p>
        <br><br><br>
        <p><strong>Administrator</strong></p>
    </div>

    <div class="no-print" style="position: fixed; bottom: 20px; right: 20px;">
        <button onclick="window.close()"
            style="padding: 10px 20px; cursor: pointer; background: #333; color: #fff; border: none; border-radius: 5px;">Tutup</button>
    </div>
</body>
</html>
