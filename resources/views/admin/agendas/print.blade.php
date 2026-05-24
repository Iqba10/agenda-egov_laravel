@php
    $namaBulan = [
        '1' => 'Januari', '2' => 'Februari', '3' => 'Maret',
        '4' => 'April', '5' => 'Mei', '6' => 'Juni',
        '7' => 'Juli', '8' => 'Agustus', '9' => 'September',
        '10' => 'Oktober', '11' => 'November', '12' => 'Desember',
    ];
    $tanggalMulai = $filters['start_date'] ?? '';
    $tanggalAkhir = $filters['end_date'] ?? '';
    $bulan = $filters['month'] ?? '';
    $tahun = $filters['year'] ?? '';
    
    if ($tanggalMulai && $tanggalAkhir) {
        $periodeTeks = \Carbon\Carbon::parse($tanggalMulai)->translatedFormat('d M Y') . ' - ' . \Carbon\Carbon::parse($tanggalAkhir)->translatedFormat('d M Y');
    } elseif ($tanggalMulai) {
        $periodeTeks = 'Mulai ' . \Carbon\Carbon::parse($tanggalMulai)->translatedFormat('d M Y');
    } elseif ($bulan && $tahun) {
        $periodeTeks = ($namaBulan[(string)(int)$bulan] ?? $bulan) . ' ' . $tahun;
    } elseif ($tahun) {
        $periodeTeks = 'Tahun ' . $tahun;
    } elseif ($bulan) {
        $periodeTeks = 'Bulan ' . ($namaBulan[(string)(int)$bulan] ?? $bulan);
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
        * {
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Arial', sans-serif;
            font-size: 12px;
            color: #333;
            margin: 0;
            padding: 20px;
        }

        .filter-section {
            background: #f8f9fa;
            border: 1px solid #dee2e6;
            border-radius: 8px;
            padding: 20px;
            margin-bottom: 30px;
        }

        .filter-section h3 {
            margin: 0 0 15px 0;
            font-size: 16px;
            color: #333;
        }

        .filter-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
            gap: 15px;
            margin-bottom: 15px;
        }

        .filter-group {
            display: flex;
            flex-direction: column;
        }

        .filter-group label {
            font-size: 11px;
            font-weight: bold;
            color: #666;
            margin-bottom: 5px;
            text-transform: uppercase;
        }

        .filter-group input,
        .filter-group select {
            padding: 8px 12px;
            border: 1px solid #ced4da;
            border-radius: 4px;
            font-size: 13px;
        }

        .filter-buttons {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
        }

        .btn {
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 13px;
            font-weight: bold;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 6px;
        }

        .btn-primary {
            background: #0d6efd;
            color: #fff;
        }

        .btn-success {
            background: #198754;
            color: #fff;
        }

        .btn-secondary {
            background: #6c757d;
            color: #fff;
        }

        .btn-outline {
            background: #fff;
            color: #333;
            border: 1px solid #ced4da;
        }

        .btn:hover {
            opacity: 0.9;
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
                display: none !important;
            }

            body {
                padding: 20px;
            }

            .filter-section {
                display: none !important;
            }
        }
    </style>
</head>
<body>
    {{-- Filter Section (tidak tampil saat print) --}}
    <div class="filter-section no-print">
        <h3>Filter Laporan Agenda</h3>
        <form method="GET" action="{{ route('admin.agendas.print') }}">
            <div class="filter-grid">
                <div class="filter-group">
                    <label>Tanggal Mulai</label>
                    <input type="date" name="start_date" value="{{ $tanggalMulai }}">
                </div>
                <div class="filter-group">
                    <label>Tanggal Akhir</label>
                    <input type="date" name="end_date" value="{{ $tanggalAkhir }}">
                </div>
                <div class="filter-group">
                    <label>Bulan</label>
                    <select name="month">
                        <option value="">Semua Bulan</option>
                        @foreach ($namaBulan as $key => $nama)
                            <option value="{{ $key }}" {{ $bulan == $key ? 'selected' : '' }}>{{ $nama }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="filter-group">
                    <label>Tahun</label>
                    <select name="year">
                        <option value="">Semua Tahun</option>
                        @for ($y = now()->year + 1; $y >= 2020; $y--)
                            <option value="{{ $y }}" {{ $tahun == $y ? 'selected' : '' }}>{{ $y }}</option>
                        @endfor
                    </select>
                </div>
                <div class="filter-group">
                    <label>Status</label>
                    <select name="status">
                        <option value="">Semua Status</option>
                        <option value="terjadwal" {{ ($filters['status'] ?? '') == 'terjadwal' ? 'selected' : '' }}>Terjadwal</option>
                        <option value="berlangsung" {{ ($filters['status'] ?? '') == 'berlangsung' ? 'selected' : '' }}>Berlangsung</option>
                        <option value="selesai" {{ ($filters['status'] ?? '') == 'selesai' ? 'selected' : '' }}>Selesai</option>
                        <option value="dibatalkan" {{ ($filters['status'] ?? '') == 'dibatalkan' ? 'selected' : '' }}>Dibatalkan</option>
                    </select>
                </div>
            </div>
            <div class="filter-buttons">
                <button type="submit" class="btn btn-primary">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"></circle><path d="m21 21-4.3-4.3"></path></svg>
                    Filter
                </button>
                <button type="button" class="btn btn-success" onclick="window.print()">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="6 9 6 2 18 2 18 9"></polyline><path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"></path><rect width="12" height="8" x="6" y="14"></rect></svg>
                    Cetak Laporan
                </button>
                <a href="{{ route('admin.agendas.print') }}" class="btn btn-outline">Reset Filter</a>
                <a href="{{ route('admin.dashboard') }}" class="btn btn-secondary">Kembali</a>
            </div>
        </form>
    </div>

    {{-- Print Content --}}
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
</body>
</html>
