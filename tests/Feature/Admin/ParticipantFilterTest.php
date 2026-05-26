<?php

use App\Models\Participant;
use App\Models\User;
use App\Models\ViolationType;

/**
 * Helper to create an admin user and a participant with a given created_at date.
 *
 * @param  array{admin_id?: int, created_at?: string}  $overrides
 * @return array{admin: User, participant: Participant}
 */
function createAdminAndParticipant(array $overrides = []): array
{
    $admin = User::factory()->create([
        'role' => 'admin',
        'is_active' => true,
    ]);

    $violationType = ViolationType::firstOrCreate(
        ['name' => 'Test Violation'],
        ['description' => 'For testing purposes'],
    );

    $pesertaUser = User::factory()->create([
        'role' => 'peserta',
        'nik' => fake()->unique()->numerify('################'),
        'is_active' => true,
    ]);

    $participant = Participant::forceCreate([
        'user_id' => $pesertaUser->id,
        'assigned_admin_id' => $overrides['admin_id'] ?? $admin->id,
        'full_name' => $pesertaUser->name,
        'nik' => $pesertaUser->nik,
        'address' => 'Test Address',
        'phone' => '08123456789',
        'violation_type_id' => $violationType->id,
        'supervision_start' => now()->subDays(30),
        'supervision_end' => now()->addDays(30),
        'quota_type' => 'weekly',
        'quota_amount' => 2,
        'status' => 'active',
        'created_at' => $overrides['created_at'] ?? now(),
        'updated_at' => now(),
    ]);

    return ['admin' => $admin, 'participant' => $participant];
}

// -------------------------------------------------------
// Dashboard Filter Tests
// -------------------------------------------------------

test('dashboard loads successfully with no filters', function () {
    $admin = User::factory()->create(['role' => 'admin']);

    $this->actingAs($admin)
        ->get(route('admin.dashboard'))
        ->assertSuccessful()
        ->assertSee('Filter Data');
});

test('dashboard filters by date range', function () {
    ['admin' => $admin] = createAdminAndParticipant(['created_at' => '2026-01-15']);
    createAdminAndParticipant(['admin_id' => $admin->id, 'created_at' => '2026-03-20']);

    $response = $this->actingAs($admin)
        ->get(route('admin.dashboard', [
            'date_from' => '2026-03-01',
            'date_to' => '2026-03-31',
        ]));

    $response->assertSuccessful();

    // The paginator should only contain participants created in March
    $recentParticipants = $response->viewData('recentParticipants');
    expect($recentParticipants)->toHaveCount(1);
});

test('dashboard filters by admin id', function () {
    ['admin' => $admin1] = createAdminAndParticipant();
    ['admin' => $admin2] = createAdminAndParticipant();

    $response = $this->actingAs($admin1)
        ->get(route('admin.dashboard', ['admin_id' => $admin1->id]));

    $response->assertSuccessful();

    $recentParticipants = $response->viewData('recentParticipants');
    expect($recentParticipants)->toHaveCount(1);
    expect($recentParticipants->first()->assigned_admin_id)->toBe($admin1->id);
});

// -------------------------------------------------------
// Participants Index Filter Tests
// -------------------------------------------------------

test('participants index loads successfully with no filters', function () {
    $admin = User::factory()->create(['role' => 'admin']);

    $this->actingAs($admin)
        ->get(route('admin.participants.index'))
        ->assertSuccessful()
        ->assertSee('Filter Data');
});

test('participants index filters by date range', function () {
    ['admin' => $admin] = createAdminAndParticipant(['created_at' => '2026-02-10']);
    createAdminAndParticipant(['admin_id' => $admin->id, 'created_at' => '2026-04-15']);

    $response = $this->actingAs($admin)
        ->get(route('admin.participants.index', [
            'date_from' => '2026-04-01',
            'date_to' => '2026-04-30',
        ]));

    $response->assertSuccessful();

    $participants = $response->viewData('participants');
    expect($participants)->toHaveCount(1);
});

test('participants index filters by admin id', function () {
    ['admin' => $admin1] = createAdminAndParticipant();
    ['admin' => $admin2] = createAdminAndParticipant();

    $response = $this->actingAs($admin1)
        ->get(route('admin.participants.index', ['admin_id' => $admin2->id]));

    $response->assertSuccessful();

    $participants = $response->viewData('participants');
    expect($participants)->toHaveCount(1);
    expect($participants->first()->assigned_admin_id)->toBe($admin2->id);
});

