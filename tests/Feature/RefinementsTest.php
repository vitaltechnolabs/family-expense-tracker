<?php

namespace Tests\Feature;

use App\Models\Expense;
use App\Models\Family;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RefinementsTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_see_member_list_in_settings()
    {
        $family = Family::create(['family_name' => 'Test Family', 'slug' => 'test-family']);
        $admin = User::create([
            'family_id' => $family->family_id,
            'name' => 'Admin',
            'email' => 'admin@test.com',
            'password' => bcrypt('password'),
            'role' => 'admin',
        ]);
        $member = User::create([
            'family_id' => $family->family_id,
            'name' => 'Member',
            'email' => 'member@test.com',
            'password' => bcrypt('password'),
            'role' => 'member',
        ]);

        $this->actingAs($admin);
        $response = $this->get(route('settings.index'));

        $response->assertStatus(200);
        $response->assertSee('Family Members');
        $response->assertSee('Member');
    }

    public function test_member_cannot_see_member_list_in_settings()
    {
        $family = Family::create(['family_name' => 'Test Family', 'slug' => 'test-family']);
        $member = User::create([
            'family_id' => $family->family_id,
            'name' => 'Member',
            'email' => 'member@test.com',
            'password' => bcrypt('password'),
            'role' => 'member',
        ]);

        $this->actingAs($member);
        $response = $this->get(route('settings.index'));

        $response->assertStatus(200);
        $response->assertDontSee('Family Members');
    }

    public function test_dashboard_visibility_logic()
    {
        $family = Family::create(['family_name' => 'Test Family', 'slug' => 'test-family']);
        $admin = User::create([
            'family_id' => $family->family_id,
            'name' => 'Admin',
            'email' => 'admin@test.com',
            'password' => bcrypt('password'),
            'role' => 'admin',
        ]);
        $member1 = User::create([
            'family_id' => $family->family_id,
            'name' => 'Member 1',
            'email' => 'm1@test.com',
            'password' => bcrypt('password'),
            'role' => 'member',
        ]);
        $member2 = User::create([
            'family_id' => $family->family_id,
            'name' => 'Member 2',
            'email' => 'm2@test.com',
            'password' => bcrypt('password'),
            'role' => 'member',
        ]);

        // Create categories and tags (needed for factory/creation if strict)
        // For simplicity, we'll just use raw creation or assume nullable if not strictly enforced in test setup, 
        // but Expense model requires them.
        $category = \App\Models\Category::create(['family_id' => $family->family_id, 'category_name' => 'Food', 'is_default' => true]);
        $tag = \App\Models\Tag::create(['family_id' => $family->family_id, 'tag_name' => 'Lunch', 'category_id' => $category->category_id, 'is_default' => true]);

        // Expense by Member 1
        Expense::create([
            'family_id' => $family->family_id,
            'logged_by_user_id' => $member1->user_id,
            'amount' => 100,
            'date' => now(),
            'category_id' => $category->category_id,
            'tag_id' => $tag->tag_id,
            'payment_method' => 'Cash',
            'from_account_user_id' => $member1->user_id,
        ]);

        // Expense by Member 2
        Expense::create([
            'family_id' => $family->family_id,
            'logged_by_user_id' => $member2->user_id,
            'amount' => 200,
            'date' => now(),
            'category_id' => $category->category_id,
            'tag_id' => $tag->tag_id,
            'payment_method' => 'Cash',
            'from_account_user_id' => $member2->user_id,
        ]);

        // 1. Admin sees all (Total 300)
        $this->actingAs($admin);
        $response = $this->get(route('dashboard'));
        $response->assertSee('₹300.00');

        // 2. Member 1 sees only own (Total 100)
        $this->actingAs($member1);
        $response = $this->get(route('dashboard'));
        // file_put_contents(base_path('debug.html'), $response->getContent());
        $response->assertSee('₹100.00');
        $response->assertDontSee('₹300.00');

        // 3. Member 1 with permission sees all (Total 300)
        $member1->update(['can_view_all_expenses' => true]);
        $response = $this->get(route('dashboard'));
        $response->assertSee('₹300.00');
    }
}
