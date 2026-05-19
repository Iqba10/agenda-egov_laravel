<?php

namespace Tests\Feature;

use App\Models\Agenda;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AgendaEgovFlowTest extends TestCase
{
    use RefreshDatabase;

    public function test_public_agenda_pages_can_be_rendered(): void
    {
        $agenda = $this->createAgenda();

        $this->get('/')->assertOk()->assertSee($agenda->perihal_kegiatan);
        $this->get(route('agenda.show', $agenda))->assertOk()->assertSee($agenda->tempat);
    }

    public function test_role_middleware_blocks_regular_users_from_admin_area(): void
    {
        $user = User::factory()->create(['role' => 'user']);

        $this->actingAs($user)->get(route('admin.dashboard'))->assertForbidden();
    }

    public function test_agenda_effective_status_follows_schedule_unless_cancelled(): void
    {
        $scheduled = $this->createAgenda([
            'status' => 'terjadwal',
            'waktu_mulai' => now()->addHour()->format('Y-m-d H:i:s'),
            'waktu_selesai' => now()->addHours(2)->format('Y-m-d H:i:s'),
        ]);
        $ongoing = $this->createAgenda([
            'status' => 'terjadwal',
            'waktu_mulai' => now()->subHour()->format('Y-m-d H:i:s'),
            'waktu_selesai' => now()->addHour()->format('Y-m-d H:i:s'),
        ]);
        $finished = $this->createAgenda([
            'status' => 'terjadwal',
            'waktu_mulai' => now()->subHours(3)->format('Y-m-d H:i:s'),
            'waktu_selesai' => now()->subHour()->format('Y-m-d H:i:s'),
        ]);
        $cancelled = $this->createAgenda([
            'status' => 'dibatalkan',
            'waktu_mulai' => now()->subHour()->format('Y-m-d H:i:s'),
            'waktu_selesai' => now()->addHour()->format('Y-m-d H:i:s'),
        ]);

        $this->assertSame('terjadwal', $scheduled->effective_status);
        $this->assertSame('berlangsung', $ongoing->effective_status);
        $this->assertSame('selesai', $finished->effective_status);
        $this->assertSame('dibatalkan', $cancelled->effective_status);
    }

    public function test_admin_can_manage_users_and_agendas(): void
    {
        $admin  = User::factory()->create(['role' => 'admin']);
        $user   = User::factory()->create(['role' => 'admin']);
        $agenda = $this->createAgenda(['status' => 'selesai']);

        $user->update(['role' => 'user']);
        $this->assertSame('user', $user->fresh()->role);

        $this->actingAs($admin)->get(route('admin.dashboard'))
            ->assertOk();

        $this->actingAs($admin)->get(route('admin.agendas.print', ['status' => 'selesai']))
            ->assertOk()
            ->assertSee($agenda->perihal_kegiatan);
    }

    private function createAgenda(array $overrides = []): Agenda
    {
        return Agenda::create($this->agendaPayload($overrides));
    }

    private function agendaPayload(array $overrides = []): array
    {
        return array_merge([
            'jenis_agenda' => 'internal',
            'perihal_kegiatan' => 'Rapat koordinasi SPBE',
            'waktu_mulai' => now()->addDay()->format('Y-m-d H:i:s'),
            'waktu_selesai' => now()->addDay()->addHours(2)->format('Y-m-d H:i:s'),
            'tempat' => 'Ruang Rapat Diskominfo',
            'asal_surat' => 'Kepala Dinas',
            'tanggal_surat' => now()->format('Y-m-d'),
            'pakaian' => 'PDH',
            'disposisi' => 'Dihadiri pejabat terkait',
            'petugas_ditugaskan' => 'Operator Bidang eGov',
            'status' => 'terjadwal',
            'keterangan' => 'Agenda uji otomatis',
        ], $overrides);
    }
}
