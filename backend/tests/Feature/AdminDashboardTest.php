<?php

namespace Tests\Feature;

use App\Models\LogAktivitas;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Pagination\LengthAwarePaginator;
use Tests\TestCase;

class AdminDashboardTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_dashboard_renders_paginated_search_results(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);

        LogAktivitas::create([
            'user_id' => $admin->id,
            'aktivitas' => 'Memperbarui alat laboratorium',
        ]);
        LogAktivitas::create([
            'user_id' => $admin->id,
            'aktivitas' => 'Menyetujui peminjaman',
        ]);

        $response = $this->actingAs($admin)->get(route('admin.dashboard', [
            'search' => 'Memperbarui',
        ]));

        $response->assertOk()
            ->assertSee('Memperbarui alat laboratorium')
            ->assertDontSee('Menyetujui peminjaman')
            ->assertViewHas('logs', fn ($logs) => $logs instanceof LengthAwarePaginator);
    }
}