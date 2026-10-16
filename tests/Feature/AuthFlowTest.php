<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Poli;
use App\Models\Obat;
use App\Models\JadwalPeriksa;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthFlowTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->withoutVite();
    }

    public function test_login_page_posts_to_login_submit_route(): void
    {
        $this->get(route('login'))
            ->assertOk()
            ->assertSee('action="' . route('login.submit') . '"', false);
    }

    public function test_admin_can_log_in_and_is_redirected_to_existing_admin_page(): void
    {
        $user = User::factory()->create([
            'role' => 'admin',
            'password' => 'secret-password',
        ]);

        $this->post(route('login.submit'), [
            'email' => $user->email,
            'password' => 'secret-password',
        ])
            ->assertRedirect(route('admin.poli.index'))
            ->assertSessionHas('_token');

        $this->assertAuthenticatedAs($user);
    }

    public function test_admin_can_view_the_poli_management_page(): void
    {
        $user = User::factory()->create([
            'role' => 'admin',
        ]);

        $this->actingAs($user);

        $this->get(route('admin.poli.index'))
            ->assertOk()
            ->assertSee('Data Poli')
            ->assertSee('Tambah Poli')
            ->assertSee('Belum ada data poli');

        $this->get(route('polis.create'))
            ->assertOk()
            ->assertSee('Tambah Poli')
            ->assertSee('Masukkan nama poli...')
            ->assertSee('Masukkan keterangan poli...')
            ->assertSee('Simpan');
    }

    public function test_admin_can_open_the_poli_edit_page(): void
    {
        $admin = User::factory()->create([
            'role' => 'admin',
        ]);
        $poli = Poli::create([
            'nama_poli' => 'Umum',
            'keterangan' => 'Poli umum',
        ]);

        $this->actingAs($admin)
            ->get(route('polis.edit', $poli))
            ->assertOk()
            ->assertSee('Edit Poli')
            ->assertSee('Umum');
    }

    public function test_admin_can_open_patient_management_and_form_pages(): void
    {
        $admin = User::factory()->create([
            'role' => 'admin',
        ]);
        $pasien = User::factory()->create([
            'role' => 'pasien',
            'nama' => 'Pasien Contoh',
            'no_ktp' => '1234567890123456',
            'no_hp' => '081234567890',
            'alamat' => 'Jalan Melati',
        ]);

        $this->actingAs($admin)
            ->get(route('pasien.index'))
            ->assertOk()
            ->assertSee('Data Pasien')
            ->assertSee('Tambah Pasien')
            ->assertSee('Pasien Contoh')
            ->assertSee('1234567890123456');

        $this->get(route('pasien.create'))
            ->assertOk()
            ->assertSee('Tambah Pasien')
            ->assertSee('Masukkan nama pasien...');

        $this->get(route('pasien.edit', $pasien))
            ->assertOk()
            ->assertSee('Edit Pasien')
            ->assertSee('Pasien Contoh')
            ->assertSee('Kosongkan jika tidak ingin mengubah...');
    }

    public function test_admin_can_open_medicine_management_and_form_pages(): void
    {
        $admin = User::factory()->create([
            'role' => 'admin',
        ]);
        $obat = Obat::create([
            'nama_obat' => 'Paracetamol',
            'kemasan' => 'Strip',
            'harga' => 12000,
        ]);

        $this->actingAs($admin)
            ->get(route('obat.index'))
            ->assertOk()
            ->assertSee('Data Obat')
            ->assertSee('Tambah Obat')
            ->assertSee('Paracetamol')
            ->assertSee('Strip')
            ->assertSee('Rp 12.000');

        $this->get(route('obat.create'))
            ->assertOk()
            ->assertSee('Tambah Obat')
            ->assertSee('Masukkan nama obat...');

        $this->get(route('obat.edit', $obat))
            ->assertOk()
            ->assertSee('Edit Obat')
            ->assertSee('Paracetamol');
    }

    public function test_doctor_can_open_jadwal_periksa_from_sidebar(): void
    {
        $dokter = User::factory()->create([
            'role' => 'dokter',
        ]);
        $jadwal = JadwalPeriksa::create([
            'id_dokter' => $dokter->id,
            'hari' => 'Senin',
            'jam_mulai' => '08:00',
            'jam_selesai' => '12:00',
        ]);

        $this->actingAs($dokter)
            ->get(route('dokter.dashboard'))
            ->assertOk()
            ->assertSee('Jadwal Periksa')
            ->assertSee(route('jadwal-periksa.index'));

        $this->get(route('jadwal-periksa.index'))
            ->assertOk()
            ->assertSee('Jadwal Periksa')
            ->assertSee('Senin')
            ->assertSee('08:00')
            ->assertSee('12:00');

        $this->get(route('jadwal-periksa.create'))
            ->assertOk()
            ->assertSee('Tambah Jadwal Periksa');

        $this->get(route('jadwal-periksa.edit', $jadwal))
            ->assertOk()
            ->assertSee('Edit Jadwal Periksa')
            ->assertSee('Senin');
    }

    public function test_admin_can_open_doctor_management_and_form_pages(): void
    {
        $admin = User::factory()->create([
            'role' => 'admin',
        ]);
        $poli = Poli::create([
            'nama_poli' => 'Umum',
            'keterangan' => 'Poli umum',
        ]);
        $dokter = User::factory()->create([
            'role' => 'dokter',
            'id_poli' => $poli->id,
            'nama' => 'Dokter Contoh',
            'no_ktp' => '1234567890123456',
            'no_hp' => '081234567890',
            'alamat' => 'Jalan Melati',
        ]);

        $this->actingAs($admin)
            ->get(route('dokter.index'))
            ->assertOk()
            ->assertSee('Data Dokter')
            ->assertSee('Tambah Dokter')
            ->assertSee('No. KTP')
            ->assertSee('No. HP')
            ->assertSee('Alamat')
            ->assertSee('1234567890123456')
            ->assertSee('Jalan Melati')
            ->assertSee('Umum');

        $this->get(route('dokter.create'))
            ->assertOk()
            ->assertSee('Tambah Dokter')
            ->assertSee('Masukkan nama dokter...')
            ->assertSee('Masukkan No. KTP...')
            ->assertSee('Masukkan No. HP...')
            ->assertSee('Masukkan alamat...')
            ->assertSee('Pilih Poli')
            ->assertSee('Minimal 8 karakter...')
            ->assertSee('Simpan');

        $this->get(route('dokter.edit', $dokter))
            ->assertOk()
            ->assertSee('Edit Dokter')
            ->assertSee($dokter->nama);
    }

    public function test_invalid_login_returns_a_field_error(): void
    {
        $this->from(route('login'))
            ->post(route('login.submit'), [
                'email' => 'unknown@example.com',
                'password' => 'wrong-password',
            ])
            ->assertRedirect(route('login'))
            ->assertSessionHasErrors('email');
    }

    public function test_register_page_posts_to_register_submit_route(): void
    {
        $this->get(route('register'))
            ->assertOk()
            ->assertSee('action="' . route('register.submit') . '"', false);
    }

    public function test_patient_can_register_with_a_confirmed_password(): void
    {
        $this->post(route('register.submit'), [
            'nama' => 'Siti Aminah',
            'alamat' => 'Jalan Melati 10',
            'no_ktp' => '0012345678901234',
            'no_hp' => '081234567890',
            'email' => 'siti@example.com',
            'password' => 'secret-password',
            'password_confirmation' => 'secret-password',
        ])
            ->assertRedirect(route('login'))
            ->assertSessionHas('success', 'Registrasi berhasil! Silakan login.');

        $this->assertDatabaseHas('users', [
            'nama' => 'Siti Aminah',
            'no_ktp' => '0012345678901234',
            'no_hp' => '081234567890',
            'email' => 'siti@example.com',
            'role' => 'pasien',
        ]);
    }

    public function test_register_requires_matching_password_confirmation(): void
    {
        $this->post(route('register.submit'), [
            'nama' => 'Siti Aminah',
            'alamat' => 'Jalan Melati 10',
            'no_ktp' => '0012345678901234',
            'no_hp' => '081234567890',
            'email' => 'siti@example.com',
            'password' => 'secret-password',
            'password_confirmation' => 'different-password',
        ])->assertSessionHasErrors('password');

        $this->assertDatabaseMissing('users', [
            'email' => 'siti@example.com',
        ]);
    }
}
