<?php

namespace App\Services;

use App\Models\Course;
use App\Models\CourseModule;
use App\Models\CourseLesson;
use App\Models\Certificate;
use App\Models\CourseEnrollment;
use App\Models\LessonCompletion;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

class EducationalResourceService
{
    /**
     * Create a new course
     *
     * @param array $data
     * @param int $instructorId
     * @return Course
     */
    public function createCourse(array $data, int $instructorId): Course
    {
        $course = Course::create([
            'title' => $data['title'],
            'description' => $data['description'] ?? null,
            'summary' => $data['summary'] ?? null,
            'objectives' => $data['objectives'] ?? null,
            'level' => $data['level'] ?? 'beginner',
            'category' => $data['category'] ?? 'trading_basics',
            'type' => $data['type'] ?? 'course',
            'instructor_id' => $instructorId,
            'duration_minutes' => $data['duration_minutes'] ?? null,
            'price' => $data['price'] ?? 0,
            'discount_price' => $data['discount_price'] ?? null,
            'is_active' => $data['is_active'] ?? true,
            'is_free' => $data['is_free'] ?? false,
            'requires_certificate' => $data['requires_certificate'] ?? false,
            'prerequisites' => $data['prerequisites'] ?? null,
            'what_you_will_learn' => $data['what_you_will_learn'] ?? null,
            'thumbnail_url' => $data['thumbnail_url'] ?? null,
            'preview_video_url' => $data['preview_video_url'] ?? null,
        ]);

        // Generate slug based on title
        $course->update([
            'slug' => $this->generateUniqueSlug($course->title, $course->id, 'courses')
        ]);

        return $course->fresh();
    }

    /**
     * Add a module to a course
     *
     * @param array $data
     * @param int $courseId
     * @return CourseModule
     */
    public function addModule(array $data, int $courseId): CourseModule
    {
        return CourseModule::create([
            'course_id' => $courseId,
            'title' => $data['title'],
            'description' => $data['description'] ?? null,
            'module_order' => $data['module_order'] ?? 0,
        ]);
    }

    /**
     * Add a lesson to a course module
     *
     * @param array $data
     * @param int $moduleId
     * @param int $courseId
     * @return CourseLesson
     */
    public function addLesson(array $data, int $moduleId, int $courseId): CourseLesson
    {
        $lesson = CourseLesson::create([
            'module_id' => $moduleId,
            'course_id' => $courseId,
            'title' => $data['title'],
            'content' => $data['content'] ?? null,
            'summary' => $data['summary'] ?? null,
            'type' => $data['type'] ?? 'article',
            'video_url' => $data['video_url'] ?? null,
            'download_url' => $data['download_url'] ?? null,
            'duration_minutes' => $data['duration_minutes'] ?? null,
            'is_preview' => $data['is_preview'] ?? false,
            'is_free' => $data['is_free'] ?? false,
            'requires_completion' => $data['requires_completion'] ?? true,
            'lesson_order' => $data['lesson_order'] ?? 0,
            'quiz_questions_count' => $data['quiz_questions_count'] ?? 0,
        ]);

        // Update module lesson count
        $module = CourseModule::find($moduleId);
        $module->increment('lesson_count');

        return $lesson;
    }

    /**
     * Enroll a user in a course
     *
     * @param int $userId
     * @param int $courseId
     * @return CourseEnrollment
     */
    public function enrollUser(int $userId, int $courseId): CourseEnrollment
    {
        $enrollment = CourseEnrollment::firstOrCreate([
            'user_id' => $userId,
            'course_id' => $courseId,
        ], [
            'status' => 'enrolled',
            'progress_percentage' => 0,
        ]);

        // Update course student count
        $course = Course::find($courseId);
        $course->increment('total_students');

        return $enrollment;
    }

    /**
     * Mark a lesson as completed
     *
     * @param int $userId
     * @param int $lessonId
     * @param int|null $score
     * @return LessonCompletion
     */
    public function completeLesson(int $userId, int $lessonId, int $score = null): LessonCompletion
    {
        $lesson = CourseLesson::find($lessonId);
        $course = $lesson->course;

        $completion = LessonCompletion::updateOrCreate([
            'user_id' => $userId,
            'lesson_id' => $lessonId,
        ], [
            'course_id' => $course->id,
            'completed_at' => now(),
            'score' => $score,
            'is_passed' => $score ? $score >= 70 : true, // Assuming 70% as passing grade
        ]);

        // Update user's course progress
        $this->updateCourseProgress($userId, $course->id);

        // Check if course is completed and issue certificate if applicable
        if ($this->isCourseCompleted($userId, $course->id)) {
            $this->issueCertificate($userId, $course->id);
        }

        return $completion;
    }

    /**
     * Update course progress for a user
     *
     * @param int $userId
     * @param int $courseId
     * @return void
     */
    private function updateCourseProgress(int $userId, int $courseId): void
    {
        $enrollment = CourseEnrollment::where('user_id', $userId)
            ->where('course_id', $courseId)
            ->first();

        if (!$enrollment) {
            return;
        }

        $totalLessons = CourseLesson::where('course_id', $courseId)->count();
        $completedLessons = LessonCompletion::where('user_id', $userId)
            ->where('course_id', $courseId)
            ->whereNotNull('completed_at')
            ->count();

        $progressPercentage = $totalLessons > 0 ? ($completedLessons / $totalLessons) * 100 : 0;

        $enrollment->update([
            'progress_percentage' => round($progressPercentage, 2),
            'completed_lessons' => $completedLessons,
            'last_accessed_at' => now(),
        ]);
    }

