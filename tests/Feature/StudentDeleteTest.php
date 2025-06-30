<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Student;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Passport\Passport;
use Tests\TestCase;

class StudentDeleteTest extends TestCase
{
    use RefreshDatabase;

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
}
