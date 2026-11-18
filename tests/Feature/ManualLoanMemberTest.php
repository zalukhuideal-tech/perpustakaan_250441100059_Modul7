<?php

namespace Tests\Feature;

use App\Models\Anggota;
use App\Models\Buku;
use App\Models\Kategori;
use App\Models\User;
use Database\Seeders\RolePermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ManualLoanMemberTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_and_petugas_can_create_loans_for_registered_or_new_manual_members(): void
    {
        $this->seed(RolePermissionSeeder::class);

        $kategori = Kategori::factory()->create();
        $buku = Buku::factory()->create(['kategori_id' => $kategori->id]);
        $registeredUser = User::factory()->create();
        $registeredUser->assignRole('siswa');
        $registeredAnggota = $registeredUser->anggota()->create([
            'nama' => $registeredUser->name,
            'email' => $registeredUser->email,
            'no_telepon' => '-',
        ]);
        $legacyManualAnggota = Anggota::factory()->create([
            'nama' => 'Anggota Manual Lama',
            'dapat_dipilih_untuk_peminjaman' => false,
        ]);

        foreach ([
            ['admin', 'admin.peminjaman.index', 'admin.peminjaman.store'],
            ['petugas', 'petugas.peminjaman.index', 'petugas.peminjaman.store'],
        ] as [$role, $formRoute, $storeRoute]) {
            $user = User::factory()->create();
            $user->assignRole($role);

            $this->actingAs($user)
                ->get(route($formRoute, ['tambah' => 1]))
                ->assertOk()
                ->assertSee($registeredAnggota->nama)
                ->assertDontSee($legacyManualAnggota->nama)
                ->assertDontSee('Kembali ke dashboard');

            $this->actingAs($user)
                ->post(route($storeRoute), [
                    'anggota_id' => $registeredAnggota->id,
                    'buku_id' => $buku->id,
                    'tanggal_pinjam' => '2026-11-18',
                ])
                ->assertRedirect();

            $this->assertDatabaseHas('peminjamans', [
                'anggota_id' => $registeredAnggota->id,
                'buku_id' => $buku->id,
                'tanggal_pinjam' => '2026-11-18',
                'status' => 'dipinjam',
            ]);

            $namaAnggotaBaru = "Anggota Manual {$role}";

            $this->actingAs($user)
                ->post(route($storeRoute), [
                    'nama_anggota_baru' => $namaAnggotaBaru,
                    'buku_id' => $buku->id,
                    'tanggal_pinjam' => '2026-11-18',
                ])
                ->assertRedirect();

            $anggotaBaru = Anggota::query()
                ->where('nama', $namaAnggotaBaru)
                ->firstOrFail();

            $this->assertNull($anggotaBaru->user_id);
            $this->assertTrue($anggotaBaru->dapat_dipilih_untuk_peminjaman);
            $this->assertDatabaseHas('peminjamans', [
                'anggota_id' => $anggotaBaru->id,
                'buku_id' => $buku->id,
                'tanggal_pinjam' => '2026-11-18',
                'status' => 'dipinjam',
            ]);

            $this->actingAs($user)
                ->get(route($formRoute, ['tambah' => 1]))
                ->assertOk()
                ->assertSee($namaAnggotaBaru);
        }
    }

    public function test_petugas_create_route_redirects_to_the_inline_loan_form(): void
    {
        $this->seed(RolePermissionSeeder::class);

        $petugas = User::factory()->create();
        $petugas->assignRole('petugas');

        $this->actingAs($petugas)
            ->get(route('petugas.peminjaman.create'))
            ->assertRedirect(route('petugas.peminjaman.index', ['tambah' => 1]));
    }

    public function test_loan_creation_rejects_hidden_legacy_members_and_two_member_inputs(): void
    {
        $this->seed(RolePermissionSeeder::class);

        $legacyManualAnggota = Anggota::factory()->create([
            'dapat_dipilih_untuk_peminjaman' => false,
        ]);
        $buku = Buku::factory()->create(['kategori_id' => Kategori::factory()->create()->id]);

        foreach ([
            ['admin', 'admin.peminjaman.index', 'admin.peminjaman.store'],
            ['petugas', 'petugas.peminjaman.index', 'petugas.peminjaman.store'],
        ] as [$role, $indexRoute, $storeRoute]) {
            $user = User::factory()->create();
            $user->assignRole($role);

            $this->actingAs($user)
                ->from(route($indexRoute, ['tambah' => 1]))
                ->post(route($storeRoute), [
                    'anggota_id' => $legacyManualAnggota->id,
                    'buku_id' => $buku->id,
                    'tanggal_pinjam' => '2026-11-18',
                ])
                ->assertRedirect(route($indexRoute, ['tambah' => 1]))
                ->assertSessionHasErrors('anggota_id');

            $response = $this->actingAs($user)
                ->from(route($indexRoute, ['tambah' => 1]))
                ->post(route($storeRoute), [
                    'anggota_id' => $legacyManualAnggota->id,
                    'nama_anggota_baru' => 'Tidak Boleh Keduanya',
                    'buku_id' => $buku->id,
                    'tanggal_pinjam' => '2026-11-18',
                ]);

            $response
                ->assertRedirect(route($indexRoute, ['tambah' => 1]))
                ->assertSessionHasErrors([
                    'anggota_id' => 'Pilih anggota tersimpan atau isi nama anggota baru, tidak keduanya.',
                    'nama_anggota_baru' => 'Pilih anggota tersimpan atau isi nama anggota baru, tidak keduanya.',
                ]);

            $this->followingRedirects()
                ->actingAs($user)
                ->from(route($indexRoute, ['tambah' => 1]))
                ->post(route($storeRoute), [
                    'anggota_id' => $legacyManualAnggota->id,
                    'nama_anggota_baru' => 'Tidak Boleh Keduanya',
                    'buku_id' => $buku->id,
                    'tanggal_pinjam' => '2026-11-18',
                ])
                ->assertOk()
                ->assertSee('Pilih anggota tersimpan atau isi nama anggota baru, tidak keduanya.');
        }

        $this->assertDatabaseCount('peminjamans', 0);
    }
}
