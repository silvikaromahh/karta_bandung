<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Support\Facades\Storage;   
use App\Http\Controllers\Controller;
use App\Models\GameMatch;
use App\Models\News;
use App\Models\Registration;
use App\Models\Team;
use App\Models\TournamentSetting;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class AdminController extends Controller
{
    // ── DASHBOARD ────────────────────────────────────────────────
    public function dashboard()
    {
        $stats = [
            'total_pendaftar'    => Registration::count(),
            'terverifikasi'      => Registration::where('status', 'Terverifikasi')->count(),
            'menunggu'           => Registration::where('status', 'Menunggu Verifikasi')->count(),
            'ditolak'            => Registration::where('status', 'Ditolak')->count(),
            'total_tim'          => Team::count(),
            'pertandingan_mendatang' => GameMatch::where('status', 'Mendatang')->count(),
        ];
        $latestRegistrations = Registration::with('user')->latest()->take(7)->get();
        $upcomingMatches = GameMatch::with(['homeTeam', 'awayTeam'])
            ->where('status', 'Mendatang')->orderBy('match_date')->take(5)->get();
        $needVerification = Registration::with('user')
            ->where('status', 'Menunggu Verifikasi')->latest()->take(5)->get();

        // Chart data: registrations per day (last 7 days)
        $chartData = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = now()->subDays($i)->format('Y-m-d');
            $chartData[] = [
                'date' => now()->subDays($i)->isoFormat('D MMM'),
                'count' => Registration::whereDate('created_at', $date)->count(),
            ];
        }

        return view('admin.dashboard', compact('stats', 'latestRegistrations', 'upcomingMatches', 'needVerification', 'chartData'));
    }

    // ── DATA PENDAFTAR ────────────────────────────────────────────
    public function pendaftar(Request $request)
    {
        $query = Registration::with('user');
        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('user', fn($q) => $q->where('name', 'like', "%$search%")
                ->orWhere('email', 'like', "%$search%"))
                ->orWhere('nama_tim', 'like', "%$search%")
                ->orWhere('kecamatan', 'like', "%$search%");
        }
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        $registrations = $query->latest()->paginate(15);
        return view('admin.pendaftar', compact('registrations'));
    }

    public function destroyPendaftar($id)
    {
        $registration = Registration::with('members')->findOrFail($id);
        foreach ($registration->members as $m) {
            if ($m->ktp_path) Storage::disk('public')->delete($m->ktp_path);
            if ($m->kk_path) Storage::disk('public')->delete($m->kk_path);
            if ($m->pasfoto_path) Storage::disk('public')->delete($m->pasfoto_path);
            $m->delete();
        }
        if ($registration->surat_rekomendasi_path) {
            Storage::disk('public')->delete($registration->surat_rekomendasi_path);
        }
        $registration->delete();

        return back()->with('success', 'Data pendaftaran berhasil dihapus.');
    }

    // ── VERIFIKASI ────────────────────────────────────────────────
    public function verifikasi(Request $request)
    {
        $registrations = Registration::with('user')
            ->where('status', 'Menunggu Verifikasi')
            ->latest()->paginate(10);
        return view('admin.verifikasi', compact('registrations'));
    }

    public function verifikasiDetail($id)
    {
        $registration = Registration::with(['user', 'verifier'])->findOrFail($id);
        return view('admin.verifikasi-detail', compact('registration'));
    }

    public function verifikasiCetak($id)
    {
        $registration = Registration::with(['user', 'verifier', 'members'])->findOrFail($id);
        return view('admin.verifikasi-cetak', compact('registration'));
    }

    public function destroyAnggotaTerverifikasi($registrationId, $memberId)
    {
        $registration = Registration::findOrFail($registrationId);
        if ($registration->status !== 'Terverifikasi') {
            return back()->with('error', 'Data pemain dan official hanya dapat dihapus setelah pendaftaran terverifikasi.');
        }

        $member = $registration->members()->findOrFail($memberId);
        foreach (['ktp_path', 'kk_path', 'pasfoto_path', 'surat_sehat_path'] as $documentPath) {
            if ($member->{$documentPath}) {
                Storage::disk('public')->delete($member->{$documentPath});
            }
        }
        $member->delete();

        return back()->with('success', ucfirst($member->tipe) . ' ' . $member->full_name . ' berhasil dihapus.');
    }

    public function terima($id)
    {
        $registration = Registration::findOrFail($id);
        $registration->update([
            'status' => 'Terverifikasi',
            'rejection_reason' => null,
            'verified_at' => now(),
            'verified_by' => Auth::id(),
        ]);
        // Send notification to user
        $registration->user->notifications()->create([
            'title' => 'Pendaftaran Diterima',
            'message' => 'Selamat! Berkas pendaftaran Anda telah diverifikasi dan disetujui oleh panitia KARTA.BDG.',
            'type' => 'success',
        ]);
        return redirect()->route('admin.verifikasi')->with('success', 'Pendaftaran tim ' . $registration->nama_tim . ' berhasil diterima.');
    }

    public function tolak(Request $request, $id)
    {
        $request->validate(['rejection_reason' => 'required|string|min:10'], [
            'rejection_reason.required' => 'Alasan penolakan wajib diisi.',
            'rejection_reason.min' => 'Alasan penolakan minimal 10 karakter.',
        ]);
        $registration = Registration::findOrFail($id);
        $registration->update([
            'status' => 'Ditolak',
            'rejection_reason' => $request->rejection_reason,
            'verified_at' => null,
            'verified_by' => Auth::id(),
        ]);
        $registration->user->notifications()->create([
            'title' => 'Pendaftaran Ditolak',
            'message' => 'Pendaftaran Anda ditolak. Alasan: ' . $request->rejection_reason . ' Silakan perbaiki dan daftarkan kembali.',
            'type' => 'danger',
        ]);
        return redirect()->route('admin.verifikasi')->with('success', 'Pendaftaran berhasil ditolak dengan alasan yang diberikan.');
    }

    // ── PERTANDINGAN ─────────────────────────────────────────────
    public function pertandingan()
    {
        $matches = GameMatch::with(['homeTeam', 'awayTeam'])->orderBy('match_date')->paginate(15);
        $teams = Team::orderBy('name')->get();
        return view('admin.pertandingan', compact('matches', 'teams'));
    }

    public function storePertandingan(Request $request)
    {
        $request->validate([
            'home_team_id' => 'required|exists:teams,id',
            'away_team_id' => 'required|exists:teams,id|different:home_team_id',
            'match_date' => 'required|date',
            'venue' => 'required|string|max:150',
            'stage' => 'required|string|max:100',
            'status' => 'required|in:Mendatang,Berlangsung,Selesai,Ditunda',
        ]);
        GameMatch::create($request->all());
        return back()->with('success', 'Pertandingan berhasil ditambahkan.');
    }

    public function editPertandingan($id)
    {
        $match = GameMatch::findOrFail($id);
        $teams = Team::orderBy('name')->get();
        return view('admin.pertandingan-edit', compact('match', 'teams'));
    }

    public function updatePertandingan(Request $request, $id)
    {
        $request->validate([
            'home_team_id' => 'required|exists:teams,id',
            'away_team_id' => 'required|exists:teams,id|different:home_team_id',
            'match_date' => 'required|date',
            'venue' => 'required|string|max:150',
            'stage' => 'required|string|max:100',
            'status' => 'required|in:Mendatang,Berlangsung,Selesai,Ditunda',
        ]);
        $match = GameMatch::findOrFail($id);
        $match->update($request->all());
        return redirect()->route('admin.pertandingan')->with('success', 'Data pertandingan berhasil diperbarui.');
    }

    public function deletePertandingan($id)
    {
        GameMatch::findOrFail($id)->delete();
        return back()->with('success', 'Pertandingan berhasil dihapus.');
    }

    // ── HASIL & KLASEMEN ─────────────────────────────────────────
    public function hasilKlasemen()
    {
        $matches = GameMatch::with(['homeTeam', 'awayTeam'])->orderByDesc('match_date')->get();
        $groupA = Team::where('group_name', 'Grup A')->orderByDesc('points')->orderByDesc('goal_difference')->get();
        $groupB = Team::where('group_name', 'Grup B')->orderByDesc('points')->orderByDesc('goal_difference')->get();
        return view('admin.hasil-klasemen', compact('matches', 'groupA', 'groupB'));
    }

    public function inputHasil(Request $request, $id)
    {
        $request->validate([
            'home_score' => 'required|integer|min:0',
            'away_score' => 'required|integer|min:0',
        ]);
        $match = GameMatch::with(['homeTeam', 'awayTeam'])->findOrFail($id);
        $oldStatus = $match->status;

        $match->update([
            'home_score' => $request->home_score,
            'away_score' => $request->away_score,
            'status' => 'Selesai',
        ]);

        // Update standings only if match wasn't already "Selesai"
        if ($oldStatus !== 'Selesai') {
            $this->updateStandings($match);
        }

        return back()->with('success', 'Hasil pertandingan berhasil disimpan dan klasemen diperbarui.');
    }

    private function updateStandings(GameMatch $match)
    {
        $home = $match->homeTeam;
        $away = $match->awayTeam;
        $hs = $match->home_score;
        $as = $match->away_score;

        $home->played++;
        $away->played++;
        $home->goals_for += $hs;
        $home->goals_against += $as;
        $away->goals_for += $as;
        $away->goals_against += $hs;

        if ($hs > $as) {
            $home->won++; $home->points += 3;
            $away->lost++;
        } elseif ($hs < $as) {
            $away->won++; $away->points += 3;
            $home->lost++;
        } else {
            $home->drawn++; $home->points++;
            $away->drawn++; $away->points++;
        }

        $home->goal_difference = $home->goals_for - $home->goals_against;
        $away->goal_difference = $away->goals_for - $away->goals_against;
        $home->save();
        $away->save();
    }

    // ── BERITA ───────────────────────────────────────────────────
    public function berita()
    {
        $news = News::with('author')->orderByDesc('published_at')->paginate(10);
        return view('admin.berita', compact('news'));
    }

    public function createBerita()
    {
        return view('admin.berita-form');
    }

    public function storeBerita(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:200',
            'category' => 'required|in:Pengumuman,Berita,Peraturan',
            'summary' => 'required|string|max:300',
            'content' => 'required|string',
        ]);
        News::create([
            'title' => $request->title,
            'slug' => Str::slug($request->title) . '-' . Str::random(5),
            'category' => $request->category,
            'summary' => $request->summary,
            'content' => $request->content,
            'is_published' => $request->boolean('is_published'),
            'published_at' => $request->boolean('is_published') ? now() : null,
            'created_by' => Auth::id(),
        ]);
        return redirect()->route('admin.berita')->with('success', 'Artikel berhasil diterbitkan.');
    }

    public function editBerita($id)
    {
        $article = News::findOrFail($id);
        return view('admin.berita-form', compact('article'));
    }

    public function updateBerita(Request $request, $id)
    {
        $request->validate([
            'title' => 'required|string|max:200',
            'category' => 'required|in:Pengumuman,Berita,Peraturan',
            'summary' => 'required|string|max:300',
            'content' => 'required|string',
        ]);
        $article = News::findOrFail($id);
        $article->update([
            'title' => $request->title,
            'category' => $request->category,
            'summary' => $request->summary,
            'content' => $request->content,
            'is_published' => $request->boolean('is_published'),
            'published_at' => $request->boolean('is_published') ? ($article->published_at ?? now()) : null,
        ]);
        return redirect()->route('admin.berita')->with('success', 'Artikel berhasil diperbarui.');
    }

    public function deleteBerita($id)
    {
        News::findOrFail($id)->delete();
        return back()->with('success', 'Artikel berhasil dihapus.');
    }

    // ── PENGATURAN TURNAMEN ───────────────────────────────────────
    public function pengaturan()
    {
        $settings = TournamentSetting::getSettings();
        return view('admin.pengaturan', compact('settings'));
    }

    public function updatePengaturan(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:200',
            'registration_schedule' => 'required|string',
            'tournament_dates' => 'required|string',
            'location' => 'required|string',
            'total_prize' => 'required|string',
            'registration_status' => 'required|in:Buka,Tutup,Segera',
        ]);
        $settings = TournamentSetting::getSettings();
        $settings->update($request->except('_token', '_method', 'logo'));
        return back()->with('success', 'Pengaturan turnamen berhasil disimpan.');
    }

    // ── TIM ──────────────────────────────────────────────────────
    public function tim()
    {
        $teams = Team::orderByDesc('points')->get();
        return view('admin.tim', compact('teams'));
    }

    public function storeTim(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:100',
            'kecamatan' => 'nullable|string|max:100',
            'group_name' => 'required|string|max:50',
        ]);
        Team::create([
            'name' => $request->name,
            'kecamatan' => $request->kecamatan ?: $request->name,
            'group_name' => $request->group_name,
        ]);
        return back()->with('success', 'Tim berhasil ditambahkan.');
    }

    public function deleteTim($id)
    {
        Team::findOrFail($id)->delete();
        return back()->with('success', 'Tim berhasil dihapus.');
    }
}
