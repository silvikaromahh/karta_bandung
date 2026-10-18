@extends('layouts.app')
@section('title', 'Peserta — KARTA.BDG Cup 2026')
@section('body-style', 'background:#f1f5f9;')
@section('layout')
<div class="d-flex">

    <!-- SIDEBAR -->
    <aside class="peserta-sidebar" id="pesertaSidebar">
        <div class="sidebar-logo d-flex align-items-center gap-3">
            <img src="{{ asset('images/logo.png') }}" alt="Logo" style="height: 44px; width: auto;">
            <div>
                <div class="oswald fs-5">KARTA.BDG</div>
                <small style="font-family:'Inter',sans-serif;font-size:0.62rem;font-weight:400;color:rgba(255,255,255,0.4);letter-spacing:0.1em;text-transform:uppercase;">Dashboard Peserta</small>
            </div>
        </div>

        @php $reg = auth()->user()->registration; @endphp

        <nav class="py-2">
            <a href="{{ route('peserta.dashboard') }}" class="nav-link {{ request()->routeIs('peserta.dashboard') ? 'active' : '' }}">
                <i class="fas fa-home"></i> Dashboard
            </a>
            <a href="{{ route('peserta.pendaftaran') }}" class="nav-link {{ request()->routeIs('peserta.pendaftaran') ? 'active' : '' }}">
                <i class="fas fa-file-alt"></i> Pendaftaran Saya
                @if($reg && $reg->status === 'Ditolak')
                    <span class="badge bg-danger ms-auto" style="font-size:0.6rem;">!</span>
                @endif
            </a>
            <a href="{{ route('peserta.profil') }}" class="nav-link {{ request()->routeIs('peserta.profil') ? 'active' : '' }}">
                <i class="fas fa-user"></i> Profil Saya
            </a>
            <div style="height:1px;background:rgba(255,255,255,0.08);margin:8px 18px;"></div>
            <a href="{{ route('peserta.jadwal') }}" class="nav-link {{ request()->routeIs('peserta.jadwal') ? 'active' : '' }}">
                <i class="fas fa-calendar-alt"></i> Jadwal Pertandingan
            </a>
            <a href="{{ route('peserta.hasil-klasemen') }}" class="nav-link {{ request()->routeIs('peserta.hasil-klasemen') ? 'active' : '' }}">
                <i class="fas fa-trophy"></i> Hasil & Klasemen
            </a>
            <a href="{{ route('peserta.pengumuman') }}" class="nav-link {{ request()->routeIs('peserta.pengumuman') ? 'active' : '' }}">
                <i class="fas fa-bullhorn"></i> Pengumuman
            </a>
            <a href="{{ route('peserta.notifikasi') }}" class="nav-link {{ request()->routeIs('peserta.notifikasi') ? 'active' : '' }}">
                <i class="fas fa-bell"></i> Notifikasi
                @php $unread = auth()->user()->notifications()->where('is_read', false)->count(); @endphp
                @if($unread > 0)
                    <span class="badge bg-danger ms-auto rounded-pill" style="font-size:0.65rem;">{{ $unread }}</span>
                @endif
            </a>
            <div style="height:1px;background:rgba(255,255,255,0.08);margin:8px 18px;"></div>
            <a href="{{ route('peserta.pengaturan') }}" class="nav-link {{ request()->routeIs('peserta.pengaturan') ? 'active' : '' }}">
                <i class="fas fa-sliders-h"></i> Pengaturan Akun
            </a>
        </nav>

        <div class="sidebar-bottom" style="padding:14px;border-top:1px solid rgba(255,255,255,0.08);">
            <div class="d-flex align-items-center gap-2 mb-2 px-2">
                <div class="admin-avatar" style="background:#1a9b52;font-size:0.8rem;">{{ strtoupper(substr(auth()->user()->name,0,1)) }}</div>
                <div class="lh-sm">
                    <div class="text-white fw-semibold" style="font-size:0.8rem;">{{ Str::limit(auth()->user()->name, 18) }}</div>
                    <div style="font-size:0.65rem;color:rgba(255,255,255,0.4);">Peserta</div>
                </div>
            </div>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="nav-link border-0 bg-transparent w-100 text-start mt-1" style="color:rgba(255,255,255,0.5);">
                    <i class="fas fa-sign-out-alt"></i> Keluar
                </button>
            </form>
        </div>
    </aside>

    <div id="sidebarOverlay2" class="d-none" style="position:fixed;inset:0;background:rgba(0,0,0,0.5);z-index:1040;" onclick="toggleSidebar()"></div>

    <!-- MAIN -->
    <div class="flex-grow-1 d-flex flex-column" style="min-width:0;min-height:100vh;">
        <header class="admin-topbar d-flex align-items-center justify-content-between">
            <div class="d-flex align-items-center gap-3">
                <button onclick="toggleSidebar()" class="btn btn-sm btn-light d-lg-none">
                    <i class="fas fa-bars"></i>
                </button>
                <span class="fw-bold" style="color:#0a1628;font-size:1rem;">@yield('page-title', 'Dashboard')</span>
            </div>
            <div class="d-flex align-items-center gap-2">
                <a href="{{ route('beranda') }}" target="_blank" class="btn btn-outline-secondary btn-sm">
                    <i class="fas fa-globe me-1"></i> Website
                </a>
            </div>
        </header>

        <div class="p-4 flex-grow-1">
            @if(session('success'))
                <div class="alert alert-success alert-dismissible d-flex align-items-center gap-2 fade show">
                    <i class="fas fa-check-circle"></i> {{ session('success') }}
                    <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert"></button>
                </div>
            @endif
            @if(session('error'))
                <div class="alert alert-danger alert-dismissible d-flex align-items-center gap-2 fade show">
                    <i class="fas fa-exclamation-circle"></i> {{ session('error') }}
                    <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @yield('content')
        </div>
    </div>
</div>

@push('scripts')
<script>
function toggleSidebar() {
    const s = document.getElementById('pesertaSidebar');
    const o = document.getElementById('sidebarOverlay2');
    s.classList.toggle('show');
    o.classList.toggle('d-none');
}
</script>
@endpush
@endsection
