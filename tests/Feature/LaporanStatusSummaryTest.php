<?php

namespace Tests\Feature;

use App\Models\Anggota;
use App\Models\Buku;
use App\Models\Kategori;
use App\Models\Peminjaman;
use App\Models\User;
use Database\Seeders\RolePermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LaporanStatusSummaryTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_and_petugas_reports_render_consistent_loan_status_summaries(): void
    {
        $this->seed(RolePermissionSeeder::class);

        $kategori = Kategori::factory()->create();
        $anggota = Anggota::factory()->create();

        foreach ([
            ['menunggu', null],
            ['disetujui', null],
            ['dipinjam', null],
            ['dikembalikan', now()->toDateString()],
            ['ditolak', null],
        ] as [$status, $tanggalKembali]) {
            Peminjaman::factory()->create([
                'anggota_id' => $anggota->id,
                'buku_id' => Buku::factory()->create(['kategori_id' => $kategori->id])->id,
                'status' => $status,
                'tanggal_kembali' => $tanggalKembali,
            ]);
        }

        foreach ([
            ['admin', 'admin.laporan.index'],
            ['petugas', 'petugas.laporan.index'],
        ] as [$role, $routeName]) {
            $user = User::factory()->create();
            $user->assignRole($role);

            $this->actingAs($user)
                ->get(route($routeName))
                ->assertOk()
                ->assertViewHas('totalPeminjaman', 5)
                ->assertViewHas('menungguPersetujuan', 1)
                ->assertViewHas('sedangDipinjam', 2)
                ->assertViewHas('totalDikembalikan', 1)
                ->assertViewHas('totalDitolak', 1)
                ->assertSee('Menunggu Persetujuan')
                ->assertSee('Sedang Dipinjam')
                ->assertSee('Sudah Dikembalikan')
                ->assertSee('Ditolak');
        }
    }
}
