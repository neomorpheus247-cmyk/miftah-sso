<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Course;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class CourseEnrollmentController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth:sanctum']);
    }

    /**
     * Enroll a student in a course.
     */
    public function enroll(Request $request, string $courseId): JsonResponse
    {
        $course = Course::findOrFail($courseId);
        
        // Check if user is already enrolled
        if ($course->students()->where('users.id', auth()->id())->exists()) {
            return response()->json([
                'message' => 'Already enrolled in this course'
            ], 409);
        }

        // Enroll the student
        $course->students()->attach(auth()->id());

        return response()->json([
            'message' => 'Successfully enrolled in course'
        ]);
    }

    /**
     * Remove a student from a course.
     */
    public function unenroll(Request $request, string $courseId): JsonResponse
    {
        $course = Course::findOrFail($courseId);
        
        // Check if user is enrolled
        if (!$course->students()->where('users.id', auth()->id())->exists()) {
            return response()->json([
                'message' => 'Not enrolled in this course'
            ], 404);
        }

        // Remove the student
        $course->students()->detach(auth()->id());

        return response()->json([
            'message' => 'Successfully unenrolled from course'
        ]);
    }

    /**
     * Get enrolled students for a course.
     */
    public function getEnrolledStudents(string $courseId): JsonResponse
    {
        $course = Course::findOrFail($courseId);

        // Only teachers and admins can view enrolled students
        if (!auth()->user()->hasRole(['admin', 'teacher'])) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $students = $course->students()->select('id', 'name', 'email')->get();

        return response()->json([
            'data' => $students
        ]);
    }
}