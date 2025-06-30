<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Passport\Passport;
use Tests\TestCase;

class CreateStudentTest extends TestCase
{
    use RefreshDatabase;

    public function test_authenticated_user_can_create_student()
    {
        $user = User::factory()->create([
            'role' => 'teacher',
        ]);

        Passport::actingAs($user);

        $response = $this->postJson('/api/students', [
            'name' => 'kunal',
            'email' => 'kunal.appnox@gmail.com',
            'age' => 23,
        ]);

        $response->assertStatus(201);

        $response->assertJsonStructure([
            'success',
            'message',
            'student' => [
                'id',
                'name',
                'email',
                'age',
            ],
        ]);

        $this->assertDatabaseHas('students', [
            'name' => 'kunal',
            'email' => 'kunal.appnox@gmail.com',
            'age' => 23,
        ]);
    }
}
