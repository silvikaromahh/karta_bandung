<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Berkas Verifikasi Tim - {{ $registration->nama_tim }} | KARTA.BDG CUP 2026</title>
    <link rel="icon" type="image/png" href="{{ asset('images/logo.png') }}?v={{ file_exists(public_path('images/logo.png')) ? filemtime(public_path('images/logo.png')) : time() }}">
    <link rel="shortcut icon" type="image/x-icon" href="{{ asset('favicon.ico') }}?v={{ file_exists(public_path('favicon.ico')) ? filemtime(public_path('favicon.ico')) : time() }}">
    <link rel="apple-touch-icon" href="{{ asset('images/logo.png') }}?v={{ file_exists(public_path('images/logo.png')) ? filemtime(public_path('images/logo.png')) : time() }}">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Cabinet+Grotesk:wght@700;800;900&family=Oswald:wght@500;600;700&family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=JetBrains+Mono:wght@500;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary-navy: #0b1a30;
            --primary-blue: #1d4ed8;
            --primary-emerald: #059669;
            --primary-gold: #d97706;
            --accent-gold: #fbbf24;
            --dark-surface: #0f172a;
            --light-bg: #f8fafc;
            --border-color: #e2e8f0;
        }

        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background: #eef2f6;
            color: #1e293b;
            -webkit-print-color-adjust: exact !important;
            print-color-adjust: exact !important;
            font-size: 0.86rem;
            min-height: 100vh;
        }

        .oswald { font-family: 'Oswald', sans-serif; }
        .cabinet { font-family: 'Cabinet Grotesk', 'Oswald', sans-serif; }
        .mono { font-family: 'JetBrains Mono', monospace; }

        /* Floating Modern Action Bar */
        .action-bar-wrapper {
            position: sticky;
            top: 16px;
            z-index: 1000;
        }
        .action-bar {
            background: rgba(255, 255, 255, 0.94);
            backdrop-filter: blur(12px);
            border-radius: 16px;
            box-shadow: 0 10px 30px -5px rgba(11, 26, 48, 0.12), 0 0 0 1px rgba(11, 26, 48, 0.05);
            border: 1px solid rgba(226, 232, 240, 0.8);
            padding: 14px 22px;
        }

        /* Document Paper Sheet */
        .paper-sheet {
            background: #ffffff;
            border-radius: 20px;
            box-shadow: 0 20px 45px -10px rgba(15, 23, 42, 0.1), 0 0 0 1px rgba(15, 23, 42, 0.05);
            padding: 42px 48px;
            position: relative;
            overflow: hidden;
            margin-bottom: 50px;
        }

        /* Top Olympic/Tournament Ribbon */
        .paper-sheet::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 7px;
            background: linear-gradient(90deg, #1d4ed8 0%, #059669 35%, #fbbf24 70%, #dc2626 100%);
        }

        /* Subtle Watermark */
        .watermark-bg {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%) rotate(-25deg);
            font-family: 'Oswald', sans-serif;
            font-size: 6.5rem;
            font-weight: 900;
            color: rgba(15, 23, 42, 0.022);
            pointer-events: none;
            user-select: none;
            white-space: nowrap;
            z-index: 0;
            text-transform: uppercase;
            letter-spacing: 12px;
        }

        /* Official Kop Surat Header */
        .doc-header {
            position: relative;
            z-index: 1;
            padding-bottom: 24px;
            border-bottom: 2px solid #0f172a;
            margin-bottom: 24px;
        }
        .doc-header-line-gold {
            position: absolute;
            bottom: -5px;
            left: 0;
            right: 0;
            height: 2px;
            background: linear-gradient(90deg, #fbbf24 0%, #d97706 50%, #fbbf24 100%);
        }

        /* Team Hero Showcase Card */
        .team-hero-card {
            background: linear-gradient(135deg, #091e3a 0%, #0f2d57 50%, #153e77 100%);
            border-radius: 16px;
            padding: 24px 28px;
            color: #ffffff;
            position: relative;
            overflow: hidden;
            box-shadow: 0 10px 25px -5px rgba(9, 30, 58, 0.25);
            margin-bottom: 28px;
            z-index: 1;
        }
        .team-hero-card::after {
            content: '';
            position: absolute;
            top: -50px;
            right: -50px;
            width: 220px;
            height: 220px;
            background: radial-gradient(circle, rgba(251, 191, 36, 0.15) 0%, transparent 70%);
            border-radius: 50%;
            pointer-events: none;
        }

        .team-emblem-badge {
            width: 64px;
            height: 64px;
            border-radius: 16px;
            background: linear-gradient(135deg, #fbbf24 0%, #d97706 100%);
            color: #0b1a30;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.85rem;
            box-shadow: 0 6px 18px rgba(217, 119, 6, 0.4);
            border: 2px solid rgba(255, 255, 255, 0.4);
            flex-shrink: 0;
        }

        /* Section Title Headers */
        .section-header-bar {
            background: linear-gradient(90deg, #0f172a 0%, #1e293b 100%);
            color: #ffffff;
            padding: 10px 20px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 18px;
            box-shadow: 0 4px 12px rgba(15, 23, 42, 0.08);
            border-left: 5px solid #059669;
            position: relative;
            z-index: 1;
        }

        /* Modern Member & Player Cards */
        .member-card {
            background: #ffffff;
            border: 1px solid #e2e8f0;
            border-radius: 16px;
            padding: 20px 22px;
            margin-bottom: 18px;
            box-shadow: 0 3px 10px rgba(0, 0, 0, 0.02);
            transition: all 0.2s cubic-bezier(0.16, 1, 0.3, 1);
            position: relative;
            z-index: 1;
            break-inside: avoid;
            page-break-inside: avoid;
        }
        .member-card:hover {
            box-shadow: 0 10px 25px -5px rgba(15, 23, 42, 0.08);
            border-color: #cbd5e1;
            transform: translateY(-2px);
        }

        /* Border accents based on position */
        .member-card.pos-kiper { border-left: 5px solid #f59e0b; }
        .member-card.pos-bek { border-left: 5px solid #2563eb; }
        .member-card.pos-gelandang { border-left: 5px solid #059669; }
        .member-card.pos-penyerang { border-left: 5px solid #dc2626; }
        .member-card.official-card { border-left: 5px solid #6366f1; }

        /* Kit Number Badge */
        .kit-number-badge {
            width: 52px;
            height: 52px;
            background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%);
            color: #fbbf24;
            border: 2px solid #fbbf24;
            border-radius: 14px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.55rem;
            font-weight: 700;
            font-family: 'Oswald', sans-serif;
            box-shadow: 0 4px 12px rgba(15, 23, 42, 0.25);
            flex-shrink: 0;
            letter-spacing: -0.5px;
        }

        /* Document / Photo Framing System */
        .doc-box {
            background: #ffffff;
            border: 1px solid #e2e8f0;
            border-radius: 12px;
            overflow: hidden;
            display: flex;
            flex-direction: column;
            transition: all 0.2s ease;
            box-shadow: 0 2px 6px rgba(0, 0, 0, 0.03);
            height: 100%;
        }
        .doc-box:hover {
            border-color: #059669;
            box-shadow: 0 6px 18px rgba(5, 150, 105, 0.12);
        }

        .doc-box-tag {
            background: #f1f5f9;
            border-bottom: 1px solid #e2e8f0;
            padding: 5px 10px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            font-size: 0.68rem;
            font-weight: 700;
            color: #334155;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .doc-box-image-wrap {
            position: relative;
            background: #f8fafc;
            overflow: hidden;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-grow: 1;
        }

        /* Specific Aspect Ratios for True Document Previews */
        .doc-box-pasfoto .doc-box-image-wrap {
            height: 145px;
        }
        .doc-box-pasfoto .doc-box-image-wrap img {
            width: auto;
            max-width: 100%;
            height: 100%;
            object-fit: contain;
            background: #e2e8f0;
            transition: transform 0.25s ease;
        }

        .doc-box-doc .doc-box-image-wrap {
            height: 145px;
        }
        .doc-box-doc .doc-box-image-wrap img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform 0.25s ease;
        }

        .doc-box-image-wrap:hover img {
            transform: scale(1.06);
        }

        .doc-zoom-overlay {
            position: absolute;
            inset: 0;
            background: rgba(15, 23, 42, 0.6);
            backdrop-filter: blur(2px);
            color: #ffffff;
            font-size: 0.75rem;
            font-weight: 700;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 6px;
            opacity: 0;
            transition: opacity 0.2s ease;
        }
        .doc-box-image-wrap:hover .doc-zoom-overlay {
            opacity: 1;
        }

        .doc-box-bottom {
            padding: 6px 10px;
            background: #ffffff;
            border-top: 1px solid #f1f5f9;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .btn-action-mini {
            color: #059669;
            background: rgba(5, 150, 105, 0.08);
            border: 1px solid rgba(5, 150, 105, 0.2);
            font-size: 0.68rem;
            font-weight: 700;
            padding: 3px 9px;
            border-radius: 6px;
            text-decoration: none;
            transition: all 0.15s ease;
            display: inline-flex;
            align-items: center;
            gap: 4px;
        }
        .btn-action-mini:hover {
            background: #059669;
            color: #ffffff;
        }

        /* Posisi Badge Styles */
        .pos-pill {
            font-size: 0.72rem;
            font-weight: 800;
            padding: 4px 10px;
            border-radius: 8px;
            letter-spacing: 0.5px;
            text-transform: uppercase;
            display: inline-flex;
            align-items: center;
            gap: 5px;
        }
        .pos-pill-kiper { background: rgba(245, 158, 11, 0.15); color: #b45309; border: 1px solid rgba(245, 158, 11, 0.3); }
        .pos-pill-bek { background: rgba(37, 99, 235, 0.15); color: #1d4ed8; border: 1px solid rgba(37, 99, 235, 0.3); }
        .pos-pill-gelandang { background: rgba(5, 150, 105, 0.15); color: #047857; border: 1px solid rgba(5, 150, 105, 0.3); }
        .pos-pill-penyerang { background: rgba(220, 38, 38, 0.15); color: #b91c1c; border: 1px solid rgba(220, 38, 38, 0.3); }

        /* Authentic Verification Seal / Stempel */
        .seal-stamp-container {
            position: relative;
            display: inline-block;
        }
        .seal-stamp {
            border: 3px dashed #059669;
            color: #059669;
            padding: 8px 18px;
            border-radius: 12px;
            font-family: 'Oswald', sans-serif;
            text-transform: uppercase;
            letter-spacing: 1.5px;
            font-size: 0.88rem;
            font-weight: 700;
            transform: rotate(-3deg);
            background: rgba(5, 150, 105, 0.05);
            display: inline-block;
            box-shadow: 0 4px 12px rgba(5, 150, 105, 0.1);
        }

        /* Print Media Styles */
        @media print {
            .no-print {
                display: none !important;
            }
            body {
                background: #ffffff !important;
                padding: 0 !important;
                font-size: 0.82rem !important;
                color: #000000 !important;
            }
            .paper-sheet {
                box-shadow: none !important;
                padding: 10px 0 !important;
                border-radius: 0 !important;
                margin-bottom: 0 !important;
            }
            .paper-sheet::before {
                display: none !important;
            }
            .team-hero-card {
                background: #091e3a !important;
                color: #ffffff !important;
                box-shadow: none !important;
                padding: 16px 20px !important;
                margin-bottom: 16px !important;
            }
            .member-card {
                box-shadow: none !important;
                border: 1px solid #cbd5e1 !important;
                margin-bottom: 14px !important;
                padding: 14px !important;
                transform: none !important;
            }
            .doc-box {
                box-shadow: none !important;
                border: 1px solid #cbd5e1 !important;
            }
            .doc-box-pasfoto .doc-box-image-wrap,
            .doc-box-doc .doc-box-image-wrap {
                height: 120px !important;
            }
            .doc-zoom-overlay {
                display: none !important;
            }
            .btn-action-mini {
                display: none !important;
            }
            .watermark-bg {
                display: block !important;
                opacity: 0.03 !important;
            }
        }
    </style>
</head>
<body class="py-4">

    <!-- FLOATING ACTION BAR (SCREEN ONLY) -->
    <div class="container no-print mb-4 action-bar-wrapper">
        <div class="action-bar d-flex justify-content-between align-items-center flex-wrap gap-3">
            <div class="d-flex align-items-center gap-3">
                <div style="width:46px; height:46px; border-radius:12px; background:linear-gradient(135deg, #1d4ed8, #059669); color:#ffffff; display:flex; align-items:center; justify-content:center; font-size:1.3rem; box-shadow:0 4px 10px rgba(29, 78, 216, 0.25);">
                    <i class="fas fa-file-signature"></i>
                </div>
                <div>
                    <div class="d-flex align-items-center gap-2">
                        <h6 class="fw-bold mb-0 oswald text-dark" style="letter-spacing:0.8px; font-size:1.1rem;">BERKAS AKREDITASI REGISTRASI RESMI</h6>
                        <span class="badge bg-success-subtle text-success border border-success-subtle fw-bold" style="font-size:0.68rem;">READY TO PRINT</span>
                    </div>
                    <small class="text-muted">Klik pada kartu foto dokumen untuk memperbesar atau klik Unduh. Hasil cetak berstandar format A4.</small>
                </div>
            </div>
            @php
                $backUrl = (auth()->check() && auth()->user()->role === 'admin') 
                    ? route('admin.verifikasi.detail', $registration->id) 
                    : route('peserta.pendaftaran');
            @endphp
            <div class="d-flex align-items-center gap-2">
                <a href="{{ $backUrl }}" onclick="handleKembali(event, '{{ $backUrl }}')" class="btn btn-outline-secondary btn-sm px-3 fw-semibold">
                    <i class="fas fa-arrow-left me-1"></i> Kembali
                </a>
                <button onclick="window.print()" class="btn btn-primary btn-sm px-4 fw-bold shadow-sm" style="background:linear-gradient(135deg, #1d4ed8, #2563eb); border:none;">
                    <i class="fas fa-print me-1"></i> Cetak / Simpan PDF
                </button>
            </div>
        </div>
    </div>

    <!-- MAIN PRINTABLE PAPER CONTAINER -->
    <div class="container">
        <div class="paper-sheet">

            <!-- WATERMARK DI LATAR BELAKANG -->
            <div class="watermark-bg">KARTA.BDG CUP 2026</div>

            <!-- KOP SURAT RESMI TURNAMEN -->
            <div class="doc-header">
                <div class="row align-items-center g-3">
                    <div class="col-md-8 col-sm-7 d-flex align-items-center gap-3">
                        <img src="{{ asset('images/logo.png') }}" alt="Logo KARTA.BDG CUP" style="height: 82px; width: auto; filter: drop-shadow(0 4px 8px rgba(0,0,0,0.15));" class="flex-shrink-0">
                        <div>
                            <div class="d-flex align-items-center gap-2 mb-1 flex-wrap">
                                <span class="badge" style="background:#0f172a; color:#fbbf24; font-size:0.65rem; font-weight:800; letter-spacing:1px;">OFFICIAL ROSTER DOSSIER</span>
                                <span class="badge bg-primary" style="font-size:0.65rem; font-weight:700;">MEN'S FOOTBALL U-23</span>
                                <span class="badge bg-success" style="font-size:0.65rem; font-weight:700;">TAHUN 2026</span>
                            </div>
                            <h1 class="oswald fw-bold mb-0 text-dark" style="letter-spacing:1.5px; font-size:2rem; line-height:1.1;">KARTA.BDG CUP 2026</h1>
                            <div class="fw-bold text-secondary small text-uppercase" style="letter-spacing:0.8px; font-size:0.82rem;">TURNAMEN SEPAK BOLA KARANG TARUNA SE-KOTA BANDUNG</div>
                            <small class="text-muted d-block mt-1" style="font-size:0.75rem;">
                                <i class="fas fa-map-marker-alt text-danger me-1"></i> P.H.H. Mustofa No. 39 Komp. Surapati Core Blok J. 21
                            </small>
                        </div>
                    </div>
                    <div class="col-md-4 col-sm-5 text-sm-end text-start">
                        <div class="p-3 rounded-3 text-start text-sm-end" style="background:#f8fafc; border:1px solid #e2e8f0; display:inline-block; min-width:210px;">
                            <span class="text-muted d-block text-uppercase fw-bold" style="font-size:0.65rem; letter-spacing:0.8px;">Nomor Registrasi Kontingen</span>
                            <span class="oswald fw-bold text-dark mono fs-5" style="letter-spacing:1px; color:#0f172a;">#REG-2026-{{ str_pad($registration->id, 4, '0', STR_PAD_LEFT) }}</span>
                            <div class="mt-1 pt-1 border-top" style="font-size:0.7rem; color:#64748b;">
                                <i class="fas fa-clock text-secondary me-1"></i> {{ now()->translatedFormat('d F Y, H:i') }} WIB
                            </div>
                        </div>
                    </div>
                </div>
                <div class="doc-header-line-gold"></div>
            </div>

            <!-- HERO CARD KONTINGEN TIM -->
            <div class="team-hero-card">
                <div class="row align-items-center g-3">
                    <div class="col-lg-5 col-md-6">
                        <div class="d-flex align-items-center gap-3">
                            <div class="team-emblem-badge">
                                <i class="fas fa-shield-alt"></i>
                            </div>
                            <div>
                                <span class="text-white-50 text-uppercase fw-bold" style="font-size:0.7rem; letter-spacing:1px;">KONTINGEN KECAMATAN</span>
                                <h2 class="cabinet fw-bold mb-0 text-white" style="letter-spacing:0.5px; font-size:1.85rem;">{{ $registration->nama_tim }}</h2>
                                <div class="d-flex align-items-center gap-2 mt-1">
                                    <span class="badge" style="background:rgba(255,255,255,0.15); border:1px solid rgba(255,255,255,0.25); font-size:0.72rem;">
                                        <i class="fas fa-city me-1 text-warning"></i> Kec. {{ $registration->kecamatan }}
                                    </span>
                                    <span class="badge" style="background:rgba(251,191,36,0.2); color:#fbbf24; border:1px solid rgba(251,191,36,0.4); font-size:0.72rem;">
                                        Kota Bandung
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-7 col-md-6">
                        <div class="row g-2 text-start">
                            <div class="col-sm-4 col-6">
                                <div class="p-2 rounded-2" style="background:rgba(255,255,255,0.06); border:1px solid rgba(255,255,255,0.1);">
                                    <span class="text-white-50 d-block" style="font-size:0.68rem;">Manager / Akun:</span>
                                    <strong class="d-block text-white text-truncate small">{{ $registration->user->name }}</strong>
                                    <small class="text-white-50 text-truncate d-block" style="font-size:0.68rem;">{{ $registration->user->email }}</small>
                                </div>
                            </div>
                            <div class="col-sm-4 col-6">
                                <div class="p-2 rounded-2" style="background:rgba(255,255,255,0.06); border:1px solid rgba(255,255,255,0.1);">
                                    <span class="text-white-50 d-block" style="font-size:0.68rem;">Surat Rekomendasi:</span>
                                    @if($registration->surat_rekomendasi_path)
                                        <div class="d-flex align-items-center justify-content-between mt-1 gap-1">
                                            <span class="badge bg-success" style="font-size:0.68rem;"><i class="fas fa-check-circle me-1"></i> Terlampir</span>
                                            <div class="d-flex align-items-center gap-1 no-print">
                                                <button type="button" class="btn btn-sm btn-link p-0 text-warning text-decoration-none fw-bold" style="font-size:0.68rem;" onclick="previewImage('{{ Storage::url($registration->surat_rekomendasi_path) }}', 'Surat Rekomendasi - {{ $registration->nama_tim }}', 'surat_rekomendasi_{{ Str::slug($registration->nama_tim) }}.pdf')">
                                                    Lihat <i class="fas fa-eye ms-1"></i>
                                                </button>
                                                <span class="text-white-50 small">|</span>
                                                <a href="{{ Storage::url($registration->surat_rekomendasi_path) }}" target="_blank" class="text-warning text-decoration-none fw-bold" style="font-size:0.68rem;" title="Buka PDF di Tab Baru">
                                                    <i class="fas fa-external-link-alt"></i>
                                                </a>
                                            </div>
                                        </div>
                                    @else
                                        <span class="badge bg-secondary text-white-50 mt-1" style="font-size:0.68rem;">Tidak Ada</span>
                                    @endif
                                </div>
                            </div>
                            <div class="col-sm-4 col-12">
                                <div class="p-2 rounded-2" style="background:rgba(255,255,255,0.06); border:1px solid rgba(255,255,255,0.1);">
                                    <span class="text-white-50 d-block" style="font-size:0.68rem;">Status Akreditasi:</span>
                                    @if($registration->status === 'Terverifikasi')
                                        <span class="badge bg-success text-white px-2 py-1 mt-1 fw-bold d-inline-flex align-items-center gap-1" style="font-size:0.75rem;">
                                            <i class="fas fa-check-double"></i> TERVERIFIKASI
                                        </span>
                                    @elseif($registration->status === 'Menunggu Verifikasi')
                                        <span class="badge bg-warning text-dark px-2 py-1 mt-1 fw-bold d-inline-flex align-items-center gap-1" style="font-size:0.75rem;">
                                            <i class="fas fa-hourglass-half"></i> MENUNGGU VERIFIKASI
                                        </span>
                                    @else
                                        <span class="badge bg-danger text-white px-2 py-1 mt-1 fw-bold d-inline-flex align-items-center gap-1" style="font-size:0.75rem;">
                                            <i class="fas fa-times-circle"></i> DITOLAK
                                        </span>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- BAGIAN 1: OFFICIAL TIM -->
            <div class="section-header-bar" style="border-left-color: #6366f1;">
                <div class="d-flex align-items-center gap-2">
                    <i class="fas fa-user-tie text-warning"></i>
                    <span class="oswald fw-bold text-uppercase" style="letter-spacing:1px; font-size:0.95rem;">DATA OFFICIAL & STAF TEKNIS KONTINGEN</span>
                </div>
                <span class="badge bg-white text-dark fw-bold px-2 py-1" style="font-size:0.7rem;">
                    {{ $registration->members()->where('tipe', 'official')->count() }} Official Terdaftar
                </span>
            </div>

            @php $officials = $registration->members()->where('tipe', 'official')->get(); @endphp
            @forelse($officials as $off)
            <div class="member-card official-card">
                <div class="row align-items-center g-3">
                    <div class="col-lg-5 col-md-5">
                        <div class="d-flex align-items-start gap-3">
                            <div style="width:48px; height:48px; border-radius:12px; background:linear-gradient(135deg, #6366f1, #4f46e5); color:#ffffff; display:flex; align-items:center; justify-content:center; font-size:1.35rem; flex-shrink:0; box-shadow:0 4px 10px rgba(99,102,241,0.25);">
                                <i class="fas fa-user-tie"></i>
                            </div>
                            <div>
                                <span class="badge bg-primary-subtle text-primary border border-primary-subtle fw-bold mb-1" style="font-size:0.72rem;">
                                    {{ $off->jabatan }}
                                </span>
                                <h5 class="fw-bold mb-1 text-dark oswald" style="letter-spacing:0.5px; font-size:1.15rem;">{{ $off->full_name }}</h5>
                                <div class="p-2 rounded-2 bg-light border mt-2" style="font-size:0.76rem;">
                                    <div class="mb-1"><span class="text-muted">NIK:</span> <strong class="mono text-dark">{{ $off->nik }}</strong></div>
                                    <div><span class="text-muted">Tanggal Lahir:</span> <strong class="text-dark">{{ $off->tanggal_lahir->translatedFormat('d F Y') }}</strong></div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-7 col-md-7">
                        <div class="row g-2">
                            <!-- Pas Foto Official (Portrait) -->
                            <div class="col-6">
                                <div class="doc-box doc-box-pasfoto">
                                    <div class="doc-box-tag">
                                        <span><i class="fas fa-portrait me-1 text-primary"></i> Pas Foto</span>
                                        <span class="text-muted" style="font-size:0.6rem;">3x4</span>
                                    </div>
                                    @if($off->pasfoto_path)
                                        <div class="doc-box-image-wrap" onclick="previewImage('{{ Storage::url($off->pasfoto_path) }}', 'Pas Foto Official - {{ $off->full_name }}', 'pasfoto_official_{{ Str::slug($off->full_name) }}.jpg')">
                                            <img src="{{ Storage::url($off->pasfoto_path) }}" alt="Pas Foto Official">
                                            <div class="doc-zoom-overlay"><i class="fas fa-search-plus"></i> Perbesar</div>
                                        </div>
                                        <div class="doc-box-bottom">
                                            <span class="text-muted" style="font-size:0.65rem;">Foto Resmi</span>
                                            <a href="{{ Storage::url($off->pasfoto_path) }}" download="pasfoto_official_{{ Str::slug($off->full_name) }}.jpg" class="btn-action-mini" title="Unduh Pas Foto">
                                                <i class="fas fa-download"></i> Unduh
                                            </a>
                                        </div>
                                    @else
                                        <div class="py-4 text-muted small bg-light d-flex align-items-center justify-content-center flex-column" style="height:145px;">
                                            <i class="fas fa-image fa-2x mb-1 text-secondary opacity-40"></i>
                                            <span style="font-size:0.7rem;">Belum Diunggah</span>
                                        </div>
                                    @endif
                                </div>
                            </div>
                            <!-- KTP Official (Landscape) -->
                            <div class="col-6">
                                <div class="doc-box doc-box-doc">
                                    <div class="doc-box-tag">
                                        <span><i class="fas fa-id-card me-1 text-success"></i> E-KTP Asli</span>
                                        <span class="text-muted" style="font-size:0.6rem;">Identitas</span>
                                    </div>
                                    @if($off->ktp_path)
                                        <div class="doc-box-image-wrap" onclick="previewImage('{{ Storage::url($off->ktp_path) }}', 'Foto E-KTP Official - {{ $off->full_name }}', 'ktp_official_{{ Str::slug($off->full_name) }}.jpg')">
                                            <img src="{{ Storage::url($off->ktp_path) }}" alt="Foto KTP Official">
                                            <div class="doc-zoom-overlay"><i class="fas fa-search-plus"></i> Perbesar</div>
                                        </div>
                                        <div class="doc-box-bottom">
                                            <span class="text-muted" style="font-size:0.65rem;">Scan E-KTP</span>
                                            <a href="{{ Storage::url($off->ktp_path) }}" download="ktp_official_{{ Str::slug($off->full_name) }}.jpg" class="btn-action-mini" title="Unduh KTP">
                                                <i class="fas fa-download"></i> Unduh
                                            </a>
                                        </div>
                                    @else
                                        <div class="py-4 text-muted small bg-light d-flex align-items-center justify-content-center flex-column" style="height:145px;">
                                            <i class="fas fa-id-card fa-2x mb-1 text-secondary opacity-40"></i>
                                            <span style="font-size:0.7rem;">Belum Diunggah</span>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            @empty
            <div class="text-center py-4 text-muted mb-4 border rounded-3 bg-light">
                <i class="fas fa-user-slash fa-2x mb-2 text-secondary opacity-50 d-block"></i>
                Belum ada data official tim yang didaftarkan.
            </div>
            @endforelse

            <!-- BAGIAN 2: SKUAD PEMAIN TIM -->
            <div class="section-header-bar mt-4" style="border-left-color: #059669;">
                <div class="d-flex align-items-center gap-2">
                    <i class="fas fa-users text-success"></i>
                    <span class="oswald fw-bold text-uppercase" style="letter-spacing:1px; font-size:0.95rem;">DATA SKUAD PEMAIN KONTINGEN (DAFTAR RESMI)</span>
                </div>
                <span class="badge bg-white text-dark fw-bold px-2 py-1" style="font-size:0.7rem;">
                    {{ $registration->members()->where('tipe', 'pemain')->count() }} Pemain Terdaftar
                </span>
            </div>

            @php 
                $pemainList = $registration->members()->where('tipe', 'pemain')->get();
                $posClass = [
                    'Kiper' => 'pos-kiper',
                    'Bek' => 'pos-bek',
                    'Gelandang' => 'pos-gelandang',
                    'Penyerang' => 'pos-penyerang',
                ];
                $posPillClass = [
                    'Kiper' => 'pos-pill-kiper',
                    'Bek' => 'pos-pill-bek',
                    'Gelandang' => 'pos-pill-gelandang',
                    'Penyerang' => 'pos-pill-penyerang',
                ];
                $posIcon = [
                    'Kiper' => 'fa-mitten',
                    'Bek' => 'fa-shield-halved',
                    'Gelandang' => 'fa-compass',
                    'Penyerang' => 'fa-bolt',
                ];
            @endphp

            @foreach($pemainList as $idx => $pem)
            <div class="member-card {{ $posClass[$pem->posisi] ?? '' }}">
                <div class="row align-items-center g-3">
                    <!-- Bio Pemain Kolom Kiri -->
                    <div class="col-lg-4 col-md-4">
                        <div class="d-flex align-items-center gap-3 mb-2">
                            <div class="kit-number-badge">
                                {{ $pem->nomor_punggung }}
                            </div>
                            <div>
                                <span class="pos-pill {{ $posPillClass[$pem->posisi] ?? 'bg-secondary text-white' }} mb-1">
                                    <i class="fas {{ $posIcon[$pem->posisi] ?? 'fa-futbol' }} me-1"></i> {{ $pem->posisi }}
                                </span>
                                <h5 class="fw-bold mb-0 text-dark oswald" style="letter-spacing:0.5px; font-size:1.2rem;">{{ $pem->full_name }}</h5>
                            </div>
                        </div>
                        <div class="p-2 rounded-2 bg-light border mt-2" style="font-size:0.76rem;">
                            <div class="mb-1"><span class="text-muted">NIK:</span> <strong class="mono text-dark">{{ $pem->nik }}</strong></div>
                            <div class="mb-1"><span class="text-muted">Tanggal Lahir:</span> <strong class="text-dark">{{ $pem->tanggal_lahir->translatedFormat('d F Y') }}</strong></div>
                            @php
                                $usia = $pem->tanggal_lahir->age;
                            @endphp
                            <div class="d-flex justify-content-between align-items-center">
                                <div><span class="text-muted">No. Punggung:</span> <strong class="text-dark">#{{ $pem->nomor_punggung }}</strong></div>
                                <div><span class="badge bg-secondary-subtle text-secondary" style="font-size:0.68rem;">Usia: {{ $usia }} Thn</span></div>
                            </div>
                        </div>
                    </div>

                    <!-- Dokumen Foto Kolom Kanan (Pas Foto, KTP, KK, Surat Sehat) -->
                    <div class="col-lg-8 col-md-8">
                        <div class="row g-2">
                            <!-- 1. Pas Foto Pemain (Portrait) -->
                            <div class="col-6">
                                <div class="doc-box doc-box-pasfoto">
                                    <div class="doc-box-tag">
                                        <span><i class="fas fa-portrait me-1 text-primary"></i> Pas Foto</span>
                                        <span class="text-muted" style="font-size:0.58rem;">3x4</span>
                                    </div>
                                    @if($pem->pasfoto_path)
                                        <div class="doc-box-image-wrap" onclick="previewImage('{{ Storage::url($pem->pasfoto_path) }}', 'Pas Foto - {{ $pem->full_name }} (#{{ $pem->nomor_punggung }})', 'pasfoto_{{ Str::slug($pem->full_name) }}.jpg')">
                                            <img src="{{ Storage::url($pem->pasfoto_path) }}" alt="Pas Foto">
                                            <div class="doc-zoom-overlay"><i class="fas fa-search-plus"></i> Zoom</div>
                                        </div>
                                        <div class="doc-box-bottom">
                                            <span class="text-muted" style="font-size:0.63rem;">Pas Foto</span>
                                            <a href="{{ Storage::url($pem->pasfoto_path) }}" download="pasfoto_{{ Str::slug($pem->full_name) }}.jpg" class="btn-action-mini" title="Unduh Pas Foto">
                                                <i class="fas fa-download"></i>
                                            </a>
                                        </div>
                                    @else
                                        <div class="py-4 text-muted small bg-light d-flex align-items-center justify-content-center flex-column" style="height:145px;">
                                            <i class="fas fa-user-circle fa-2x mb-1 text-secondary opacity-40"></i>
                                            <span style="font-size:0.68rem;">Tidak Ada</span>
                                        </div>
                                    @endif
                                </div>
                            </div>

                            <!-- 2. Foto KTP Pemain (Landscape) -->
                            <div class="col-6">
                                <div class="doc-box doc-box-doc">
                                    <div class="doc-box-tag">
                                        <span><i class="fas fa-id-card me-1 text-success"></i> E-KTP</span>
                                        <span class="text-muted" style="font-size:0.58rem;">KTP</span>
                                    </div>
                                    @if($pem->ktp_path)
                                        <div class="doc-box-image-wrap" onclick="previewImage('{{ Storage::url($pem->ktp_path) }}', 'Foto E-KTP - {{ $pem->full_name }}', 'ktp_{{ Str::slug($pem->full_name) }}.jpg')">
                                            <img src="{{ Storage::url($pem->ktp_path) }}" alt="Foto KTP">
                                            <div class="doc-zoom-overlay"><i class="fas fa-search-plus"></i> Zoom</div>
                                        </div>
                                        <div class="doc-box-bottom">
                                            <span class="text-muted" style="font-size:0.63rem;">E-KTP</span>
                                            <a href="{{ Storage::url($pem->ktp_path) }}" download="ktp_{{ Str::slug($pem->full_name) }}.jpg" class="btn-action-mini" title="Unduh KTP">
                                                <i class="fas fa-download"></i>
                                            </a>
                                        </div>
                                    @else
                                        <div class="py-4 text-muted small bg-light d-flex align-items-center justify-content-center flex-column" style="height:145px;">
                                            <i class="fas fa-id-card fa-2x mb-1 text-secondary opacity-40"></i>
                                            <span style="font-size:0.68rem;">Tidak Ada</span>
                                        </div>
                                    @endif
                                </div>
                            </div>

                            <!-- 3. Foto KK Pemain (Landscape) -->
                            <div class="col-6">
                                <div class="doc-box doc-box-doc">
                                    <div class="doc-box-tag">
                                        <span><i class="fas fa-users me-1 text-warning"></i> KK Asli</span>
                                        <span class="text-muted" style="font-size:0.58rem;">Keluarga</span>
                                    </div>
                                    @if($pem->kk_path)
                                        <div class="doc-box-image-wrap" onclick="previewImage('{{ Storage::url($pem->kk_path) }}', 'Foto Kartu Keluarga - {{ $pem->full_name }}', 'kk_{{ Str::slug($pem->full_name) }}.jpg')">
                                            <img src="{{ Storage::url($pem->kk_path) }}" alt="Foto KK">
                                            <div class="doc-zoom-overlay"><i class="fas fa-search-plus"></i> Zoom</div>
                                        </div>
                                        <div class="doc-box-bottom">
                                            <span class="text-muted" style="font-size:0.63rem;">KK</span>
                                            <a href="{{ Storage::url($pem->kk_path) }}" download="kk_{{ Str::slug($pem->full_name) }}.jpg" class="btn-action-mini" title="Unduh KK">
                                                <i class="fas fa-download"></i>
                                            </a>
                                        </div>
                                    @else
                                        <div class="py-4 text-muted small bg-light d-flex align-items-center justify-content-center flex-column" style="height:145px;">
                                            <i class="fas fa-file-invoice fa-2x mb-1 text-secondary opacity-40"></i>
                                            <span style="font-size:0.68rem;">Tidak Ada</span>
                                        </div>
                                    @endif
                                </div>
                            </div>

                            <!-- 4. Surat Keterangan Sehat Pemain -->
                            <div class="col-6">
                                <div class="doc-box doc-box-doc">
                                    <div class="doc-box-tag">
                                        <span><i class="fas fa-heartbeat me-1 text-danger"></i> Surat Sehat</span>
                                        <span class="text-muted" style="font-size:0.58rem;">Kesehatan</span>
                                    </div>
                                    @if($pem->surat_sehat_path)
                                        <div class="doc-box-image-wrap" onclick="previewImage('{{ Storage::url($pem->surat_sehat_path) }}', 'Surat Keterangan Sehat - {{ $pem->full_name }}', 'surat_sehat_{{ Str::slug($pem->full_name) }}.{{ pathinfo($pem->surat_sehat_path, PATHINFO_EXTENSION) }}')">
                                            <img src="{{ Storage::url($pem->surat_sehat_path) }}" alt="Surat Keterangan Sehat">
                                            <div class="doc-zoom-overlay"><i class="fas fa-search-plus"></i> Zoom</div>
                                        </div>
                                        <div class="doc-box-bottom">
                                            <span class="text-muted" style="font-size:0.63rem;">Surat Sehat</span>
                                            <a href="{{ Storage::url($pem->surat_sehat_path) }}" download="surat_sehat_{{ Str::slug($pem->full_name) }}.{{ pathinfo($pem->surat_sehat_path, PATHINFO_EXTENSION) }}" class="btn-action-mini" title="Unduh Surat Keterangan Sehat">
                                                <i class="fas fa-download"></i>
                                            </a>
                                        </div>
                                    @else
                                        <div class="py-4 text-muted small bg-light d-flex align-items-center justify-content-center flex-column" style="height:145px;">
                                            <i class="fas fa-heartbeat fa-2x mb-1 text-secondary opacity-40"></i>
                                            <span style="font-size:0.68rem;">Tidak Ada</span>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            @endforeach

            <!-- PENGESAHAN DOKUMEN -->
            <div class="mt-5 pt-4 border-top" style="page-break-inside: avoid; position:relative; z-index:1;">
                <div class="row text-center g-4 justify-content-between">
                    <div class="col-5">
                        <p class="text-muted small mb-1">Penanggung Jawab Kontingen,</p>
                        <p class="text-dark fw-bold small mb-4">Official / Manager Tim</p>
                        <div style="height: 55px;"></div>
                        <p class="fw-bold mb-0 text-dark oswald" style="font-size:1.05rem;"><u>{{ $registration->user->name }}</u></p>
                        <small class="text-muted d-block">Kontingen Kec. {{ $registration->nama_tim }}</small>
                    </div>
                    <div class="col-5">
                        <p class="text-muted small mb-1">Bandung, {{ now()->translatedFormat('d F Y') }}</p>
                        <p class="text-dark fw-bold small mb-4">Mengetahui / Mengesahkan,</p>
                        <div style="height: 55px;"></div>
                        <p class="fw-bold mb-0 text-dark oswald" style="font-size:1.05rem;"><u>KETUA KARANG TARUNA</u></p>
                        <small class="text-muted d-block">Karang Taruna Kota Bandung</small>
                    </div>
                </div>
            </div>

            <!-- FOOTER LEGALITAS -->
            <div class="mt-4 pt-3 text-center border-top text-muted" style="font-size:0.68rem; letter-spacing:0.5px;">
                Dokumen akreditasi ini merupakan arsip legalitas resmi turnamen sepak bola KARTA.BDG Cup 2026 yang telah melalui tahapan verifikasi data fisik & administrasi digital oleh Panitia Pelaksana Karang Taruna Kota Bandung.
            </div>

        </div>
    </div>

    <!-- MODAL LIGHTBOX PREVIEW DOKUMEN -->
    <div class="modal fade no-print" id="imagePreviewModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-xl">
            <div class="modal-content border-0 rounded-4 overflow-hidden shadow-lg">
                <div class="modal-header text-white py-3" style="background:#0f172a;">
                    <div class="d-flex align-items-center gap-2">
                        <i id="previewModalIcon" class="fas fa-search-plus text-warning"></i>
                        <h6 class="modal-title oswald fw-bold mb-0" id="previewModalTitle" style="letter-spacing:0.5px;">Preview Dokumen</h6>
                    </div>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body text-center p-0 bg-light" style="min-height:420px; display:flex; align-items:center; justify-content:center;">
                    <!-- Preview Gambar -->
                    <div id="previewImageWrapper" class="p-3 w-100 text-center" style="display:none;">
                        <img id="previewModalImg" src="" alt="Dokumen" style="max-height:75vh; max-width:100%; object-fit:contain; border-radius:10px; box-shadow:0 8px 30px rgba(0,0,0,0.18);">
                    </div>
                    <!-- Preview PDF -->
                    <div id="previewPdfWrapper" class="w-100" style="display:none; height:78vh;">
                        <iframe id="previewModalIframe" src="" style="width:100%; height:78vh; border:none;" title="Preview PDF"></iframe>
                    </div>
                </div>
                <div class="modal-footer bg-white d-flex justify-content-between p-3">
                    <button type="button" class="btn btn-outline-secondary btn-sm px-3 fw-semibold" data-bs-dismiss="modal">Tutup</button>
                    <div class="d-flex gap-2">
                        <a id="previewModalNewTab" href="#" target="_blank" class="btn btn-outline-primary btn-sm px-3 fw-semibold">
                            <i class="fas fa-external-link-alt me-1"></i> Buka di Tab Baru
                        </a>
                        <a id="previewModalDownload" href="#" download class="btn btn-success btn-sm px-4 fw-bold shadow-sm">
                            <i class="fas fa-download me-1"></i> Unduh Berkas
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function handleKembali(e, fallbackUrl) {
            if (window.opener) {
                try {
                    window.close();
                } catch(err) {}
            }
            window.location.href = fallbackUrl;
        }

        function isPdfUrl(url) {
            if (!url) return false;
            return url.toLowerCase().split('?')[0].endsWith('.pdf');
        }

        function previewImage(url, title, filename) {
            document.getElementById('previewModalTitle').textContent = title;
            
            var imgWrapper = document.getElementById('previewImageWrapper');
            var pdfWrapper = document.getElementById('previewPdfWrapper');
            var modalImg = document.getElementById('previewModalImg');
            var modalIframe = document.getElementById('previewModalIframe');
            var modalIcon = document.getElementById('previewModalIcon');
            
            if (isPdfUrl(url)) {
                imgWrapper.style.display = 'none';
                modalImg.src = '';
                pdfWrapper.style.display = 'block';
                modalIframe.src = url;
                if (modalIcon) modalIcon.className = 'fas fa-file-pdf text-danger';
            } else {
                pdfWrapper.style.display = 'none';
                modalIframe.src = '';
                imgWrapper.style.display = 'block';
                modalImg.src = url;
                if (modalIcon) modalIcon.className = 'fas fa-image text-warning';
            }
            
            var dl = document.getElementById('previewModalDownload');
            dl.href = url;
            if (filename) {
                dl.setAttribute('download', filename);
            } else {
                dl.removeAttribute('download');
            }
            
            document.getElementById('previewModalNewTab').href = url;
            
            var modal = new bootstrap.Modal(document.getElementById('imagePreviewModal'));
            modal.show();
        }

        document.getElementById('imagePreviewModal').addEventListener('hidden.bs.modal', function () {
            var modalImg = document.getElementById('previewModalImg');
            var modalIframe = document.getElementById('previewModalIframe');
            if (modalImg) modalImg.src = '';
            if (modalIframe) modalIframe.src = '';
        });
    </script>
</body>
</html>
