
<x-petugas-layout>

    <!-- JUDUL HALAMAN -->
    <div class="admin-welcome">
        <h1 class="admin-page-title">
            Manajemen Peminjaman
        </h1>

        <p>
            Kelola data peminjaman buku perpustakaan.
            Petugas dapat memantau status peminjaman dan mencatat
            pengembalian buku.
        </p>
    </div>

    @if (request()->boolean('tambah') || $errors->any())
        <div class="admin-information" style="margin-top: 0; margin-bottom: 20px;">
            <h2 class="admin-section-title">Formulir Tambah Peminjaman</h2>
            @if ($errors->has('anggota_id') && $errors->has('nama_anggota_baru'))
                <div role="alert" style="margin-bottom: 16px; padding: 12px; border-radius: 6px; background-color: #fee2e2; color: #991b1b;">
                    Pilih anggota tersimpan atau isi nama anggota baru, tidak keduanya.
                </div>
            @endif
            <form action="{{ route('petugas.peminjaman.store') }}" method="POST" class="admin-inline-form">
                @csrf
                <div class="admin-form-grid">
                    <div>
                        <label for="anggota_id">Pilih Anggota Tersimpan</label>
                        <select id="anggota_id" name="anggota_id">
                            <option value="">Pilih anggota yang sudah terdaftar atau pernah ditambahkan</option>
                            @foreach ($anggotas as $anggota)
                                <option value="{{ $anggota->id }}" @selected(old('anggota_id') == $anggota->id)>{{ $anggota->nama }}</option>
                            @endforeach
                        </select>
                        @error('anggota_id')
                            <p class="form-error">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label for="nama_anggota_baru">Atau Tambahkan Anggota Baru</label>
                        <input id="nama_anggota_baru" name="nama_anggota_baru" type="text" value="{{ old('nama_anggota_baru') }}" placeholder="Ketik nama anggota baru">
                        <p style="margin-top: 6px; color: #64748b; font-size: 0.875rem;">Isi salah satu: pilih anggota tersimpan atau ketik nama anggota baru.</p>
                        @error('nama_anggota_baru')
                            <p class="form-error">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label for="buku_id">Judul Buku</label>
                        <select id="buku_id" name="buku_id" required>
                            <option value="">Pilih Buku</option>
                            @foreach ($bukus as $buku)
                                <option value="{{ $buku->id }}" @selected(old('buku_id') == $buku->id)>{{ $buku->judul }}</option>
                            @endforeach
                        </select>
                        @error('buku_id')
                            <p class="form-error">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label for="tanggal_pinjam">Tanggal Pinjam</label>
                        <input id="tanggal_pinjam" name="tanggal_pinjam" type="date" value="{{ old('tanggal_pinjam', date('Y-m-d')) }}" required>
                        @error('tanggal_pinjam')
                            <p class="form-error">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
                <div class="admin-form-actions">
                    <button type="submit" class="admin-form-submit">Simpan Peminjaman</button>
                    <a href="{{ route('petugas.peminjaman.index') }}" class="admin-form-back">Kembali</a>
                </div>
            </form>
        </div>
    @endif

    <!-- TOMBOL KEMBALI -->
    @if (! request()->boolean('tambah') && ! $errors->any())
        <div style="margin-bottom: 20px;">
            <a href="{{ route('petugas.dashboard') }}"
               aria-label="Kembali ke dashboard"
               title="Kembali ke dashboard"
               class="dashboard-back-button">
                <svg class="dashboard-back-icon" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                    <path d="M17 12H7m4-4-4 4 4 4" />
                </svg>
            </a>
        </div>
    @endif

    <!-- NOTIFIKASI BERHASIL -->
    @if (session('success'))
        <div style="background-color: #dcfce7; color: #166534; padding: 12px; margin-bottom: 16px; border-radius: 6px;">
            {{ session('success') }}
        </div>
    @endif

    <!-- NOTIFIKASI GAGAL -->
    @if (session('error'))
        <div style="background-color: #fee2e2; color: #991b1b; padding: 12px; margin-bottom: 16px; border-radius: 6px;">
            {{ session('error') }}
        </div>
    @endif

    <!-- DAFTAR PEMINJAMAN -->
    <div class="admin-information" style="margin-top: 0;">

        <div style="display: flex; justify-content: space-between; align-items: center; gap: 15px; flex-wrap: wrap; margin-bottom: 25px;">
            <h3 class="admin-section-title" style="margin-bottom: 0;">
                Daftar Seluruh Peminjaman
            </h3>

            <a href="{{ route('petugas.peminjaman.index', ['tambah' => 1]) }}" class="admin-add-button">
                + Tambah Peminjaman
            </a>
        </div>

        <div style="overflow-x: auto;">

            <table class="w-full min-w-max border-collapse border border-gray-300">

                <thead>
                    <tr class="bg-gray-100">
                        <th class="border border-gray-300 p-3">No</th>
                        <th class="border border-gray-300 p-3">Nama Anggota</th>
                        <th class="border border-gray-300 p-3">Judul Buku</th>
                        <th class="border border-gray-300 p-3">Tanggal Pinjam</th>
                        <th class="border border-gray-300 p-3">Tanggal Kembali</th>
                        <th class="border border-gray-300 p-3">Status</th>
                        <th class="border border-gray-300 p-3">Aksi</th>
                    </tr>
                </thead>

                <tbody>

                    @forelse ($peminjamans as $peminjaman)

                        <tr class="hover:bg-gray-50">

                            <td class="border border-gray-300 p-3 text-center">
                                {{ $loop->iteration }}
                            </td>

                            <td class="border border-gray-300 p-3">
                                {{ $peminjaman->anggota->nama ?? '-' }}
                            </td>

                            <td class="border border-gray-300 p-3">
                                {{ $peminjaman->buku->judul ?? '-' }}
                            </td>

                            <td class="border border-gray-300 p-3 text-center">
                                {{ $peminjaman->tanggal_pinjam }}
                            </td>

                            <td class="border border-gray-300 p-3 text-center">
                                {{ $peminjaman->tanggal_kembali ?? '-' }}
                            </td>

                            <!-- STATUS PEMINJAMAN -->
                            <td class="border border-gray-300 p-3 text-center">

                                @if ($peminjaman->status === 'menunggu')
                                    <span style="color: #b45309; font-weight: 600;">Menunggu Persetujuan</span>
                                @elseif ($peminjaman->status === 'ditolak')
                                    <span style="color: #b91c1c; font-weight: 600;">Ditolak</span>
                                @elseif ($peminjaman->status === 'dikembalikan' || $peminjaman->tanggal_kembali)

                                    <span style="color: #166534; font-weight: 600;">
                                        Sudah Dikembalikan
                                    </span>

                                @elseif (in_array($peminjaman->status, ['disetujui', 'dipinjam'], true))
                                    <span class="inline-flex items-center gap-1 rounded-full border border-blue-200 bg-blue-50 px-2.5 py-1 font-semibold text-blue-700">
                                        <span aria-hidden="true">●</span> Sedang Dipinjam
                                    </span>
                                @else
                                    <span class="inline-flex rounded-full border border-slate-300 px-2.5 py-1 font-semibold text-slate-600">{{ ucfirst($peminjaman->status) }}</span>

                                @endif

                            </td>

                            <!-- AKSI -->
                            <td class="border border-gray-300 p-3 text-center">

                                @if ($peminjaman->status === 'menunggu')
                                    <div class="flex flex-wrap justify-center gap-2">
                                        <form action="{{ route('petugas.peminjaman.proses', [$peminjaman, 'disetujui']) }}" method="POST">
                                            @csrf
                                            <button type="submit" class="rounded-md border border-green-700 bg-green-50 px-3 py-1.5 font-semibold text-green-700 hover:bg-green-100">✓ Setujui</button>
                                        </form>
                                        <form action="{{ route('petugas.peminjaman.proses', [$peminjaman, 'ditolak']) }}" method="POST">
                                            @csrf
                                            <button type="submit" class="rounded-md border border-red-700 bg-red-50 px-3 py-1.5 font-semibold text-red-700 hover:bg-red-100">× Tolak</button>
                                        </form>
                                    </div>
                                @elseif (in_array($peminjaman->status, ['disetujui', 'dipinjam'], true) && !$peminjaman->tanggal_kembali)

                                    <form
                                        action="{{ route('petugas.peminjaman.kembalikan', $peminjaman->id) }}"
                                        method="POST"
                                        onsubmit="return confirm('Apakah buku ini sudah dikembalikan?')"
                                    >

                                        @csrf

                                        <button
                                            type="submit"
                                            class="rounded-md border border-blue-700 bg-blue-50 px-3 py-1.5 font-semibold text-blue-700 hover:bg-blue-100"
                                        >
                                            ↩ Kembalikan
                                        </button>

                                    </form>

                                @else

                                    <span class="inline-flex rounded-full border border-green-200 bg-green-50 px-2.5 py-1 font-semibold text-green-700">
                                        ✓ Selesai
                                    </span>

                                @endif

                            </td>

                        </tr>

                    @empty

                        <tr>
                            <td colspan="7"
                                class="border border-gray-300 p-4 text-center text-gray-500">
                                Belum ada data peminjaman.
                            </td>
                        </tr>

                    @endforelse

                </tbody>

            </table>

        </div>

    </div>

</x-petugas-layout>
