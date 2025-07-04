<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Student;
// use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Passport\Passport;
use Tests\TestCase;

class StudentDeleteTest extends TestCase
{
    // use RefreshDatabase;

    /** @test */
    public function it_deletes_a_student_successfully()
    {
        Passport::actingAs(
            User::factory()->create(['role' => 'teacher']),
            ['*']
        );

        $student = Student::factory()->create();

        $response = $this->deleteJson("/api/students/{$student->id}");

        $response->assertStatus(200)
            ->assertJsonFragment([
                'message' => 'Student Deleted Successfully.'
            ]);

        $this->assertDatabaseMissing('students', ['id' => $student->id]);
    }

    /** @test */
    public function it_fails_to_delete_student_if_not_authenticated()
    {
        $student = Student::factory()->create();

        $response = $this->deleteJson("/api/students/{$student->id}");

        $response->assertStatus(401);
    }

    /** @test */
    public function it_fails_to_delete_student_with_invalid_id()
    {
        Passport::actingAs(
            User::factory()->create(['role' => 'teacher']),
            ['*']
        );

        $response = $this->deleteJson("/api/students/9999");

        $response->assertStatus(500);
    }

    /** @test */
    public function it_denies_deletion_for_non_teacher_roles()
    {
        Passport::actingAs(
            User::factory()->create(['role' => 'student']),
            ['*']
        );

        $student = Student::factory()->create();

        $response = $this->deleteJson("/api/students/{$student->id}");

        $response->assertStatus(403);
    }

    /** @test */
    public function it_handles_multiple_deletion_requests_gracefully()
    {
        Passport::actingAs(
            User::factory()->create(['role' => 'teacher']),
            ['*']
        );

        $student = Student::factory()->create();

        $this->deleteJson("/api/students/{$student->id}")
            ->assertStatus(200);

        $this->deleteJson("/api/students/{$student->id}")
            ->assertStatus(500);
    }
}
