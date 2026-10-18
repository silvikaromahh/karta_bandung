@extends('layouts.app')
@section('title', 'Admin — KARTA.BDG Cup 2026')
@section('body-style', 'background:#f1f5f9;')
@section('layout')
<div class="d-flex">

    <!-- SIDEBAR -->
    <aside class="admin-sidebar" id="adminSidebar">
        <div class="sidebar-logo d-flex align-items-center gap-3">
            <img src="{{ asset('images/logo.png') }}" alt="Logo" style="height: 44px; width: auto;">
            <div>
                <div>KARTA.BDG</div>
                <small>Panel Admin • Cup 2026</small>
            </div>
        </div>

        <nav class="py-2">
            <div class="nav-section">Utama</div>
            <a href="{{ route('admin.dashboard') }}" class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                <i class="fas fa-tachometer-alt"></i> Dashboard
            </a>

            <div class="nav-section">Peserta</div>
            <a href="{{ route('admin.pendaftar') }}" class="nav-link {{ request()->routeIs('admin.pendaftar') ? 'active' : '' }}">
                <i class="fas fa-users"></i> Data Pendaftar
            </a>
            <a href="{{ route('admin.verifikasi') }}" class="nav-link {{ request()->routeIs('admin.verifikasi*') ? 'active' : '' }}">
                <i class="fas fa-user-check"></i> Verifikasi Peserta
            </a>

            <div class="nav-section">Turnamen</div>
            <a href="{{ route('admin.tim') }}" class="nav-link {{ request()->routeIs('admin.tim*') ? 'active' : '' }}">
                <i class="fas fa-shield-alt"></i> Kelola Tim
            </a>
            <a href="{{ route('admin.pertandingan') }}" class="nav-link {{ request()->routeIs('admin.pertandingan*') ? 'active' : '' }}">
                <i class="fas fa-futbol"></i> Pertandingan
            </a>
            <a href="{{ route('admin.hasil-klasemen') }}" class="nav-link {{ request()->routeIs('admin.hasil-klasemen') ? 'active' : '' }}">
                <i class="fas fa-trophy"></i> Hasil & Klasemen
            </a>

            <div class="nav-section">Konten</div>
            <a href="{{ route('admin.berita') }}" class="nav-link {{ request()->routeIs('admin.berita*') ? 'active' : '' }}">
                <i class="fas fa-newspaper"></i> Berita & Pengumuman
            </a>

            <div class="nav-section">Sistem</div>
            <a href="{{ route('admin.pengaturan') }}" class="nav-link {{ request()->routeIs('admin.pengaturan') ? 'active' : '' }}">
                <i class="fas fa-cog"></i> Pengaturan Turnamen
            </a>
        </nav>

        <div class="sidebar-bottom" style="padding:14px;border-top:1px solid rgba(255,255,255,0.08);">
            <div class="d-flex align-items-center gap-2 mb-2 px-2">
                <div class="admin-avatar" style="background:#3b82f6;font-size:0.8rem;">{{ strtoupper(substr(auth()->user()->name, 0, 1)) }}</div>
                <div class="lh-sm">
                    <div class="text-white fw-semibold" style="font-size:0.8rem;">{{ Str::limit(auth()->user()->name, 18) }}</div>
                    <div style="font-size:0.65rem;color:rgba(255,255,255,0.4);">Administrator</div>
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

    <!-- OVERLAY for mobile -->
    <div id="sidebarOverlay" class="d-none" style="position:fixed;inset:0;background:rgba(0,0,0,0.5);z-index:1040;" onclick="toggleSidebar()"></div>

    <!-- MAIN -->
    <div class="flex-grow-1 d-flex flex-column" style="min-width:0;min-height:100vh;">
        <header class="admin-topbar d-flex align-items-center justify-content-between">
            <div class="d-flex align-items-center gap-3">
                <button onclick="toggleSidebar()" class="btn btn-sm btn-light d-lg-none" title="Toggle Sidebar">
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
    const s = document.getElementById('adminSidebar');
    const o = document.getElementById('sidebarOverlay');
    s.classList.toggle('show');
    o.classList.toggle('d-none');
}
</script>
@endpush
@endsection
