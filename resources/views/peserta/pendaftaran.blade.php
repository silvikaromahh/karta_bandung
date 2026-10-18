@extends('layouts.peserta')
@section('page-title', 'Pendaftaran Tim')

@section('content')
@php
    $reg = $reg ?? $registration ?? auth()->user()->registration;
    $statusInfo = [
        null => ['color'=>'secondary','icon'=>'fa-circle','label'=>'Belum Mendaftar','desc'=>'Anda belum mendaftarkan tim. Daftarkan sekarang!'],
        'Menunggu Verifikasi' => ['color'=>'warning','icon'=>'fa-hourglass-half','label'=>'Menunggu Verifikasi','desc'=>'Data tim sedang diperiksa oleh panitia. Verifikasi pemain berlangsung tgl 2 Nov s/d 10 Nov 2026.'],
        'Terverifikasi' => ['color'=>'success','icon'=>'fa-check-circle','label'=>'Terverifikasi','desc'=>'Selamat! Tim Anda resmi terdaftar.'],
        'Ditolak' => ['color'=>'danger','icon'=>'fa-times-circle','label'=>'Ditolak','desc'=>'Pendaftaran tim ditolak. Silakan perbaiki dan kirim ulang.'],
    ];
    $status = $reg ? $reg->status : null;
    $info = $statusInfo[$status] ?? $statusInfo[null];
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

{{-- STATUS CARD --}}
<div class="card border-0 card-karta mb-4" style="border-left:5px solid var(--bs-{{ $info['color'] }})!important;">
    <div class="card-body d-flex align-items-center gap-4 p-4">
        <div style="width:56px;height:56px;border-radius:50%;background:var(--bs-{{ $info['color'] }}-bg, #f8fafc);display:flex;align-items:center;justify-content:center;flex-shrink:0;font-size:1.5rem;" class="text-{{ $info['color'] }}">
            <i class="fas {{ $info['icon'] }}"></i>
        </div>
        <div>
            <h5 class="fw-bold mb-1" style="color:#0a1628;">Status: {{ $info['label'] }}</h5>
            <p class="text-muted mb-0 small">{{ $info['desc'] }}</p>
            @if($reg && $reg->status === 'Ditolak' && $reg->rejection_reason)
            <div class="mt-2 p-2 rounded-3" style="background:#fff1f2;border:1px solid #fecaca;">
                <span class="text-danger small"><strong>Alasan:</strong> {{ $reg->rejection_reason }}</span>
            </div>
            @endif
        </div>
    </div>
</div>

