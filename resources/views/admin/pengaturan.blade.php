@extends('layouts.admin')
@section('page-title', 'Pengaturan Turnamen')

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-9">
        <div class="card border-0 card-karta">
            <div class="card-header border-0 bg-white py-3">
                <h5 class="oswald fw-bold mb-0" style="color:#0a1628;"><i class="fas fa-cog text-success me-2"></i> Pengaturan Turnamen</h5>
                <small class="text-muted">Konfigurasi umum turnamen yang tampil di halaman publik</small>
            </div>
            <div class="card-body p-4">
                <form method="POST" action="{{ route('admin.pengaturan.update') }}">
                    @csrf
                    <div class="row g-4">
                        <div class="col-md-8">
                            <label class="form-label fw-semibold small">Nama Turnamen <span class="text-danger">*</span></label>
                            <input type="text" name="name" class="form-control" value="{{ old('name', $settings->name) }}" required>
                            <div class="form-text text-muted">Nama resmi turnamen yang tampil di seluruh halaman.</div>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold small">Status Pendaftaran <span class="text-danger">*</span></label>
                            <select name="registration_status" class="form-select" required>
                                @foreach(['Buka','Tutup','Segera'] as $s)
                                <option value="{{ $s }}" {{ $settings->registration_status === $s ? 'selected' : '' }}>{{ $s }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold small">Jadwal Pendaftaran <span class="text-danger">*</span></label>
                            <input type="text" name="registration_schedule" class="form-control"
                                   value="{{ old('registration_schedule', $settings->registration_schedule) }}"
                                   placeholder="cth: 10 Oktober 2026 - 31 Oktober 2026" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold small">Tanggal Tutup Pendaftaran</label>
                            <input type="date" name="registration_closes_at" class="form-control"
                                   value="{{ old('registration_closes_at', $settings->registration_closes_at?->format('Y-m-d')) }}">
                            <div class="form-text text-muted">Pendaftaran otomatis ditutup mulai tanggal ini. Kosongkan jika tidak memakai tanggal penutupan otomatis.</div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold small">Tanggal Pelaksanaan Turnamen <span class="text-danger">*</span></label>
                            <input type="text" name="tournament_dates" class="form-control"
                                   value="{{ old('tournament_dates', $settings->tournament_dates) }}"
                                   placeholder="cth: 10 November - 05 Desember 2026" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold small">Lokasi Pertandingan <span class="text-danger">*</span></label>
                            <input type="text" name="location" class="form-control"
                                   value="{{ old('location', $settings->location) }}"
                                   placeholder="cth: Lapangan sepak bola Lodaya Bandung" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold small">Total Hadiah <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="fas fa-trophy text-warning"></i></span>
                                <input type="text" name="total_prize" class="form-control"
                                       value="{{ old('total_prize', $settings->total_prize) }}"
                                       placeholder="cth: Rp 50.000.000" required>
                            </div>
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-semibold small">Peraturan Turnamen</label>
                            <textarea name="rules" class="form-control" rows="6"
                                      placeholder="Tulis peraturan turnamen...">{{ old('rules', $settings->rules) }}</textarea>
                            <div class="form-text text-muted">Gunakan angka/baris baru untuk memisahkan setiap peraturan.</div>
                        </div>
                    </div>

                    <div class="d-flex gap-2 mt-4">
                        <button type="submit" class="btn btn-green fw-bold px-5">
                            <i class="fas fa-save me-2"></i> Simpan Pengaturan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
