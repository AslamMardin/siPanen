@extends('layouts.app')
@section('title', 'Dashboard Pimpinan')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h5 class="fw-bold mb-0">Dashboard Pimpinan</h5>
        <small class="text-muted">Rekapitulasi Produksi Padi – Dinas Pertanian Kab. Polman</small>
    </div>
    <div>
        <a href="{{ route('pimpinan.dashboard.pdf', request()->query()) }}" class="btn btn-danger btn-sm" target="_blank">
            <i class="bi bi-file-earmark-pdf me-1"></i> Unduh Laporan PDF
        </a>
    </div>
</div>

{{-- Filter --}}
<div class="card border-0 shadow-sm mb-3">
    <div class="card-body py-2">
        <form method="GET" class="d-flex gap-2 align-items-center flex-wrap">
            <select name="tahun" class="form-select form-select-sm" style="width:auto">
                @foreach($tahunList as $t)
                    <option value="{{ $t }}" {{ $tahun == $t ? 'selected' : '' }}>{{ $t }}</option>
                @endforeach
            </select>
            <select name="musim_tanam" class="form-select form-select-sm" style="width:auto">
                <option value="">Semua Musim</option>
                <option value="Musim Hujan" {{ $musim_tanam === 'Musim Hujan' ? 'selected' : '' }}>Musim Hujan</option>
                <option value="Musim Kemarau" {{ $musim_tanam === 'Musim Kemarau' ? 'selected' : '' }}>Musim Kemarau</option>
            </select>
            <select name="kecamatan_id" class="form-select form-select-sm" style="width:auto">
                <option value="">Semua Kecamatan</option>
                @foreach($kecamatans as $kec)
                    <option value="{{ $kec->id }}" {{ $kecamatan_id == $kec->id ? 'selected' : '' }}>{{ $kec->nama }}</option>
                @endforeach
            </select>
            <button class="btn btn-sm btn-success">Tampilkan</button>
        </form>
    </div>
</div>

{{-- Stat Cards --}}
<div class="row g-3 mb-4">
    <div class="col-md-4">
        <div class="card stat-card p-3">
            <div class="d-flex align-items-center gap-3">
                <div class="icon-wrap bg-success-subtle text-success">
                    <i class="bi bi-basket2-fill"></i>
                </div>
                <div>
                    <div class="fs-4 fw-bold">{{ number_format($stats['total_produksi'], 2) }}</div>
                    <div class="small text-muted">Total Produksi (ton)</div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card stat-card p-3">
            <div class="d-flex align-items-center gap-3">
                <div class="icon-wrap bg-info-subtle text-info">
                    <i class="bi bi-map"></i>
                </div>
                <div>
                    <div class="fs-4 fw-bold">{{ number_format($stats['total_luas_panen'], 2) }}</div>
                    <div class="small text-muted">Total Luas Panen (ha)</div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card stat-card p-3">
            <div class="d-flex align-items-center gap-3">
                <div class="icon-wrap bg-primary-subtle text-primary">
                    <i class="bi bi-file-earmark-check"></i>
                </div>
                <div>
                    <div class="fs-4 fw-bold">{{ $stats['total_laporan'] }}</div>
                    <div class="small text-muted">Laporan Disetujui</div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row g-3">
    {{-- Bar: Produksi per kecamatan --}}
    <div class="col-md-7">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white fw-semibold">Produksi Per Kecamatan (ton)</div>
            <div class="card-body">
                <canvas id="chartBar" height="200"></canvas>
            </div>
        </div>
    </div>

    {{-- Line: Tren produksi --}}
    <div class="col-md-5">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white fw-semibold">Tren Produksi Per Tahun</div>
            <div class="card-body">
                <canvas id="chartLine" height="200"></canvas>
            </div>
        </div>
    </div>

    {{-- Tabel per kecamatan --}}
    <div class="col-12">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white fw-semibold">Rekapitulasi Per Kecamatan</div>
            <div class="table-responsive">
                <table class="table table-hover mb-0 align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>Kecamatan</th>
                            <th>Total Luas Panen (ha)</th>
                            <th>Total Produksi (ton)</th>
                            <th>Rata-rata Produktivitas</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($produksiPerKecamatan as $row)
                        <tr>
                            <td class="fw-semibold">{{ $row->kecamatan->nama }}</td>
                            <td>{{ number_format($row->total_luas, 2) }}</td>
                            <td>{{ number_format($row->total_produksi, 2) }}</td>
                            <td>
                                @if($row->total_luas > 0)
                                    {{ number_format($row->total_produksi / $row->total_luas, 2) }} ton/ha
                                @else
                                    -
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4" class="text-center text-muted py-3">Tidak ada data.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
    // Bar chart
    const barLabels = @json($produksiPerKecamatan->pluck('kecamatan.nama'));
    const barData   = @json($produksiPerKecamatan->pluck('total_produksi'));

    new Chart(document.getElementById('chartBar'), {
        type: 'bar',
        data: {
            labels: barLabels,
            datasets: [{
                label: 'Produksi (ton)',
                data: barData,
                backgroundColor: 'rgba(46,125,50,.75)',
                borderRadius: 5,
            }]
        },
        options: { plugins: { legend: { display: false } }, scales: { y: { beginAtZero: true } } }
    });

    // Line chart (tren per tahun)
    const trenRaw  = @json($trenProduksi);
    const tahunSet = [...new Set(trenRaw.map(r => r.tahun))].sort();
    const musims   = ['Musim Hujan', 'Musim Kemarau'];
    const colors   = ['rgba(46,125,50,.8)', 'rgba(2,136,209,.8)'];

    const datasets = musims.map((m, i) => ({
        label: m,
        data: tahunSet.map(t => {
            const row = trenRaw.find(r => r.tahun == t && r.musim_tanam === m);
            return row ? row.total_produksi : 0;
        }),
        borderColor: colors[i],
        backgroundColor: colors[i].replace('.8', '.15'),
        fill: true, tension: 0.3,
    }));

    new Chart(document.getElementById('chartLine'), {
        type: 'line',
        data: { labels: tahunSet, datasets },
        options: { scales: { y: { beginAtZero: true } } }
    });
</script>
@endpush
