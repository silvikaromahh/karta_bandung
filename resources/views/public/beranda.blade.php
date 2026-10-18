@extends('layouts.public')
@section('title', "KARTA.BDG Men's Football Cup 2026 – Turnamen Sepak Bola Bandung")

@section('content')

<!-- HERO -->
<section class="hero-section">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-7">
                <div class="hero-overline">
                    <i class="fas fa-futbol"></i> Turnamen Resmi Antar Kecamatan
                </div>
                <h1 class="oswald display-4 fw-bold text-white mb-3">
                    KARTA.BDG<br><span class="text-success">Men's Football</span><br>Cup 2026
                </h1>
                <p class="text-white-50 fs-5 mb-4" style="max-width:480px;">
                    Turnamen sepak bola bergengsi antar kecamatan se-Kota Bandung. Tunjukkan kemampuan terbaikmu dan jadilah yang terdepan!
                </p>
                <div class="d-flex flex-wrap gap-3 mb-5">
                    <a href="{{ route('register') }}" class="btn btn-green btn-lg fw-bold px-4">
                        <i class="fas fa-futbol me-2"></i> Masuk/Login
                    </a>
                </div>

                <div class="d-flex flex-wrap gap-4 pt-4" style="border-top:1px solid rgba(255,255,255,0.15);">
                    <div>
                        <div class="hero-stat-value">{{ $settings->total_prize }}</div>
                        <div class="hero-stat-label">Total Hadiah</div>
                    </div>
                    <div style="width:1px;background:rgba(255,255,255,0.15);"></div>
                    <div>
                        <div class="hero-stat-value">{{ $settings->location }}</div>
                        <div class="hero-stat-label">Lokasi</div>
                    </div>
                    <div style="width:1px;background:rgba(255,255,255,0.15);"></div>
                    <div>
                        @php
                            $statusClass = $settings->registration_status === 'Buka' ? 'success' : ($settings->registration_status === 'Segera' ? 'warning' : 'secondary');
                        @endphp
                        <span class="badge bg-{{ $statusClass }} fs-6">{{ $settings->registration_status }}</span>
                        <div class="hero-stat-label mt-1">Status Pendaftaran</div>
                    </div>
                </div>
            </div>

            <div class="col-lg-5 d-none d-lg-block">
                <div class="card border-0 rounded-4 shadow-lg" style="background:rgba(255,255,255,0.05);backdrop-filter:blur(10px);border:1px solid rgba(255,255,255,0.1)!important;">
                    <div class="card-body p-4">
                        <h5 class="text-white oswald mb-3"><i class="fas fa-info-circle text-success me-2"></i> Info Turnamen</h5>
                        <div class="d-flex flex-column gap-3">
                            <div class="d-flex gap-3 align-items-start">
                                <div style="width:36px;height:36px;border-radius:8px;background:rgba(26,155,82,0.2);display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                                    <i class="fas fa-calendar text-success"></i>
                                </div>
                                <div>
                                    <div style="font-size:0.7rem;color:rgba(255,255,255,0.4);text-transform:uppercase;letter-spacing:0.08em;">Jadwal Pendaftaran</div>
                                    <div class="text-white small fw-semibold">{{ $settings->registration_schedule }}</div>
                                </div>
                            </div>
                            <div class="d-flex gap-3 align-items-start">
                                <div style="width:36px;height:36px;border-radius:8px;background:rgba(26,155,82,0.2);display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                                    <i class="fas fa-flag text-success"></i>
                                </div>
                                <div>
                                    <div style="font-size:0.7rem;color:rgba(255,255,255,0.4);text-transform:uppercase;letter-spacing:0.08em;">Pelaksanaan</div>
                                    <div class="text-white small fw-semibold">{{ $settings->tournament_dates }}</div>
                                </div>
                            </div>
                            <div class="d-flex gap-3 align-items-start">
                                <div style="width:36px;height:36px;border-radius:8px;background:rgba(26,155,82,0.2);display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                                    <i class="fas fa-trophy text-success"></i>
                                </div>
                                <div>
                                    <div style="font-size:0.7rem;color:rgba(255,255,255,0.4);text-transform:uppercase;letter-spacing:0.08em;">Total Hadiah</div>
                                    <div class="text-white small fw-semibold">{{ $settings->total_prize }}</div>
                                </div>
                            </div>
                            <div class="d-flex gap-3 align-items-start">
                                <div style="width:36px;height:36px;border-radius:8px;background:rgba(26,155,82,0.2);display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                                    <i class="fas fa-map-marker-alt text-success"></i>
                                </div>
                                <div>
                                    <div style="font-size:0.7rem;color:rgba(255,255,255,0.4);text-transform:uppercase;letter-spacing:0.08em;">Venue</div>
                                    <div class="text-white small fw-semibold">{{ $settings->location }}</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- KEUNGGULAN -->
