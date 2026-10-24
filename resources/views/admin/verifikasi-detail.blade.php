@extends('layouts.admin')
@section('page-title', 'Detail Verifikasi – ' . $registration->nama_tim)

@section('content')
<div class="mb-4 d-flex justify-content-between align-items-center flex-wrap gap-2">
    <div class="d-flex gap-2">
        <a href="{{ route('admin.verifikasi') }}" class="btn btn-outline-secondary btn-sm">
            <i class="fas fa-arrow-left me-1"></i> Kembali
        </a>
        <a href="{{ route('admin.verifikasi.cetak', $registration->id) }}" target="_blank" class="btn btn-outline-primary btn-sm fw-semibold">
            <i class="fas fa-print me-1"></i> Cetak / Export PDF
        </a>
    </div>
    
    @if($registration->status === 'Menunggu Verifikasi')
    <div class="d-flex gap-2">
        <button class="btn btn-danger btn-sm" data-bs-toggle="collapse" data-bs-target="#formTolak">
            <i class="fas fa-times me-1"></i> Tolak
        </button>
        <form method="POST" action="{{ route('admin.verifikasi.terima', $registration->id) }}" onsubmit="return confirm('Yakin menerima pendaftaran tim {{ $registration->nama_tim }}?')">
            @csrf
            <button type="submit" class="btn btn-success btn-sm">
                <i class="fas fa-check me-1"></i> Terima Pendaftaran
            </button>
        </form>
    </div>
    @endif
</div>

@if($registration->status === 'Menunggu Verifikasi')
<div class="collapse mb-4" id="formTolak">
    <div class="card border-0 card-karta">
        <div class="card-body">
            <form method="POST" action="{{ route('admin.verifikasi.tolak', $registration->id) }}" onsubmit="return confirm('Yakin menolak pendaftaran ini?')">
                @csrf
                <label class="form-label fw-semibold small">Alasan Penolakan <span class="text-danger">*</span></label>
                <textarea name="rejection_reason" class="form-control mb-3" rows="3" placeholder="Jelaskan alasan penolakan..." required></textarea>
                <button type="submit" class="btn btn-danger btn-sm"><i class="fas fa-ban me-1"></i> Kirim Penolakan</button>
            </form>
        </div>
    </div>
</div>
@endif

