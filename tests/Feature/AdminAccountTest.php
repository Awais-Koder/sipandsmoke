<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AdminAccountTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_update_email_and_password(): void
    {
        $admin = User::factory()->create([
            'is_admin' => true,
            'password' => Hash::make('old-password'),
        ]);

        $this->actingAs($admin)
            ->get(route('admin.account'))
            ->assertOk()
            ->assertSee('Email and password')
            ->assertSee($admin->email);

        $this->put(route('admin.account.update'), [
                'email' => 'new-admin@example.com',
                'current_password' => 'old-password',
                'password' => 'new-password-123',
                'password_confirmation' => 'new-password-123',
            ])
            ->assertRedirect(route('admin.account'))
            ->assertSessionHas('status');

        $admin->refresh();

        $this->assertSame('new-admin@example.com', $admin->email);
        $this->assertTrue(Hash::check('new-password-123', $admin->password));
    }

    public function test_admin_can_update_email_without_changing_password(): void
    {
        $admin = User::factory()->create([
            'is_admin' => true,
            'password' => Hash::make('old-password'),
        ]);

        $this->actingAs($admin)
            ->put(route('admin.account.update'), [
                'email' => 'another-admin@example.com',
                'current_password' => 'old-password',
            ])
            ->assertRedirect(route('admin.account'));

        $admin->refresh();

        $this->assertSame('another-admin@example.com', $admin->email);
        $this->assertTrue(Hash::check('old-password', $admin->password));
    }

    public function test_wrong_current_password_is_rejected(): void
    {
        $admin = User::factory()->create([
            'is_admin' => true,
            'password' => Hash::make('old-password'),
        ]);

        $this->actingAs($admin)
            ->from(route('admin.account'))
            ->put(route('admin.account.update'), [
                'email' => 'new-admin@example.com',
                'current_password' => 'not-the-password',
                'password' => 'new-password-123',
                'password_confirmation' => 'new-password-123',
            ])
            ->assertSessionHasErrors('current_password');

        $this->assertSame($admin->email, $admin->fresh()->email);
    }
}
