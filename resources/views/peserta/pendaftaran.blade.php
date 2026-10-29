@extends('layouts.peserta')
@section('page-title', 'Pendaftaran Tim')

@section('content')
@php
    $reg = $reg ?? $registration ?? auth()->user()->registration;
    $statusInfo = [
        null => ['color'=>'secondary','icon'=>'fa-circle','label'=>'Belum Mendaftar','desc'=>'Anda belum mendaftarkan tim. Daftarkan sekarang!'],
        'Menunggu Verifikasi' => ['color'=>'warning','icon'=>'fa-hourglass-half','label'=>'Menunggu Verifikasi','desc'=>'Data tim sedang diperiksa oleh panitia. Verifikasi pemain berlangsung tgl 2 Nov s/d 10 Nov 2026.'],
        'Draf' => ['color'=>'info','icon'=>'fa-pen','label'=>'Draf','desc'=>'Data tersimpan sebagai draf. Simpan tiap pemain atau official, lalu ajukan ke admin setelah lengkap.'],
        'Terverifikasi' => ['color'=>'success','icon'=>'fa-check-circle','label'=>'Terverifikasi','desc'=>'Selamat! Tim Anda resmi terdaftar.'],
        'Ditolak' => ['color'=>'danger','icon'=>'fa-times-circle','label'=>'Ditolak','desc'=>'Pendaftaran tim ditolak. Silakan perbaiki dan kirim ulang.'],
    ];
    $status = $reg ? $reg->status : null;
    $isLocked = ($reg && in_array($reg->status, ['Menunggu Verifikasi', 'Terverifikasi'], true)) || $registrationClosed;
    $info = $statusInfo[$status] ?? $statusInfo[null];
    $pemainList = old('pemain', $reg ? $reg->members()->where('tipe', 'pemain')->get()->toArray() : [[]]);
    $officialList = old('official', $reg ? $reg->members()->where('tipe', 'official')->get()->toArray() : []);
@endphp

<div class="mb-4 d-flex justify-content-between align-items-center flex-wrap gap-2">
    <div>
        <h4 class="oswald fw-bold mb-0" style="color:#0a1628;">Pendaftaran Tim</h4>
        <p class="text-muted small mb-0">Lengkapi data tim, pemain (Maks. 25), dan official (Maks. 5) untuk mengikuti turnamen</p>
    </div>
    @if($reg)
    <div>
        <a href="{{ route('peserta.pendaftaran.cetak') }}" target="_blank" class="btn btn-outline-primary btn-sm fw-bold">
            <i class="fas fa-print me-1"></i> Cetak Berkas / Export PDF
        </a>
    </div>
    @endif
</div>

@if($registrationClosed)
<div class="alert alert-danger d-flex align-items-center gap-2 mb-4" role="alert">
    <i class="fas fa-lock fa-lg"></i>
    <div><strong>Pendaftaran telah ditutup.</strong>
        @if($settings->registration_closes_at)
            Pendaftaran ditutup mulai {{ $settings->registration_closes_at->format('d/m/Y') }}.
        @else
            Saat ini pendaftaran tidak dibuka oleh panitia.
        @endif
        Data yang sudah tersimpan tetap dapat dilihat, tetapi tidak dapat diubah atau diajukan.
    </div>
</div>
@endif

{{-- STATUS CARD --}}
<div class="card border-0 card-karta mb-4" style="border-left:5px solid var(--bs-{{ $info['color'] }})!important;">
    <div class="card-body p-4">
        <div class="d-flex align-items-center justify-content-between flex-wrap gap-3">
            <div class="d-flex align-items-center gap-3">
                <div style="width:56px;height:56px;border-radius:50%;background:var(--bs-{{ $info['color'] }}-bg, #f8fafc);display:flex;align-items:center;justify-content:center;flex-shrink:0;font-size:1.5rem;" class="text-{{ $info['color'] }}">
                    <i class="fas {{ $info['icon'] }}"></i>
                </div>
                <div>
                    <h5 class="fw-bold mb-1" style="color:#0a1628;">Status: {{ $info['label'] }}</h5>
                    <p class="text-muted mb-0 small">
                        @if($reg && $reg->status === 'Menunggu Verifikasi')
                            Data sudah diajukan dan sedang diperiksa panitia.
                        @elseif($reg && $reg->status === 'Terverifikasi')
                            Pendaftaran tim Anda sudah terverifikasi dan resmi terkunci oleh panitia. Hubungi panitia jika perlu perubahan data.
                        @else
                            {{ $info['desc'] }}
                        @endif
                    </p>
                    @if($reg && $reg->status === 'Ditolak' && $reg->rejection_reason)
                    <div class="mt-2 p-2 rounded-3" style="background:#fff1f2;border:1px solid #fecaca;">
                        <span class="text-danger small"><strong>Alasan:</strong> {{ $reg->rejection_reason }}</span>
                    </div>
                    @endif
                </div>
            </div>

            {{-- Ringkasan Cepat --}}
            <div class="d-flex align-items-center gap-2">
                <div class="text-center p-2 px-3 rounded-3 bg-light border">
                    <div class="text-muted" style="font-size:0.7rem;text-transform:uppercase;letter-spacing:0.05em;font-weight:600;">Peserta</div>
                    <div class="fs-5 fw-bold text-primary"><span class="display-pemain-count">{{ count($pemainList) }}</span>/25</div>
                </div>
                <div class="text-center p-2 px-3 rounded-3 bg-light border">
                    <div class="text-muted" style="font-size:0.7rem;text-transform:uppercase;letter-spacing:0.05em;font-weight:600;">Official</div>
                    <div class="fs-5 fw-bold text-info"><span class="display-official-count">{{ count($officialList) }}</span>/5</div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- KARTU PROGRES DATA SAAT INI --}}
