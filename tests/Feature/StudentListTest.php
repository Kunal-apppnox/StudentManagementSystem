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
}
