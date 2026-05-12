<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>Laporan Panen Padi</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            color: #333;
        }

        .header {
            text-align: center;
            border-bottom: 2px solid #333;
            padding-bottom: 10px;
            margin-bottom: 20px;
        }

        .header h3,
        .header h4,
        .header h5 {
            margin: 0;
            padding: 2px;
        }

        .title {
            text-align: center;
            margin-bottom: 20px;
            font-weight: bold;
            font-size: 14px;
        }

        .summary {
            margin-bottom: 20px;
            width: 100%;
        }

        .summary td {
            padding: 4px;
        }

        table.data {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }

        table.data th,
        table.data td {
            border: 1px solid #aaa;
            padding: 6px;
            text-align: left;
        }

        table.data th {
            background-color: #f4f4f4;
            font-weight: bold;
        }

        .text-right {
            text-align: right !important;
        }

        .text-center {
            text-align: center !important;
        }
    </style>
</head>

<body>

    <div class="header">
        <h3>PEMERINTAH KABUPATEN POLEWALI MANDAR</h3>
        <h4>DINAS PERTANIAN TANAMAN PANGAN</h4>
        <h5>Jl. Budi Utomo No. 1, Pekkabata, Polewali Mandar, Sulawesi Barat</h5>
    </div>

    <div class="title">
        REKAPITULASI LAPORAN HASIL PANEN PADI<br>
        TAHUN {{ $tahun }}
    </div>

    <table class="summary">
        <tr>
            <td width="150"><strong>Tahun Laporan</strong></td>
            <td>: {{ $tahun }}</td>
        </tr>
        <tr>
            <td><strong>Musim Tanam</strong></td>
            <td>: {{ $musim_tanam ?: 'Semua Musim' }}</td>
        </tr>
    </table>

    <table class="data">
        <thead>
            <tr>
                <th width="30" class="text-center">No</th>
                <th>Kecamatan</th>
                <th class="text-right">Luas Panen (ha)</th>
                <th class="text-right">Produksi (ton)</th>
                <th class="text-right">Produktivitas (ton/ha)</th>
            </tr>
        </thead>
        <tbody>
            @php $no = 1; @endphp
            @forelse($produksiPerKecamatan as $row)
                <tr>
                    <td class="text-center">{{ $no++ }}</td>
                    <td>{{ $row->kecamatan->nama }}</td>
                    <td class="text-right">{{ number_format($row->total_luas, 2, ',', '.') }}</td>
                    <td class="text-right">{{ number_format($row->total_produksi, 2, ',', '.') }}</td>
                    <td class="text-right">
                        @if ($row->total_luas > 0)
                            {{ number_format($row->total_produksi / $row->total_luas, 2, ',', '.') }}
                        @else
                            -
                        @endif
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="5" class="text-center">Tidak ada data panen yang disetujui.</td>
                </tr>
            @endforelse
        </tbody>
        <tfoot>
            <tr>
                <th colspan="2" class="text-right">TOTAL</th>
                <th class="text-right">{{ number_format($stats['total_luas_panen'] ?? 0, 2, ',', '.') }}</th>
                <th class="text-right">{{ number_format($stats['total_produksi'] ?? 0, 2, ',', '.') }}</th>
                <th class="text-right">
                    @if (($stats['total_luas_panen'] ?? 0) > 0)
                        {{ number_format(($stats['total_produksi'] ?? 0) / $stats['total_luas_panen'], 2, ',', '.') }}
                    @else
                        -
                    @endif
                </th>
            </tr>
        </tfoot>
    </table>

    <table style="width: 100%; margin-top: 50px;">
        <tr>
            <td width="60%"></td>
            <td width="40%" class="text-center">
                Polewali Mandar, {{ now()->isoFormat('D MMMM Y') }}<br>
                Kepala Dinas Pertanian,<br>
                <br><br><br><br>
                <strong><u>(.................................)</u></strong><br>
                NIP.
            </td>
        </tr>
    </table>

</body>

</html>
