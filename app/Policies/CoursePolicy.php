<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Course;
use Illuminate\Auth\Access\HandlesAuthorization;

class CoursePolicy
{
    use HandlesAuthorization;

    public function create(User $user): bool
    {
        return $user->hasRole(['teacher', 'admin']);
    }

    public function update(User $user, Course $course): bool
    {
        return $user->hasRole('admin') || 
            ($user->hasRole('teacher') && $course->created_by === $user->id);
    }

    public function delete(User $user, Course $course): bool
    {
        return $user->hasRole('admin') || 
            ($user->hasRole('teacher') && $course->created_by === $user->id);
    }
}