<section class="py-5 bg-white">
    <div class="container">
        <div class="text-center mb-5">
            <span class="section-overline">Mengapa Bergabung?</span>
            <h2 class="oswald fw-bold mt-1" style="color:#0a1628;">Turnamen Bergengsi Kota Bandung</h2>
            <p class="text-muted">Platform digital terpadu untuk turnamen sepak bola yang modern dan profesional</p>
        </div>
        <div class="row g-4">
            @foreach([
                ['icon'=>'fa-trophy','color'=>'#dcfce7','iconColor'=>'#15803d','title'=>'Hadiah Menarik','desc'=>'Total hadiah mencapai ' . ($settings->total_prize ?? 'Rp 50.000.000') . ' untuk para tim terbaik.'],
                ['icon'=>'fa-users','color'=>'#dbeafe','iconColor'=>'#1d4ed8','title'=>'Persaudaraan Antar Kecamatan','desc'=>'Mempererat hubungan antar warga melalui olahraga yang sportif dan kompetitif.'],
                ['icon'=>'fa-star','color'=>'#fef3c7','iconColor'=>'#92400e','title'=>'Kompetisi Bergengsi','desc'=>'Sistem pertandingan yang terstruktur dan transparan dengan wasit berpengalaman.'],
                ['icon'=>'fa-mobile-alt','color'=>'#f3e8ff','iconColor'=>'#7c3aed','title'=>'Platform Digital','desc'=>'Pendaftaran online, jadwal real-time, dan klasemen langsung di genggaman tangan.'],
            ] as $f)
            <div class="col-md-6 col-lg-3">
                <div class="card border-0 h-100 card-karta p-4 text-center">
                    <div class="d-flex align-items-center justify-content-center mx-auto mb-3"
                         style="width:56px;height:56px;border-radius:14px;background:{{ $f['color'] }};">
                        <i class="fas {{ $f['icon'] }} fa-lg" style="color:{{ $f['iconColor'] }};"></i>
                    </div>
                    <h5 class="fw-bold mb-2" style="color:#0a1628;">{{ $f['title'] }}</h5>
                    <p class="text-muted small mb-0">{{ $f['desc'] }}</p>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</section>

<!-- JADWAL MENDATANG -->
@if($upcomingMatches->count())
<section class="py-5" style="background:#f8fafc;">
    <div class="container">
        <div class="d-flex align-items-center justify-content-between mb-4">
            <div>
                <span class="section-overline">Segera Berlangsung</span>
                <h2 class="oswald fw-bold mt-1 mb-0" style="color:#0a1628;">Jadwal Pertandingan</h2>
            </div>
            <a href="{{ route('jadwal') }}" class="btn btn-outline-secondary btn-sm">Lihat Semua <i class="fas fa-arrow-right ms-1"></i></a>
        </div>
        <div class="row g-3">
            @foreach($upcomingMatches as $m)
            <div class="col-md-4">
                <div class="card border-0 card-karta p-3">
                    <span class="badge bg-navy match-stage-badge mb-2">{{ $m->stage }}</span>
                    <div class="d-flex align-items-center justify-content-between gap-2 my-2">
                        <span class="fw-bold text-center flex-grow-1" style="font-size:0.85rem;color:#0a1628;">{{ $m->homeTeam->name }}</span>
                        <span class="match-score text-navy">VS</span>
                        <span class="fw-bold text-center flex-grow-1" style="font-size:0.85rem;color:#0a1628;">{{ $m->awayTeam->name }}</span>
                    </div>
                    <div class="d-flex gap-3 text-muted mt-2 pt-2 border-top" style="font-size:0.78rem;">
                        <span><i class="fas fa-calendar me-1 text-success"></i>{{ $m->match_date->translatedFormat('d M Y') }}</span>
                        <span><i class="fas fa-clock me-1 text-success"></i>{{ $m->match_date->format('H:i') }} WIB</span>
                    </div>
                    <div class="text-muted mt-1" style="font-size:0.78rem;">
                        <i class="fas fa-map-marker-alt me-1 text-success"></i>{{ $m->venue }}
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</section>
@endif

