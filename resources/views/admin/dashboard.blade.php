@extends('layouts.admin')
@section('page-title', 'Dashboard Admin')

@section('content')
<div class="row g-4 mb-4">

    <!-- STAT CARDS -->
    <div class="col-sm-6 col-xl-2">
        <div class="card border-0 rounded-3 shadow-sm h-100" style="border-top:4px solid #1a9b52!important;border:1px solid #e9ecef;">
            <div class="card-body">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <div style="width:40px;height:40px;border-radius:10px;background:#dcfce7;display:flex;align-items:center;justify-content:center;">
                        <i class="fas fa-users text-success"></i>
                    </div>
                    <span class="badge bg-success-subtle text-success small">Total</span>
                </div>
                <div class="fs-2 fw-bold" style="color:#0a1628;">{{ $stats['total_pendaftar'] }}</div>
                <div class="small text-muted">Total Pendaftar</div>
            </div>
        </div>
    </div>

    <div class="col-sm-6 col-xl-2">
        <div class="card border-0 rounded-3 shadow-sm h-100" style="border-top:4px solid #3b82f6!important;border:1px solid #e9ecef;">
            <div class="card-body">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <div style="width:40px;height:40px;border-radius:10px;background:#dbeafe;display:flex;align-items:center;justify-content:center;">
                        <i class="fas fa-user-check text-primary"></i>
                    </div>
                </div>
                <div class="fs-2 fw-bold" style="color:#0a1628;">{{ $stats['terverifikasi'] }}</div>
                <div class="small text-muted">Terverifikasi</div>
            </div>
        </div>
    </div>

    <div class="col-sm-6 col-xl-2">
        <div class="card border-0 rounded-3 shadow-sm h-100" style="border-top:4px solid #f59e0b!important;border:1px solid #e9ecef;">
            <div class="card-body">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <div style="width:40px;height:40px;border-radius:10px;background:#fef3c7;display:flex;align-items:center;justify-content:center;">
                        <i class="fas fa-clock text-warning"></i>
                    </div>
                </div>
                <div class="fs-2 fw-bold" style="color:#0a1628;">{{ $stats['menunggu'] }}</div>
                <div class="small text-muted">Menunggu Verifikasi</div>
            </div>
        </div>
    </div>

    <div class="col-sm-6 col-xl-2">
        <div class="card border-0 rounded-3 shadow-sm h-100" style="border-top:4px solid #ef4444!important;border:1px solid #e9ecef;">
            <div class="card-body">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <div style="width:40px;height:40px;border-radius:10px;background:#fee2e2;display:flex;align-items:center;justify-content:center;">
                        <i class="fas fa-times-circle text-danger"></i>
                    </div>
                </div>
                <div class="fs-2 fw-bold" style="color:#0a1628;">{{ $stats['ditolak'] }}</div>
                <div class="small text-muted">Ditolak</div>
            </div>
        </div>
    </div>

    <div class="col-sm-6 col-xl-2">
        <div class="card border-0 rounded-3 shadow-sm h-100" style="border-top:4px solid #8b5cf6!important;border:1px solid #e9ecef;">
            <div class="card-body">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <div style="width:40px;height:40px;border-radius:10px;background:#ede9fe;display:flex;align-items:center;justify-content:center;">
                        <i class="fas fa-shield-alt" style="color:#7c3aed;"></i>
                    </div>
                </div>
                <div class="fs-2 fw-bold" style="color:#0a1628;">{{ $stats['total_tim'] }}</div>
                <div class="small text-muted">Total Tim</div>
            </div>
        </div>
    </div>

    <div class="col-sm-6 col-xl-2">
        <div class="card border-0 rounded-3 shadow-sm h-100" style="border-top:4px solid #0a1628!important;border:1px solid #e9ecef;">
            <div class="card-body">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <div style="width:40px;height:40px;border-radius:10px;background:#dbeafe;display:flex;align-items:center;justify-content:center;">
                        <i class="fas fa-futbol text-navy"></i>
                    </div>
                </div>
                <div class="fs-2 fw-bold" style="color:#0a1628;">{{ $stats['pertandingan_mendatang'] }}</div>
                <div class="small text-muted">Pertandingan Mendatang</div>
            </div>
        </div>
    </div>
</div>