<div class="row g-4">
    <!-- DATA TIM -->
    <div class="col-lg-12">
        <div class="card border-0 card-karta">
            <div class="card-header border-0 bg-white py-3">
                <h6 class="fw-bold mb-0" style="color:#0a1628;"><i class="fas fa-shield-alt text-success me-2"></i> Data Tim</h6>
            </div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-3">
                        <span class="text-muted small d-block">Nama Tim</span>
                        <span class="fw-bold">{{ $registration->nama_tim }}</span>
                    </div>
                    <div class="col-md-3">
                        <span class="text-muted small d-block">Kecamatan</span>
                        <span class="fw-bold">{{ $registration->kecamatan }}</span>
                    </div>
                    <div class="col-md-3">
                        <span class="text-muted small d-block">Surat Rekomendasi</span>
                        @if($registration->surat_rekomendasi_path)
                            <a href="javascript:void(0)" onclick="openDocModal('{{ Storage::url($registration->surat_rekomendasi_path) }}', 'Surat Rekomendasi — {{ $registration->nama_tim }}', 'surat_rekomendasi_{{ Str::slug($registration->nama_tim) }}')" role="button" class="badge bg-white text-dark border rounded-2 text-decoration-none py-1 px-2 d-inline-flex align-items-center mt-1" style="font-weight: 500; font-size: 0.73rem; border-color: #cbd5e1 !important; color: #334155 !important; cursor: pointer;">
                                <i class="fas fa-eye text-muted me-1"></i> Lihat surat
                            </a>
                        @else
                            <span class="badge bg-secondary">Belum Upload</span>
                        @endif
                    </div>
                    <div class="col-md-3">
                        <span class="text-muted small d-block">Status</span>
                        <span class="badge-status status-{{ strtolower(str_replace(' ','',explode(' ',$registration->status)[0])) }}">{{ $registration->status }}</span>
                    </div>
                    <div class="col-md-12">
                        <span class="text-muted small d-block">Akun Pendaftar</span>
                        <span class="fw-bold">{{ $registration->user->name }} ({{ $registration->user->email }})</span>
                    </div>
                </div>
                @if($registration->rejection_reason)
                <div class="mt-3 p-2 rounded" style="background:#fff1f2;border:1px solid #fecaca;">
                    <span class="text-danger small"><strong>Alasan Ditolak:</strong> {{ $registration->rejection_reason }}</span>
                </div>
                @endif
            </div>
        </div>
    </div>

    <!-- DATA OFFICIAL -->
    <div class="col-lg-12">
        <div class="card border-0 card-karta">
            <div class="card-header border-0 bg-white py-3">
                <h6 class="fw-bold mb-0" style="color:#0a1628;"><i class="fas fa-user-tie text-info me-2"></i> Data Official</h6>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="bg-white" style="border-bottom: 1px solid #e9ecef;">
                            <tr>
                                <th class="small text-muted py-3 px-4">Nama Lengkap</th>
                                <th class="small text-muted py-3">Jabatan</th>
                                <th class="small text-muted py-3">NIK & Tgl Lahir</th>
                                <th class="small text-muted py-3">Dokumen</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($registration->members()->where('tipe', 'official')->get() as $off)
                            <tr>
                                <td class="px-4 fw-medium">{{ $off->full_name }}</td>
                                <td>{{ $off->jabatan }}</td>
                                <td>
                                    <div>{{ $off->nik }}</div>
                                    <small class="text-muted">{{ $off->tanggal_lahir->format('d M Y') }}</small>
                                </td>
                                <td>
                                    @if($off->pasfoto_path)<a href="javascript:void(0)" onclick="openDocModal('{{ Storage::url($off->pasfoto_path) }}', 'Pas Foto — {{ $off->full_name }}', 'pasfoto_{{ Str::slug($off->full_name) }}.jpg')" role="button" class="badge bg-white text-dark border rounded-2 text-decoration-none py-1 px-2 me-1 mb-1 d-inline-flex align-items-center" style="font-weight: 500; font-size: 0.73rem; border-color: #cbd5e1 !important; color: #334155 !important; cursor: pointer;"><i class="fas fa-image text-muted me-1"></i> Pas foto</a>@endif
                                    @if($off->ktp_path)<a href="javascript:void(0)" onclick="openDocModal('{{ Storage::url($off->ktp_path) }}', 'Foto KTP — {{ $off->full_name }}', 'ktp_{{ Str::slug($off->full_name) }}.jpg')" role="button" class="badge bg-white text-dark border rounded-2 text-decoration-none py-1 px-2 me-1 mb-1 d-inline-flex align-items-center" style="font-weight: 500; font-size: 0.73rem; border-color: #cbd5e1 !important; color: #334155 !important; cursor: pointer;"><i class="fas fa-id-card text-muted me-1"></i> KTP</a>@endif
                                    @if(!$off->pasfoto_path && !$off->ktp_path)
                                        <span class="text-muted small fst-italic">Belum upload</span>
                                    @endif
                                </td>
                            </tr>
                            @empty
                            <tr><td colspan="4" class="text-center py-3 text-muted">Belum ada official</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- DATA PEMAIN -->
    <div class="col-lg-12">
        <div class="card border-0 card-karta">
            <div class="card-header border-0 bg-white py-3">
                <h6 class="fw-bold mb-0" style="color:#0a1628;"><i class="fas fa-users text-primary me-2"></i> Data Pemain</h6>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="bg-white" style="border-bottom: 1px solid #e9ecef;">
                            <tr>
                                <th class="small text-muted py-3 px-4">No.</th>
                                <th class="small text-muted py-3">Nama Pemain</th>
                                <th class="small text-muted py-3">Posisi</th>
                                <th class="small text-muted py-3">NIK & Tgl Lahir</th>
                                <th class="small text-muted py-3">Dokumen</th>
                                <th class="small text-muted py-3 text-center pe-4" style="width: 100px;">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($registration->members()->where('tipe', 'pemain')->get() as $pem)
                            <tr>
                                <td class="px-4 fw-bold text-center" style="width:50px;">{{ $pem->nomor_punggung }}</td>
                                <td class="fw-medium">{{ $pem->full_name }}</td>
                                <td>{{ $pem->posisi }}</td>
                                <td>
                                    <div>{{ $pem->nik }}</div>
                                    @if($pem->nomor_kk)<small class="text-muted d-block" style="font-size:0.75rem;">No. KK: {{ $pem->nomor_kk }}</small>@endif
                                    <small class="text-muted">{{ $pem->tanggal_lahir->format('d M Y') }}</small>
                                </td>
                                <td>
                                    @if($pem->pasfoto_path)<a href="javascript:void(0)" onclick="openDocModal('{{ Storage::url($pem->pasfoto_path) }}', 'Pas Foto — {{ $pem->full_name }}', 'pasfoto_{{ Str::slug($pem->full_name) }}.jpg')" role="button" class="badge bg-white text-dark border rounded-2 text-decoration-none py-1 px-2 me-1 mb-1 d-inline-flex align-items-center" style="font-weight: 500; font-size: 0.73rem; border-color: #cbd5e1 !important; color: #334155 !important; cursor: pointer;"><i class="fas fa-image text-muted me-1"></i> Pas foto</a>@endif
                                    @if($pem->ktp_path)<a href="javascript:void(0)" onclick="openDocModal('{{ Storage::url($pem->ktp_path) }}', 'Foto KTP — {{ $pem->full_name }}', 'ktp_{{ Str::slug($pem->full_name) }}.jpg')" role="button" class="badge bg-white text-dark border rounded-2 text-decoration-none py-1 px-2 me-1 mb-1 d-inline-flex align-items-center" style="font-weight: 500; font-size: 0.73rem; border-color: #cbd5e1 !important; color: #334155 !important; cursor: pointer;"><i class="fas fa-id-card text-muted me-1"></i> KTP</a>@endif
                                    @if($pem->kk_path)<a href="javascript:void(0)" onclick="openDocModal('{{ Storage::url($pem->kk_path) }}', 'Kartu Keluarga — {{ $pem->full_name }}', 'kk_{{ Str::slug($pem->full_name) }}.jpg')" role="button" class="badge bg-white text-dark border rounded-2 text-decoration-none py-1 px-2 me-1 mb-1 d-inline-flex align-items-center" style="font-weight: 500; font-size: 0.73rem; border-color: #cbd5e1 !important; color: #334155 !important; cursor: pointer;"><i class="fas fa-file-alt text-muted me-1"></i> KK</a>@endif
                                    @if($pem->surat_sehat_path)<a href="javascript:void(0)" onclick="openDocModal('{{ Storage::url($pem->surat_sehat_path) }}', 'Surat Sehat — {{ $pem->full_name }}', 'surat_sehat_{{ Str::slug($pem->full_name) }}.jpg')" role="button" class="badge bg-white text-dark border rounded-2 text-decoration-none py-1 px-2 me-1 mb-1 d-inline-flex align-items-center" style="font-weight: 500; font-size: 0.73rem; border-color: #cbd5e1 !important; color: #334155 !important; cursor: pointer;"><i class="fas fa-heart text-muted me-1"></i> Surat sehat</a>@endif
                                    @if(!$pem->pasfoto_path && !$pem->ktp_path && !$pem->kk_path && !$pem->surat_sehat_path)
                                        <span class="text-muted small fst-italic">Belum upload</span>
                                    @endif
                                </td>
                                <td class="text-center pe-4">
                                    <form method="POST" action="{{ route('admin.verifikasi.member.delete', [$registration->id, $pem->id]) }}" onsubmit="return confirm('Apakah Anda yakin ingin menghapus data pemain {{ addslashes($pem->full_name) }}?')" class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger py-1 px-2 d-inline-flex align-items-center" title="Hapus Pemain" style="font-size: 0.78rem; border-radius: 6px;">
                                            <i class="fas fa-trash-alt me-1"></i> Hapus
                                        </button>
                                    </form>
                                </td>
                            </tr>
                            @empty
                            <tr><td colspan="6" class="text-center py-3 text-muted">Belum ada data pemain</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- MODAL PREVIEW DOKUMEN -->
