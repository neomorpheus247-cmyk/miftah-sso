<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Course;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;

class CourseController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:sanctum');
    }

    /**
     * Display a listing of the resource.
     */
    public function index(): JsonResponse
    {
        $courses = Course::with('creator:id,name')->get();

        return response()->json(['data' => $courses]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): JsonResponse
    {
        $user = Auth::user();

        if (!$user->can('create courses')) {
            return response()->json(['message' => 'Forbidden'], 403);
        }

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
        ]);

        $course = Course::create([
            ...$validated,
            'created_by' => $user->id,
        ]);

        return response()->json([
            'message' => 'Course created successfully',
            'data' => $course,
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id): JsonResponse
    {
        $course = Course::with(['creator:id,name', 'students:id,name,email'])
            ->findOrFail($id);

        return response()->json(['data' => $course]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id): JsonResponse
    {
        $user = Auth::user();
        $course = Course::findOrFail($id);

        // Admins can update any course
        if ($user->hasRole('admin')) {
            $allowed = true;
        }
        // Teachers can only update their own courses
        elseif ($user->hasRole('teacher') && $course->created_by === $user->id) {
            $allowed = true;
        } else {
            $allowed = false;
        }

        if (!$allowed) {
            return response()->json(['message' => 'Forbidden'], 403);
        }

        $validated = $request->validate([
            'title' => 'sometimes|required|string|max:255',
            'description' => 'sometimes|required|string',
        ]);

        $course->update($validated);

        return response()->json([
            'message' => 'Course updated successfully',
            'data' => $course,
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id): Response|JsonResponse
    {
        $user = Auth::user();
        $course = Course::findOrFail($id);

        // Only the creator or an admin can delete
        if ($user->id !== $course->created_by && !$user->hasRole('admin')) {
            return response()->json(['message' => 'Forbidden'], 403);
        }

        $course->students()->detach();
        $course->delete();

        return response()->noContent();
    }

    /**
     * Enroll the authenticated student in a course.
     */
    public function enroll(string $id): JsonResponse
    {
        $user = Auth::user();
        $course = Course::findOrFail($id);

        if (!$user->can('enroll in courses')) {
            return response()->json(['message' => 'Forbidden'], 403);
        }

        $course->students()->syncWithoutDetaching([$user->id]);

        return response()->json(['message' => 'Enrolled successfully']);
    }

    /**
     * Unenroll the authenticated student from a course.
     */
    public function unenroll(string $id): JsonResponse
    {
        $user = Auth::user();
        $course = Course::findOrFail($id);

        if (!$user->can('enroll in courses')) {
            return response()->json(['message' => 'Forbidden'], 403);
        }

        $course->students()->detach($user->id);

        return response()->json(['message' => 'Unenrolled successfully']);
    }
}
