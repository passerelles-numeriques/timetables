<?php

namespace App\Services;

use App\Models\Enum\DataFileEnum;
use App\Services\DataFileManager;
class LessonService
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
     * Get lesson from his id
     *
     * @param int $lessonId
     * @return void
     */
    public static function getLesson($lessonId)
    {
        $lessons = DataFileManager::getDataFile(DataFileEnum::LESSONS);
        foreach ($lessons as $lesson) {
            if($lesson->id == $lessonId) {
                return $lesson;
            }
        }
        throw new \Exception("This id doesn't exist", 1);
    }

    /**
     * Test if a module is link to lessons
     *
     * @param int $idModule
     * @return boolean
     */
    public static function haveLessonLinkToModule($idModule) {
        $isLink = false;
        $lessons = DataFileManager::getDataFile(DataFileEnum::LESSONS);
        foreach ($lessons as $lesson) {
            if($lesson->module == $idModule) {
                $isLink = true;
            }
        }
        return $isLink;
    }
}
