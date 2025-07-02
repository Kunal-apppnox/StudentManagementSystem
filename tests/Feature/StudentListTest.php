<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Student;
use Laravel\Passport\Passport;
use Illuminate\Foundation\Testing\RefreshDatabase;

class StudentListTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_lists_all_students_successfully()
    {
        Passport::actingAs(
            User::factory()->create(['role' => 'teacher']),
            ['*']
        );

        Student::factory()->count(3)->create();

        $response = $this->getJson('/api/students');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'students' => [
                    '*' => ['id', 'name', 'email', 'age', 'created_at', 'updated_at']
                ]
            ]);
    }

    /** @test */
    public function it_returns_empty_list_when_no_students_exist()
    {
        Passport::actingAs(
            User::factory()->create(['role' => 'teacher']),
            ['*']
        );

        $response = $this->getJson('/api/students');

        $response->assertStatus(200)
            ->assertJson([
                'students' => []
            ]);
    }

    /** @test */
    public function it_returns_unauthorized_for_unauthenticated_user()
    {
        $response = $this->getJson('/api/students');

        $response->assertStatus(401); // Unauthorized
    }

    /** @test */
    public function it_rejects_access_for_non_teacher_roles()
    {
        Passport::actingAs(
            User::factory()->create(['role' => 'student']), // Assuming only teachers can list
            ['*']
        );

        $response = $this->getJson('/api/students');

        $response->assertStatus(403); // Forbidden, if policy is applied
    }

    /** @test */
    public function it_returns_correct_data_for_each_student()
    {
        Passport::actingAs(
            User::factory()->create(['role' => 'teacher']),
            ['*']
        );

        $students = Student::factory()->count(2)->create();

        $response = $this->getJson('/api/students');

        $response->assertStatus(200)
            ->assertJsonFragment([
                'name' => $students[0]->name,
                'email' => $students[0]->email,
                'age' => $students[0]->age,
            ])
            ->assertJsonFragment([
                'name' => $students[1]->name,
                'email' => $students[1]->email,
                'age' => $students[1]->age,
            ]);
    }
}
