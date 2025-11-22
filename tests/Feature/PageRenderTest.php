<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Family;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PageRenderTest extends TestCase
{
    use RefreshDatabase;

    public function test_all_pages_render_correctly(): void
    {
        // 1. Test Guest Pages
        $response = $this->get('/login');
        $response->assertStatus(200);

        $response = $this->get('/register');
        $response->assertStatus(200);

        // 2. Setup Authenticated User
        $user = User::factory()->create();
        $family = Family::create([
            'family_name' => 'Test Family',
            'admin_user_id' => $user->user_id,
        ]);
        $user->update(['family_id' => $family->family_id]);

        $this->actingAs($user);

        // 3. Test Authenticated Pages
        $pages = [
            '/dashboard',
            '/expenses',
            '/expenses/create',
            '/categories',
            '/categories/create',
            '/tags',
            '/tags/create',
            '/reports',
            '/settings',
            '/profile',
            '/family/invite',
        ];

        foreach ($pages as $page) {
            $response = $this->get($page);
            $response->assertStatus(200);
        }
    }
}
