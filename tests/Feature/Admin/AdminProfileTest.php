<?php

use App\Models\User;
use Illuminate\Support\Facades\Hash;

test('admin profile page is displayed successfully', function () {
    $admin = User::factory()->create([
        'role' => 'admin',
        'is_active' => true,
    ]);

    $response = $this->actingAs($admin)
        ->get(route('admin.profile.edit'));

    $response->assertSuccessful();
    $response->assertSee('Edit Profil Admin');
});

test('admin profile can be updated successfully', function () {
    $admin = User::factory()->create([
        'role' => 'admin',
        'is_active' => true,
    ]);

    $response = $this->actingAs($admin)
        ->patch(route('admin.profile.update'), [
            'name' => 'Updated Admin Name',
            'email' => 'updatedadmin@example.com',
        ]);

    $response->assertSessionHasNoErrors();
    $response->assertRedirect(route('admin.profile.edit'));

    $admin->refresh();
    expect($admin->name)->toBe('Updated Admin Name');
    expect($admin->email)->toBe('updatedadmin@example.com');
});

test('admin profile password can be optionally updated', function () {
    $admin = User::factory()->create([
        'role' => 'admin',
        'password' => Hash::make('oldpassword'),
        'is_active' => true,
    ]);

    $response = $this->actingAs($admin)
        ->patch(route('admin.profile.update'), [
            'name' => $admin->name,
            'email' => $admin->email,
            'password' => 'newpassword123',
            'password_confirmation' => 'newpassword123',
        ]);

    $response->assertSessionHasNoErrors();
    $response->assertRedirect(route('admin.profile.edit'));

    $admin->refresh();
    expect(Hash::check('newpassword123', $admin->password))->toBeTrue();
});

test('crucial security: admin cannot change their role during profile update', function () {
    $admin = User::factory()->create([
        'role' => 'admin',
        'is_active' => true,
    ]);

    $response = $this->actingAs($admin)
        ->patch(route('admin.profile.update'), [
            'name' => 'Name',
            'email' => 'email@example.com',
            'role' => 'peserta', // Attempting to change role
        ]);

    $response->assertSessionHasNoErrors();
    $admin->refresh();
    expect($admin->role)->toBe('admin'); // Role remains unchanged
});

test('crucial security: root super admin retains is_root_super_admin flag even if email changes', function () {
    $superAdmin = User::factory()->create([
        'role' => 'admin',
        'email' => 'pccpolrestabessemarang@gmail.com',
        'is_root_super_admin' => true,
        'is_active' => true,
    ]);

    $response = $this->actingAs($superAdmin)
        ->patch(route('admin.profile.update'), [
            'name' => 'New Super Name',
            'email' => 'newsuperemail@example.com', // Changing email
        ]);

    $response->assertSessionHasNoErrors();
    $response->assertRedirect(route('admin.profile.edit'));

    $superAdmin->refresh();
    expect($superAdmin->email)->toBe('newsuperemail@example.com');
    expect($superAdmin->is_root_super_admin)->toBeTrue(); // Flag must remain true
});