    /**
     * Check if a course is completed by a user
     *
     * @param int $userId
     * @param int $courseId
     * @return bool
     */
    private function isCourseCompleted(int $userId, int $courseId): bool
    {
        $course = Course::find($courseId);
        $totalLessons = CourseLesson::where('course_id', $courseId)->count();

        if ($totalLessons === 0) {
            return false;
        }

        $completedLessons = LessonCompletion::where('user_id', $userId)
            ->where('course_id', $courseId)
            ->whereNotNull('completed_at')
            ->count();

        return $completedLessons >= $totalLessons;
    }

    /**
     * Issue a certificate for course completion
     *
     * @param int $userId
     * @param int $courseId
     * @return Certificate|null
     */
    private function issueCertificate(int $userId, int $courseId): ?Certificate
    {
        $course = Course::find($courseId);
        
        if (!$course || !$course->requires_certificate) {
            return null;
        }

        // Check if user has already completed the course
        $enrollment = CourseEnrollment::where('user_id', $userId)
            ->where('course_id', $courseId)
            ->where('status', 'completed')
            ->first();

        if ($enrollment) {
            return null; // User already has certificate
        }

        $certificate = Certificate::create([
            'certificate_number' => $this->generateCertificateNumber($userId, $courseId),
            'user_id' => $userId,
            'course_id' => $courseId,
            'title' => 'Certificate of Completion - ' . $course->title,
            'description' => 'This is to certify that ' . auth()->user()->name . ' has successfully completed the course: ' . $course->title,
            'expires_at' => $course->requires_certificate ? now()->addYears(2) : null,
            'is_active' => true,
        ]);

        // Update enrollment status
        $enrollment = CourseEnrollment::where('user_id', $userId)
            ->where('course_id', $courseId)
            ->first();

        if ($enrollment) {
            $enrollment->update([
                'status' => 'certified',
                'completed_at' => now(),
                'has_certificate' => true,
                'certificate_id' => $certificate->id,
            ]);
        }

        return $certificate;
    }

    /**
     * Generate a unique certificate number
     *
     * @param int $userId
     * @param int $courseId
     * @return string
     */
    private function generateCertificateNumber(int $userId, int $courseId): string
    {
        $timestamp = now()->format('Ymd');
        $uniquePart = strtoupper(Str::random(6));
        
        return "CERT-{$timestamp}-{$userId}-{$courseId}-{$uniquePart}";
    }

    /**
     * Generate a unique slug
     *
     * @param string $name
     * @param int $id
     * @param string $table
     * @return string
     */
    private function generateUniqueSlug(string $name, int $id, string $table): string
    {
        $slug = Str::slug($name);
        $originalSlug = $slug;

        $count = 1;
        while (DB::table($table)->where('slug', $slug)->where('id', '!=', $id)->exists()) {
            $slug = $originalSlug . '-' . $count;
            $count++;
        }

        return $slug;
    }

    /**
     * Get course progress for a user
     *
     * @param int $userId
     * @param int $courseId
     * @return array
     */
    public function getCourseProgress(int $userId, int $courseId): array
    {
        $enrollment = CourseEnrollment::where('user_id', $userId)
            ->where('course_id', $courseId)
            ->first();

        if (!$enrollment) {
            return [
                'progress_percentage' => 0,
                'completed_lessons' => 0,
                'total_lessons' => 0,
                'status' => 'not_enrolled'
            ];
        }

        $totalLessons = CourseLesson::where('course_id', $courseId)->count();
        $completedLessons = $enrollment->completed_lessons;

        return [
            'progress_percentage' => $enrollment->progress_percentage,
            'completed_lessons' => $completedLessons,
            'total_lessons' => $totalLessons,
            'status' => $enrollment->status,
            'estimated_completion_time' => $this->estimateTimeToCompletion($userId, $courseId)
        ];
    }

    /**
     * Estimate time to complete a course
     *
     * @param int $userId
     * @param int $courseId
     * @return string
     */
    private function estimateTimeToCompletion(int $userId, int $courseId): string
    {
        $course = Course::find($courseId);
        $userProgress = $this->getCourseProgress($userId, $courseId);

        if ($userProgress['total_lessons'] === 0) {
            return 'N/A';
        }

        $remainingLessons = $userProgress['total_lessons'] - $userProgress['completed_lessons'];
        
        // Calculate average lesson duration based on completed lessons if available
        $avgLessonDuration = CourseLesson::where('course_id', $courseId)
            ->avg('duration_minutes');

        if (!$avgLessonDuration) {
            return 'Time estimation not available';
        }

        $estimatedMinutes = $remainingLessons * $avgLessonDuration;

        $hours = floor($estimatedMinutes / 60);
        $minutes = $estimatedMinutes % 60;

        return $hours > 0 ? "{$hours}h {$minutes}m" : "{$minutes}m";
    }
}