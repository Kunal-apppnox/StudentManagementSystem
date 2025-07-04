<?php

namespace Tests\Feature;

use App\Models\User;
// use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Passport\Passport;
use Tests\TestCase;

class CreateStudentTest extends TestCase
{
    // use RefreshDatabase;

    public function test_authenticated_user_can_create_student()
    {
        $user = User::factory()->create([
            'role' => 'teacher',
        ]);

        Passport::actingAs($user);

        $studentData = [
            'name' => 'Kunal Sharma',
            'email' => 'kunal' . uniqid() . '@gmail.com',
            'age' => 24,
        ];

        $response = $this->postJson('/api/students', $studentData);

        $response->assertStatus(201);

        $response->assertJsonStructure([
            'success',
            'message',
            'student' => [
                'id',
                'name',
                'email',
                'age',
                'created_at',
                'updated_at',
            ],
        ]);

        $response->assertJson([
            'success' => true,
            'message' => 'New Student has been created and Mail has been sent.',
            'student' => [
                'name' => $studentData['name'],
                'email' => $studentData['email'],
                'age' => $studentData['age'],
            ]
        ]);

        $this->assertDatabaseHas('students', [
            'name' => $studentData['name'],
            'email' => $studentData['email'],
            'age' => $studentData['age'],
        ]);

        $responseData = $response->json();
        $this->assertArrayHasKey('created_at', $responseData['student']);
        $this->assertArrayHasKey('updated_at', $responseData['student']);

        $this->assertMatchesRegularExpression('/^.+\@\S+\.\S+$/', $responseData['student']['email']);
    }

    /** @test */
    public function test_it_fails_if_email_is_invalid()
    {
        Passport::actingAs(User::factory()->create(['role' => 'teacher']));

        $response = $this->postJson('/api/students', [
            'name' => 'Aman',
            'email' => 'amanmail.com',
            'age' => 23,
        ]);

        $response->assertStatus(422)
            ->assertJsonFragment([
                'Email must be in proper format like example@gmail.com'
            ]);
    }


    /** @test */

    public function it_fails_if_name_is_missing()
    {
        Passport::actingAs(User::factory()->create(['role' => 'teacher']));

        $response = $this->postJson('/api/students', [
            'email' => 'Kunal@appnox.com',
            'age' => 24,
        ]);

        $response->assertStatus(422)
            ->assertJsonFragment([
                'Name is required, Please enter a name.'
            ]);

    }

    /** @test */

    public function it_fails_if_age_is_not_a_number()
    {
        Passport::actingAs(User::factory()->create(['role' => 'teacher']));

        $response = $this->postJson('/api/students', [
            'name' => 'Kunal',
            'email' => 'Kunal@appnox.com',
            'age' => 'twenty-three',
        ]);

        $response->assertStatus(422)
            ->assertJsonFragment([
                'Age must be an integer.'
            ]);
    }

    /** @test */
    public function it_fails_if_email_is_duplicate()
    {
        Passport::actingAs(User::factory()->create(['role' => 'teacher']));

        $this->postJson('/api/students', [
            'name' => 'First',
            'email' => 'duplicate@appnox.com',
            'age' => 20,
        ]);

        $response = $this->postJson('/api/students', [
            'name' => 'Second',
            'email' => 'duplicate@appnox.com',
            'age' => 22,
        ]);

        $response->assertStatus(422)
            ->assertJsonFragment([
                'Email must be unique. This email is already taken.'
            ]);
    }

    /** @test */


    public function unauthenticated_user_cannot_create_student()
    {
        $response = $this->postJson('/api/students', [
            'name' => 'Kunal Sharma',
            'email' => 'kunal@appnox.com',
            'age' => 22
        ]);

        $response->assertStatus(401);
    }

    /** @test */
    public function user_with_student_role_cannot_create_student()
    {
        Passport::actingAs(User::factory()->create(['role' => 'student']));

        $response = $this->postJson('/api/students', [
            'name' => 'Kunal',
            'email' => 'student@appnox.com',
            'age' => 23,
        ]);

        $response->assertStatus(403);
    }

}
