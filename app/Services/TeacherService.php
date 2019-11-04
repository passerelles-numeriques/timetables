<?php

namespace App\Services;

use App\Models\Enum\DataFileEnum;
use App\Services\DataFileManager;
class TeacherService
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
     * Get teacher from his id
     *
     * @param int $teacherId
     * @return boolean
     */
    public static function getTeacher($teacherId)
    {
        $teachers = DataFileManager::getDataFile(DataFileEnum::TEACHERS);
        foreach ($teachers as $teacher) {
            if($teacher->id == $teacherId) {
                return $teacher;
            }
        }
        throw new \Exception("This id doesn't exist", 1);
    }
}