<div class="card border-0 card-karta mb-4">
    <div class="card-body p-4">
        <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
            <div>
                <h6 class="fw-bold mb-1" style="color:#0a1628;">
                    <i class="fas fa-tasks text-success me-2"></i> Data Saat Ini
                </h6>
                <p class="text-muted small mb-0">Pantau progres pengisian data peserta dan official tim Anda</p>
            </div>
            <div id="badgeMaksimalPill" class="{{ (count($pemainList) >= 25 && count($officialList) >= 5) ? '' : 'd-none' }}">
                <span class="badge bg-success-subtle text-success border border-success-subtle px-3 py-2 rounded-pill fw-bold">
                    <i class="fas fa-check-circle me-1"></i> 25 / 25 Peserta &bull; 5 / 5 Official
                </span>
            </div>
        </div>

        <div class="row g-3">
            {{-- Progres Peserta --}}
            <div class="col-md-6">
                <div class="p-3 rounded-3 bg-light border">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <span class="fw-semibold text-dark small">
                            <i class="fas fa-users text-primary me-1"></i> Peserta
                        </span>
                        <span class="fw-bold fs-6 text-primary">
                            <span class="display-pemain-count">{{ count($pemainList) }}</span> / 25
                        </span>
                    </div>
                    <div class="progress" style="height: 12px; border-radius: 6px; background-color: #e2e8f0;">
                        <div id="progressBarPemain" class="progress-bar progress-bar-striped progress-bar-animated bg-primary" 
                             role="progressbar" 
                             style="width: {{ min(100, (count($pemainList) / 25) * 100) }}%; transition: width 0.3s ease;" 
                             aria-valuenow="{{ count($pemainList) }}" aria-valuemin="0" aria-valuemax="25">
                        </div>
                    </div>
                    <div class="d-flex justify-content-between mt-2 small text-muted" style="font-size: 0.75rem;">
                        <span>Min. 1 Peserta</span>
                        <span id="textStatusPemain" class="fw-semibold text-primary">
                            {{ count($pemainList) >= 25 ? 'Maksimal 25 Peserta' : 'Sisa kuota: ' . (25 - count($pemainList)) . ' peserta' }}
                        </span>
                        <span>Maks. 25</span>
                    </div>
                </div>
            </div>

            {{-- Progres Official --}}
            <div class="col-md-6">
                <div class="p-3 rounded-3 bg-light border">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <span class="fw-semibold text-dark small">
                            <i class="fas fa-user-tie text-info me-1"></i> Official
                        </span>
                        <span class="fw-bold fs-6 text-info">
                            <span class="display-official-count">{{ count($officialList) }}</span> / 5
                        </span>
                    </div>
                    <div class="progress" style="height: 12px; border-radius: 6px; background-color: #e2e8f0;">
                        <div id="progressBarOfficial" class="progress-bar progress-bar-striped progress-bar-animated bg-info" 
                             role="progressbar" 
                             style="width: {{ min(100, (count($officialList) / 5) * 100) }}%; transition: width 0.3s ease;" 
                             aria-valuenow="{{ count($officialList) }}" aria-valuemin="0" aria-valuemax="5">
                        </div>
                    </div>
                    <div class="d-flex justify-content-between mt-2 small text-muted" style="font-size: 0.75rem;">
                        <span>Opsional</span>
                        <span id="textStatusOfficial" class="fw-semibold text-info">
                            {{ count($officialList) >= 5 ? 'Maksimal 5 Official' : 'Sisa kuota: ' . (5 - count($officialList)) . ' official' }}
                        </span>
                        <span>Maks. 5</span>
                    </div>
                </div>
            </div>
        </div>

        {{-- Banner jika sudah maksimal --}}
        <div id="bannerMaksimal" class="alert alert-success d-flex align-items-center gap-2 mb-0 mt-3 py-2 px-3 fw-bold small {{ (count($pemainList) >= 25 && count($officialList) >= 5) ? '' : 'd-none' }}">
            <i class="fas fa-check-circle fa-lg"></i>
            <span>Data peserta dan official sudah maksimal.</span>
        </div>
    </div>
</div>

{{-- FORMULIR PENDAFTARAN --}}
@if(!$isLocked)
<div class="alert alert-info small mb-3">
    Simpan data tim terlebih dahulu. Setelah itu, simpan setiap pemain dan official secara terpisah; berkas dikirim satu per satu. Setelah semua lengkap, pilih <strong>Ajukan ke Admin</strong>.
