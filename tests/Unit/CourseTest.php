<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Models\User;
use App\Models\Course;
use Illuminate\Foundation\Testing\RefreshDatabase;

class CourseTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_can_create_a_course()
    {
        $creator = User::factory()->create();
        
        $course = Course::create([
            'title' => 'Test Course',
            'description' => 'This is a test course',
            'created_by' => $creator->id
        ]);

        $this->assertDatabaseHas('courses', [
            'title' => 'Test Course',
            'description' => 'This is a test course',
            'created_by' => $creator->id
        ]);

        $this->assertEquals('Test Course', $course->title);
        $this->assertEquals('This is a test course', $course->description);
        $this->assertEquals($creator->id, $course->created_by);
    }

    /** @test */
    public function it_belongs_to_a_creator()
    {
        $creator = User::factory()->create();
        
        $course = Course::factory()->create([
            'created_by' => $creator->id
        ]);

        $this->assertInstanceOf(User::class, $course->creator);
        $this->assertEquals($creator->id, $course->creator->id);
    }

    /** @test */
    public function it_can_have_students()
    {
        $course = Course::factory()->create();
        $students = User::factory()->count(3)->create();

        $course->students()->attach($students->pluck('id'));

        $this->assertCount(3, $course->students);
        $this->assertInstanceOf(User::class, $course->students->first());
    }

    /** @test */
    public function students_can_be_enrolled_and_unenrolled()
    {
        $course = Course::factory()->create();
        $student = User::factory()->create();

        // Enroll student
        $course->students()->attach($student);
        $this->assertTrue($course->students->contains($student));

        // Unenroll student
        $course->students()->detach($student);
        $this->assertFalse($course->fresh()->students->contains($student));
    }
}