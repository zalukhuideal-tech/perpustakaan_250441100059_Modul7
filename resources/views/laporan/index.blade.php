
@php
    $layout = Auth::user()->hasRole('admin')
        ? 'admin-layout'
        : 'petugas-layout';

    $dashboardRoute = Auth::user()->hasRole('admin')
        ? 'admin.dashboard'
        : 'petugas.dashboard';
@endphp

<x-dynamic-component :component="$layout">

    {{-- JUDUL HALAMAN --}}
    <h1 class="admin-page-title">
        Laporan Peminjaman Perpustakaan
    </h1>

    {{-- TOMBOL KEMBALI --}}
    <div style="margin-bottom: 20px;">
        <a href="{{ route($dashboardRoute) }}"
           aria-label="Kembali ke dashboard"
           title="Kembali ke dashboard"
           class="dashboard-back-button">
            <svg class="dashboard-back-icon" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                <path d="M17 12H7m4-4-4 4 4 4" />
            </svg>
        </a>
    </div>

    {{-- RINGKASAN LAPORAN --}}
    <div class="admin-card-grid" style="max-width: 100%; margin-bottom: 25px;">

        <div class="admin-card">
            <h3>Total Peminjaman</h3>
            <p>{{ $totalPeminjaman }}</p>
            <span>Seluruh transaksi peminjaman</span>
        </div>

        <div class="admin-card">
            <h3>Menunggu Persetujuan</h3>
            <p style="color: #b45309;">
                {{ $menungguPersetujuan }}
            </p>
            <span>Pengajuan belum diproses</span>
        </div>

        <div class="admin-card">
            <h3>Sedang Dipinjam</h3>
            <p style="color: #1d4ed8;">
                {{ $sedangDipinjam }}
            </p>
            <span>Transaksi masih berlangsung</span>
        </div>

        <div class="admin-card">
            <h3>Sudah Dikembalikan</h3>
            <p style="color: #15803d;">
                {{ $totalDikembalikan }}
            </p>
            <span>Transaksi selesai</span>
        </div>

        <div class="admin-card">
            <h3>Ditolak</h3>
            <p style="color: #b91c1c;">
                {{ $totalDitolak }}
            </p>
            <span>Pengajuan tidak disetujui</span>
        </div>

    </div>

    {{-- TABEL LAPORAN --}}
    <div class="admin-information" style="margin-top: 0;">

        <h3 class="admin-section-title">
            Data Laporan Peminjaman
        </h3>

        <div style="overflow-x: auto;">

            <table style="width: 100%; border-collapse: collapse; background-color: white;">

                <thead>
                    <tr style="background-color: #f1f5f9;">
                        <th style="border: 1px solid #e2e8f0; padding: 13px;">No</th>
                        <th style="border: 1px solid #e2e8f0; padding: 13px;">Nama Anggota</th>
                        <th style="border: 1px solid #e2e8f0; padding: 13px;">Judul Buku</th>
                        <th style="border: 1px solid #e2e8f0; padding: 13px;">Tanggal Pinjam</th>
                        <th style="border: 1px solid #e2e8f0; padding: 13px;">Tanggal Kembali</th>
                        <th style="border: 1px solid #e2e8f0; padding: 13px;">Status</th>
                    </tr>
                </thead>

                <tbody>

                    @forelse ($peminjamans as $index => $peminjaman)

                        <tr>
                            <td style="border: 1px solid #e2e8f0; padding: 13px; text-align: center;">
                                {{ $peminjamans->firstItem() + $index }}
                            </td>

                            <td style="border: 1px solid #e2e8f0; padding: 13px;">
                                {{ $peminjaman->anggota->nama ?? '-' }}
                            </td>

                            <td style="border: 1px solid #e2e8f0; padding: 13px;">
                                {{ $peminjaman->buku->judul ?? '-' }}
                            </td>

                            <td style="border: 1px solid #e2e8f0; padding: 13px; text-align: center; white-space: nowrap;">
                                {{ $peminjaman->tanggal_pinjam }}
                            </td>

                            <td style="border: 1px solid #e2e8f0; padding: 13px; text-align: center; white-space: nowrap;">
                                {{ $peminjaman->tanggal_kembali ?? '-' }}
                            </td>

                            <td style="border: 1px solid #e2e8f0; padding: 13px; text-align: center; white-space: nowrap;">

                                @if ($peminjaman->status === 'menunggu')
                                    <span style="color: #b45309; font-weight: 600;">
                                        Menunggu Persetujuan
                                    </span>
                                @elseif ($peminjaman->status === 'ditolak')
                                    <span style="color: #b91c1c; font-weight: 600;">
                                        Ditolak
                                    </span>
                                @elseif ($peminjaman->status === 'dikembalikan')
                                    <span style="color: #15803d; font-weight: 600;">
                                        Dikembalikan
                                    </span>
                                @else
                                    <span style="color: #1d4ed8; font-weight: 600;">
                                        Sedang Dipinjam
                                    </span>
                                @endif

                            </td>
                        </tr>

                    @empty

                        <tr>
                            <td colspan="6" style="border: 1px solid #e2e8f0; padding: 20px; text-align: center;">
                                Belum ada data peminjaman.
                            </td>
                        </tr>

                    @endforelse

                </tbody>

            </table>

        </div>

        {{-- PAGINATION --}}
        <div style="margin-top: 20px;">
            {{ $peminjamans->links() }}
        </div>

    </div>

</x-dynamic-component>
