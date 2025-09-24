<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Course;
use Laravel\Sanctum\Sanctum;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\TestCase;
use Illuminate\Contracts\Console\Kernel;
use Illuminate\Foundation\Application;

class CourseApiTest extends TestCase
{
    use RefreshDatabase;

    public function createApplication(): Application
    {
        $app = require __DIR__ . '/../../bootstrap/app.php';
        $app->make(Kernel::class)->bootstrap();
        return $app;
    }

    protected function setUp(): void
    {
        parent::setUp();

        // Use sqlite in memory
        config(['database.default' => 'sqlite']);
        config(['database.connections.sqlite' => [
            'driver'   => 'sqlite',
            'database' => ':memory:',
            'prefix'   => '',
        ]]);

        // Reset Spatie cache
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Run migrations + seeders
        $this->artisan('migrate', ['--seed' => true]);

        // Create roles/permissions
        $this->createRolesAndPermissions();

        // Load API routes
        $this->registerApiRoutes();
    }

    protected function createRolesAndPermissions(): void
    {
        // Roles
        $admin   = Role::findOrCreate('admin');
        $teacher = Role::findOrCreate('teacher');
        $student = Role::findOrCreate('student');

        // Permissions
        $permissions = [
            'view courses',
            'create courses',
            'edit courses',
            'delete courses',
            'enroll in courses',
        ];

        foreach ($permissions as $permission) {
            Permission::findOrCreate($permission);
        }

        // Assign
        $admin->givePermissionTo(Permission::all());
        $teacher->givePermissionTo(['view courses', 'create courses', 'edit courses', 'delete courses']);
        $student->givePermissionTo(['view courses', 'enroll in courses']);
    }

    protected function registerApiRoutes(): void
    {
        if (file_exists(app()->getCachedRoutesPath())) {
            unlink(app()->getCachedRoutesPath());
        }

        $this->app['router']->group(['prefix' => 'api'], function ($router) {
            require base_path('routes/api.php');
        });
    }

    /** @test */
    public function guests_cannot_access_courses()
    {
        $response = $this->getJson('/api/courses');
        $response->assertUnauthorized();
    }

    /** @test */
    public function authenticated_users_can_view_courses()
    {
        $user = User::factory()->create()->assignRole('student');
        Course::factory()->count(3)->create();

        Sanctum::actingAs($user);

        $response = $this->getJson('/api/courses');

        $response->assertOk()->assertJsonCount(3, 'data');
    }

    /** @test */
    public function teachers_can_create_courses()
    {
        $teacher = User::factory()->create()->assignRole('teacher');

        Sanctum::actingAs($teacher);

        $response = $this->postJson('/api/courses', [
            'title'       => 'New Course',
            'description' => 'Course Description',
        ]);

        $response->assertCreated()->assertJson([
            'data' => [
                'title'       => 'New Course',
                'description' => 'Course Description',
                'created_by'  => $teacher->id,
            ],
        ]);

        $this->assertDatabaseHas('courses', [
            'title'       => 'New Course',
            'description' => 'Course Description',
            'created_by'  => $teacher->id,
        ]);
    }

    /** @test */
    public function students_cannot_create_courses()
    {
        $student = User::factory()->create()->assignRole('student');

        Sanctum::actingAs($student);

        $response = $this->postJson('/api/courses', [
            'title'       => 'New Course',
            'description' => 'Course Description',
        ]);

        $response->assertForbidden();
    }

    /** @test */
    public function teachers_can_update_their_own_courses()
    {
        $teacher = User::factory()->create()->assignRole('teacher');
        $course  = Course::factory()->create(['created_by' => $teacher->id]);

        Sanctum::actingAs($teacher);

        $response = $this->putJson("/api/courses/{$course->id}", [
            'title'       => 'Updated Course',
            'description' => 'Updated Description',
        ]);

        $response->assertOk()->assertJson([
            'data' => [
                'title'       => 'Updated Course',
                'description' => 'Updated Description',
            ],
        ]);
    }

    /** @test */
    public function teachers_cannot_update_other_teachers_courses()
    {
        $teacher1 = User::factory()->create()->assignRole('teacher');
        $teacher2 = User::factory()->create()->assignRole('teacher');

        $course = Course::factory()->create(['created_by' => $teacher1->id]);

        Sanctum::actingAs($teacher2);

        $response = $this->putJson("/api/courses/{$course->id}", [
            'title'       => 'Updated Course',
            'description' => 'Updated Description',
        ]);

        $response->assertForbidden();
    }

    /** @test */
    public function admin_can_update_any_course()
    {
        $admin   = User::factory()->create()->assignRole('admin');
        $teacher = User::factory()->create()->assignRole('teacher');

        $course = Course::factory()->create(['created_by' => $teacher->id]);

        Sanctum::actingAs($admin);

        $response = $this->putJson("/api/courses/{$course->id}", [
            'title'       => 'Updated by Admin',
            'description' => 'Updated Description',
        ]);

        $response->assertOk()->assertJson([
            'data' => [
                'title'       => 'Updated by Admin',
                'description' => 'Updated Description',
            ],
        ]);
    }

    /** @test */
    public function course_can_be_deleted_by_creator()
    {
        $teacher = User::factory()->create()->assignRole('teacher');
        $course  = Course::factory()->create(['created_by' => $teacher->id]);

        Sanctum::actingAs($teacher);

        $response = $this->deleteJson("/api/courses/{$course->id}");

        $response->assertNoContent();
        $this->assertDatabaseMissing('courses', ['id' => $course->id]);
    }

    /** @test */
    public function students_can_enroll_in_courses()
    {
        $student = User::factory()->create()->assignRole('student');
        $course  = Course::factory()->create();

        Sanctum::actingAs($student);

        $response = $this->postJson("/api/courses/{$course->id}/enroll");

        $response->assertOk();
        $this->assertTrue($course->fresh()->students->contains($student));
    }

    /** @test */
    public function students_can_unenroll_from_courses()
    {
        $student = User::factory()->create()->assignRole('student');
        $course  = Course::factory()->create();
        $course->students()->attach($student);

        Sanctum::actingAs($student);

        $response = $this->deleteJson("/api/courses/{$course->id}/enroll");

        $response->assertOk();
        $this->assertFalse($course->fresh()->students->contains($student));
    }
}