</div>
@endif
<form method="POST" action="{{ route('peserta.pendaftaran.submit') }}" enctype="multipart/form-data" id="formPendaftaran">
    @csrf

    @if($errors->any())
    <div class="alert alert-danger py-3 px-4 mb-4 shadow-sm border-0 rounded-3" id="validationErrorsBox" style="background:#fee2e2;border-left:5px solid #ef4444!important;">
        <div class="d-flex align-items-center gap-2 fw-bold text-danger mb-2">
            <i class="fas fa-exclamation-triangle fa-lg"></i>
            <span>Ada data yang belum sesuai (Formulir belum tersimpan):</span>
        </div>
        <ul class="mb-0 ps-3 text-danger small">
            @foreach(array_unique($errors->all()) as $e)
                <li>{{ $e }}</li>
            @endforeach
        </ul>
        <div class="mt-2 text-muted small fst-italic">
            * Catatan: Karena validasi belum terpenuhi, data belum tersimpan ke server. Silakan lengkapi / perbaiki data dan pilih file kembali sebelum mengirim ulang.
        </div>
    </div>
    @endif

    {{-- DATA TIM --}}
    <div class="card border-0 card-karta mb-4">
        <div class="card-header border-0 bg-white py-3">
            <h6 class="fw-bold mb-0" style="color:#0a1628;"><i class="fas fa-shield-alt text-success me-2"></i> Data Tim</h6>
        </div>
        <div class="card-body p-4">
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label fw-semibold small">Nama Tim / Kecamatan <span class="text-danger">*</span></label>
                    @php
                        $kecamatanList = [
                            'Andir', 'Antapani', 'Arcamanik', 'Astana Anyar', 'Babakan Ciparay',
                            'Bandung Kidul', 'Bandung Kulon', 'Bandung Wetan', 'Batununggal', 'Bojongloa Kaler',
                            'Bojongloa Kidul', 'Buahbatu', 'Cibeunying Kaler', 'Cibeunying Kidul', 'Cibiru',
                            'Cicendo', 'Cidadap', 'Cinambo', 'Coblong', 'Gedebage',
                            'Kiaracondong', 'Lengkong', 'Mandalajati', 'Panyileukan', 'Rancasari',
                            'Regol', 'Sukajadi', 'Sukasari', 'Sumur Bandung', 'Ujungberung'
                        ];
                        $selectedNamaTim = old('nama_tim', $reg->nama_tim ?? '');
                    @endphp
                    @if($reg && $reg->status == 'Terverifikasi')
                        <input type="text" name="nama_tim" class="form-control" value="{{ $selectedNamaTim }}" readonly>
                    @else
                        <select name="nama_tim" class="form-select" required>
                            <option value="">-- Pilih Kecamatan / Nama Tim --</option>
                            @foreach($kecamatanList as $kec)
                                <option value="{{ $kec }}" {{ $selectedNamaTim == $kec ? 'selected' : '' }}>{{ $kec }}</option>
                            @endforeach
                        </select>
                    @endif
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-semibold small">Surat Rekomendasi (PDF/Gambar, Max 2MB) <span class="text-danger">*</span></label>
                    <input type="file" name="surat_rekomendasi" class="form-control" accept=".pdf,.jpg,.jpeg,.png" {{ (!$reg || !$reg->surat_rekomendasi_path) ? 'required' : '' }} {{ $isLocked ? 'disabled' : '' }}>
                    @if($reg && $reg->surat_rekomendasi_path)
                    <div class="mt-2 d-flex align-items-center gap-2">
                        <a href="{{ Storage::url($reg->surat_rekomendasi_path) }}" target="_blank" class="badge bg-light text-primary border text-decoration-none py-1 px-2 d-inline-flex align-items-center shadow-sm">
                            <i class="fas fa-file-pdf me-1"></i> Lihat Surat Rekomendasi
                        </a>
                        <span class="text-success small fw-semibold"><i class="fas fa-check-circle"></i> Terunggah</span>
                    </div>
                    <small class="text-muted d-block mt-1" style="font-size:0.75rem;">Pilih file baru jika ingin mengganti.</small>
                    @endif
                    @if(!$isLocked)
                    <button type="button" class="btn btn-sm btn-outline-success mt-3" id="btnSaveTeam"><i class="fas fa-save me-1"></i> Simpan Data Tim</button>
                    <span class="small text-muted ms-2" id="teamSaveState"></span>
                    @endif
                </div>
            </div>
        </div>
    </div>

    {{-- DATA PEMAIN --}}
    <div class="card border-0 card-karta mb-4">
        <div class="card-header border-0 bg-white py-3 d-flex justify-content-between align-items-center flex-wrap gap-2">
            <div class="d-flex align-items-center gap-2">
                <h6 class="fw-bold mb-0" style="color:#0a1628;"><i class="fas fa-users text-primary me-2"></i> Data Pemain (Maks. 25)</h6>
                <span class="badge bg-primary-subtle text-primary border border-primary-subtle px-2 py-1" style="font-size:0.78rem;">
                    Peserta: <strong class="display-pemain-count">{{ count($pemainList) }}</strong>/25
                </span>
            </div>
            @if(!$isLocked)
            <button type="button" class="btn btn-sm btn-outline-primary fw-semibold" id="btnTambahPemain" {{ count($pemainList) >= 25 ? 'disabled' : '' }}>
                <i class="fas {{ count($pemainList) >= 25 ? 'fa-check' : 'fa-plus' }} me-1"></i>
                {{ count($pemainList) >= 25 ? 'Tambah Pemain (Maksimal)' : '+ Tambah Pemain' }}
            </button>
            @endif
        </div>
        <div class="card-body p-4" id="containerPemain">
            @php $pemainList = old('pemain', $reg ? $reg->members()->where('tipe', 'pemain')->get()->toArray() : [[]]); @endphp
            
            @foreach($pemainList as $index => $pemain)
            @php
                $dbPemain = (isset($pemain['id']) && $reg) ? $reg->members->firstWhere('id', $pemain['id']) : null;
                $pPasfoto = $pemain['pasfoto_path'] ?? ($dbPemain->pasfoto_path ?? null);
                $pKtp = $pemain['ktp_path'] ?? ($dbPemain->ktp_path ?? null);
                $pKk = $pemain['kk_path'] ?? ($dbPemain->kk_path ?? null);
                $pSuratSehat = $pemain['surat_sehat_path'] ?? ($dbPemain->surat_sehat_path ?? null);
            @endphp
            <div class="pemain-item border p-3 mb-3 rounded position-relative">
                @if(!$isLocked)
                <button type="button" class="btn-close position-absolute top-0 end-0 m-2 btn-hapus-pemain" aria-label="Close"></button>
                @endif
                <h6 class="fw-bold small mb-3">Pemain #<span class="pemain-counter">{{ $index + 1 }}</span></h6>
                
                @if(isset($pemain['id']))
                <input type="hidden" name="pemain[{{ $index }}][id]" value="{{ $pemain['id'] }}">
                @endif
                
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label fw-semibold small">Nama Lengkap</label>
                        <input type="text" name="pemain[{{ $index }}][full_name]" class="form-control form-control-sm" value="{{ $pemain['full_name'] ?? '' }}" required {{ $isLocked ? 'readonly' : '' }}>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold small">NIK</label>
                        <input type="text" name="pemain[{{ $index }}][nik]" class="form-control form-control-sm" maxlength="16" minlength="16" pattern="[0-9]{16}" title="16 digit angka NIK" value="{{ $pemain['nik'] ?? '' }}" required {{ $isLocked ? 'readonly' : '' }}>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold small">Nomor KK</label>
                        <input type="text" name="pemain[{{ $index }}][nomor_kk]" class="form-control form-control-sm" maxlength="16" minlength="16" pattern="[0-9]{16}" placeholder="16 digit" value="{{ $pemain['nomor_kk'] ?? '' }}" required {{ $isLocked ? 'readonly' : '' }}>
                        <small class="text-muted d-block mt-1">Masukkan 16 digit nomor Kartu Keluarga.</small>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold small">Tanggal Lahir</label>
                        <input type="date" name="pemain[{{ $index }}][tanggal_lahir]" class="form-control form-control-sm" value="{{ isset($pemain['tanggal_lahir']) ? \Carbon\Carbon::parse($pemain['tanggal_lahir'])->format('Y-m-d') : '' }}" required {{ $isLocked ? 'readonly' : '' }}>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-semibold small">Posisi</label>
                        <select name="pemain[{{ $index }}][posisi]" class="form-select form-select-sm" required {{ $isLocked ? 'disabled' : '' }}>
                            <option value="">-- Posisi --</option>
                            @foreach(['Kiper','Bek Kiri','Bek Kanan','Bek Tengah','Gelandang Bertahan','Gelandang Tengah','Gelandang Serang','Sayap Kiri','Sayap Kanan','Striker'] as $p)
                            <option value="{{ $p }}" {{ ($pemain['posisi'] ?? '') === $p ? 'selected' : '' }}>{{ $p }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-semibold small">No Punggung</label>
                        <input type="number" name="pemain[{{ $index }}][nomor_punggung]" class="form-control form-control-sm" min="1" max="99" value="{{ $pemain['nomor_punggung'] ?? '' }}" required {{ $isLocked ? 'readonly' : '' }}>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-semibold small">Pas Foto (Max 2MB)</label>
                        @if($pPasfoto)
                            <div class="mb-1 d-flex align-items-center gap-2">
                                <a href="{{ Storage::url($pPasfoto) }}" target="_blank" class="badge bg-light text-primary border text-decoration-none py-1 px-2 d-inline-flex align-items-center shadow-sm">
                                    <i class="fas fa-eye me-1"></i> Lihat Pas Foto
                                </a>
                                <span class="text-success small fw-semibold"><i class="fas fa-check-circle"></i> Terunggah</span>
                            </div>
                        @endif
                        <input type="file" name="pemain[{{ $index }}][pasfoto]" class="form-control form-control-sm" accept="image/jpeg,image/png" {{ $pPasfoto ? '' : 'required' }} {{ $isLocked ? 'disabled' : '' }}>
                        @if($pPasfoto)
                            <small class="text-muted d-block" style="font-size:0.75rem;">Pilih file baru jika ingin mengganti.</small>
                        @endif
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-semibold small">Foto KTP (Max 2MB)</label>
                        @if($pKtp)
                            <div class="mb-1 d-flex align-items-center gap-2">
                                <a href="{{ Storage::url($pKtp) }}" target="_blank" class="badge bg-light text-primary border text-decoration-none py-1 px-2 d-inline-flex align-items-center shadow-sm">
                                    <i class="fas fa-id-card me-1"></i> Lihat Foto KTP
                                </a>
                                <span class="text-success small fw-semibold"><i class="fas fa-check-circle"></i> Terunggah</span>
                            </div>
                        @endif
                        <input type="file" name="pemain[{{ $index }}][ktp]" class="form-control form-control-sm" accept="image/jpeg,image/png,application/pdf" {{ $pKtp ? '' : 'required' }} {{ $isLocked ? 'disabled' : '' }}>
                        @if($pKtp)
                            <small class="text-muted d-block" style="font-size:0.75rem;">Pilih file baru jika ingin mengganti.</small>
                        @endif
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-semibold small">Foto KK (Max 2MB)</label>
                        @if($pKk)
                            <div class="mb-1 d-flex align-items-center gap-2">
                                <a href="{{ Storage::url($pKk) }}" target="_blank" class="badge bg-light text-primary border text-decoration-none py-1 px-2 d-inline-flex align-items-center shadow-sm">
                                    <i class="fas fa-file-alt me-1"></i> Lihat KK
                                </a>
                                <span class="text-success small fw-semibold"><i class="fas fa-check-circle"></i> Terunggah</span>
                            </div>
                        @endif
                        <input type="file" name="pemain[{{ $index }}][kk]" class="form-control form-control-sm" accept="image/jpeg,image/png,application/pdf" {{ $pKk ? '' : 'required' }} {{ $isLocked ? 'disabled' : '' }}>
                        @if($pKk)
                            <small class="text-muted d-block" style="font-size:0.75rem;">Pilih file baru jika ingin mengganti.</small>
                        @endif
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-semibold small">Surat Keterangan Sehat (Gambar, max 2MB)</label>
                        @if($pSuratSehat)
                            <div class="mb-1 d-flex align-items-center gap-2">
                                <a href="{{ Storage::url($pSuratSehat) }}" target="_blank" class="badge bg-light text-primary border text-decoration-none py-1 px-2 d-inline-flex align-items-center shadow-sm">
                                    <i class="fas fa-file-medical me-1"></i> Lihat Surat Sehat
                                </a>
                                <span class="text-success small fw-semibold"><i class="fas fa-check-circle"></i> Terunggah</span>
                            </div>
                        @endif
                        <input type="file" name="pemain[{{ $index }}][surat_sehat]" class="form-control form-control-sm" accept="image/jpeg,image/png,application/pdf" {{ $pSuratSehat ? '' : 'required' }} {{ $isLocked ? 'disabled' : '' }}>
                        @if($pSuratSehat)
                            <small class="text-muted d-block" style="font-size:0.75rem;">Pilih file baru jika ingin mengganti.</small>
                        @endif
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </div>

    {{-- DATA OFFICIAL --}}
    <div class="card border-0 card-karta mb-4">
        <div class="card-header border-0 bg-white py-3 d-flex justify-content-between align-items-center flex-wrap gap-2">
            <div class="d-flex align-items-center gap-2">
                <h6 class="fw-bold mb-0" style="color:#0a1628;"><i class="fas fa-user-tie text-info me-2"></i> Data Official (Maks. 5)</h6>
                <span class="badge bg-info-subtle text-info border border-info-subtle px-2 py-1" style="font-size:0.78rem;">
                    Official: <strong class="display-official-count">{{ count($officialList) }}</strong>/5
                </span>
            </div>
            @if(!$isLocked)
            <button type="button" class="btn btn-sm btn-outline-info fw-semibold" id="btnTambahOfficial" {{ count($officialList) >= 5 ? 'disabled' : '' }}>
                <i class="fas {{ count($officialList) >= 5 ? 'fa-check' : 'fa-plus' }} me-1"></i>
                {{ count($officialList) >= 5 ? 'Tambah Official (Maksimal)' : '+ Tambah Official' }}
            </button>
            @endif
        </div>
        <div class="card-body p-4" id="containerOfficial">
            @php $officialList = old('official', $reg ? $reg->members()->where('tipe', 'official')->get()->toArray() : []); @endphp
            
            @if(count($officialList) == 0 && !$isLocked)
                <p class="text-muted small text-center" id="emptyOfficialMsg">Belum ada official. Klik "Tambah Official" jika ada.</p>
            @endif
            
            @foreach($officialList as $index => $official)
            @php
                $dbOfficial = (isset($official['id']) && $reg) ? $reg->members->firstWhere('id', $official['id']) : null;
                $pasfotoPath = $official['pasfoto_path'] ?? ($dbOfficial->pasfoto_path ?? null);
                $ktpPath = $official['ktp_path'] ?? ($dbOfficial->ktp_path ?? null);
            @endphp
            <div class="official-item border p-3 mb-3 rounded position-relative">
                @if(!$isLocked)
                <button type="button" class="btn-close position-absolute top-0 end-0 m-2 btn-hapus-official" aria-label="Close"></button>
                @endif
                <h6 class="fw-bold small mb-3">Official #<span class="official-counter">{{ $index + 1 }}</span></h6>
                
                @if(isset($official['id']))
                <input type="hidden" name="official[{{ $index }}][id]" value="{{ $official['id'] }}">
                @endif
                
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label fw-semibold small">Nama Lengkap</label>
                        <input type="text" name="official[{{ $index }}][full_name]" class="form-control form-control-sm" value="{{ $official['full_name'] ?? '' }}" required {{ $isLocked ? 'readonly' : '' }}>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label fw-semibold small">NIK</label>
                        <input type="text" name="official[{{ $index }}][nik]" class="form-control form-control-sm" maxlength="16" minlength="16" pattern="[0-9]{16}" title="16 digit angka NIK" value="{{ $official['nik'] ?? '' }}" required {{ $isLocked ? 'readonly' : '' }}>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label fw-semibold small">Jabatan</label>
                        <select name="official[{{ $index }}][jabatan]" class="form-select form-select-sm" required {{ $isLocked ? 'disabled' : '' }}>
                            <option value="">-- Pilih --</option>
                            @foreach(['Manajer','Pelatih Kepala','Asisten Pelatih','Pelatih Kiper','Medis','Kitman','Fisioterapis','Ofisial Lainnya'] as $j)
                            <option value="{{ $j }}" {{ ($official['jabatan'] ?? '') === $j ? 'selected' : '' }}>{{ $j }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-semibold small">Tanggal Lahir</label>
                        <input type="date" name="official[{{ $index }}][tanggal_lahir]" class="form-control form-control-sm" value="{{ isset($official['tanggal_lahir']) ? \Carbon\Carbon::parse($official['tanggal_lahir'])->format('Y-m-d') : '' }}" required {{ $isLocked ? 'readonly' : '' }}>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-semibold small">Pas Foto (Max 2MB)</label>
                        @if($pasfotoPath)
                            <div class="mb-1 d-flex align-items-center gap-2">
                                <a href="{{ Storage::url($pasfotoPath) }}" target="_blank" class="badge bg-light text-primary border text-decoration-none py-1 px-2 d-inline-flex align-items-center shadow-sm">
                                    <i class="fas fa-eye me-1"></i> Lihat Pas Foto
                                </a>
                                <span class="text-success small fw-semibold"><i class="fas fa-check-circle"></i> Terunggah</span>
                            </div>
                        @endif
                        <input type="file" name="official[{{ $index }}][pasfoto]" class="form-control form-control-sm" accept="image/jpeg,image/png" {{ $pasfotoPath ? '' : 'required' }} {{ $isLocked ? 'disabled' : '' }}>
                        @if($pasfotoPath)
                            <small class="text-muted d-block" style="font-size:0.75rem;">Pilih file baru jika ingin mengganti.</small>
                        @endif
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-semibold small">Foto KTP (Max 2MB)</label>
                        @if($ktpPath)
                            <div class="mb-1 d-flex align-items-center gap-2">
                                <a href="{{ Storage::url($ktpPath) }}" target="_blank" class="badge bg-light text-primary border text-decoration-none py-1 px-2 d-inline-flex align-items-center shadow-sm">
                                    <i class="fas fa-id-card me-1"></i> Lihat Foto KTP
                                </a>
                                <span class="text-success small fw-semibold"><i class="fas fa-check-circle"></i> Terunggah</span>
                            </div>
                        @endif
                        <input type="file" name="official[{{ $index }}][ktp]" class="form-control form-control-sm" accept="image/jpeg,image/png,application/pdf" {{ $ktpPath ? '' : 'required' }} {{ $isLocked ? 'disabled' : '' }}>
                        @if($ktpPath)
                            <small class="text-muted d-block" style="font-size:0.75rem;">Pilih file baru jika ingin mengganti.</small>
                        @endif
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </div>

    <div class="d-flex gap-2 justify-content-end">
        @if($isLocked)
        <div class="alert alert-success mb-0 flex-grow-1 d-flex align-items-center gap-2 py-2">
            <i class="fas fa-lock text-success"></i>
            <span class="small"><strong>Pendaftaran terkunci.</strong>
                @if($registrationClosed) Pengisian dan pengajuan sudah ditutup.
                @elseif($reg->status === 'Menunggu Verifikasi') Data sedang diperiksa admin.
                @else Hubungi panitia jika perlu perubahan data. @endif
            </span>
        </div>
        @else
        <button type="button" class="btn btn-green fw-bold btn-lg px-5" id="btnSubmit">
            <i class="fas fa-paper-plane me-2"></i> Ajukan ke Admin
        </button>
        @endif
    </div>
</form>

{{-- TEMPLATE UNTUK JS (Disembunyikan) --}}
<div id="templatePemain" class="d-none">
    <div class="pemain-item border p-3 mb-3 rounded position-relative">
        <button type="button" class="btn-close position-absolute top-0 end-0 m-2 btn-hapus-pemain" aria-label="Close"></button>
        <h6 class="fw-bold small mb-3">Pemain #<span class="pemain-counter">X</span></h6>
        <div class="row g-3">
            <div class="col-md-6">
                <label class="form-label fw-semibold small">Nama Lengkap</label>
                <input type="text" name="pemain[__INDEX__][full_name]" class="form-control form-control-sm" required>
            </div>
            <div class="col-md-6">
                <label class="form-label fw-semibold small">NIK</label>
                <input type="text" name="pemain[__INDEX__][nik]" class="form-control form-control-sm" maxlength="16" required>
            </div>
            <div class="col-md-6">
                <label class="form-label fw-semibold small">Nomor KK</label>
                <input type="text" name="pemain[__INDEX__][nomor_kk]" class="form-control form-control-sm" maxlength="16" placeholder="16 digit" required>
                <small class="text-muted d-block mt-1">Masukkan 16 digit nomor Kartu Keluarga.</small>
            </div>
            <div class="col-md-6">
                <label class="form-label fw-semibold small">Tanggal Lahir</label>
                <input type="date" name="pemain[__INDEX__][tanggal_lahir]" class="form-control form-control-sm" required>
            </div>
            <div class="col-md-4">
                <label class="form-label fw-semibold small">Posisi</label>
                <select name="pemain[__INDEX__][posisi]" class="form-select form-select-sm" required>
                    <option value="">-- Posisi --</option>
                    @foreach(['Kiper','Bek Kiri','Bek Kanan','Bek Tengah','Gelandang Bertahan','Gelandang Tengah','Gelandang Serang','Sayap Kiri','Sayap Kanan','Striker'] as $p)
                    <option value="{{ $p }}">{{ $p }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-4">
                <label class="form-label fw-semibold small">No Punggung</label>
                <input type="number" name="pemain[__INDEX__][nomor_punggung]" class="form-control form-control-sm" min="1" max="99" required>
            </div>
            <div class="col-md-4">
                <label class="form-label fw-semibold small">Pas Foto (Max 2MB)</label>
                <input type="file" name="pemain[__INDEX__][pasfoto]" class="form-control form-control-sm" accept="image/jpeg,image/png" required>
            </div>
            <div class="col-md-4">
                <label class="form-label fw-semibold small">Foto KTP (Max 2MB)</label>
                <input type="file" name="pemain[__INDEX__][ktp]" class="form-control form-control-sm" accept="image/jpeg,image/png,application/pdf" required>
            </div>
            <div class="col-md-4">
                <label class="form-label fw-semibold small">Foto KK (Max 2MB)</label>
                <input type="file" name="pemain[__INDEX__][kk]" class="form-control form-control-sm" accept="image/jpeg,image/png,application/pdf" required>
            </div>
            <div class="col-md-4">
                <label class="form-label fw-semibold small">Surat Keterangan Sehat (Gambar, max 2MB)</label>
                <input type="file" name="pemain[__INDEX__][surat_sehat]" class="form-control form-control-sm" accept="image/jpeg,image/png,application/pdf" required>
            </div>
        </div>
    </div>
</div>

<div id="templateOfficial" class="d-none">
    <div class="official-item border p-3 mb-3 rounded position-relative">
        <button type="button" class="btn-close position-absolute top-0 end-0 m-2 btn-hapus-official" aria-label="Close"></button>
        <h6 class="fw-bold small mb-3">Official #<span class="official-counter">X</span></h6>
        <div class="row g-3">
            <div class="col-md-6">
                <label class="form-label fw-semibold small">Nama Lengkap</label>
                <input type="text" name="official[__INDEX__][full_name]" class="form-control form-control-sm" required>
            </div>
            <div class="col-md-3">
                <label class="form-label fw-semibold small">NIK</label>
                <input type="text" name="official[__INDEX__][nik]" class="form-control form-control-sm" maxlength="16" minlength="16" pattern="[0-9]{16}" title="16 digit angka NIK" required>
            </div>
            <div class="col-md-3">
                <label class="form-label fw-semibold small">Jabatan</label>
                <select name="official[__INDEX__][jabatan]" class="form-select form-select-sm" required>
                    <option value="">-- Pilih --</option>
                    @foreach(['Manajer','Pelatih Kepala','Asisten Pelatih','Pelatih Kiper','Medis','Kitman','Fisioterapis','Ofisial Lainnya'] as $j)
                    <option value="{{ $j }}">{{ $j }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-4">
                <label class="form-label fw-semibold small">Tanggal Lahir</label>
                <input type="date" name="official[__INDEX__][tanggal_lahir]" class="form-control form-control-sm" required>
            </div>
            <div class="col-md-4">
                <label class="form-label fw-semibold small">Pas Foto (Max 2MB)</label>
                <input type="file" name="official[__INDEX__][pasfoto]" class="form-control form-control-sm file-input-field" accept="image/jpeg,image/png" required>
            </div>
            <div class="col-md-4">
                <label class="form-label fw-semibold small">Foto KTP (Max 2MB)</label>
                <input type="file" name="official[__INDEX__][ktp]" class="form-control form-control-sm file-input-field" accept="image/jpeg,image/png,application/pdf" required>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const MAX_PEMAIN = 25;
    const MAX_OFFICIAL = 5;

    // Scroll ke error jika ada kesalahan validasi
    const validationBox = document.getElementById('validationErrorsBox');
    if (validationBox) {
        validationBox.scrollIntoView({ behavior: 'smooth', block: 'center' });
    }

    let pemainCount = document.querySelectorAll('#containerPemain .pemain-item').length;
    let officialCount = document.querySelectorAll('#containerOfficial .official-item').length;
    let pemainIndex = pemainCount;
    let officialIndex = officialCount;

    function updateCounters() {
        let players = document.querySelectorAll('#containerPemain .pemain-item');
        pemainCount = players.length;
        players.forEach((el, idx) => {
            const counter = el.querySelector('.pemain-counter');
            if (counter) counter.innerText = idx + 1;
        });

        let officials = document.querySelectorAll('#containerOfficial .official-item');
        officialCount = officials.length;
        officials.forEach((el, idx) => {
            const counter = el.querySelector('.official-counter');
            if (counter) counter.innerText = idx + 1;
        });

        // Update semua elemen jumlah data tersimpan / saat ini
        document.querySelectorAll('.display-pemain-count').forEach(el => el.innerText = pemainCount);
        document.querySelectorAll('.display-official-count').forEach(el => el.innerText = officialCount);

        // Update progress bar & label Pemain
        const pBarPemain = document.getElementById('progressBarPemain');
        if (pBarPemain) {
            const pctPemain = Math.min(100, Math.round((pemainCount / MAX_PEMAIN) * 100));
            pBarPemain.style.width = pctPemain + '%';
            pBarPemain.setAttribute('aria-valuenow', pemainCount);
        }
        const txtPemain = document.getElementById('textStatusPemain');
        if (txtPemain) {
            txtPemain.innerText = pemainCount >= MAX_PEMAIN ? 'Maksimal 25 Peserta' : 'Sisa kuota: ' + (MAX_PEMAIN - pemainCount) + ' peserta';
        }

        // Update progress bar & label Official
        const pBarOfficial = document.getElementById('progressBarOfficial');
        if (pBarOfficial) {
            const pctOfficial = Math.min(100, Math.round((officialCount / MAX_OFFICIAL) * 100));
            pBarOfficial.style.width = pctOfficial + '%';
            pBarOfficial.setAttribute('aria-valuenow', officialCount);
        }
        const txtOfficial = document.getElementById('textStatusOfficial');
        if (txtOfficial) {
            txtOfficial.innerText = officialCount >= MAX_OFFICIAL ? 'Maksimal 5 Official' : 'Sisa kuota: ' + (MAX_OFFICIAL - officialCount) + ' official';
        }

        // Toggle banner dan badge jika kedua data sudah maksimal
        const bannerMaksimal = document.getElementById('bannerMaksimal');
        const badgeMaksimalPill = document.getElementById('badgeMaksimalPill');
        if (pemainCount >= MAX_PEMAIN && officialCount >= MAX_OFFICIAL) {
            if (bannerMaksimal) bannerMaksimal.classList.remove('d-none');
            if (badgeMaksimalPill) badgeMaksimalPill.classList.remove('d-none');
        } else {
            if (bannerMaksimal) bannerMaksimal.classList.add('d-none');
            if (badgeMaksimalPill) badgeMaksimalPill.classList.add('d-none');
        }

        // Update tombol Tambah Pemain (dinonaktifkan jika sudah 25)
        const btnPemain = document.getElementById('btnTambahPemain');
        if (btnPemain) {
            if (pemainCount >= MAX_PEMAIN) {
                btnPemain.disabled = true;
                btnPemain.classList.add('disabled');
                btnPemain.innerHTML = '<i class="fas fa-check me-1"></i> Tambah Pemain (Maksimal)';
            } else {
                btnPemain.disabled = false;
                btnPemain.classList.remove('disabled');
                btnPemain.innerHTML = '<i class="fas fa-plus me-1"></i> + Tambah Pemain';
            }
        }

        // Update tombol Tambah Official (dinonaktifkan jika sudah 5)
        const btnOfficial = document.getElementById('btnTambahOfficial');
        if (btnOfficial) {
            if (officialCount >= MAX_OFFICIAL) {
                btnOfficial.disabled = true;
                btnOfficial.classList.add('disabled');
                btnOfficial.innerHTML = '<i class="fas fa-check me-1"></i> Tambah Official (Maksimal)';
            } else {
                btnOfficial.disabled = false;
                btnOfficial.classList.remove('disabled');
                btnOfficial.innerHTML = '<i class="fas fa-plus me-1"></i> + Tambah Official';
            }
        }
    }

    const csrf = document.querySelector('#formPendaftaran input[name="_token"]')?.value;
    const api = {
        draft: @json(route('peserta.pendaftaran.draft')),
        member: @json(route('peserta.pendaftaran.member')),
        file: @json(route('peserta.pendaftaran.member.file')),
        submit: @json(route('peserta.pendaftaran.submit')),
        deleteBase: @json(url('/peserta/pendaftaran/member')),
    };
    const editable = @json(!$isLocked);
    document.getElementById('formPendaftaran')?.addEventListener('submit', event => event.preventDefault());

    async function postForm(url, data, method = 'POST') {
        const response = await fetch(url, { method, headers: { 'X-CSRF-TOKEN': csrf, 'Accept': 'application/json' }, body: data });
        const result = await response.json().catch(() => ({}));
        if (!response.ok) throw new Error(result.message || Object.values(result.errors || {}).flat().join(' ') || 'Data gagal disimpan.');
        return result;
    }

    function teamFormData() {
        const data = new FormData();
        data.append('nama_tim', document.querySelector('[name="nama_tim"]')?.value || '');
        const recommendation = document.querySelector('[name="surat_rekomendasi"]');
        if (recommendation?.files?.[0]) data.append('surat_rekomendasi', recommendation.files[0]);
        return data;
    }

    async function saveTeam() {
        const teamName = document.querySelector('[name="nama_tim"]');
        const recommendation = document.querySelector('[name="surat_rekomendasi"]');
        if (!teamName?.checkValidity() || !recommendation?.checkValidity()) {
            teamName?.reportValidity();
            recommendation?.reportValidity();
            throw new Error('Lengkapi nama tim dan surat rekomendasi terlebih dahulu.');
        }
        const result = await postForm(api.draft, teamFormData());
        if (recommendation) {
            recommendation.value = '';
            recommendation.required = false;
        }
        return result;
    }

    function memberData(item, type) {
        const data = new FormData();
        data.append('tipe', type);
        item.querySelectorAll('input,select,textarea').forEach((field) => {
            const match = field.name.match(/\]\[([^\]]+)\]$/);
            if (!match || field.type === 'file' || field.disabled) return;
            if (match[1] === 'id') data.append('id', field.value);
            else data.append(match[1], field.value);
        });
        return data;
    }

    function validateMember(item) {
        const controls = [...item.querySelectorAll('input:not([type="hidden"]), select, textarea')]
            .filter(control => !control.disabled);
        const invalid = controls.find(control => !control.checkValidity());
        if (invalid) {
            invalid.reportValidity();
            return false;
        }
        return true;
    }

    function ensureMemberButtons() {
        if (!editable) return;
        document.querySelectorAll('#containerPemain .pemain-item, #containerOfficial .official-item').forEach((item) => {
            if (item.querySelector('.btn-save-member')) return;
            const actions = document.createElement('div');
            actions.className = 'd-flex align-items-center gap-2 mt-3';
            actions.innerHTML = '<button type="button" class="btn btn-sm btn-primary btn-save-member"><i class="fas fa-save me-1"></i> Simpan Anggota</button><span class="small text-muted member-save-state"></span>';
            if (item.querySelector('input[name$="[id]"]')?.value) actions.querySelector('.member-save-state').textContent = 'Tersimpan';
            item.appendChild(actions);
        });
    }

    document.getElementById('btnSaveTeam')?.addEventListener('click', async function() {
        const state = document.getElementById('teamSaveState');
        this.disabled = true;
        if (state) state.textContent = 'Menyimpan…';
        try {
            await saveTeam();
            if (state) state.textContent = 'Data tim tersimpan.';
        } catch (error) {
            if (state) state.textContent = error.message;
        } finally { this.disabled = false; }
    });

    document.getElementById('btnSubmit')?.addEventListener('click', async function() {
        const items = [...document.querySelectorAll('#containerPemain .pemain-item, #containerOfficial .official-item')];
        if (items.some(item => !item.querySelector('input[name$="[id]"]')?.value)) {
            alert('Simpan setiap pemain dan official terlebih dahulu.');
            return;
        }
        this.disabled = true;
        try {
            await saveTeam();
            const data = new FormData();
            data.append('confirmation', '1');
            const result = await postForm(api.submit, data);
            window.location.href = result.redirect;
        } catch (error) {
            alert(error.message);
            this.disabled = false;
        }
    });

    document.addEventListener('click', async function(e) {
        const button = e.target.closest('.btn-save-member');
        if (!button) return;
        const item = button.closest('.pemain-item, .official-item');
        const type = item.classList.contains('pemain-item') ? 'pemain' : 'official';
        const state = item.querySelector('.member-save-state');
        button.disabled = true;
        if (state) state.textContent = 'Menyimpan data…';
        try {
            await saveTeam();
            if (!validateMember(item)) {
                throw new Error('Periksa data anggota yang wajib diisi.');
            }
            const result = await postForm(api.member, memberData(item, type));
            let idInput = item.querySelector('input[name$="[id]"]');
            if (!idInput) {
                idInput = document.createElement('input');
                idInput.type = 'hidden';
                idInput.name = `${type}[${Date.now()}][id]`;
                item.prepend(idInput);
            }
            idInput.value = result.id;
            const fileFields = type === 'pemain' ? ['pasfoto','ktp','kk','surat_sehat'] : ['pasfoto','ktp'];
            for (const fieldName of fileFields) {
                const input = item.querySelector(`input[type="file"][name$="[${fieldName}]"]`);
                if (input?.files?.[0]) {
                    const upload = new FormData();
                    upload.append('member_id', result.id);
                    upload.append('field', fieldName);
                    upload.append('file', input.files[0]);
                    await postForm(api.file, upload);
                    input.value = '';
                    input.required = false;
                }
            }
            if (state) state.textContent = 'Tersimpan. Berkas dikirim satu per satu.';
        } catch (error) {
            if (state) state.textContent = error.message;
        } finally { button.disabled = false; }
    });

    document.getElementById('btnTambahPemain')?.addEventListener('click', () => setTimeout(ensureMemberButtons, 0));
    document.getElementById('btnTambahOfficial')?.addEventListener('click', () => setTimeout(ensureMemberButtons, 0));

    document.getElementById('btnTambahPemain')?.addEventListener('click', function() {
        if(pemainCount >= MAX_PEMAIN) {
            alert('Maksimal pemain adalah ' + MAX_PEMAIN);
            return;
        }
        
        let template = document.getElementById('templatePemain').innerHTML;
        template = template.replace(/__INDEX__/g, pemainIndex);
        
        document.getElementById('containerPemain').insertAdjacentHTML('beforeend', template);
        pemainIndex++;
        updateCounters();
    });

    document.getElementById('btnTambahOfficial')?.addEventListener('click', function() {
        if(officialCount >= MAX_OFFICIAL) {
            alert('Maksimal official adalah ' + MAX_OFFICIAL);
            return;
        }
        
        let emptyMsg = document.getElementById('emptyOfficialMsg');
        if(emptyMsg) emptyMsg.style.display = 'none';

        let template = document.getElementById('templateOfficial').innerHTML;
        template = template.replace(/__INDEX__/g, officialIndex);
        
        document.getElementById('containerOfficial').insertAdjacentHTML('beforeend', template);
        officialIndex++;
        updateCounters();
    });

    document.addEventListener('click', async function(e) {
        if(e.target.classList.contains('btn-hapus-pemain') || e.target.closest('.btn-hapus-pemain')) {
            if(pemainCount <= 1) {
                alert('Minimal harus ada 1 pemain!');
                return;
            }
            if(confirm('Hapus pemain ini?')) {
                const item = e.target.closest('.pemain-item');
                if (item) {
                    const id = item.querySelector('input[name$="[id]"]')?.value;
                    if (id) {
                        const response = await fetch(`${api.deleteBase}/${id}`, { method: 'DELETE', headers: { 'X-CSRF-TOKEN': csrf, 'Accept': 'application/json' } });
                        if (!response.ok) { alert('Pemain gagal dihapus dari draf.'); return; }
                    }
                    item.remove();
                }
                updateCounters();
            }
        }
        if(e.target.classList.contains('btn-hapus-official') || e.target.closest('.btn-hapus-official')) {
            if(confirm('Hapus official ini?')) {
                const item = e.target.closest('.official-item');
                if (item) {
                    const id = item.querySelector('input[name$="[id]"]')?.value;
                    if (id) {
                        const response = await fetch(`${api.deleteBase}/${id}`, { method: 'DELETE', headers: { 'X-CSRF-TOKEN': csrf, 'Accept': 'application/json' } });
                        if (!response.ok) { alert('Official gagal dihapus dari draf.'); return; }
                    }
                    item.remove();
                }
                updateCounters();
                if(officialCount === 0) {
                    let emptyMsg = document.getElementById('emptyOfficialMsg');
                    if(emptyMsg) emptyMsg.style.display = 'block';
                }
            }
        }
    });

    // Feedback visual ketika pengguna memilih file baru
    document.addEventListener('change', function(e) {
        if (e.target && e.target.type === 'file') {
            const input = e.target;
            const parent = input.parentElement;
            let previewIndicator = parent.querySelector('.file-chosen-indicator');
            if (input.files && input.files[0]) {
                const fileName = input.files[0].name;
                const fileSize = (input.files[0].size / 1024 / 1024).toFixed(2);
                if (!previewIndicator) {
                    previewIndicator = document.createElement('div');
                    previewIndicator.className = 'file-chosen-indicator mt-1 text-primary small d-flex align-items-center gap-1';
                    parent.appendChild(previewIndicator);
                }
                previewIndicator.innerHTML = '<i class="fas fa-file-alt"></i> <span>' + fileName + ' (' + fileSize + ' MB)</span>';
            } else if (previewIndicator) {
                previewIndicator.remove();
            }
        }
    });

    // Jalankan updateCounters saat halaman dimuat
    ensureMemberButtons();
    updateCounters();
});
</script>
@endpush
@endsection
