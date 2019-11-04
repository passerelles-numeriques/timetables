<?php

namespace App\Services;

use App\Models\Enum\DataFileEnum;
use App\Services\DataFileManager;
use App\Services\LessonService;
use App\Services\ModuleService;

class StatisticService
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Get event from a google calendar filtered by the name of the lesson
     *
     * @param int $lessonId
     * @return mixed  list of events
     */
    public static function getLessonStats($lessonId)
    {
        $errorInfo = new \stdClass();
        try {
            $lesson = LessonService::getLesson($lessonId);
            if(!isset($lesson->module)) {
                $errorInfo->noModule = true;
                return $errorInfo;
            }
            if(!isset($lesson->teacher)) {
                $errorInfo->noTeacher = true;
                return $errorInfo;
            }
            try{
                $module = ModuleService::getModule($lesson->module);
            } catch (\Exception $e) {
                $errorInfo->noModule = true;
                return $errorInfo;
            }
            try{
            $teacher = TeacherService::getTeacher($lesson->teacher);
            } catch (\Exception $e) {
                $errorInfo->noTeacher = true;
                return $errorInfo;
            }
    
            $googleData = GoogleCalendarService::getCalendarEvents(
                $teacher->google_id,
                $module->startDate, $module->endDate, false, false, $lesson->name);
            return $googleData;
        } catch (\Exception $e) {
                $errorInfo->noLesson = true;
                return $errorInfo;
        }
    }
}
