<?php

namespace Tests\Feature;

use App\Models\Family;
use App\Models\User;
use App\Models\Expense;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class EnhancementsTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_manage_members_and_permissions()
    {
        $family = Family::create(['family_name' => 'Test Family']);
        $admin = User::create([
            'family_id' => $family->family_id,
            'name' => 'Admin',
            'email' => 'admin@test.com',
            'password' => 'password',
            'role' => 'admin',
        ]);
        $member = User::create([
            'family_id' => $family->family_id,
            'name' => 'Member',
            'email' => 'member@test.com',
            'password' => 'password',
            'role' => 'member',
        ]);

        $this->actingAs($admin);

        // 1. Admin can see members in settings
        $response = $this->get(route('settings.index'));
        $response->assertStatus(200);
        $response->assertSee('Member');

        // 2. Admin can update member permissions
        $response = $this->put(route('family.member.update', $member->user_id), [
            'name' => 'Member Updated',
            'role' => 'member',
            'can_view_all_expenses' => '1',
        ]);
        $response->assertSessionHasNoErrors();
        $this->assertTrue((bool) $member->fresh()->can_view_all_expenses);
    }

    public function test_navigation_and_controller_restrictions()
    {
        $family = Family::create(['family_name' => 'Test Family']);
        $member = User::create([
            'family_id' => $family->family_id,
            'name' => 'Member',
            'email' => 'member@test.com',
            'password' => 'password',
            'role' => 'member',
        ]);

        $this->actingAs($member);

        // 1. Non-admin cannot access Categories
        $response = $this->get(route('categories.index'));
        $response->assertStatus(403);

        // 2. Non-admin cannot access Tags
        $response = $this->get(route('tags.index'));
        $response->assertStatus(403);
    }

    public function test_visibility_logic()
    {
        $family = Family::create(['family_name' => 'Test Family']);
        $admin = User::create([
            'family_id' => $family->family_id,
            'name' => 'Admin',
            'email' => 'admin@test.com',
            'password' => 'password',
            'role' => 'admin',
        ]);
        $member1 = User::create([
            'family_id' => $family->family_id,
            'name' => 'Member 1',
            'email' => 'm1@test.com',
            'password' => 'password',
            'role' => 'member',
        ]);
        $member2 = User::create([
            'family_id' => $family->family_id,
            'name' => 'Member 2',
            'email' => 'm2@test.com',
            'password' => 'password',
            'role' => 'member',
        ]);

        $category = \App\Models\Category::create([
            'family_id' => $family->family_id,
            'category_name' => 'Test Category',
            'is_default' => true,
        ]);

        $tag = \App\Models\Tag::create([
            'family_id' => $family->family_id,
            'tag_name' => 'Test Tag',
            'category_id' => $category->category_id,
            'is_default' => true,
        ]);

        Expense::create([
            'family_id' => $family->family_id,
            'logged_by_user_id' => $member1->user_id,
            'from_account_user_id' => $member1->user_id,
            'amount' => 100,
            'date' => now(),
            'category_id' => $category->category_id,
            'tag_id' => $tag->tag_id,
        ]);

        Expense::create([
            'family_id' => $family->family_id,
            'logged_by_user_id' => $member2->user_id,
            'from_account_user_id' => $member2->user_id,
            'amount' => 200,
            'date' => now(),
            'category_id' => $category->category_id,
            'tag_id' => $tag->tag_id,
        ]);

        // 1. Admin sees all
        $this->actingAs($admin);
        $response = $this->get(route('expenses.index'));
        $response->assertSee('100');
        $response->assertSee('200');

        // 2. Member 1 sees only own
        $this->actingAs($member1);
        $response = $this->get(route('expenses.index'));
        $response->assertSee('100.00');
        $response->assertDontSee('200.00');
        $response->assertDontSee('Member 2');

        // 3. Member with permission sees all
        $member1->update(['can_view_all_expenses' => true]);
        $response = $this->get(route('expenses.index'));
        $response->assertSee('100.00');
        $response->assertSee('200.00');
        $response->assertSee('Member 2');
    }
}
