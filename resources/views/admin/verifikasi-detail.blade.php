@extends('layouts.admin')
@section('page-title', 'Detail Verifikasi – ' . $registration->nama_tim)

@section('content')
<style>
    .document-list { display: flex; flex-wrap: wrap; gap: .5rem; min-width: 210px; }
    .document-item { display: inline-flex; align-items: center; gap: .2rem; padding: .2rem; border: 1px solid #e2e8f0; border-radius: .65rem; background: #f8fafc; }
    .document-view { display: inline-flex; align-items: center; justify-content: center; min-height: 30px; border-radius: .45rem; text-decoration: none; transition: background-color .15s ease, color .15s ease; }
    .document-view { gap: .4rem; padding: .25rem .6rem; color: #334155; font-size: .75rem; font-weight: 600; border: 0; background: transparent; cursor: pointer; }
    .document-view:hover { background: #e2e8f0; color: #0f172a; }
</style>
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
                            <div class="document-list mt-2">
                                <div class="document-item">
                                    <button type="button" class="document-view js-document-preview" data-document-url="{{ asset('storage/' . $registration->surat_rekomendasi_path) }}" data-document-title="Surat Rekomendasi"><i class="fas fa-eye"></i> Lihat surat</button>
                                </div>
                            </div>
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
                        <thead class="table-light">
                            <tr>
                                <th class="small text-muted py-3 px-4">Nama Lengkap</th>
                                <th class="small text-muted py-3">Jabatan</th>
                                <th class="small text-muted py-3">NIK & Tgl Lahir</th>
                                <th class="small text-muted py-3">Dokumen</th>
                                @if($registration->status === 'Terverifikasi')<th class="small text-muted py-3 text-end pe-4">Aksi</th>@endif
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
                                    <div class="document-list">
                                        @if($off->pasfoto_path)<div class="document-item"><button type="button" class="document-view js-document-preview" data-document-url="{{ Storage::url($off->pasfoto_path) }}" data-document-title="Pas Foto Official — {{ $off->full_name }}"><i class="fas fa-image"></i> Pas foto</button></div>@endif
                                        @if($off->ktp_path)<div class="document-item"><button type="button" class="document-view js-document-preview" data-document-url="{{ Storage::url($off->ktp_path) }}" data-document-title="KTP Official — {{ $off->full_name }}"><i class="fas fa-id-card"></i> KTP</button></div>@endif
                                        @if(!$off->pasfoto_path && !$off->ktp_path)<span class="text-muted small">Belum ada dokumen</span>@endif
                                    </div>
                                </td>
                                @if($registration->status === 'Terverifikasi')
                                <td class="text-end pe-4">
                                    <form method="POST" action="{{ route('admin.verifikasi.anggota.destroy', [$registration->id, $off->id]) }}" onsubmit="return confirm(@js('Hapus official ' . $off->full_name . '? Dokumen yang diunggah juga akan dihapus.'))">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger"><i class="fas fa-trash-alt me-1"></i> Hapus</button>
                                    </form>
                                </td>
                                @endif
                            </tr>
                            @empty
                            <tr><td colspan="{{ $registration->status === 'Terverifikasi' ? 5 : 4 }}" class="text-center py-3 text-muted">Belum ada official</td></tr>
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
                        <thead class="table-light">
                            <tr>
                                <th class="small text-muted py-3 px-4">No.</th>
                                <th class="small text-muted py-3">Nama Pemain</th>
                                <th class="small text-muted py-3">Posisi</th>
                                <th class="small text-muted py-3">NIK & Tgl Lahir</th>
                                <th class="small text-muted py-3">Dokumen</th>
                                @if($registration->status === 'Terverifikasi')<th class="small text-muted py-3 text-end pe-4">Aksi</th>@endif
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
                                    <small class="text-muted d-block">No. KK: {{ $pem->nomor_kk ?: '—' }}</small>
                                    <small class="text-muted">{{ $pem->tanggal_lahir->format('d M Y') }}</small>
                                </td>
                                <td>
                                    <div class="document-list">
                                        @if($pem->pasfoto_path)<div class="document-item"><button type="button" class="document-view js-document-preview" data-document-url="{{ Storage::url($pem->pasfoto_path) }}" data-document-title="Pas Foto — {{ $pem->full_name }}"><i class="fas fa-image"></i> Pas foto</button></div>@endif
                                        @if($pem->ktp_path)<div class="document-item"><button type="button" class="document-view js-document-preview" data-document-url="{{ Storage::url($pem->ktp_path) }}" data-document-title="KTP — {{ $pem->full_name }}"><i class="fas fa-id-card"></i> KTP</button></div>@endif
                                        @if($pem->kk_path)<div class="document-item"><button type="button" class="document-view js-document-preview" data-document-url="{{ Storage::url($pem->kk_path) }}" data-document-title="Kartu Keluarga — {{ $pem->full_name }}"><i class="fas fa-file-alt"></i> KK</button></div>@endif
                                        @if($pem->surat_sehat_path)<div class="document-item"><button type="button" class="document-view js-document-preview" data-document-url="{{ Storage::url($pem->surat_sehat_path) }}" data-document-title="Surat Keterangan Sehat — {{ $pem->full_name }}"><i class="fas fa-heartbeat"></i> Surat sehat</button></div>@endif
                                        @if(!$pem->pasfoto_path && !$pem->ktp_path && !$pem->kk_path && !$pem->surat_sehat_path)<span class="text-muted small">Belum ada dokumen</span>@endif
                                    </div>
                                </td>
                                @if($registration->status === 'Terverifikasi')
                                <td class="text-end pe-4">
                                    <form method="POST" action="{{ route('admin.verifikasi.anggota.destroy', [$registration->id, $pem->id]) }}" onsubmit="return confirm(@js('Hapus pemain ' . $pem->full_name . '? Dokumen yang diunggah juga akan dihapus.'))">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger"><i class="fas fa-trash-alt me-1"></i> Hapus</button>
                                    </form>
                                </td>
                                @endif
                            </tr>
                            @empty
                            <tr><td colspan="{{ $registration->status === 'Terverifikasi' ? 6 : 5 }}" class="text-center py-3 text-muted">Belum ada pemain</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="documentPreviewModal" tabindex="-1" aria-labelledby="documentPreviewTitle" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-xl modal-fullscreen-lg-down">
        <div class="modal-content border-0 shadow-lg overflow-hidden">
            <div class="modal-header py-2 px-3">
                <h5 class="modal-title fs-6 fw-bold" id="documentPreviewTitle">Pratinjau dokumen</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
            </div>
            <div class="modal-body p-0 position-relative" style="height:min(78vh, 800px); background:#e2e8f0;">
                <a id="documentPreviewDownload" href="#" download class="btn btn-primary btn-sm position-absolute top-0 end-0 m-3 shadow" style="z-index:2;" aria-label="Unduh dokumen">
                    <i class="fas fa-download me-1"></i> Unduh
                </a>
                <img id="documentPreviewImage" src="" alt="Pratinjau dokumen" class="d-none w-100 h-100 p-3" style="object-fit:contain;">
                <iframe id="documentPreviewFrame" src="" title="Pratinjau dokumen PDF" class="d-none w-100 h-100 border-0"></iframe>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const modalElement = document.getElementById('documentPreviewModal');
        const previewModal = new bootstrap.Modal(modalElement);
        const image = document.getElementById('documentPreviewImage');
        const frame = document.getElementById('documentPreviewFrame');
        const title = document.getElementById('documentPreviewTitle');
        const download = document.getElementById('documentPreviewDownload');

        document.querySelectorAll('.js-document-preview').forEach(function (button) {
            button.addEventListener('click', function () {
                const url = button.dataset.documentUrl;
                const path = new URL(url, window.location.href).pathname.toLowerCase();
                const isPdf = path.endsWith('.pdf');

                title.textContent = button.dataset.documentTitle || 'Pratinjau dokumen';
                download.href = url;
                image.classList.toggle('d-none', isPdf);
                frame.classList.toggle('d-none', !isPdf);
                image.removeAttribute('src');
                frame.removeAttribute('src');
                if (isPdf) frame.src = url;
                else image.src = url;
                previewModal.show();
            });
        });

        modalElement.addEventListener('hidden.bs.modal', function () {
            image.removeAttribute('src');
            frame.removeAttribute('src');
        });
    });
</script>
@endsection
