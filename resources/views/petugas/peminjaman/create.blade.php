
<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Tambah Peminjaman Buku
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow-sm sm:rounded-lg p-6">

                <form action="{{ route('petugas.peminjaman.store') }}" method="POST">
                    @csrf

                    <div class="mb-4">
                        <label for="anggota_id" class="block font-medium text-sm text-gray-700">
                            Pilih Anggota Tersimpan
                        </label>

                        <select name="anggota_id" id="anggota_id"
                            class="block mt-1 w-full border-gray-300 rounded-md shadow-sm">
                            <option value="">-- Pilih anggota yang sudah terdaftar atau pernah ditambahkan --</option>

                            @foreach ($anggotas as $anggota)
                                <option value="{{ $anggota->id }}" @selected(old('anggota_id') == $anggota->id)>
                                    {{ $anggota->nama }}
                                </option>
                            @endforeach
                        </select>

                        @error('anggota_id')
                            <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="mb-4">
                        <label for="nama_anggota_baru" class="block font-medium text-sm text-gray-700">
                            Atau Tambahkan Anggota Baru
                        </label>

                        <input type="text" name="nama_anggota_baru" id="nama_anggota_baru"
                            value="{{ old('nama_anggota_baru') }}" placeholder="Ketik nama anggota baru"
                            class="block mt-1 w-full border-gray-300 rounded-md shadow-sm">
                        <p class="mt-1 text-sm text-gray-500">Isi salah satu: pilih anggota tersimpan atau ketik nama anggota baru.</p>

                        @error('nama_anggota_baru')
                            <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="mb-4">
                        <label for="buku_id" class="block font-medium text-sm text-gray-700">
                            Judul Buku
                        </label>

                        <select name="buku_id" id="buku_id"
                            class="block mt-1 w-full border-gray-300 rounded-md shadow-sm" required>
                            <option value="">-- Pilih Buku --</option>

                            @foreach ($bukus as $buku)
                                <option value="{{ $buku->id }}">
                                    {{ $buku->judul }}
                                </option>
                            @endforeach
                        </select>

                        @error('buku_id')
                            <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="mb-4">
                        <label for="tanggal_pinjam" class="block font-medium text-sm text-gray-700">
                            Tanggal Pinjam
                        </label>

                        <input type="date" name="tanggal_pinjam" id="tanggal_pinjam"
                            value="{{ date('Y-m-d') }}"
                            class="block mt-1 w-full border-gray-300 rounded-md shadow-sm" required>

                        @error('tanggal_pinjam')
                            <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="flex items-center gap-3">
                        <button type="submit"
                            class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-md">
                            Simpan Peminjaman
                        </button>

                        <a href="{{ route('petugas.peminjaman.index') }}"
                            class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-md">
                            Kembali
                        </a>
                    </div>
                </form>

            </div>
        </div>
    </div>
</x-app-layout>
