
<x-siswa-layout>

    <div class="p-6">

        {{-- TOMBOL KEMBALI --}}
        <a href="{{ route('siswa.dashboard') }}"
            aria-label="Kembali ke dashboard"
            title="Kembali ke dashboard"
            class="dashboard-back-button">
            <svg class="dashboard-back-icon" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                <path d="M17 12H7m4-4-4 4 4 4" />
            </svg>
        </a>

        {{-- JUDUL HALAMAN --}}
        <h1 class="text-2xl font-bold text-gray-800 mt-6">
            Daftar Buku Perpustakaan
        </h1>

        {{-- TABEL DAFTAR BUKU --}}
        <div class="mt-6 bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">

            <h3 class="text-lg font-semibold mb-4">
                Koleksi Buku
            </h3>

            <div class="overflow-x-auto">

                <table class="w-full border-collapse border border-gray-300">

                    <thead class="bg-gray-100">
                        <tr>
                            <th class="border p-3">No</th>
                            <th class="border p-3">Judul Buku</th>
                            <th class="border p-3">Penulis</th>
                            <th class="border p-3">Kategori</th>
                            <th class="border p-3">Tahun Terbit</th>
                            <th class="border p-3">Aksi</th>
                        </tr>
                    </thead>

                    <tbody>

                        @forelse ($bukus as $index => $buku)

                            <tr>
                                <td class="border p-3 text-center">
                                    {{ $bukus->firstItem() + $index }}
                                </td>

                                <td class="border p-3">
                                    {{ $buku->judul }}
                                </td>

                                <td class="border p-3">
                                    {{ $buku->penulis }}
                                </td>

                                <td class="border p-3">
                                    {{ $buku->kategori->nama ?? '-' }}
                                </td>

                                <td class="border p-3 text-center">
                                    {{ $buku->tahun_terbit }}
                                </td>

                                <td class="border p-3 text-center">
                                    @can('loans.request')
                                        <form method="POST" action="{{ route('peminjaman.request', $buku) }}">
                                            @csrf
                                            <button type="submit" class="whitespace-nowrap rounded-md bg-blue-600 px-3 py-2 font-semibold text-white hover:bg-blue-700">
                                                Ajukan Peminjaman
                                            </button>
                                        </form>
                                    @endcan
                                </td>
                            </tr>

                        @empty

                            <tr>
                                <td colspan="6" class="border p-4 text-center">
                                    Belum ada data buku.
                                </td>
                            </tr>

                        @endforelse

                    </tbody>

                </table>

            </div>

            {{-- PAGINATION --}}
            <div class="mt-4">
                {{ $bukus->links() }}
            </div>

        </div>

    </div>

</x-siswa-layout>
