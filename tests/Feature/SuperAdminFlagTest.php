<?php

use App\Models\User;
use Illuminate\Support\Facades\Schema;

test('users table has is_root_super_admin column', function () {
    expect(Schema::hasColumn('users', 'is_root_super_admin'))->toBeTrue();
});

test('pccpolrestabessemarang email has is_root_super_admin set to true by default', function () {
    // Check if the user seeded from AdminUserSeeder has the flag set to true
    $superAdmin = User::where('email', 'pccpolrestabessemarang@gmail.com')->first();

    if ($superAdmin) {
        expect($superAdmin->is_root_super_admin)->toBeTrue();
    } else {
        $this->markTestSkipped('Super Admin user was not seeded.');
    }
});

test('super admin middleware allows access to root super admin or super_admin role', function () {
    // Create root super admin
    $superAdmin = User::factory()->create([
        'role' => 'admin',
        'is_root_super_admin' => true,
        'is_active' => true,
    ]);

    $response = $this->actingAs($superAdmin)
        ->get(route('admin.accounts.index'));

    $response->assertSuccessful();
});

test('super admin middleware blocks standard admin who is not root super admin', function () {
    // Create a standard admin without is_root_super_admin flag
    $standardAdmin = User::factory()->create([
        'role' => 'admin',
        'is_root_super_admin' => false,
        'is_active' => true,
    ]);

    $response = $this->actingAs($standardAdmin)
        ->get(route('admin.accounts.index'));

    $response->assertForbidden();
});
