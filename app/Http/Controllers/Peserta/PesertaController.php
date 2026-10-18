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

        if ($user->registration && in_array($user->registration->status, ['Menunggu Verifikasi', 'Terverifikasi'])) {
            return back()->with('error', 'Anda sudah memiliki pendaftaran yang aktif.');
        }

        $reg = $user->registration;

        $pemainSehatRules = [];
        foreach ((array) $request->input('pemain', []) as $index => $pemainInput) {
            $member = isset($pemainInput['id']) && $reg
                ? $reg->members()->where('tipe', 'pemain')->find($pemainInput['id'])
                : null;
            $pemainSehatRules["pemain.{$index}.surat_sehat"] = ($member && $member->surat_sehat_path)
                ? 'nullable|image|mimes:jpg,jpeg,png|max:2048'
                : 'required|image|mimes:jpg,jpeg,png|max:2048';
        }

        $request->validate(array_merge([
            'nama_tim'           => 'required|string|max:100',
            'surat_rekomendasi'  => ($reg && $reg->surat_rekomendasi_path) ? 'nullable|file|mimes:jpg,jpeg,png,pdf|max:2048' : 'required|file|mimes:jpg,jpeg,png,pdf|max:2048',
            'pemain'             => 'required|array|min:1|max:25',
            'pemain.*.full_name' => 'required|string|max:100',
            'pemain.*.nik'       => 'required|string|size:16',
            'pemain.*.nomor_kk'  => 'required|string|digits:16',
            'pemain.*.tanggal_lahir' => 'required|date',
            'pemain.*.posisi'     => 'required|string|max:50',
            'pemain.*.nomor_punggung' => 'required|integer|min:1|max:99',
            'pemain.*.ktp'       => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:2048',
            'pemain.*.kk'        => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:2048',
            'pemain.*.pasfoto'   => 'nullable|file|mimes:jpg,jpeg,png|max:2048',
            'pemain.*.surat_sehat' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
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
            'pemain.*.nomor_kk.digits' => 'Nomor KK pemain harus terdiri dari 16 digit angka.',
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
            'pemain.*.pasfoto.mimes' => 'Pas foto pemain harus berupa file JPG, JPEG, atau PNG.',
            'pemain.*.pasfoto.max' => 'Ukuran pas foto pemain maksimal 2MB.',
            'pemain.*.surat_sehat.required' => 'Surat keterangan sehat pemain wajib diunggah.',
            'pemain.*.surat_sehat.image' => 'Surat keterangan sehat harus berupa gambar.',
            'pemain.*.surat_sehat.mimes' => 'Surat keterangan sehat harus berupa JPG, JPEG, atau PNG.',
            'pemain.*.surat_sehat.max' => 'Ukuran surat keterangan sehat maksimal 2MB.',

            'official.*.full_name.required' => 'Nama lengkap official wajib diisi.',
            'official.*.nik.required' => 'NIK official wajib diisi.',
            'official.*.nik.size' => 'NIK official harus terdiri dari 16 digit angka.',
            'official.*.tanggal_lahir.required' => 'Tanggal lahir official wajib diisi.',
            'official.*.jabatan.required' => 'Jabatan official wajib diisi.',
            'official.*.ktp.mimes' => 'Foto KTP official harus berupa file JPG, JPEG, PNG, atau PDF.',
            'official.*.ktp.max' => 'Ukuran foto KTP official maksimal 2MB.',
            'official.*.pasfoto.mimes' => 'Pas foto official harus berupa file JPG, JPEG, atau PNG.',
            'official.*.pasfoto.max' => 'Ukuran pas foto official maksimal 2MB.',
        ], $pemainSehatRules));

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

        if (!$reg) {
            $regData['user_id'] = $user->id;
            $reg = Registration::create($regData);
        } else {
            $reg->update($regData);
        }

        // Process Pemain
        $existingPemainIds = [];
        foreach ($request->pemain as $pData) {
            $memberData = [
                'tipe' => 'pemain',
                'full_name' => $pData['full_name'],
                'nik' => $pData['nik'],
                'nomor_kk' => $pData['nomor_kk'],
                'tanggal_lahir' => $pData['tanggal_lahir'],
                'posisi' => $pData['posisi'],
                'nomor_punggung' => $pData['nomor_punggung'],
            ];

            if (isset($pData['id'])) {
                $member = $reg->members()->find($pData['id']);
            } else {
                $member = $reg->members()->make();
            }

            if (isset($pData['ktp'])) {
                if ($member->ktp_path) Storage::disk('public')->delete($member->ktp_path);
                $memberData['ktp_path'] = $pData['ktp']->store('registrations/ktp', 'public');
            }
            if (isset($pData['kk'])) {
                if ($member->kk_path) Storage::disk('public')->delete($member->kk_path);
                $memberData['kk_path'] = $pData['kk']->store('registrations/kk', 'public');
            }
            if (isset($pData['pasfoto'])) {
                if ($member->pasfoto_path) Storage::disk('public')->delete($member->pasfoto_path);
                $memberData['pasfoto_path'] = $pData['pasfoto']->store('registrations/pasfoto', 'public');
            }
            if (isset($pData['surat_sehat'])) {
                if ($member->surat_sehat_path) Storage::disk('public')->delete($member->surat_sehat_path);
                $memberData['surat_sehat_path'] = $pData['surat_sehat']->store('registrations/surat-sehat', 'public');
            }

            $member->fill($memberData);
            $member->save();
            $existingPemainIds[] = $member->id;
        }

        // Process Official
        $existingOfficialIds = [];
        if ($request->has('official') && is_array($request->official)) {
            foreach ($request->official as $oData) {
                $memberData = [
                    'tipe' => 'official',
                    'full_name' => $oData['full_name'],
                    'nik' => $oData['nik'],
                    'tanggal_lahir' => $oData['tanggal_lahir'],
                    'jabatan' => $oData['jabatan'],
                ];

                if (isset($oData['id'])) {
                    $member = $reg->members()->find($oData['id']);
                } else {
                    $member = $reg->members()->make();
                }

                if (isset($oData['ktp'])) {
                    if ($member->ktp_path) Storage::disk('public')->delete($member->ktp_path);
                    $memberData['ktp_path'] = $oData['ktp']->store('registrations/ktp', 'public');
                }
                if (isset($oData['pasfoto'])) {
                    if ($member->pasfoto_path) Storage::disk('public')->delete($member->pasfoto_path);
                    $memberData['pasfoto_path'] = $oData['pasfoto']->store('registrations/pasfoto', 'public');
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
            if ($m->pasfoto_path) Storage::disk('public')->delete($m->pasfoto_path);
            if ($m->surat_sehat_path) Storage::disk('public')->delete($m->surat_sehat_path);
            $m->delete();
        }

        return back()->with('success', 'Pendaftaran tim berhasil dikirim! Mohon tunggu proses verifikasi oleh panitia.');
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
