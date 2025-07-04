<?php

namespace Tests\Feature;

use App\Models\User;
// use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Illuminate\Support\Facades\Hash;

class LoginTeacherTest extends TestCase
{
    // use RefreshDatabase;

    protected string $loginEndpoint = '/api/login';

    /** @test */
    public function it_logs_in_a_teacher_successfully()
    {
        $email = 'teacher' . uniqid() . '@example.com';

        $user = User::factory()->create([
            'email' => $email,
            'password' => Hash::make('password123'),
        ]);

        $response = $this->postJson($this->loginEndpoint, [
            'email' => $email,
            'password' => 'password123',
        ]);

        $response->assertStatus(200)
            ->assertJsonStructure([
                'message',
                'token',
            ]);
    }

    /** @test */
    public function it_fails_if_password_is_missing()
    {
        $response = $this->postJson($this->loginEndpoint, [
            'email' => 'teacher@example.com',
        ]);

        $response->assertStatus(422)
            ->assertJsonFragment([
                'Password can not be empty, Please enter a password',
            ]);
    }

    /** @test */
    public function it_fails_if_password_is_too_short()
    {
        $response = $this->postJson($this->loginEndpoint, [
            'email' => 'teacher@example.com',
            'password' => '123',
        ]);

        $response->assertStatus(422)
            ->assertJsonFragment([
                'Password contains at least 6 characters',
            ]);
    }

    /** @test */
    public function it_fails_if_credentials_are_incorrect()
    {
        User::where('email', 'teacher@example.com')->delete();

        $user = User::factory()->create([
            'email' => 'teacher@example.com',
            'password' => Hash::make('correct_password'),
            'role' => 'teacher',
        ]);

        $response = $this->postJson($this->loginEndpoint, [
            'email' => 'teacher@example.com',
            'password' => 'wrong_password',
        ]);

        $response->assertStatus(401)
            ->assertJson([
                'message' => 'Invalid credentials',
            ]);
    }
}
