@extends('layouts.public')
@section('title', 'Masuk – KARTA.BDG Cup 2026')

@section('content')
<div style="background:linear-gradient(135deg,#0a1628 0%,#1a3356 100%);min-height:100vh;display:flex;align-items:center;padding:100px 0 60px;">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-10 col-lg-8">
                <div class="row g-0 rounded-4 overflow-hidden shadow-lg">

                    <!-- LEFT PANEL -->
                    <div class="col-md-5 d-none d-md-flex flex-column justify-content-between p-5"
                         style="background:linear-gradient(160deg,#1a9b52,#0a1628);">
                        <div>
                            <div class="d-flex align-items-center gap-3 mb-2">
                                <img src="{{ asset('images/logo.png') }}" alt="Logo KARTA.BDG" style="height: 48px; width: auto;">
                                <span class="oswald fs-3 fw-bold text-white">KARTA.BDG</span>
                            </div>
                            <p class="text-white-50 small">Men's Football Cup 2026</p>
                        </div>
                        <div>
                            <h3 class="oswald text-white fw-bold mb-3">Selamat Datang Kembali!</h3>
                            <p class="text-white-50 small mb-4">Masuk ke akun Anda untuk mengakses dashboard, memantau jadwal pertandingan, dan melihat status pendaftaran.</p>
                            <div class="d-flex flex-column gap-2">
                                <div class="d-flex align-items-center gap-2 text-white-50 small">
                                    <i class="fas fa-check-circle text-success"></i> Pantau jadwal pertandingan
                                </div>
                                <div class="d-flex align-items-center gap-2 text-white-50 small">
                                    <i class="fas fa-check-circle text-success"></i> Cek status pendaftaran
                                </div>
                                <div class="d-flex align-items-center gap-2 text-white-50 small">
                                    <i class="fas fa-check-circle text-success"></i> Terima notifikasi terbaru
                                </div>
                            </div>
                        </div>
                        <div>
                            <p class="text-white-50 small mb-0"><i class="fas fa-shield-alt text-success me-1"></i> Pendaftaran Resmi Kontingen</p>
                        </div>
                    </div>

                    <!-- RIGHT PANEL -->
                    <div class="col-md-7 bg-white p-5">
                        <h4 class="oswald fw-bold mb-1" style="color:#0a1628;">Masuk Akun Resmi</h4>
                        <p class="text-muted small mb-4">Silakan masuk menggunakan akun resmi Kecamatan / Admin.</p>

                        @if(session('warning'))
                        <div class="alert alert-warning d-flex align-items-center gap-2 py-2 small">
                            <i class="fas fa-exclamation-triangle"></i> {{ session('warning') }}
                        </div>
                        @endif

                        @if($errors->any())
                        <div class="alert alert-danger d-flex align-items-center gap-2 py-2">
                            <i class="fas fa-exclamation-circle"></i>
                            <div>{{ $errors->first() }}</div>
                        </div>
                        @endif

                        @if(session('success'))
                        <div class="alert alert-success d-flex align-items-center gap-2 py-2">
                            <i class="fas fa-check-circle"></i> {{ session('success') }}
                        </div>
                        @endif

                        <form method="POST" action="{{ route('login.post') }}">
                            @csrf
                            <div class="mb-3">
                                <label class="form-label fw-semibold small">Alamat Email <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="fas fa-envelope text-muted"></i></span>
                                    <input type="email" name="email" class="form-control @error('email') is-invalid @enderror"
                                           placeholder="kecamatan@karta-bdg.id" value="{{ old('email') }}" required autocomplete="email">
                                </div>
                            </div>

                            <div class="mb-3">
                                <label class="form-label fw-semibold small">Kata Sandi <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="fas fa-lock text-muted"></i></span>
                                    <input type="password" name="password" id="pwInput" class="form-control" placeholder="Masukkan kata sandi" required>
                                    <button type="button" class="input-group-text bg-white" onclick="togglePw()">
                                        <i class="fas fa-eye text-muted" id="eyeIcon"></i>
                                    </button>
                                </div>
                            </div>

                            <div class="d-flex align-items-center justify-content-between mb-4">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="remember" id="remember">
                                    <label class="form-check-label small" for="remember">Ingat saya</label>
                                </div>
                            </div>

                            <button type="submit" class="btn btn-green w-100 fw-bold py-2">
                                <i class="fas fa-sign-in-alt me-2"></i> Masuk Sekarang
                            </button>
                        </form>

                        {{--
			 <div class="mt-4 p-3 rounded-3 small" style="background:#f0fdf4;border:1px solid #bbf7d0;">
                            <div class="mb-1">
                                <i class="fas fa-info-circle text-success me-1"></i>
                                <strong>Akun Kecamatan:</strong> Masukkan email <code>[nama-kecamatan]@karta-bdg.id</code>
                            </div>
                            <div>
                                <i class="fas fa-key text-success me-1"></i>
                                <strong>Kata Sandi:</strong> <code>12345678</code>
                            </div>
                        </div> 
			--}}
                    </div>

                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
function togglePw() {
    const i = document.getElementById('pwInput');
    const e = document.getElementById('eyeIcon');
    if (i.type === 'password') { i.type = 'text'; e.className = 'fas fa-eye-slash text-muted'; }
    else { i.type = 'password'; e.className = 'fas fa-eye text-muted'; }
}
</script>
@endpush
@endsection
