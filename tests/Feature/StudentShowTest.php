<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Student;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Passport\Passport;
use Tests\TestCase;

class StudentShowTest extends TestCase
{
    use RefreshDatabase;

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
}
