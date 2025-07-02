<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Student;
use Laravel\Passport\Passport;
use Illuminate\Foundation\Testing\RefreshDatabase;

class StudentUpdateTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_updates_a_student_successfully()
    {
        Passport::actingAs(User::factory()->create(['role' => 'teacher']));

        $student = Student::factory()->create();

        $response = $this->putJson("/api/students/{$student->id}", [
            'name' => 'Kunal Sharma',
            'email' => 'kunal.appnox@gmail.com',
            'age' => 25,
        ]);

        $response->assertStatus(200)
                 ->assertJsonFragment(['name' => 'Kunal Sharma']);

        $this->assertDatabaseHas('students', ['id' => $student->id, 'name' => 'Kunal Sharma']);
    }

    /** @test */
    public function it_returns_validation_error_for_invalid_email_format()
    {
        Passport::actingAs(User::factory()->create(['role' => 'teacher']));

        $student = Student::factory()->create();

        $response = $this->putJson("/api/students/{$student->id}", [
            'email' => 'invalidemail'
        ]);

        $response->assertStatus(422)
                 ->assertJsonFragment(['The email field must be a valid email address.']);
    }

    /** @test */
    public function it_returns_validation_error_for_non_integer_age()
    {
        Passport::actingAs(User::factory()->create(['role' => 'teacher']));

        $student = Student::factory()->create();

        $response = $this->putJson("/api/students/{$student->id}", [
            'age' => 'twenty'
        ]);

        $response->assertStatus(422)
                 ->assertJsonFragment(['The age field must be an integer.']);
    }

    /** @test */
    public function it_returns_unauthorized_for_non_authenticated_user()
    {
        $student = Student::factory()->create();

        $response = $this->putJson("/api/students/{$student->id}", [
            'name' => 'Unauthorized'
        ]);

        $response->assertStatus(401); 
    }

    /** @test */
    public function it_allows_partial_updates()
    {
        Passport::actingAs(User::factory()->create(['role' => 'teacher']));

        $student = Student::factory()->create([
            'name' => 'Old Name',
            'email' => 'old@example.com',
            'age' => 22
        ]);

        $response = $this->putJson("/api/students/{$student->id}", [
            'name' => 'Updated Only Name'
        ]);

        $response->assertStatus(200)
                 ->assertJsonFragment(['name' => 'Updated Only Name']);

        $this->assertDatabaseHas('students', [
            'id' => $student->id,
            'name' => 'Updated Only Name',
            'email' => 'old@example.com',
        ]);
    }

    /** @test */
    public function it_returns_404_if_student_not_found()
    {
        Passport::actingAs(User::factory()->create(['role' => 'teacher']));

        $response = $this->putJson("/api/students/999999", [
            'name' => 'Ghost Student'
        ]);

        $response->assertStatus(500);
    }
}