{{-- FORMULIR PENDAFTARAN --}}
<form method="POST" action="{{ route('peserta.pendaftaran.store') }}" enctype="multipart/form-data" id="formPendaftaran">
    @csrf

    @if($errors->any())
    <div class="alert alert-danger py-2 small mb-4">
        <ul class="mb-0 ps-3">@foreach(array_unique($errors->all()) as $e)<li>{{ $e }}</li>@endforeach</ul>
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
                    <input type="file" name="surat_rekomendasi" class="form-control" accept=".pdf,.jpg,.jpeg,.png" {{ (!$reg || !$reg->surat_rekomendasi_path) ? 'required' : '' }} {{ $reg && $reg->status == 'Terverifikasi' ? 'disabled' : '' }}>
                    @if($reg && $reg->surat_rekomendasi_path)
                    <div class="mt-2">
                        <a href="{{ asset('storage/' . $reg->surat_rekomendasi_path) }}" target="_blank" class="btn btn-sm btn-outline-primary">
                            <i class="fas fa-file-pdf me-1"></i> Lihat Surat Rekomendasi
                        </a>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    {{-- DATA PEMAIN --}}
    <div class="card border-0 card-karta mb-4">
        <div class="card-header border-0 bg-white py-3 d-flex justify-content-between align-items-center">
            <h6 class="fw-bold mb-0" style="color:#0a1628;"><i class="fas fa-users text-primary me-2"></i> Data Pemain (Maks. 25)</h6>
            @if(!$reg || $reg->status != 'Terverifikasi')
            <button type="button" class="btn btn-sm btn-outline-primary" id="btnTambahPemain"><i class="fas fa-plus"></i> Tambah Pemain</button>
            @endif
        </div>
        <div class="card-body p-4" id="containerPemain">
            @php $pemainList = old('pemain', $reg ? $reg->members()->where('tipe', 'pemain')->get()->toArray() : [[]]); @endphp
            
            @foreach($pemainList as $index => $pemain)
            <div class="pemain-item border p-3 mb-3 rounded position-relative">
                @if(!$reg || $reg->status != 'Terverifikasi')
                <button type="button" class="btn-close position-absolute top-0 end-0 m-2 btn-hapus-pemain" aria-label="Close"></button>
                @endif
                <h6 class="fw-bold small mb-3">Pemain #<span class="pemain-counter">{{ $index + 1 }}</span></h6>
                
                @if(isset($pemain['id']))
                <input type="hidden" name="pemain[{{ $index }}][id]" value="{{ $pemain['id'] }}">
                @endif
                
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label fw-semibold small">Nama Lengkap</label>
                        <input type="text" name="pemain[{{ $index }}][full_name]" class="form-control form-control-sm" value="{{ $pemain['full_name'] ?? '' }}" required {{ $reg && $reg->status == 'Terverifikasi' ? 'readonly' : '' }}>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold small">NIK</label>
                        <input type="text" name="pemain[{{ $index }}][nik]" class="form-control form-control-sm" maxlength="16" value="{{ $pemain['nik'] ?? '' }}" required {{ $reg && $reg->status == 'Terverifikasi' ? 'readonly' : '' }}>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold small">Nomor KK</label>
                        <input type="text" name="pemain[{{ $index }}][nomor_kk]" class="form-control form-control-sm" inputmode="numeric" maxlength="16" pattern="[0-9]{16}" value="{{ $pemain['nomor_kk'] ?? '' }}" placeholder="16 digit" required {{ $reg && $reg->status == 'Terverifikasi' ? 'readonly' : '' }}>
                        <small class="text-muted">Masukkan 16 digit nomor Kartu Keluarga.</small>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-semibold small">Tanggal Lahir</label>
                        <input type="date" name="pemain[{{ $index }}][tanggal_lahir]" class="form-control form-control-sm" value="{{ isset($pemain['tanggal_lahir']) ? \Carbon\Carbon::parse($pemain['tanggal_lahir'])->format('Y-m-d') : '' }}" required {{ $reg && $reg->status == 'Terverifikasi' ? 'readonly' : '' }}>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-semibold small">Posisi</label>
                        <select name="pemain[{{ $index }}][posisi]" class="form-select form-select-sm" required {{ $reg && $reg->status == 'Terverifikasi' ? 'disabled' : '' }}>
                            <option value="">-- Posisi --</option>
                            @foreach(['Kiper','Bek Kiri','Bek Kanan','Bek Tengah','Gelandang Bertahan','Gelandang Tengah','Gelandang Serang','Sayap Kiri','Sayap Kanan','Striker'] as $p)
                            <option value="{{ $p }}" {{ ($pemain['posisi'] ?? '') === $p ? 'selected' : '' }}>{{ $p }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-semibold small">No Punggung</label>
                        <input type="number" name="pemain[{{ $index }}][nomor_punggung]" class="form-control form-control-sm" min="1" max="99" value="{{ $pemain['nomor_punggung'] ?? '' }}" required {{ $reg && $reg->status == 'Terverifikasi' ? 'readonly' : '' }}>
                    </div>
                    
                    {{-- File Uploads Pemain --}}
                    <div class="col-md-4">
                        <label class="form-label fw-semibold small">Pas Foto (Max 2MB)</label>
                        @if(isset($pemain['pasfoto_path']) && $pemain['pasfoto_path'])
                            <div class="mb-1"><a href="{{ Storage::url($pemain['pasfoto_path']) }}" target="_blank" class="small">Lihat File</a></div>
                        @endif
                        <input type="file" name="pemain[{{ $index }}][pasfoto]" class="form-control form-control-sm" accept="image/jpeg,image/png" {{ isset($pemain['id']) ? '' : 'required' }} {{ $reg && $reg->status == 'Terverifikasi' ? 'disabled' : '' }}>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-semibold small">Foto KTP (Max 2MB)</label>
                        @if(isset($pemain['ktp_path']) && $pemain['ktp_path'])
                            <div class="mb-1"><a href="{{ Storage::url($pemain['ktp_path']) }}" target="_blank" class="small">Lihat File</a></div>
                        @endif
                        <input type="file" name="pemain[{{ $index }}][ktp]" class="form-control form-control-sm" accept="image/jpeg,image/png,application/pdf" {{ isset($pemain['id']) ? '' : 'required' }} {{ $reg && $reg->status == 'Terverifikasi' ? 'disabled' : '' }}>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-semibold small">Foto KK (Max 2MB)</label>
                        @if(isset($pemain['kk_path']) && $pemain['kk_path'])
                            <div class="mb-1"><a href="{{ Storage::url($pemain['kk_path']) }}" target="_blank" class="small">Lihat File</a></div>
                        @endif
                        <input type="file" name="pemain[{{ $index }}][kk]" class="form-control form-control-sm" accept="image/jpeg,image/png,application/pdf" {{ isset($pemain['id']) ? '' : 'required' }} {{ $reg && $reg->status == 'Terverifikasi' ? 'disabled' : '' }}>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-semibold small">Surat Keterangan Sehat (Gambar, max 2MB)</label>
                        @if(!empty($pemain['surat_sehat_path']))
                            <div class="mb-1"><a href="{{ Storage::url($pemain['surat_sehat_path']) }}" target="_blank" class="small">Lihat Surat Keterangan Sehat</a></div>
                        @endif
                        <input type="file" name="pemain[{{ $index }}][surat_sehat]" class="form-control form-control-sm" accept="image/jpeg,image/png" {{ !empty($pemain['surat_sehat_path']) ? '' : 'required' }} {{ $reg && $reg->status == 'Terverifikasi' ? 'disabled' : '' }}>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </div>

    {{-- DATA OFFICIAL --}}
    <div class="card border-0 card-karta mb-4">
        <div class="card-header border-0 bg-white py-3 d-flex justify-content-between align-items-center">
            <h6 class="fw-bold mb-0" style="color:#0a1628;"><i class="fas fa-user-tie text-info me-2"></i> Data Official (Maks. 5)</h6>
            @if(!$reg || $reg->status != 'Terverifikasi')
            <button type="button" class="btn btn-sm btn-outline-info" id="btnTambahOfficial"><i class="fas fa-plus"></i> Tambah Official</button>
            @endif
        </div>
        <div class="card-body p-4" id="containerOfficial">
            @php $officialList = old('official', $reg ? $reg->members()->where('tipe', 'official')->get()->toArray() : []); @endphp
            
            @if(count($officialList) == 0 && (!$reg || $reg->status != 'Terverifikasi'))
                <p class="text-muted small text-center" id="emptyOfficialMsg">Belum ada official. Klik "Tambah Official" jika ada.</p>
            @endif
            
            @foreach($officialList as $index => $official)
            <div class="official-item border p-3 mb-3 rounded position-relative">
                @if(!$reg || $reg->status != 'Terverifikasi')
                <button type="button" class="btn-close position-absolute top-0 end-0 m-2 btn-hapus-official" aria-label="Close"></button>
                @endif
                <h6 class="fw-bold small mb-3">Official #<span class="official-counter">{{ $index + 1 }}</span></h6>
                
                @if(isset($official['id']))
                <input type="hidden" name="official[{{ $index }}][id]" value="{{ $official['id'] }}">
                @endif
                
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label fw-semibold small">Nama Lengkap</label>
                        <input type="text" name="official[{{ $index }}][full_name]" class="form-control form-control-sm" value="{{ $official['full_name'] ?? '' }}" required {{ $reg && $reg->status == 'Terverifikasi' ? 'readonly' : '' }}>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label fw-semibold small">NIK</label>
                        <input type="text" name="official[{{ $index }}][nik]" class="form-control form-control-sm" maxlength="16" value="{{ $official['nik'] ?? '' }}" required {{ $reg && $reg->status == 'Terverifikasi' ? 'readonly' : '' }}>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label fw-semibold small">Jabatan</label>
                        <select name="official[{{ $index }}][jabatan]" class="form-select form-select-sm" required {{ $reg && $reg->status == 'Terverifikasi' ? 'disabled' : '' }}>
                            <option value="">-- Pilih --</option>
                            @foreach(['Manajer','Pelatih Kepala','Asisten Pelatih','Pelatih Kiper','Medis','Kitman'] as $j)
                            <option value="{{ $j }}" {{ ($official['jabatan'] ?? '') === $j ? 'selected' : '' }}>{{ $j }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-semibold small">Tanggal Lahir</label>
                        <input type="date" name="official[{{ $index }}][tanggal_lahir]" class="form-control form-control-sm" value="{{ isset($official['tanggal_lahir']) ? \Carbon\Carbon::parse($official['tanggal_lahir'])->format('Y-m-d') : '' }}" required {{ $reg && $reg->status == 'Terverifikasi' ? 'readonly' : '' }}>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-semibold small">Pas Foto (Max 2MB)</label>
                        @if(isset($official['pasfoto_path']) && $official['pasfoto_path'])
                            <div class="mb-1"><a href="{{ Storage::url($official['pasfoto_path']) }}" target="_blank" class="small">Lihat File</a></div>
                        @endif
                        <input type="file" name="official[{{ $index }}][pasfoto]" class="form-control form-control-sm" accept="image/jpeg,image/png" {{ isset($official['id']) ? '' : 'required' }} {{ $reg && $reg->status == 'Terverifikasi' ? 'disabled' : '' }}>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-semibold small">Foto KTP (Max 2MB)</label>
                        @if(isset($official['ktp_path']) && $official['ktp_path'])
                            <div class="mb-1"><a href="{{ Storage::url($official['ktp_path']) }}" target="_blank" class="small">Lihat File</a></div>
                        @endif
                        <input type="file" name="official[{{ $index }}][ktp]" class="form-control form-control-sm" accept="image/jpeg,image/png,application/pdf" {{ isset($official['id']) ? '' : 'required' }} {{ $reg && $reg->status == 'Terverifikasi' ? 'disabled' : '' }}>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </div>

    <div class="d-flex gap-2 justify-content-end">
        @if($reg && $reg->status === 'Terverifikasi')
        <div class="alert alert-success mb-0 flex-grow-1 d-flex align-items-center gap-2 py-2">
            <i class="fas fa-lock text-success"></i>
            <span class="small"><strong>Pendaftaran terkunci.</strong> Hubungi panitia jika perlu perubahan data.</span>
        </div>
        @else
        <button type="submit" class="btn btn-green fw-bold btn-lg px-5" id="btnSubmit">
            <i class="fas fa-paper-plane me-2"></i>
            {{ $reg ? 'Perbarui & Kirim Ulang' : 'Kirim Pendaftaran' }}
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
            <div class="col-md-4">
                <label class="form-label fw-semibold small">Tanggal Lahir</label>
                <input type="date" name="pemain[__INDEX__][tanggal_lahir]" class="form-control form-control-sm" required>
            </div>
            <div class="col-md-4">
                <label class="form-label fw-semibold small">Nomor KK</label>
                <input type="text" name="pemain[__INDEX__][nomor_kk]" class="form-control form-control-sm" inputmode="numeric" maxlength="16" pattern="[0-9]{16}" placeholder="16 digit" required>
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
                <input type="file" name="pemain[__INDEX__][surat_sehat]" class="form-control form-control-sm" accept="image/jpeg,image/png" required>
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
                <input type="text" name="official[__INDEX__][nik]" class="form-control form-control-sm" maxlength="16" required>
            </div>
            <div class="col-md-3">
                <label class="form-label fw-semibold small">Jabatan</label>
                <select name="official[__INDEX__][jabatan]" class="form-select form-select-sm" required>
                    <option value="">-- Pilih --</option>
                    @foreach(['Manajer','Pelatih Kepala','Asisten Pelatih','Pelatih Kiper','Medis','Kitman'] as $j)
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
                <input type="file" name="official[__INDEX__][pasfoto]" class="form-control form-control-sm" accept="image/jpeg,image/png" required>
            </div>
            <div class="col-md-4">
                <label class="form-label fw-semibold small">Foto KTP (Max 2MB)</label>
                <input type="file" name="official[__INDEX__][ktp]" class="form-control form-control-sm" accept="image/jpeg,image/png,application/pdf" required>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    let pemainCount = document.querySelectorAll('#containerPemain .pemain-item').length;
    let officialCount = document.querySelectorAll('#containerOfficial .official-item').length;
    let pemainIndex = pemainCount;
    let officialIndex = officialCount;

    const MAX_PEMAIN = 25;
    const MAX_OFFICIAL = 5;

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

    document.addEventListener('click', function(e) {
        if(e.target.classList.contains('btn-hapus-pemain')) {
            if(pemainCount <= 1) {
                alert('Minimal harus ada 1 pemain!');
                return;
            }
            if(confirm('Hapus pemain ini?')) {
                e.target.closest('.pemain-item').remove();
                updateCounters();
            }
        }
        if(e.target.classList.contains('btn-hapus-official')) {
            if(confirm('Hapus official ini?')) {
                e.target.closest('.official-item').remove();
                updateCounters();
                if(officialCount === 0) {
                    let emptyMsg = document.getElementById('emptyOfficialMsg');
                    if(emptyMsg) emptyMsg.style.display = 'block';
                }
            }
        }
    });

    function updateCounters() {
        let players = document.querySelectorAll('#containerPemain .pemain-item');
        pemainCount = players.length;
        players.forEach((el, idx) => el.querySelector('.pemain-counter').innerText = idx + 1);

        let officials = document.querySelectorAll('#containerOfficial .official-item');
        officialCount = officials.length;
        officials.forEach((el, idx) => el.querySelector('.official-counter').innerText = idx + 1);
    }
});
</script>
@endpush
@endsection
