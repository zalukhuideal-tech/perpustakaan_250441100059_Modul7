<?php

namespace App\Http\Controllers;

use App\Models\Peminjaman;

class LaporanController extends Controller
{
    // Menampilkan laporan peminjaman
    public function index()
    {
        // Mengambil data peminjaman beserta anggota dan buku
        $peminjamans = Peminjaman::with(['anggota', 'buku'])
            ->latest()
            ->paginate(10);

        // Menghitung total seluruh peminjaman
        $totalPeminjaman = Peminjaman::count();

        // Menghitung pengajuan yang masih menunggu persetujuan.
        $menungguPersetujuan = Peminjaman::where('status', 'menunggu')
            ->count();

        // Menghitung peminjaman aktif sesuai status pada manajemen peminjaman.
        $sedangDipinjam = Peminjaman::whereIn('status', ['disetujui', 'dipinjam'])
            ->whereNull('tanggal_kembali')
            ->count();

        // Menghitung transaksi yang sudah selesai dikembalikan.
        $totalDikembalikan = Peminjaman::where('status', 'dikembalikan')
            ->count();

        // Menghitung pengajuan yang ditolak.
        $totalDitolak = Peminjaman::where('status', 'ditolak')
            ->count();

        return view('laporan.index', compact(
            'peminjamans',
            'totalPeminjaman',
            'menungguPersetujuan',
            'sedangDipinjam',
            'totalDikembalikan',
            'totalDitolak'
        ));
    }
}