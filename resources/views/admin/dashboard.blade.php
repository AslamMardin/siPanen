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
                <i class="bi bi-file-earmark-pdf me-1"></i> Lihat Laporan PDF
            </a>
            <span class="text-muted small">{{ now()->isoFormat('dddd, D MMMM Y') }}</span>
        </div>
    </div>

    {{-- Stat Cards Asli --}}
    <div class="row g-3 mb-3">
        <div class="col-6 col-md-3">
            <div class="card stat-card h-100 p-3 shadow-sm border-0">
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
            <div class="card stat-card h-100 p-3 shadow-sm border-0">
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
            <div class="card stat-card h-100 p-3 shadow-sm border-0">
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
            <div class="card stat-card h-100 p-3 shadow-sm border-0">
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

    {{-- Analisis Baru: YoY dan Musim Berjalan --}}
    <div class="row g-3 mb-4">
        <div class="col-md-6">
            <div class="card border-0 shadow-sm p-3 h-100 bg-primary text-white" style="background: linear-gradient(45deg, #198754, #20c997);">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <div class="small text-white-50 mb-1">Total Panen {{ $tahunIni }}</div>
                        <h2 class="fw-bold mb-0">{{ number_format($totalTahunIni, 1) }} <small class="fs-6 fw-normal">Ton</small></h2>
                    </div>
                    <div class="text-end">
                        <div class="small text-white-50 mb-1">Pertumbuhan (YoY)</div>
                        @if($growth >= 0)
                            <span class="badge bg-white text-success fs-6"><i class="bi bi-arrow-up-right-circle-fill"></i> +{{ number_format($growth, 1) }}%</span>
                        @else
                            <span class="badge bg-white text-danger fs-6"><i class="bi bi-arrow-down-right-circle-fill"></i> {{ number_format($growth, 1) }}%</span>
                        @endif
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card border-0 shadow-sm p-3 h-100 bg-dark text-white">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <div class="small text-white-50 mb-1">Panen {{ $musimBerjalan }} ({{ $tahunIni }})</div>
                        <h2 class="fw-bold mb-0">{{ number_format($produksiMusimIni, 1) }} <small class="fs-6 fw-normal">Ton</small></h2>
                    </div>
                    <div class="text-end">
                        <div class="small text-white-50 mb-1">Kontribusi Tahun Ini</div>
                        <span class="badge bg-light text-dark fs-6">{{ number_format($persentaseMusimIni, 1) }}%</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Laporan Menunggu & Produksi Kecamatan (Yang Lama) --}}
    <div class="row g-3 mb-4">
        <div class="col-md-7">
            <div class="card border-0 shadow-sm h-100">
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

        <div class="col-md-5">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white fw-semibold">Produksi Per Kecamatan</div>
                <div class="card-body d-flex align-items-center justify-content-center">
                    <div style="width:100%; max-height:220px;">
                        <canvas id="chartKecamatan"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Grafik Tren Tahunan & Donat --}}
    <div class="row g-3 mb-4">
        <div class="col-md-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white fw-semibold">
                    Total Hasil Panen Pertahun (Ton)
                </div>
                <div class="card-body">
                    <canvas id="chartTahunanLengkap" height="120"></canvas>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white fw-semibold">
                    Perbandingan Musim per Tahun (Ton)
                </div>
                <div class="card-body">
                    <canvas id="chartKombinasi" height="120"></canvas>
                </div>
            </div>
        </div>
    </div>

    {{-- Proporsi Musim & Tabel YTD --}}
    <div class="row g-3 mb-4">
        <div class="col-md-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white fw-semibold">
                    Proporsi Musim {{ $tahunIni }}
                </div>
                <div class="card-body d-flex align-items-center justify-content-center">
                    <div style="max-width: 200px;">
                        <canvas id="chartDoughnut"></canvas>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-8">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white fw-semibold">
                    Ringkasan Hasil Panen (Year-To-Date)
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover table-striped mb-0 text-center">
                            <thead class="table-light">
                                <tr>
                                    <th>Tahun</th>
                                    <th>Musim Hujan (Ton)</th>
                                    <th>Musim Kemarau (Ton)</th>
                                    <th class="fw-bold">Total Tahunan (Ton)</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($ytdTable as $row)
                                <tr>
                                    <td class="fw-semibold">{{ $row['tahun'] }}</td>
                                    <td>{{ number_format($row['hujan'], 1) }}</td>
                                    <td>{{ number_format($row['kemarau'], 1) }}</td>
                                    <td class="fw-bold text-success">{{ number_format($row['total'], 1) }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
    <script>
        // 1. Chart Kecamatan (Existing)
        const labelsKecamatan = @json($produksiPerKecamatan->pluck('kecamatan.nama'));
        const dataKecamatan = @json($produksiPerKecamatan->pluck('total_produksi'));

        new Chart(document.getElementById('chartKecamatan'), {
            type: 'bar',
            data: {
                labels: labelsKecamatan,
                datasets: [{
                    label: 'Produksi (ton)',
                    data: dataKecamatan,
                    backgroundColor: 'rgba(46,125,50,.7)',
                    borderRadius: 4,
                }]
            },
            options: {
                plugins: { legend: { display: false } },
                scales: { y: { beginAtZero: true } },
                maintainAspectRatio: false,
            }
        });

        const labelsTahun = @json($listTahun);
        const dataHujan = @json($chartHujan);
        const dataKemarau = @json($chartKemarau);
        const dataTahunan = @json($chartTahunan);

        // 2. Chart Dedicated Tahunan (Baru ditambahkan)
        new Chart(document.getElementById('chartTahunanLengkap'), {
            type: 'bar',
            data: {
                labels: labelsTahun,
                datasets: [{
                    label: 'Total Panen (Ton)',
                    data: dataTahunan,
                    backgroundColor: '#0d6efd',
                    borderRadius: 6,
                }]
            },
            options: {
                responsive: true,
                plugins: { legend: { display: false } },
                scales: {
                    y: { beginAtZero: true }
                }
            }
        });

        // 3. Chart Kombinasi (Musim vs Tahunan)
        new Chart(document.getElementById('chartKombinasi'), {
            data: {
                labels: labelsTahun,
                datasets: [
                    {
                        type: 'line',
                        label: 'Total Tahunan (Ton)',
                        data: dataTahunan,
                        borderColor: '#0d6efd',
                        backgroundColor: '#0d6efd',
                        borderWidth: 2,
                        tension: 0.3,
                        pointRadius: 4,
                        fill: false
                    },
                    {
                        type: 'bar',
                        label: 'Musim Hujan (Ton)',
                        data: dataHujan,
                        backgroundColor: '#198754',
                        borderRadius: 4,
                    },
                    {
                        type: 'bar',
                        label: 'Musim Kemarau (Ton)',
                        data: dataKemarau,
                        backgroundColor: '#ffc107',
                        borderRadius: 4,
                    }
                ]
            },
            options: {
                responsive: true,
                interaction: {
                    mode: 'index',
                    intersect: false,
                },
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });

        // 4. Doughnut Chart Proporsi Musim Tahun Ini
        const dHujan = {{ $doughnutHujan }};
        const dKemarau = {{ $doughnutKemarau }};
        
        new Chart(document.getElementById('chartDoughnut'), {
            type: 'doughnut',
            data: {
                labels: ['Musim Hujan', 'Musim Kemarau'],
                datasets: [{
                    data: [dHujan, dKemarau],
                    backgroundColor: ['#198754', '#ffc107'],
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: { boxWidth: 12 }
                    }
                }
            }
        });
    </script>
@endpush
