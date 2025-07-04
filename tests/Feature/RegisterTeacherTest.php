<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
// use Illuminate\Foundation\Testing\RefreshDatabase;

class RegisterTeacherTest extends TestCase
{
    // use RefreshDatabase;

    /** @test */
    public function it_registers_a_teacher_successfully()
    {
        $email = 'teacher' . uniqid() . '@example.com';

        $payload = [
            'name' => 'Kunal Sharma',
            'email' => $email,
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'role' => 'teacher',
        ];

        $response = $this->postJson('/api/register', $payload);

        $response->assertStatus(201)
            ->assertJson([
                'message' => 'User registered with the defined role',
            ]);

        $this->assertDatabaseHas('users', [
            'email' => strtolower($email),
            'role' => 'teacher',
        ]);
    }

    /** @test */
    public function it_fails_if_name_is_missing()
    {
        $payload = [
            'email' => 'kunal@example.com',
            'password' => 'password123',
            'role' => 'teacher',
        ];

        $response = $this->postJson('/api/register', $payload);

        $response->assertStatus(422)
            ->assertJsonFragment([
                'Name can not be null, Please enter a name',
            ]);
    }

    /** @test */
    public function it_fails_if_email_is_invalid()
    {
        $payload = [
            'name' => 'Kunal Sharma',
            'email' => 'invalid-email',
            'password' => 'password123',
            'role' => 'teacher',
        ];

        $response = $this->postJson('/api/register', $payload);

        $response->assertStatus(422)
            ->assertJsonFragment([
                'Email must be in email format like example@gmail.com',
            ]);
    }

    /** @test */

    public function it_fails_if_email_is_not_unique()
    {
        $email = 'unique' . uniqid() . '@example.com';
        User::factory()->create(['email' => $email]);

        $response = $this->postJson('/api/register', [
            'name' => 'Test User',
            'email' => $email,
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'role' => 'teacher',
        ]);

        $response->assertStatus(422)
            ->assertJson([
                'success' => false,
                'message' => 'Some validation error has occured, Please check below:'
            ])
            ->assertJsonFragment([
                'This email is already taken , Please enter a unique email'
            ]);
    }

    /** @test */
    public function it_fails_if_password_is_missing()
    {
        $payload = [
            'name' => 'Kunal Sharma',
            'email' => 'kunal@example.com',
            'role' => 'teacher',
        ];

        $response = $this->postJson('/api/register', $payload);

        $response->assertStatus(422)
            ->assertJsonFragment([
                'Please enter the password',
            ]);
    }

    /** @test */
    public function it_fails_if_role_is_not_teacher()
    {
        $payload = [
            'name' => 'Kunal Sharma',
            'email' => 'kunal@example.com',
            'password' => 'password123',
            'role' => 'student',
        ];

        $response = $this->postJson('/api/register', $payload);

        $response->assertStatus(422)
            ->assertJsonFragment([
                'Role should be only teacher',
            ]);
    }

    /** @test */
    public function it_fails_if_any_field_exceeds_max_length()
    {
        $payload = [
            'name' => str_repeat('a', 256),
            'email' => str_repeat('a', 250) . '@mail.com',
            'password' => str_repeat('p', 256),
            'role' => 'teacher',
        ];

        $response = $this->postJson('/api/register', $payload);

        $response->assertStatus(422);
    }
}