test('participants index combines search with date filter', function () {
    ['admin' => $admin, 'participant' => $p] = createAdminAndParticipant(['created_at' => '2026-05-01']);

    $response = $this->actingAs($admin)
        ->get(route('admin.participants.index', [
            'search' => $p->full_name,
            'date_from' => '2026-05-01',
            'date_to' => '2026-05-31',
        ]));

    $response->assertSuccessful();

    $participants = $response->viewData('participants');
    expect($participants)->toHaveCount(1);
});

// -------------------------------------------------------
// Reports Index Filter Tests
// -------------------------------------------------------

test('reports index loads successfully with no filters', function () {
    $admin = User::factory()->create(['role' => 'admin']);

    $this->actingAs($admin)
        ->get(route('admin.reports.index'))
        ->assertSuccessful()
        ->assertSee('Filter Data');
});

test('reports index filters by date range', function () {
    ['admin' => $admin] = createAdminAndParticipant(['created_at' => '2026-01-05']);
    createAdminAndParticipant(['admin_id' => $admin->id, 'created_at' => '2026-06-10']);

    $response = $this->actingAs($admin)
        ->get(route('admin.reports.index', [
            'date_from' => '2026-06-01',
            'date_to' => '2026-06-30',
        ]));

    $response->assertSuccessful();

    $participants = $response->viewData('participants');
    expect($participants)->toHaveCount(1);
});

test('reports index filters by admin id', function () {
    ['admin' => $admin1] = createAdminAndParticipant();
    ['admin' => $admin2] = createAdminAndParticipant();

    $response = $this->actingAs($admin1)
        ->get(route('admin.reports.index', ['admin_id' => $admin1->id]));

    $response->assertSuccessful();

    $participants = $response->viewData('participants');
    expect($participants)->toHaveCount(1);
    expect($participants->first()->assigned_admin_id)->toBe($admin1->id);
});

test('reports index combines all filters', function () {
    ['admin' => $admin1] = createAdminAndParticipant(['created_at' => '2026-03-15']);
    ['admin' => $admin2] = createAdminAndParticipant(['created_at' => '2026-03-20']);

    $response = $this->actingAs($admin1)
        ->get(route('admin.reports.index', [
            'date_from' => '2026-03-01',
            'date_to' => '2026-03-31',
            'admin_id' => $admin1->id,
        ]));

    $response->assertSuccessful();

    $participants = $response->viewData('participants');
    expect($participants)->toHaveCount(1);
    expect($participants->first()->assigned_admin_id)->toBe($admin1->id);
});

// -------------------------------------------------------
// Filter preserves query string in pagination
// -------------------------------------------------------

test('filter preserves query string on dashboard pagination', function () {
    $admin = User::factory()->create(['role' => 'admin']);

    $response = $this->actingAs($admin)
        ->get(route('admin.dashboard', [
            'date_from' => '2026-01-01',
            'admin_id' => $admin->id,
            'per_page' => 5,
        ]));

    $response->assertSuccessful();
});

// -------------------------------------------------------
// Dynamic Stat Filter Tests — Status Akun
// -------------------------------------------------------

test('participants index filters by status_akun aktif', function () {
    ['admin' => $admin, 'participant' => $active] = createAdminAndParticipant();
    // Create an inactive participant under the same admin
    $inactiveUser = User::factory()->create(['role' => 'peserta', 'nik' => fake()->unique()->numerify('################'), 'is_active' => false]);
    $violationType = \App\Models\ViolationType::first();
    Participant::forceCreate([
        'user_id' => $inactiveUser->id,
        'assigned_admin_id' => $admin->id,
        'full_name' => $inactiveUser->name,
        'nik' => $inactiveUser->nik,
        'address' => 'Addr',
        'phone' => '0812',
        'violation_type_id' => $violationType->id,
        'supervision_start' => now()->subDays(30),
        'supervision_end' => now()->addDays(30),
        'quota_type' => 'weekly',
        'quota_amount' => 2,
        'status' => 'inactive',
        'created_at' => now(),
        'updated_at' => now(),
    ]);

    $response = $this->actingAs($admin)
        ->get(route('admin.participants.index', ['status_akun' => 'aktif']));

    $response->assertSuccessful();
    $participants = $response->viewData('participants');
    expect($participants->every(fn ($p) => $p->status === 'active'))->toBeTrue();
});

