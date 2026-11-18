<x-siswa-layout>
    <div class="p-6">
        <a href="{{ route('siswa.peminjaman.index') }}"
           aria-label="Kembali ke daftar peminjaman"
           title="Kembali ke daftar peminjaman"
           class="inline-flex h-11 w-11 items-center justify-center rounded-full text-red-800 transition hover:bg-red-50 hover:text-red-950">
            <span aria-hidden="true" class="text-3xl leading-none">&larr;</span>
        </a>

        <h1 class="mt-6 text-2xl font-bold text-gray-800">Detail Peminjaman</h1>

        <div class="mt-6 overflow-x-auto rounded-lg bg-white p-6 shadow-sm">
            <table class="w-full border-collapse border border-gray-300">
                <tbody>
                    <tr>
                        <th class="border bg-gray-100 p-3 text-left">ID Peminjaman</th>
                        <td class="border p-3">{{ $peminjaman->id }}</td>
                    </tr>
                    <tr>
                        <th class="border bg-gray-100 p-3 text-left">Peminjam</th>
                        <td class="border p-3">{{ $peminjaman->anggota->nama }}</td>
                    </tr>
                    <tr>
                        <th class="border bg-gray-100 p-3 text-left">Judul Buku</th>
                        <td class="border p-3">{{ $peminjaman->buku->judul }}</td>
                    </tr>
                    <tr>
                        <th class="border bg-gray-100 p-3 text-left">Tanggal Pinjam</th>
                        <td class="border p-3">{{ $peminjaman->tanggal_pinjam }}</td>
                    </tr>
                    <tr>
                        <th class="border bg-gray-100 p-3 text-left">Tanggal Kembali</th>
                        <td class="border p-3">{{ $peminjaman->tanggal_kembali ?? '-' }}</td>
                    </tr>
                    <tr>
                        <th class="border bg-gray-100 p-3 text-left">Status</th>
                        <td class="border p-3">{{ $peminjaman->status }}</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</x-siswa-layout>