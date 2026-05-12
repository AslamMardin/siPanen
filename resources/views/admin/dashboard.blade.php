@extends('layouts.app')
@section('title', 'Dashboard Admin')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h5 class="fw-bold mb-0">Dashboard Admin</h5>
            <small class="text-muted">Selamat datang, {{ auth()->user()->name }}</small>
        </div>
        <div class="d-flex align-items-center gap-3">
            <a href="{{ route('admin.dashboard.pdf') }}" class="btn btn-danger btn-sm" target="_blank">
                <i class="bi bi-file-earmark-pdf me-1"></i> Unduh Laporan PDF
            </a>
            <span class="text-muted small">{{ now()->isoFormat('dddd, D MMMM Y') }}</span>
        </div>
    </div>

    {{-- Stat Cards --}}
    <div class="row g-3 mb-4">
        <div class="col-6 col-md-3">
            <div class="card stat-card h-100 p-3">
                <div class="d-flex align-items-center gap-3">
                    <div class="icon-wrap bg-warning-subtle text-warning">
                        <i class="bi bi-clock-history"></i>
                    </div>
                    <div>
                        <div class="fs-4 fw-bold">{{ $stats['menunggu_validasi'] }}</div>
                        <div class="small text-muted">Menunggu Validasi</div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="card stat-card h-100 p-3">
                <div class="d-flex align-items-center gap-3">
                    <div class="icon-wrap bg-success-subtle text-success">
                        <i class="bi bi-check-circle-fill"></i>
                    </div>
                    <div>
                        <div class="fs-4 fw-bold">{{ $stats['disetujui'] }}</div>
                        <div class="small text-muted">Disetujui</div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="card stat-card h-100 p-3">
                <div class="d-flex align-items-center gap-3">
                    <div class="icon-wrap bg-primary-subtle text-primary">
                        <i class="bi bi-people-fill"></i>
                    </div>
                    <div>
                        <div class="fs-4 fw-bold">{{ $stats['total_petugas'] }}</div>
                        <div class="small text-muted">Petugas Aktif</div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="card stat-card h-100 p-3">
                <div class="d-flex align-items-center gap-3">
                    <div class="icon-wrap bg-info-subtle text-info">
                        <i class="bi bi-basket2-fill"></i>
                    </div>
                    <div>
                        <div class="fs-4 fw-bold">{{ number_format($stats['total_produksi'], 1) }} t</div>
                        <div class="small text-muted">Total Produksi</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-3">
        {{-- Laporan Menunggu --}}
        <div class="col-md-7">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white d-flex justify-content-between align-items-center">
                    <span class="fw-semibold">Laporan Menunggu Validasi</span>
                    <a href="{{ route('admin.validasi.index') }}" class="btn btn-sm btn-outline-success">
                        Lihat Semua
                    </a>
                </div>
                <div class="card-body p-0">
                    @forelse($laporanTerbaru as $laporan)
                        <div class="d-flex align-items-center px-3 py-2 border-bottom gap-3">
                            <div class="icon-wrap bg-warning-subtle text-warning flex-shrink-0"
                                style="width:36px;height:36px;border-radius:.4rem;font-size:1rem;">
                                <i class="bi bi-file-earmark-text"></i>
                            </div>
                            <div class="flex-grow-1 min-w-0">
                                <div class="fw-semibold small text-truncate">
                                    {{ $laporan->desa->nama ?? '-' }} – {{ $laporan->kecamatan->nama ?? '-' }}
                                </div>
                                <div class="text-muted" style="font-size:.78rem;">
                                    {{ $laporan->musim_tanam }} {{ $laporan->tahun }} &bull; {{ $laporan->user->name }}
                                </div>
                            </div>
                            <a href="{{ route('admin.validasi.show', $laporan) }}"
                                class="btn btn-sm btn-outline-primary flex-shrink-0">
                                Review
                            </a>
                        </div>
                    @empty
                        <div class="text-center text-muted py-4">
                            <i class="bi bi-inbox fs-2 d-block mb-1"></i>
                            Tidak ada laporan menunggu.
                        </div>
                    @endforelse
                </div>
            </div>
        </div>

        {{-- Produksi per Kecamatan --}}
        <div class="col-md-5">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white fw-semibold">Produksi Per Kecamatan (Ton)</div>
                <div class="card-body">
                    <canvas id="chartKecamatan" height="220"></canvas>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
    <script>
        const labels = @json($produksiPerKecamatan->pluck('kecamatan.nama'));
        const data = @json($produksiPerKecamatan->pluck('total_produksi'));

        new Chart(document.getElementById('chartKecamatan'), {
            type: 'bar',
            data: {
                labels,
                datasets: [{
                    label: 'Produksi (ton)',
                    data,
                    backgroundColor: 'rgba(46,125,50,.7)',
                    borderRadius: 6,
                }]
            },
            options: {
                plugins: {
                    legend: {
                        display: false
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true
                    }
                },
                responsive: true,
            }
        });
    </script>
@endpush