test('participants index filters by status_akun tidak_aktif', function () {
    ['admin' => $admin] = createAdminAndParticipant();
    $inactiveUser = User::factory()->create(['role' => 'peserta', 'nik' => fake()->unique()->numerify('################'), 'is_active' => false]);
    $violationType = \App\Models\ViolationType::first();
    $inactive = Participant::forceCreate([
        'user_id' => $inactiveUser->id,
        'assigned_admin_id' => $admin->id,
        'full_name' => $inactiveUser->name,
        'nik' => $inactiveUser->nik,
        'address' => 'Addr',
        'phone' => '0812',
        'violation_type_id' => $violationType->id,
        'supervision_start' => now()->subDays(30),
        'supervision_end' => now()->addDays(30),
        'quota_type' => 'weekly',
        'quota_amount' => 2,
        'status' => 'inactive',
        'created_at' => now(),
        'updated_at' => now(),
    ]);

    $response = $this->actingAs($admin)
        ->get(route('admin.participants.index', ['status_akun' => 'tidak_aktif']));

    $response->assertSuccessful();
    $participants = $response->viewData('participants');
    expect($participants)->toHaveCount(1);
    expect($participants->first()->status)->toBe('inactive');
});

// -------------------------------------------------------
// Dynamic Stat Filter Tests — Tingkat Kepatuhan
// -------------------------------------------------------

test('participants index filters by tingkat_kepatuhan patuh (no active warnings)', function () {
    ['admin' => $admin, 'participant' => $patuh] = createAdminAndParticipant();
    ['participant' => $mangkir] = createAdminAndParticipant(['admin_id' => $admin->id]);

    // Give mangkir an active level_2 warning
    \App\Models\Warning::forceCreate([
        'participant_id' => $mangkir->id,
        'level' => 'level_2',
        'status' => 'active',
        'reason' => 'Test',
        'issued_at' => now(),
    ]);

    $response = $this->actingAs($admin)
        ->get(route('admin.participants.index', ['tingkat_kepatuhan' => 'patuh']));

    $response->assertSuccessful();
    $participants = $response->viewData('participants');
    // Only the one without active warnings should appear
    expect($participants)->toHaveCount(1);
    expect($participants->first()->id)->toBe($patuh->id);
});

test('participants index filters by tingkat_kepatuhan berisiko (active level_1)', function () {
    ['admin' => $admin, 'participant' => $p1] = createAdminAndParticipant();
    ['participant' => $berisiko] = createAdminAndParticipant(['admin_id' => $admin->id]);

    // Give berisiko an active level_1 warning
    \App\Models\Warning::forceCreate([
        'participant_id' => $berisiko->id,
        'level' => 'level_1',
        'status' => 'active',
        'reason' => 'Test risk',
        'issued_at' => now(),
    ]);

    $response = $this->actingAs($admin)
        ->get(route('admin.participants.index', ['tingkat_kepatuhan' => 'berisiko']));

    $response->assertSuccessful();
    $participants = $response->viewData('participants');
    expect($participants)->toHaveCount(1);
    expect($participants->first()->id)->toBe($berisiko->id);
});

test('participants index filters by tingkat_kepatuhan mangkir (active level_2 or 3)', function () {
    ['admin' => $admin, 'participant' => $p1] = createAdminAndParticipant();
    ['participant' => $mangkir] = createAdminAndParticipant(['admin_id' => $admin->id]);

    \App\Models\Warning::forceCreate([
        'participant_id' => $mangkir->id,
        'level' => 'level_3',
        'status' => 'active',
        'reason' => 'Severe absence',
        'issued_at' => now(),
    ]);

    $response = $this->actingAs($admin)
        ->get(route('admin.participants.index', ['tingkat_kepatuhan' => 'mangkir']));

    $response->assertSuccessful();
    $participants = $response->viewData('participants');
    expect($participants)->toHaveCount(1);
    expect($participants->first()->id)->toBe($mangkir->id);
});

// -------------------------------------------------------
// Dynamic Stat Filter Tests — Progress Pengawasan
// -------------------------------------------------------

test('participants index filters by progress_pengawasan selesai', function () {
    ['admin' => $admin] = createAdminAndParticipant();
    // Create a participant whose supervision already ended
    $doneUser = User::factory()->create(['role' => 'peserta', 'nik' => fake()->unique()->numerify('################')]);
    $violationType = \App\Models\ViolationType::first();
    $done = Participant::forceCreate([
        'user_id' => $doneUser->id,
        'assigned_admin_id' => $admin->id,
        'full_name' => $doneUser->name,
        'nik' => $doneUser->nik,
        'address' => 'Addr',
        'phone' => '0812',
        'violation_type_id' => $violationType->id,
        'supervision_start' => now()->subDays(90),
        'supervision_end' => now()->subDays(1),
        'quota_type' => 'weekly',
        'quota_amount' => 2,
        'status' => 'active',
        'created_at' => now(),
        'updated_at' => now(),
    ]);

    $response = $this->actingAs($admin)
        ->get(route('admin.participants.index', ['progress_pengawasan' => 'selesai']));

    $response->assertSuccessful();
    $participants = $response->viewData('participants');
    expect($participants)->toHaveCount(1);
    expect($participants->first()->id)->toBe($done->id);
});