<div class="modal fade" id="docModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0 shadow-lg rounded-3 overflow-hidden">
            <div class="modal-header bg-white py-3 px-4 border-bottom">
                <h6 class="modal-title fw-bold text-dark mb-0" id="docModalTitle">Preview Dokumen</h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4 position-relative d-flex justify-content-center align-items-center" style="background: #e2e8f0; min-height: 480px;">
                <a href="#" download id="docModalDownload" class="btn btn-primary btn-sm px-3 shadow-sm position-absolute top-0 end-0 m-3" style="z-index: 10;">
                    <i class="fas fa-download me-1"></i> Unduh
                </a>
                <img id="docModalImg" src="" alt="Dokumen" style="max-height: 70vh; max-width: 100%; object-fit: contain; box-shadow: 0 4px 15px rgba(0,0,0,0.15);">
                <iframe id="docModalPdf" src="" style="width: 100%; height: 70vh; border: none; display: none;" title="Preview PDF"></iframe>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
function openDocModal(url, title, filename) {
    if (!url) return;
    
    document.getElementById('docModalTitle').textContent = title || 'Preview Dokumen';
    
    var imgEl = document.getElementById('docModalImg');
    var pdfEl = document.getElementById('docModalPdf');
    var dlBtn = document.getElementById('docModalDownload');
    
    dlBtn.href = url;
    if (filename) {
        dlBtn.setAttribute('download', filename);
    } else {
        dlBtn.setAttribute('download', '');
    }
    
    var isPdf = url.toLowerCase().split('?')[0].endsWith('.pdf');
    if (isPdf) {
        imgEl.style.display = 'none';
        imgEl.src = '';
        pdfEl.style.display = 'block';
        pdfEl.src = url;
    } else {
        pdfEl.style.display = 'none';
        pdfEl.src = '';
        imgEl.style.display = 'block';
        imgEl.src = url;
    }
    
    var modal = bootstrap.Modal.getOrCreateInstance(document.getElementById('docModal'));
    modal.show();
}

document.addEventListener('DOMContentLoaded', function() {
    var modalEl = document.getElementById('docModal');
    if (modalEl) {
        modalEl.addEventListener('hidden.bs.modal', function () {
            var imgEl = document.getElementById('docModalImg');
            var pdfEl = document.getElementById('docModalPdf');
            if (imgEl) imgEl.src = '';
            if (pdfEl) pdfEl.src = '';
        });
    }
});
</script>
@endpush
@endsection