<!-- HASIL TERBARU -->
@if($recentMatches->count())
<section class="py-5 bg-white">
    <div class="container">
        <div class="d-flex align-items-center justify-content-between mb-4">
            <div>
                <span class="section-overline">Pertandingan Terakhir</span>
                <h2 class="oswald fw-bold mt-1 mb-0" style="color:#0a1628;">Hasil Pertandingan</h2>
            </div>
            <a href="{{ route('hasil-klasemen') }}" class="btn btn-outline-secondary btn-sm">Lihat Semua <i class="fas fa-arrow-right ms-1"></i></a>
        </div>
        <div class="row g-3">
            @foreach($recentMatches as $m)
            <div class="col-md-4">
                <div class="card border-0 card-karta p-3">
                    <div class="d-flex align-items-center justify-content-between mb-1">
                        <span class="badge bg-success match-stage-badge">Selesai</span>
                        <span class="text-muted" style="font-size:0.75rem;">{{ $m->match_date->translatedFormat('d M Y') }}</span>
                    </div>
                    <div class="d-flex align-items-center justify-content-between gap-2 my-2">
                        <span class="fw-bold text-center flex-grow-1" style="font-size:0.85rem;color:#0a1628;">{{ $m->homeTeam->name }}</span>
                        <span class="match-score text-navy">{{ $m->home_score }} – {{ $m->away_score }}</span>
                        <span class="fw-bold text-center flex-grow-1" style="font-size:0.85rem;color:#0a1628;">{{ $m->awayTeam->name }}</span>
                    </div>
                    <div class="text-muted" style="font-size:0.78rem;">
                        <i class="fas fa-map-marker-alt me-1 text-success"></i>{{ $m->venue }}
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</section>
@endif