<div class="row g-4">
    <!-- VERIFIKASI PERLU -->
    <div class="col-lg-4">
        <div class="card border-0 card-karta h-100">
            <div class="card-header border-0 bg-white d-flex align-items-center justify-content-between py-3">
                <h6 class="fw-bold mb-0" style="color:#0a1628;">Perlu Diverifikasi</h6>
                <a href="{{ route('admin.verifikasi') }}" class="btn btn-sm btn-outline-success">Lihat Semua</a>
            </div>
            <div class="card-body p-0">
                @forelse($needVerification as $reg)
                <div class="d-flex align-items-center gap-3 px-4 py-3 border-bottom">
                    <div style="width:38px;height:38px;border-radius:50%;background:#fef3c7;display:flex;align-items:center;justify-content:center;font-weight:700;font-size:0.85rem;color:#92400e;flex-shrink:0;">
                        {{ strtoupper(substr($reg->nama_tim, 0, 1)) }}
                    </div>
                    <div class="flex-grow-1 min-w-0">
                        <div class="fw-semibold small text-truncate" style="color:#0a1628;">{{ $reg->nama_tim }}</div>
                        <div class="text-muted" style="font-size:0.72rem;">{{ $reg->kecamatan }}</div>
                    </div>
                    <a href="{{ route('admin.verifikasi.detail', $reg->id) }}" class="btn btn-sm btn-warning text-white" style="font-size:0.72rem;">Periksa</a>
                </div>
                @empty
                <div class="text-center text-muted py-5">
                    <i class="fas fa-check-circle fa-2x text-success mb-2 d-block"></i>
                    Semua sudah diverifikasi
                </div>
                @endforelse
            </div>
        </div>
    </div>

    <!-- PENDAFTAR TERBARU -->
    <div class="col-lg-7">
        <div class="card border-0 card-karta">
            <div class="card-header border-0 bg-white d-flex align-items-center justify-content-between py-3">
                <h6 class="fw-bold mb-0" style="color:#0a1628;">Pendaftar Terbaru</h6>
                <a href="{{ route('admin.pendaftar') }}" class="btn btn-sm btn-outline-secondary">Lihat Semua</a>
            </div>
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead>
                        <tr style="background:#f8fafc;">
                            <th class="fw-semibold small text-muted border-0 ps-4">Nama</th>
                            <th class="fw-semibold small text-muted border-0">Kecamatan</th>
                            <th class="fw-semibold small text-muted border-0">Tanggal</th>
                            <th class="fw-semibold small text-muted border-0">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($latestRegistrations as $reg)
                        <tr>
                            <td class="ps-4">
                                <div class="fw-semibold small" style="color:#0a1628;">{{ $reg->nama_tim }}</div>
                                <div class="text-muted" style="font-size:0.72rem;">{{ $reg->user->name ?? '-' }} ({{ $reg->user->email ?? '-' }})</div>
                            </td>
                            <td class="small">{{ $reg->kecamatan }}</td>
                            <td class="small text-muted">{{ $reg->created_at->format('d M Y') }}</td>
                            <td>
                                @php
                                    $sc = ['Terverifikasi'=>'success','Menunggu Verifikasi'=>'warning','Ditolak'=>'danger','Belum Mendaftar'=>'secondary'];
                                @endphp
                                <span class="badge bg-{{ $sc[$reg->status] ?? 'secondary' }}-subtle text-{{ $sc[$reg->status] ?? 'secondary' }} small">
                                    {{ $reg->status }}
                                </span>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4" class="text-center text-muted py-4 small">Belum ada data pendaftar.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- PERTANDINGAN MENDATANG -->
    <div class="col-lg-5">
        <div class="card border-0 card-karta">
            <div class="card-header border-0 bg-white d-flex align-items-center justify-content-between py-3">
                <h6 class="fw-bold mb-0" style="color:#0a1628;">Pertandingan Mendatang</h6>
                <a href="{{ route('admin.pertandingan') }}" class="btn btn-sm btn-outline-secondary">Kelola</a>
            </div>
            <div class="card-body p-0">
                @forelse($upcomingMatches as $m)
                <div class="px-4 py-3 border-bottom">
                    <div class="d-flex justify-content-between align-items-center mb-1">
                        <span class="badge bg-navy match-stage-badge">{{ $m->stage }}</span>
                        <span class="text-muted" style="font-size:0.72rem;">{{ $m->match_date->format('d M, H:i') }}</span>
                    </div>
                    <div class="d-flex align-items-center justify-content-between gap-2">
                        <span class="fw-semibold small text-truncate" style="max-width:100px;color:#0a1628;">{{ $m->homeTeam->name }}</span>
                        <span class="badge bg-light text-dark border">VS</span>
                        <span class="fw-semibold small text-truncate text-end" style="max-width:100px;color:#0a1628;">{{ $m->awayTeam->name }}</span>
                    </div>
                    <div class="text-muted mt-1" style="font-size:0.72rem;"><i class="fas fa-map-marker-alt text-success me-1"></i>{{ $m->venue }}</div>
                </div>
                @empty
                <div class="text-center text-muted py-5">
                    <i class="fas fa-calendar fa-2x mb-2 d-block text-muted"></i>
                    Belum ada pertandingan
                </div>
                @endforelse
            </div>
        </div>
    </div>
</div>
@endsection
