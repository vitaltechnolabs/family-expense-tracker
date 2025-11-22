<?php

namespace Tests\Feature;

use App\Models\Family;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Auth;
use Tests\TestCase;

class FamilyPortalTest extends TestCase
{
    use RefreshDatabase;

    public function test_family_portal_access_and_login()
    {
        // 1. Create Family and Users
        $family = Family::create(['family_name' => 'Test Family']);
        $admin = User::create([
            'family_id' => $family->family_id,
            'name' => 'Admin',
            'email' => 'admin@test.com',
            'password' => 'password',
            'role' => 'admin',
            'access_pin' => '1234',
        ]);
        $member = User::create([
            'family_id' => $family->family_id,
            'name' => 'Kid',
            'access_pin' => '5678', // No email/password
            'role' => 'member',
        ]);

        // 2. Visit Portal Page
        $response = $this->get(route('family.portal', $family->slug));
        $response->assertStatus(200);
        $response->assertSee('Test Family');
        $response->assertSee('Admin');
        $response->assertSee('Kid');

        // 3. Login with PIN (Success)
        $response = $this->post(route('family.login', $family->slug), [
            'user_id' => $member->user_id,
            'access_pin' => '5678',
        ]);
        $response->assertRedirect(route('dashboard'));
        $this->assertAuthenticatedAs($member);

        // 4. Login with PIN (Failure)
        Auth::logout();
        $response = $this->post(route('family.login', $family->slug), [
            'user_id' => $member->user_id,
            'access_pin' => '0000',
        ]);
        $response->assertSessionHasErrors('access_pin');
        $this->assertGuest();
    }
}