<!-- KLASEMEN SINGKAT -->
@if($standings->count())
<section class="py-5" style="background:#f8fafc;">
    <div class="container">
        <div class="d-flex align-items-center justify-content-between mb-4">
            <div>
                <span class="section-overline">Performa Tim</span>
                <h2 class="oswald fw-bold mt-1 mb-0" style="color:#0a1628;">Pratinjau Klasemen</h2>
            </div>
            <a href="{{ route('hasil-klasemen') }}" class="btn btn-outline-secondary btn-sm">Klasemen Lengkap <i class="fas fa-arrow-right ms-1"></i></a>
        </div>
        <div class="card border-0 card-karta overflow-hidden">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0 standings-table">
                    <thead>
                        <tr>
                            <th class="text-white text-center" style="width:50px;">Pos</th>
                            <th class="text-white">Tim</th>
                            <th class="text-white text-center">M</th>
                            <th class="text-white text-center">M</th>
                            <th class="text-white text-center">S</th>
                            <th class="text-white text-center">K</th>
                            <th class="text-white text-center">SG</th>
                            <th class="text-white text-center fw-bold">Poin</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($standings as $i => $team)
                        <tr>
                            <td class="text-center fw-bold
                                @if($i===0) text-success
                                @elseif($i===1) text-primary
                                @elseif($i===2) text-warning
                                @endif">{{ $i + 1 }}</td>
                            <td class="fw-semibold">{{ $team->name }}
                                <small class="text-muted d-block" style="font-size:0.72rem;">{{ $team->group_name }}</small>
                            </td>
                            <td class="text-center">{{ $team->played }}</td>
                            <td class="text-center text-success fw-semibold">{{ $team->won }}</td>
                            <td class="text-center">{{ $team->drawn }}</td>
                            <td class="text-center text-danger">{{ $team->lost }}</td>
                            <td class="text-center">{{ $team->goal_difference > 0 ? '+' : '' }}{{ $team->goal_difference }}</td>
                            <td class="text-center fw-bold fs-6" style="color:#0a1628;">{{ $team->points }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</section>
@endif

<!-- BERITA -->
@if($news->count())
<section class="py-5 bg-white">
    <div class="container">
        <div class="d-flex align-items-center justify-content-between mb-4">
            <div>
                <span class="section-overline">Terkini</span>
                <h2 class="oswald fw-bold mt-1 mb-0" style="color:#0a1628;">Berita & Pengumuman</h2>
            </div>
            <a href="{{ route('berita') }}" class="btn btn-outline-secondary btn-sm">Semua Berita <i class="fas fa-arrow-right ms-1"></i></a>
        </div>
        <div class="row g-4">
            @foreach($news as $n)
            <div class="col-md-4">
                <div class="card border-0 card-karta h-100">
                    <div style="height:180px;background:linear-gradient(135deg,#0a1628,#1a3356);display:flex;align-items:center;justify-content:center;">
                        <i class="fas fa-newspaper" style="font-size:3rem;color:rgba(255,255,255,0.15);"></i>
                    </div>
                    <div class="card-body p-4">
                        <div class="d-flex align-items-center gap-2 mb-2">
                            <span class="badge bg-success">{{ $n->category }}</span>
                            <span class="text-muted" style="font-size:0.75rem;">{{ $n->published_at?->translatedFormat('d M Y') }}</span>
                        </div>
                        <h6 class="fw-bold mb-2" style="color:#0a1628;">{{ $n->title }}</h6>
                        <p class="text-muted small">{{ Str::limit($n->summary, 100) }}</p>
                    </div>
                    <div class="card-footer bg-white border-top-0 pt-0 pb-4 px-4">
                        <a href="{{ route('berita.detail', $n->slug) }}" class="btn btn-outline-success btn-sm">
                            Baca Selengkapnya <i class="fas fa-arrow-right ms-1"></i>
                        </a>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</section>
@endif

<!-- CTA SECTION -->
<section class="py-5" style="background:linear-gradient(135deg,#0a1628 0%,#1a3356 100%);">
    <div class="container text-center py-3">
        <h2 class="oswald fw-bold text-white display-5 mb-3">Siap Bertanding?</h2>
        <p class="text-white-50 fs-5 mb-4">Daftarkan dirimu sekarang dan jadilah bagian dari turnamen paling bergengsi di Kota Bandung.</p>
        <div class="d-flex justify-content-center gap-3 flex-wrap">
            <a href="{{ route('register') }}" class="btn btn-green btn-lg fw-bold px-5">
                <i class="fas fa-user-plus me-2"></i> Daftar Sekarang
            </a>
            <a href="{{ route('jadwal') }}" class="btn btn-outline-light btn-lg px-5">
                <i class="fas fa-calendar me-2"></i> Lihat Jadwal
            </a>
        </div>
    </div>
</section>

<!-- SPONSOR -->
<section class="py-5 bg-white">
    <div class="container">
        <div class="text-center mb-4">
            <span class="section-overline">Didukung Oleh</span>
            <h5 class="oswald fw-bold mt-1" style="color:#0a1628;">Sponsor & Mitra Turnamen</h5>
        </div>
        <div class="row g-3 justify-content-center align-items-center">
            @for($s = 1; $s <= 5; $s++)
            <div class="col-6 col-md-2">
                <div class="d-flex align-items-center justify-content-center p-3 rounded-3" style="background:#f8fafc;height:70px;border:1px solid #e9ecef;">
                    <span class="text-muted fw-bold" style="font-size:0.75rem;letter-spacing:0.05em;">SPONSOR {{ $s }}</span>
                </div>
            </div>
            @endfor
        </div>
    </div>
</section>

@endsection
