<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\UserPreference;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Password;
use Tests\TestCase;

class NewsAggregatorTest extends TestCase
{
    //A test case to test user can register
    public function test_user_can_register()
    {
        $response = $this->postJson('/api/register', [
            'name' => 'Deekshith Shetty',
            'email' => 'deekshithshetty@gmail.com',
            'password' => '123456',
            'password_confirmation' => '123456'
        ]);
    
        $response->assertStatus(201)
            ->assertJsonStructure([
                'message',
                'user' => ['id', 'name', 'email', 'created_at', 'updated_at'],
                'token'
            ]);
    }

    //A test case to test user can login with valid credencials
    public function test_user_can_login_with_valid_credentials()
    {
        User::factory()->create([
            'email' => 'deekshithtest@gmail.com',
            'password' => bcrypt('123456')
        ]);

        $response = $this->postJson('/api/login', [
            'email' => 'deekshithtest@gmail.com',
            'password' => '123456'
        ]);

        $response->assertStatus(200)
            ->assertJsonStructure([
                'message',
                'token',
                'user' => ['id', 'name', 'email', 'created_at', 'updated_at']
            ]);
    }

    //A test case to test user can not login with invalid credencials
    public function test_user_cannot_login_with_invalid_credentials()
    {
        $response = $this->postJson('/api/login', [
            'email' => 'wrong@gmail.com',
            'password' => 'wrongpassword'
        ]);

        $response->assertStatus(401)
            ->assertJson([
                'message' => 'Invalid login credentials'
            ]);
    }

    //A test case to test user can logout
    public function test_user_can_logout()
    {
        $user = User::factory()->create();
        $token = $user->createToken('API Token')->plainTextToken;

        $response = $this->withHeader('Authorization', "Bearer $token")->postJson('/api/logout');

        $response->assertStatus(200)
            ->assertJson([
                'message' => 'Logout successful'
            ]);
    }

    //A test case to test user can request for password reset
    public function test_password_reset_request()
    {
        User::factory()->create(['email' => 'deekshithtest123@gmail.com']);

        $response = $this->postJson('/api/password/reset-request', [
            'email' => 'deekshithtest123@gmail.com'
        ]);

        $response->assertStatus(200)
            ->assertJson([
                'message' => 'We have emailed your password reset link.'
            ]);
    }

    //A test case to test user can reset his password
    public function test_password_can_be_reset()
    {
        $user = User::factory()->create(['email' => 'deekshithresetpassword@gmail.com']);
        $token = Password::createToken($user);

        $response = $this->postJson('/api/password/reset', [
            'email' => 'deekshithresetpassword@gmail.com',
            'password' => '456789',
            'password_confirmation' => '456789',
            'token' => $token
        ]);

        $response->assertStatus(200)
            ->assertJson([
                'message' => 'Your password has been reset.'
            ]);
    }

    //A test case to test user can set his article preferences
    public function test_user_can_set_article_preferences()
    {
        $user = User::factory()->create();
        $token = $user->createToken('API Token')->plainTextToken;

        $response = $this->withHeader('Authorization', "Bearer $token")->postJson('/api/user/article/set-preference', [
            'source' => ['BBC News'],
            'category' => ['Society'],
            'author' => ['BBC News', 'The New York Times']
        ]);

        $response->assertStatus(200)
            ->assertJsonStructure([
                'message',
                'data' => ['id', 'user_id', 'prefer_source', 'prefer_categories', 'prefer_author', 'created_at', 'updated_at']
            ]);
    }

    //A test case to test user can remove saved his article preferences
    public function test_user_can_delete_article_preferences()
    {
        $user = User::factory()->create();
        $token = $user->createToken('API Token')->plainTextToken;

        // Assume preferences are already set
        UserPreference::create([
            'user_id' => $user->id,
            'prefer_source' => 'BBC News',
            'prefer_categories' => 'Society',
            'prefer_author' => 'BBC News,The New York Times'
        ]);

        // Call the API endpoint
        $response = $this->withHeader('Authorization', "Bearer $token")->deleteJson('/api/user/article/preference');

        // Assert success response
        $response->assertStatus(200);
        $response->assertJson([
            'message' => 'user article preference removed successfully',
            'data' => '',
        ]);

        // Assert that preferences are deleted
        $this->assertDatabaseMissing('user_preferences', ['user_id' => $user->id]);
    }

    //A test case to test user can get hist saved preference
    public function test_user_can_get_article_preferences()
    {
        $user = User::factory()->create();
        $token = $user->createToken('API Token')->plainTextToken;

        // Assume preferences are already set
        UserPreference::create([
            'user_id' => $user->id,
            'prefer_source' => 'BBC News',
            'prefer_categories' => 'Society',
            'prefer_author' => 'BBC News,The New York Times'
        ]);

        $response = $this->withHeader('Authorization', "Bearer $token")->getJson('/api/user/article/get-preference');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'message',
                'data' => ['id', 'user_id', 'prefer_source', 'prefer_categories', 'prefer_author', 'created_at', 'updated_at']
            ]);
    }

    //A test case to test user can fetch article based on his preference
    public function test_user_can_get_preferred_articles()
    {
        $user = User::factory()->create();
        $token = $user->createToken('API Token')->plainTextToken;

        UserPreference::create([
            'user_id' => $user->id,
            'prefer_source' => 'BBC News',
            'prefer_categories' => 'Society',
            'prefer_author' => 'BBC News,The New York Times'
        ]);

        $response = $this->withHeader('Authorization', "Bearer $token")->getJson('/api/article/user/prefered-list', [
            'search_key' => '',
            'category' => '',
            'source' => '',
            'published_date' => '',
            'page' => 1
        ]);

        $response->assertStatus(200)
            ->assertJsonStructure([
                'message',
                'data' => [
                    'current_page', 'data' => [['id', 'source_name', 'author', 'title', 'published_at', 'category']],
                    'first_page_url', 'last_page_url', 'next_page_url', 'prev_page_url', 'total'
                ]
            ]);
    }

    //A test case to test user can get articles
    public function test_user_can_get_articles()
    {
        $user = User::factory()->create();
        $token = $user->createToken('API Token')->plainTextToken;

        $response = $this->withHeader('Authorization', "Bearer $token")->getJson('/api/article/list', [
            'search_key' => '',
            'category' => '',
            'source' => '',
            'published_date' => '',
            'page' => 1
        ]);

        $response->assertStatus(200)
            ->assertJsonStructure([
                'message',
                'data' => [
                    'current_page', 'data' => [['id', 'source_name', 'author', 'title', 'published_at', 'category']],
                    'first_page_url', 'last_page_url', 'next_page_url', 'prev_page_url', 'total'
                ]
            ]);
    }
}
