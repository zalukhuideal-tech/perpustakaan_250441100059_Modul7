<?php

namespace App\Http\Controllers\Petugas;

use App\Http\Controllers\Controller;
use App\Models\Anggota;
use App\Models\Buku;
use App\Models\Peminjaman;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class PeminjamanController extends Controller
{
    public function index()
    {
        $peminjamans = Peminjaman::with(['anggota', 'buku'])
            ->latest()
            ->get();
        $anggotas = $this->selectableAnggotas()->get();
        $bukus = Buku::all();

        return view('petugas.peminjaman.index', compact('peminjamans', 'anggotas', 'bukus'));
    }

    public function create(): RedirectResponse
    {
        return redirect()->route('petugas.peminjaman.index', ['tambah' => 1]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'anggota_id' => 'nullable|integer|exists:anggotas,id|required_without:nama_anggota_baru',
            'nama_anggota_baru' => 'nullable|string|max:255|required_without:anggota_id',
            'buku_id' => 'required|exists:bukus,id',
            'tanggal_pinjam' => 'required|date',
        ]);

        if (($validated['anggota_id'] ?? null) && ($validated['nama_anggota_baru'] ?? null)) {
            throw ValidationException::withMessages([
                'anggota_id' => 'Pilih anggota tersimpan atau isi nama anggota baru, tidak keduanya.',
                'nama_anggota_baru' => 'Pilih anggota tersimpan atau isi nama anggota baru, tidak keduanya.',
            ]);
        }

        if ($validated['anggota_id'] ?? null) {
            $anggota = $this->selectableAnggotas()
                ->find($validated['anggota_id']);

            if ($anggota === null) {
                throw ValidationException::withMessages([
                    'anggota_id' => 'Anggota yang dipilih tidak tersedia untuk peminjaman.',
                ]);
            }
        } else {
            $anggota = Anggota::create([
                'nama' => $validated['nama_anggota_baru'],
                'email' => '-',
                'no_telepon' => '-',
                'dapat_dipilih_untuk_peminjaman' => true,
            ]);
        }

        $anggota->peminjamans()->create([
            'buku_id' => $validated['buku_id'],
            'tanggal_pinjam' => $validated['tanggal_pinjam'],
            'status' => 'dipinjam',
        ]);

        return redirect()
            ->route('petugas.peminjaman.index')
            ->with('success', 'Data peminjaman berhasil ditambahkan.');
    }

    public function kembalikan(Peminjaman $peminjaman)
    {
        if ($peminjaman->tanggal_kembali !== null || ! in_array($peminjaman->status, ['disetujui', 'dipinjam'], true)) {
            return redirect()
                ->route('petugas.peminjaman.index')
                ->with('error', 'Buku sudah dikembalikan sebelumnya.');
        }

        $peminjaman->update([
            'tanggal_kembali' => now()->toDateString(),
            'status' => 'dikembalikan',
        ]);

        return redirect()
            ->route('petugas.peminjaman.index')
            ->with('success', 'Buku berhasil dikembalikan.');
    }

    public function proses(Peminjaman $peminjaman, string $status): RedirectResponse
    {
        abort_unless(in_array($status, ['disetujui', 'ditolak'], true), 404);
        abort_unless($peminjaman->status === 'menunggu', 404);

        $peminjaman->update(['status' => $status]);

        return redirect()
            ->route('petugas.peminjaman.index')
            ->with('success', 'Pengajuan peminjaman berhasil diproses.');
    }

    /**
     * @return Builder<Anggota>
     */
    private function selectableAnggotas()
    {
        return Anggota::query()
            ->where(function ($query): void {
                $query->whereNotNull('user_id')
                    ->orWhere('dapat_dipilih_untuk_peminjaman', true);
            })
            ->orderBy('nama');
    }
}