test('participants index filters by progress_pengawasan segera_selesai', function () {
    ['admin' => $admin] = createAdminAndParticipant(); // ends in 30 days — not segera selesai
    // Create a participant whose supervision ends in 5 days
    $soonUser = User::factory()->create(['role' => 'peserta', 'nik' => fake()->unique()->numerify('################')]);
    $violationType = \App\Models\ViolationType::first();
    $soon = Participant::forceCreate([
        'user_id' => $soonUser->id,
        'assigned_admin_id' => $admin->id,
        'full_name' => $soonUser->name,
        'nik' => $soonUser->nik,
        'address' => 'Addr',
        'phone' => '0812',
        'violation_type_id' => $violationType->id,
        'supervision_start' => now()->subDays(30),
        'supervision_end' => now()->addDays(5),
        'quota_type' => 'weekly',
        'quota_amount' => 2,
        'status' => 'active',
        'created_at' => now(),
        'updated_at' => now(),
    ]);

    $response = $this->actingAs($admin)
        ->get(route('admin.participants.index', ['progress_pengawasan' => 'segera_selesai']));

    $response->assertSuccessful();
    $participants = $response->viewData('participants');
    expect($participants)->toHaveCount(1);
    expect($participants->first()->id)->toBe($soon->id);
});

// -------------------------------------------------------
// Guard: tingkat_kepatuhan ignored when status_akun = tidak_aktif
// -------------------------------------------------------

test('tingkat_kepatuhan filter is skipped when status_akun is tidak_aktif', function () {
    ['admin' => $admin] = createAdminAndParticipant();
    $inactiveUser = User::factory()->create(['role' => 'peserta', 'nik' => fake()->unique()->numerify('################'), 'is_active' => false]);
    $violationType = \App\Models\ViolationType::first();
    Participant::forceCreate([
        'user_id' => $inactiveUser->id,
        'assigned_admin_id' => $admin->id,
        'full_name' => $inactiveUser->name,
        'nik' => $inactiveUser->nik,
        'address' => 'Addr',
        'phone' => '0812',
        'violation_type_id' => $violationType->id,
        'supervision_start' => now()->subDays(30),
        'supervision_end' => now()->addDays(30),
        'quota_type' => 'weekly',
        'quota_amount' => 2,
        'status' => 'inactive',
        'created_at' => now(),
        'updated_at' => now(),
    ]);

    // Combining tidak_aktif + patuh should still show the inactive participant
    // because the kepatuhan filter is skipped for inactive
    $response = $this->actingAs($admin)
        ->get(route('admin.participants.index', [
            'status_akun' => 'tidak_aktif',
            'tingkat_kepatuhan' => 'patuh',
        ]));

    $response->assertSuccessful();
    $participants = $response->viewData('participants');
    expect($participants)->toHaveCount(1);
    expect($participants->first()->status)->toBe('inactive');
});

// -------------------------------------------------------
// Dynamic Stat Filters on Dashboard & Reports
// -------------------------------------------------------

test('dashboard filters by status_akun', function () {
    ['admin' => $admin] = createAdminAndParticipant();

    $response = $this->actingAs($admin)
        ->get(route('admin.dashboard', ['status_akun' => 'aktif']));

    $response->assertSuccessful();
    $recentParticipants = $response->viewData('recentParticipants');
    expect($recentParticipants->every(fn ($p) => $p->status === 'active'))->toBeTrue();
});

test('reports index filters by status_akun', function () {
    ['admin' => $admin] = createAdminAndParticipant();

    $response = $this->actingAs($admin)
        ->get(route('admin.reports.index', ['status_akun' => 'aktif']));

    $response->assertSuccessful();
    $participants = $response->viewData('participants');
    expect($participants->every(fn ($p) => $p->status === 'active'))->toBeTrue();
});

test('reports index filters by tingkat_kepatuhan', function () {
    ['admin' => $admin] = createAdminAndParticipant();

    $response = $this->actingAs($admin)
        ->get(route('admin.reports.index', ['tingkat_kepatuhan' => 'patuh']));

    $response->assertSuccessful();
});

test('reports index filters by progress_pengawasan', function () {
    ['admin' => $admin] = createAdminAndParticipant();

    $response = $this->actingAs($admin)
        ->get(route('admin.reports.index', ['progress_pengawasan' => 'selesai']));

    $response->assertSuccessful();
});

