@extends('layouts.public')
@section('title', 'Tentang Turnamen – KARTA.BDG Cup 2026')

@section('content')
<div style="background:linear-gradient(135deg,#0a1628,#1a3356);padding:120px 0 60px;">
    <div class="container text-center text-white">
        <span class="section-overline text-success">Profil Turnamen</span>
        <h1 class="oswald fw-bold display-5 mt-1">Tentang Turnamen</h1>
        <p class="text-white-50">KARTA.BDG Men's Football Cup 2026</p>
    </div>
</div>

<div class="container py-5">
    <div class="row g-5 align-items-center mb-5">
        <div class="col-lg-6">
            <span class="section-overline">Sekilas</span>
            <h2 class="oswald fw-bold mt-1 mb-3" style="color:#0a1628;">Apa itu KARTA.BDG<br>Men's Football Cup?</h2>
            <p class="text-muted fs-5 mb-4">Turnamen sepak bola bergengsi antar kecamatan se-Kota Bandung yang diselenggarakan untuk mempererat persaudaraan, menemukan bakat lokal, dan mendorong gaya hidup aktif-sportif.</p>
            <div class="d-flex flex-column gap-3">
                @foreach([
                    ['icon'=>'fa-trophy','color'=>'#dcfce7','iconColor'=>'#15803d','text'=>'Total Hadiah Turnamen sebesar <strong>' . ($settings->total_prize ?? 'Rp 50.000.000') . '</strong>'],
                    ['icon'=>'fa-users','color'=>'#dbeafe','iconColor'=>'#1d4ed8','text'=>'Terbuka untuk warga <strong>KTP Kota Bandung</strong> usia 17–35 tahun'],
                    ['icon'=>'fa-futbol','color'=>'#fef3c7','iconColor'=>'#92400e','text'=>'Sistem <strong>Grup + Eliminasi</strong> dengan wasit berlisensi PSSI'],
                ] as $item)
                <div class="d-flex align-items-start gap-3">
                    <div style="width:40px;height:40px;border-radius:10px;background:{{ $item['color'] }};display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                        <i class="fas {{ $item['icon'] }}" style="color:{{ $item['iconColor'] }};"></i>
                    </div>
                    <p class="mb-0 mt-2">{!! $item['text'] !!}</p>
                </div>
                @endforeach
            </div>
        </div>
        <div class="col-lg-6">
            <div class="rounded-4 overflow-hidden shadow-lg" style="background:linear-gradient(135deg,#0a1628,#1a9b52);height:340px;display:flex;align-items:center;justify-content:center;">
                <div class="text-center text-white p-4">
                    <div style="font-size:5rem;font-family:'Oswald',sans-serif;font-weight:700;line-height:1;text-shadow:0 4px 20px rgba(0,0,0,0.3);">2026</div>
                    <div style="font-size:1.2rem;opacity:0.7;letter-spacing:0.2em;text-transform:uppercase;">KARTA.BDG Cup</div>
                </div>
            </div>
        </div>
    </div>

    <!-- INFO DETAIL -->
    <div class="row g-4 mb-5">
        <div class="col-md-4">
            <div class="card border-0 card-karta p-4 text-center h-100">
                <div class="d-flex align-items-center justify-content-center mb-3" style="width:60px;height:60px;border-radius:50%;background:#dcfce7;margin:auto;">
                    <i class="fas fa-calendar-alt fa-lg text-success"></i>
                </div>
                <h5 class="fw-bold mb-2" style="color:#0a1628;">Jadwal</h5>
                <p class="text-muted small mb-0">{{ $settings->tournament_dates }}</p>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-0 card-karta p-4 text-center h-100">
                <div class="d-flex align-items-center justify-content-center mb-3" style="width:60px;height:60px;border-radius:50%;background:#dbeafe;margin:auto;">
                    <i class="fas fa-map-marker-alt fa-lg text-primary"></i>
                </div>
                <h5 class="fw-bold mb-2" style="color:#0a1628;">Lokasi</h5>
                <p class="text-muted small mb-0">{{ $settings->location }}</p>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-0 card-karta p-4 text-center h-100">
                <div class="d-flex align-items-center justify-content-center mb-3" style="width:60px;height:60px;border-radius:50%;background:#fef3c7;margin:auto;">
                    <i class="fas fa-trophy fa-lg text-warning"></i>
                </div>
                <h5 class="fw-bold mb-2" style="color:#0a1628;">Hadiah</h5>
                <p class="text-muted small mb-0">{{ $settings->total_prize }}</p>
            </div>
        </div>
    </div>

    <!-- PERATURAN -->
    @if($settings->rules)
    <div class="card border-0 card-karta p-5 mb-5">
        <h3 class="oswald fw-bold mb-4" style="color:#0a1628;"><i class="fas fa-gavel text-success me-2"></i> Peraturan Turnamen</h3>
        <div style="white-space:pre-line;line-height:2;color:#374151;">{{ $settings->rules }}</div>
    </div>
    @endif

    <!-- CTA -->
    <div class="text-center py-4 rounded-4" style="background:linear-gradient(135deg,#0a1628,#1a3356);">
        <h3 class="oswald fw-bold text-white mb-3">Bergabunglah Sekarang!</h3>
        <p class="text-white-50 mb-4">Pendaftaran masih terbuka. Jangan lewatkan kesempatan ini!</p>
        <a href="{{ route('register') }}" class="btn btn-green btn-lg fw-bold px-5">
            <i class="fas fa-user-plus me-2"></i> Daftar Sekarang — Gratis
        </a>
    </div>
</div>
@endsection
