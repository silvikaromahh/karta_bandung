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
        $settings = TournamentSetting::getSettings();
        $registrationClosed = $settings->isRegistrationClosed();
        return view('peserta.pendaftaran', compact('user', 'registration', 'reg', 'settings', 'registrationClosed'));
    }

    public function pendaftaranCetak()
    {
        $user = Auth::user();
        $registration = Registration::with(['user', 'verifier', 'members'])->where('user_id', $user->id)->firstOrFail();
        return view('admin.verifikasi-cetak', compact('registration'));
    }

    public function savePendaftaranDraft(Request $request)
    {
        $this->ensureRegistrationOpen();
        $user = Auth::user();
        $reg = Registration::where('user_id', $user->id)->first();
        abort_if($reg && in_array($reg->status, ['Menunggu Verifikasi', 'Terverifikasi'], true), 403, 'Pendaftaran sudah diajukan dan sedang dikunci.');

        $request->validate([
            'nama_tim' => 'required|string|max:100',
            'surat_rekomendasi' => [($reg && $reg->surat_rekomendasi_path) ? 'nullable' : 'required', 'file', 'mimes:jpg,jpeg,png,pdf', 'max:2048'],
        ]);

        if (!$reg) {
            $reg = Registration::create([
                'user_id' => $user->id,
                'nama_tim' => $request->nama_tim,
                'kecamatan' => $request->nama_tim,
                'status' => 'Draf',
            ]);
        } else {
            $reg->update(['nama_tim' => $request->nama_tim, 'kecamatan' => $request->nama_tim, 'status' => 'Draf', 'rejection_reason' => null]);
        }

        if ($request->hasFile('surat_rekomendasi')) {
            $oldPath = $reg->surat_rekomendasi_path;
            $newPath = $request->file('surat_rekomendasi')->store('registrations/rekomendasi', 'public');
            $reg->update(['surat_rekomendasi_path' => $newPath]);
            if ($oldPath) Storage::disk('public')->delete($oldPath);
        }

        return response()->json(['ok' => true, 'registration_id' => $reg->id]);
    }

    public function savePendaftaranMember(Request $request)
    {
        $this->ensureRegistrationOpen();
        $reg = Registration::where('user_id', Auth::id())->firstOrFail();
        abort_if(in_array($reg->status, ['Menunggu Verifikasi', 'Terverifikasi'], true), 403, 'Pendaftaran sudah diajukan dan sedang dikunci.');

        $tipe = $request->input('tipe');
        abort_unless(in_array($tipe, ['pemain', 'official'], true), 422);
        $existing = $request->filled('id') ? $reg->members()->findOrFail($request->integer('id')) : null;
        abort_if($existing && $existing->tipe !== $tipe, 422);
        $limit = $tipe === 'pemain' ? 25 : 5;
        abort_if(!$existing && $reg->members()->where('tipe', $tipe)->count() >= $limit, 422, "Maksimal {$limit} {$tipe}.");

        $rules = [
            'full_name' => 'required|string|max:100',
            'nik' => 'required|string|size:16',
            'tanggal_lahir' => 'required|date',
        ];
        if ($tipe === 'pemain') {
            $rules += [
                'nomor_kk' => 'required|string|size:16',
                'posisi' => 'required|string|max:50',
                'nomor_punggung' => 'required|integer|min:1|max:99',
            ];
        } else {
            $rules['jabatan'] = 'required|string|max:50';
        }
        $validated = $request->validate($rules);
        $validated['nik'] = preg_replace('/\D/', '', $validated['nik']);
        if (isset($validated['nomor_kk'])) $validated['nomor_kk'] = preg_replace('/\D/', '', $validated['nomor_kk']);

        $member = $existing ?: $reg->members()->make(['tipe' => $tipe]);
        $member->fill($validated);
        $member->tipe = $tipe;
        $member->save();

        return response()->json(['ok' => true, 'id' => $member->id]);
    }

    public function uploadPendaftaranMemberFile(Request $request)
    {
        $this->ensureRegistrationOpen();
        $reg = Registration::where('user_id', Auth::id())->firstOrFail();
        abort_if(in_array($reg->status, ['Menunggu Verifikasi', 'Terverifikasi'], true), 403);
        $request->validate(['member_id' => 'required|integer', 'field' => 'required|string', 'file' => 'required|file|max:2048']);
        $member = $reg->members()->findOrFail($request->integer('member_id'));
        $paths = $member->tipe === 'pemain'
            ? ['pasfoto' => 'pasfoto_path', 'ktp' => 'ktp_path', 'kk' => 'kk_path', 'surat_sehat' => 'surat_sehat_path']
            : ['pasfoto' => 'pasfoto_path', 'ktp' => 'ktp_path'];
        abort_unless(isset($paths[$request->field]), 422);
        $allowedMimes = $request->field === 'pasfoto' ? 'mimes:jpg,jpeg,png' : 'mimes:jpg,jpeg,png,pdf';
        $request->validate(['file' => ['required', 'file', $allowedMimes, 'max:2048']]);
        $column = $paths[$request->field];
        $oldPath = $member->{$column};
        $newPath = $request->file('file')->store("registrations/{$request->field}", 'public');
        $member->update([$column => $newPath]);
        if ($oldPath) Storage::disk('public')->delete($oldPath);
        return response()->json(['ok' => true]);
    }

    public function submitPendaftaran(Request $request)
    {
        $this->ensureRegistrationOpen();
        $reg = Registration::with('members')->where('user_id', Auth::id())->firstOrFail();
        abort_if(in_array($reg->status, ['Menunggu Verifikasi', 'Terverifikasi'], true), 403, 'Pendaftaran sudah diajukan.');
        $request->validate(['confirmation' => 'accepted']);

        $errors = [];
        if (!$reg->nama_tim || !$reg->surat_rekomendasi_path) $errors[] = 'Data tim dan surat rekomendasi wajib disimpan.';
        $players = $reg->members->where('tipe', 'pemain');
        if ($players->isEmpty()) $errors[] = 'Minimal satu pemain harus disimpan.';
        if ($players->count() > 25 || $reg->members->where('tipe', 'official')->count() > 5) $errors[] = 'Jumlah pemain atau official melewati batas.';
        foreach ($players as $player) {
            foreach (['ktp_path', 'kk_path', 'pasfoto_path', 'surat_sehat_path'] as $field) if (!$player->{$field}) $errors[] = "Dokumen pemain {$player->full_name} belum lengkap.";
        }
        foreach ($reg->members->where('tipe', 'official') as $official) {
            foreach (['ktp_path', 'pasfoto_path'] as $field) if (!$official->{$field}) $errors[] = "Dokumen official {$official->full_name} belum lengkap.";
        }
        if ($errors) return response()->json(['message' => implode(' ', array_unique($errors))], 422);

        $reg->update(['status' => 'Menunggu Verifikasi', 'rejection_reason' => null]);
        return response()->json(['ok' => true, 'redirect' => route('peserta.pendaftaran')]);
    }

    public function deletePendaftaranMember($memberId)
    {
        $this->ensureRegistrationOpen();
        $reg = Registration::where('user_id', Auth::id())->firstOrFail();
        abort_if(in_array($reg->status, ['Menunggu Verifikasi', 'Terverifikasi'], true), 403);
        $member = $reg->members()->findOrFail($memberId);
        foreach (['ktp_path', 'kk_path', 'surat_sehat_path', 'pasfoto_path'] as $field) {
            if ($member->{$field}) Storage::disk('public')->delete($member->{$field});
        }
        $member->delete();
        return response()->json(['ok' => true]);
    }

    private function ensureRegistrationOpen(): void
    {
        abort_if(
            TournamentSetting::getSettings()->isRegistrationClosed(),
            403,
            'Pendaftaran sudah ditutup dan tidak dapat diubah atau diajukan.'
        );
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
