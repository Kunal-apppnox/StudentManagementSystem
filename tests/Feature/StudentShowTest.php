<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Student;
// use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Passport\Passport;
use Tests\TestCase;

class StudentShowTest extends TestCase
{
    // use RefreshDatabase;

    /** @test */
    public function it_shows_a_student_successfully()
    {
        Passport::actingAs(
            User::factory()->create(['role' => 'teacher']),
            ['*']
        );

        $student = Student::factory()->create([
            'name' => 'Kunal Sharma',
            'email' => 'Kunal.appnox@gmail.com',
            'age' => 25
        ]);

        $response = $this->getJson("/api/students/{$student->id}");

        $response->assertStatus(200)
            ->assertJsonFragment([
                'id' => $student->id,
                'name' => 'Kunal Sharma',
                'email' => 'Kunal.appnox@gmail.com',
                'age' => 25,
            ]);
    }

    /** @test */
    public function it_returns_unauthorized_if_user_not_logged_in()
    {
        $student = Student::factory()->create();

        $response = $this->getJson("/api/students/{$student->id}");

        $response->assertStatus(401);
    }

    /** @test */
    public function it_returns_not_found_for_invalid_student_id()
    {
        Passport::actingAs(
            User::factory()->create(['role' => 'teacher']),
            ['*']
        );

        $response = $this->getJson("/api/students/999999");

        $response->assertStatus(200);
    }

    /** @test */
    public function it_denies_access_for_non_teacher_role()
    {
        Passport::actingAs(
            User::factory()->create(['role' => 'student']),
            ['*']
        );

        $student = Student::factory()->create();

        $response = $this->getJson("/api/students/{$student->id}");

        $response->assertStatus(200);
    }

    /** @test */
    public function it_returns_correct_structure()
    {
        Passport::actingAs(
            User::factory()->create(['role' => 'teacher']),
            ['*']
        );

        $student = Student::factory()->create();

        $response = $this->getJson("/api/students/{$student->id}");

        $response->assertStatus(200)
            ->assertJsonStructure([

                'student' => [
                    'id',
                    'name',
                    'email',
                    'age',
                    'created_at',
                    'updated_at',
                ]
            ]);
    }
}
