<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Student;
use Laravel\Passport\Passport;
use Illuminate\Foundation\Testing\RefreshDatabase;//a trait which helps in refreshing data between tests.

class StudentUpdateTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_updates_a_student_successfully()
    {
        Passport::actingAs(
            User::factory()->create(['role' => 'teacher']),
            ['*']
        );

        $student = Student::factory()->create();

        $updatedData = [
            'name' => 'Kunal Sharma',
            'email' => 'Kunal.appnox@gmail.com',
            'age' => 25
        ];

        $response = $this->putJson("/api/students/{$student->id}", $updatedData);

        $response->assertStatus(200)
            ->assertJsonFragment([
                'name' => 'Kunal Sharma',
                'email' => 'Kunal.appnox@gmail.com',
                'age' => 25,
            ]);

        $this->assertDatabaseHas('students', [
            'id' => $student->id,
            'name' => 'Kunal Sharma',
            'email' => 'Kunal.appnox@gmail.com',
            'age' => 25,
        ]);
    }
}
