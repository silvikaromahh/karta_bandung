<?php

namespace App\Http\Controllers\Peserta;

use App\Http\Controllers\Controller;
use App\Models\GameMatch;
use App\Models\News;
use App\Models\Notification;
use App\Models\Registration;
use App\Models\Team;
use App\Models\TournamentSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class PesertaController extends Controller
{
    public function dashboard()
    {
        $user = Auth::user();
        $registration = $user->registration;
        $settings = TournamentSetting::getSettings();
        $upcomingMatches = GameMatch::with(['homeTeam', 'awayTeam'])
            ->where('status', 'Mendatang')
            ->orderBy('match_date')
            ->take(3)
            ->get();
        $latestNews = News::where('is_published', true)
            ->orderByDesc('published_at')
            ->take(3)
            ->get();
        $unreadNotifications = Notification::where('user_id', $user->id)
            ->where('is_read', false)->count();

        return view('peserta.dashboard', compact(
            'user', 'registration', 'settings',
            'upcomingMatches', 'latestNews', 'unreadNotifications'
        ));
    }

    public function pendaftaran()
    {
        $user = Auth::user();
        $registration = $user->registration;
        $reg = $registration;
        return view('peserta.pendaftaran', compact('user', 'registration', 'reg'));
    }

    public function pendaftaranCetak()
    {
        $user = Auth::user();
        $registration = Registration::with(['user', 'verifier', 'members'])->where('user_id', $user->id)->firstOrFail();
        return view('admin.verifikasi-cetak', compact('registration'));
    }

    public function storePendaftaran(Request $request)
    {
        $user = Auth::user();

        // 1. Ambil pendaftaran milik user yang sedang login (hanya boleh mengedit pendaftaran miliknya sendiri)
        $reg = Registration::where('user_id', $user->id)->first();

        // 2. Jika user mencoba membuat pendaftaran kedua secara eksplisit padahal sudah memiliki pendaftaran
        if ($request->has('action') && $request->action === 'create_new' && $reg) {
            return back()->with('error', 'Anda sudah memiliki pendaftaran aktif. Silakan perbarui pendaftaran yang sudah ada.');
        }

        // 3. Hanya tolak jika pendaftaran tim sudah resmi Terverifikasi (resmi terkunci oleh panitia)
        if ($reg && $reg->status === 'Terverifikasi') {
            return back()->with('error', 'Pendaftaran tim Anda sudah terverifikasi dan terkunci. Hubungi panitia jika perlu perubahan data.');
        }

        // Sanitize NIK & KK dari spasi atau strip
        if ($request->has('pemain') && is_array($request->pemain)) {
            $pemainArr = $request->pemain;
            foreach ($pemainArr as $k => $v) {
                if (isset($v['nik'])) $pemainArr[$k]['nik'] = preg_replace('/[^0-9]/', '', (string)$v['nik']);
                if (isset($v['nomor_kk'])) $pemainArr[$k]['nomor_kk'] = preg_replace('/[^0-9]/', '', (string)$v['nomor_kk']);
            }
            $request->merge(['pemain' => $pemainArr]);
        }
        if ($request->has('official') && is_array($request->official)) {
            $officialArr = $request->official;
            foreach ($officialArr as $k => $v) {
                if (isset($v['nik'])) $officialArr[$k]['nik'] = preg_replace('/[^0-9]/', '', (string)$v['nik']);
            }
            $request->merge(['official' => $officialArr]);
        }

        $request->validate([
            'nama_tim'           => 'required|string|max:100',
            'surat_rekomendasi'  => ($reg && $reg->surat_rekomendasi_path) ? 'nullable|file|mimes:jpg,jpeg,png,pdf|max:2048' : 'required|file|mimes:jpg,jpeg,png,pdf|max:2048',
            'pemain'             => 'required|array|min:1|max:25',
            'pemain.*.full_name' => 'required|string|max:100',
            'pemain.*.nik'       => 'required|string|size:16',
            'pemain.*.nomor_kk'  => 'required|string|size:16',
            'pemain.*.tanggal_lahir' => 'required|date',
            'pemain.*.posisi'     => 'required|string|max:50',
            'pemain.*.nomor_punggung' => 'required|integer|min:1|max:99',
            'pemain.*.ktp'       => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:2048',
            'pemain.*.kk'        => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:2048',
            'pemain.*.surat_sehat' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:2048',
            'pemain.*.pasfoto'   => 'nullable|file|mimes:jpg,jpeg,png|max:2048',
            'official'           => 'nullable|array|max:5',
            'official.*.full_name' => 'required|string|max:100',
            'official.*.nik'       => 'required|string|size:16',
            'official.*.tanggal_lahir' => 'required|date',
            'official.*.jabatan'   => 'required|string|max:50',
            'official.*.ktp'       => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:2048',
            'official.*.pasfoto'   => 'nullable|file|mimes:jpg,jpeg,png|max:2048',
        ], [
            'nama_tim.required' => 'Nama tim / kecamatan wajib dipilih.',
            'surat_rekomendasi.required' => 'Surat rekomendasi (PDF/Gambar) wajib diunggah.',
            'surat_rekomendasi.mimes' => 'Surat rekomendasi harus berupa file PDF, JPG, JPEG, atau PNG.',
            'surat_rekomendasi.max' => 'Ukuran surat rekomendasi maksimal 2MB.',
            
            'pemain.required' => 'Minimal 1 pemain harus didaftarkan.',
            'pemain.min' => 'Minimal 1 pemain harus didaftarkan.',
            'pemain.max' => 'Maksimal 25 pemain yang dapat didaftarkan.',
            'pemain.*.full_name.required' => 'Nama lengkap pemain wajib diisi.',
            'pemain.*.nik.required' => 'NIK pemain wajib diisi.',
            'pemain.*.nik.size' => 'NIK pemain harus terdiri dari 16 digit angka.',
            'pemain.*.nomor_kk.required' => 'Nomor KK pemain wajib diisi.',
            'pemain.*.nomor_kk.size' => 'Nomor KK pemain harus terdiri dari 16 digit angka.',
            'pemain.*.tanggal_lahir.required' => 'Tanggal lahir pemain wajib diisi.',
            'pemain.*.posisi.required' => 'Posisi pemain wajib diisi.',
            'pemain.*.nomor_punggung.required' => 'Nomor punggung pemain wajib diisi.',
            'pemain.*.nomor_punggung.integer' => 'Nomor punggung pemain harus berupa angka.',
            'pemain.*.nomor_punggung.min' => 'Nomor punggung pemain minimal 1.',
            'pemain.*.nomor_punggung.max' => 'Nomor punggung pemain maksimal 99.',
            'pemain.*.ktp.mimes' => 'Foto KTP pemain harus berupa file JPG, JPEG, PNG, atau PDF.',
            'pemain.*.ktp.max' => 'Ukuran foto KTP pemain maksimal 2MB.',
            'pemain.*.kk.mimes' => 'Foto KK pemain harus berupa file JPG, JPEG, PNG, atau PDF.',
            'pemain.*.kk.max' => 'Ukuran foto KK pemain maksimal 2MB.',
            'pemain.*.surat_sehat.mimes' => 'Surat keterangan sehat pemain harus berupa file JPG, JPEG, PNG, atau PDF.',
            'pemain.*.surat_sehat.max' => 'Ukuran surat keterangan sehat pemain maksimal 2MB.',
            'pemain.*.pasfoto.mimes' => 'Pas foto pemain harus berupa file JPG, JPEG, atau PNG.',
            'pemain.*.pasfoto.max' => 'Ukuran pas foto pemain maksimal 2MB.',

            'official.max' => 'Maksimal 5 official yang dapat didaftarkan.',
            'official.*.full_name.required' => 'Nama lengkap official wajib diisi.',
            'official.*.nik.required' => 'NIK official wajib diisi.',
            'official.*.nik.size' => 'NIK official harus terdiri dari 16 digit angka.',
            'official.*.tanggal_lahir.required' => 'Tanggal lahir official wajib diisi.',
            'official.*.jabatan.required' => 'Jabatan official wajib diisi.',
            'official.*.ktp.mimes' => 'Foto KTP official harus berupa file JPG, JPEG, PNG, atau PDF.',
            'official.*.ktp.max' => 'Ukuran foto KTP official maksimal 2MB.',
            'official.*.pasfoto.mimes' => 'Pas foto official harus berupa file JPG, JPEG, atau PNG.',
            'official.*.pasfoto.max' => 'Ukuran pas foto official maksimal 2MB.',
        ]);

        $regData = [
            'nama_tim' => $request->nama_tim,
            'kecamatan' => $request->nama_tim,
            'status' => 'Menunggu Verifikasi',
            'rejection_reason' => null,
        ];

        if ($request->hasFile('surat_rekomendasi')) {
            if ($reg && $reg->surat_rekomendasi_path) {
                Storage::disk('public')->delete($reg->surat_rekomendasi_path);
            }
            $regData['surat_rekomendasi_path'] = $request->file('surat_rekomendasi')->store('registrations/rekomendasi', 'public');
        }

        // Pisahkan logika Create Registration Baru vs Update Registration yang Sudah Dimiliki User
        if (!$reg) {
            // === LOGIKA CREATE REGISTRATION BARU ===
            $regData['user_id'] = $user->id;
            $reg = Registration::create($regData);
        } else {
            // === LOGIKA UPDATE REGISTRATION YANG SUDAH DIMILIKI USER ===
            $reg->update($regData);
        }

        // Process Pemain
        $existingPemainIds = [];
        foreach ($request->pemain as $key => $pData) {
            $memberData = [
                'registration_id' => $reg->id,
                'tipe' => 'pemain',
                'full_name' => $pData['full_name'],
                'nik' => $pData['nik'],
                'nomor_kk' => $pData['nomor_kk'] ?? null,
                'tanggal_lahir' => $pData['tanggal_lahir'],
                'posisi' => $pData['posisi'],
                'nomor_punggung' => $pData['nomor_punggung'],
            ];

            if (isset($pData['id']) && $pData['id']) {
                $member = $reg->members()->find($pData['id']);
            } else {
                $member = null;
            }

            if (!$member) {
                $member = $reg->members()->make();
            }

            $ktpFile = $request->file("pemain.{$key}.ktp") ?? ($pData['ktp'] ?? null);
            if ($ktpFile && $ktpFile instanceof \Illuminate\Http\UploadedFile) {
                if ($member->ktp_path) Storage::disk('public')->delete($member->ktp_path);
                $memberData['ktp_path'] = $ktpFile->store('registrations/ktp', 'public');
            }

            $kkFile = $request->file("pemain.{$key}.kk") ?? ($pData['kk'] ?? null);
            if ($kkFile && $kkFile instanceof \Illuminate\Http\UploadedFile) {
                if ($member->kk_path) Storage::disk('public')->delete($member->kk_path);
                $memberData['kk_path'] = $kkFile->store('registrations/kk', 'public');
            }

            $suratSehatFile = $request->file("pemain.{$key}.surat_sehat") ?? ($pData['surat_sehat'] ?? null);
            if ($suratSehatFile && $suratSehatFile instanceof \Illuminate\Http\UploadedFile) {
                if ($member->surat_sehat_path) Storage::disk('public')->delete($member->surat_sehat_path);
                $memberData['surat_sehat_path'] = $suratSehatFile->store('registrations/surat_sehat', 'public');
            }

            $pasfotoFile = $request->file("pemain.{$key}.pasfoto") ?? ($pData['pasfoto'] ?? null);
            if ($pasfotoFile && $pasfotoFile instanceof \Illuminate\Http\UploadedFile) {
                if ($member->pasfoto_path) Storage::disk('public')->delete($member->pasfoto_path);
                $memberData['pasfoto_path'] = $pasfotoFile->store('registrations/pasfoto', 'public');
            }

            $member->fill($memberData);
            $member->save();
            $existingPemainIds[] = $member->id;
        }

        // Process Official
        $existingOfficialIds = [];
        if ($request->has('official') && is_array($request->official)) {
            foreach ($request->official as $key => $oData) {
                $memberData = [
                    'registration_id' => $reg->id,
                    'tipe' => 'official',
                    'full_name' => $oData['full_name'],
                    'nik' => $oData['nik'],
                    'tanggal_lahir' => $oData['tanggal_lahir'],
                    'jabatan' => $oData['jabatan'],
                ];

                if (isset($oData['id']) && $oData['id']) {
                    $member = $reg->members()->find($oData['id']);
                } else {
                    $member = null;
                }

                if (!$member) {
                    $member = $reg->members()->make();
                }

                $ktpFile = $request->file("official.{$key}.ktp") ?? ($oData['ktp'] ?? null);
                if ($ktpFile && $ktpFile instanceof \Illuminate\Http\UploadedFile) {
                    if ($member->ktp_path) Storage::disk('public')->delete($member->ktp_path);
                    $memberData['ktp_path'] = $ktpFile->store('registrations/ktp', 'public');
                }

                $pasfotoFile = $request->file("official.{$key}.pasfoto") ?? ($oData['pasfoto'] ?? null);
                if ($pasfotoFile && $pasfotoFile instanceof \Illuminate\Http\UploadedFile) {
                    if ($member->pasfoto_path) Storage::disk('public')->delete($member->pasfoto_path);
                    $memberData['pasfoto_path'] = $pasfotoFile->store('registrations/pasfoto', 'public');
                }

                $member->fill($memberData);
                $member->save();
                $existingOfficialIds[] = $member->id;
            }
        }

        // Cleanup removed members
        $allValidIds = array_merge($existingPemainIds, $existingOfficialIds);
        $membersToDelete = $reg->members()->whereNotIn('id', $allValidIds)->get();
        foreach ($membersToDelete as $m) {
            if ($m->ktp_path) Storage::disk('public')->delete($m->ktp_path);
            if ($m->kk_path) Storage::disk('public')->delete($m->kk_path);
            if ($m->surat_sehat_path) Storage::disk('public')->delete($m->surat_sehat_path);
            if ($m->pasfoto_path) Storage::disk('public')->delete($m->pasfoto_path);
            $m->delete();
        }

        $totalPemain = count($existingPemainIds);
        $totalOfficial = count($existingOfficialIds);

        $statusMsg = ($totalPemain >= 25 && $totalOfficial >= 5) 
            ? "Data peserta dan official sudah maksimal." 
            : "Anda dapat terus menambahkan data berikutnya.";

        return back()->with('success', "Data pendaftaran tim berhasil disimpan! (Tersimpan: {$totalPemain}/25 Peserta, {$totalOfficial}/5 Official). {$statusMsg}");
    }

    public function profil()
    {
        $user = Auth::user();
        return view('peserta.profil', compact('user'));
    }

    public function updateProfil(Request $request)
    {
        $user = Auth::user();
        $request->validate([
            'name' => 'required|string|max:100',
            'phone' => 'nullable|string|max:20',
        ]);
        $user->update($request->only('name', 'phone'));
        return back()->with('success', 'Profil berhasil diperbarui.');
    }

    public function jadwal()
    {
        $matches = GameMatch::with(['homeTeam', 'awayTeam'])
            ->orderBy('match_date')
            ->get();
        return view('peserta.jadwal', compact('matches'));
    }

    public function hasilKlasemen()
    {
        $finishedMatches = GameMatch::with(['homeTeam', 'awayTeam'])
            ->where('status', 'Selesai')
            ->orderByDesc('match_date')
            ->get();
        $standings = Team::orderByDesc('points')
            ->orderByDesc('goal_difference')
            ->get()
            ->groupBy('group_name');
        return view('peserta.hasil-klasemen', compact('finishedMatches', 'standings'));
    }

    public function pengumuman()
    {
        $news = News::where('is_published', true)
            ->orderByDesc('published_at')
            ->paginate(10);
        return view('peserta.pengumuman', compact('news'));
    }

    public function notifikasi()
    {
        $user = Auth::user();
        $notifications = Notification::where('user_id', $user->id)
            ->orderByDesc('created_at')
            ->paginate(15);
        return view('peserta.notifikasi', compact('notifications'));
    }

    public function bacaSemua()
    {
        Notification::where('user_id', Auth::id())->where('is_read', false)->update(['is_read' => true]);
        return back()->with('success', 'Semua notifikasi telah ditandai sebagai dibaca.');
    }

    public function destroyNotifikasi($id)
    {
        Notification::where('user_id', Auth::id())->where('id', $id)->delete();
        return back()->with('success', 'Notifikasi berhasil dihapus.');
    }

    public function destroyAllNotifikasi()
    {
        Notification::where('user_id', Auth::id())->delete();
        return back()->with('success', 'Semua notifikasi berhasil dihapus.');
    }

    public function pengaturan()
    {
        $user = Auth::user();
        return view('peserta.pengaturan', compact('user'));
    }

    public function updatePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required',
            'password' => 'required|min:8|confirmed',
        ], [
            'current_password.required' => 'Kata sandi saat ini wajib diisi.',
            'password.required' => 'Kata sandi baru wajib diisi.',
            'password.min' => 'Kata sandi baru minimal 8 karakter.',
            'password.confirmed' => 'Konfirmasi kata sandi tidak cocok.',
        ]);

        if (!Hash::check($request->current_password, Auth::user()->password)) {
            return back()->withErrors(['current_password' => 'Kata sandi saat ini tidak sesuai.']);
        }

        Auth::user()->update(['password' => Hash::make($request->password)]);
        return back()->with('success', 'Kata sandi berhasil diubah.');
    }
}
