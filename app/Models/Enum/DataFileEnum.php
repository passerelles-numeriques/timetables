<?php

namespace App\Models\Enum;

/**
 * Enum to have the accessible data files (json)
 */
abstract class DataFileEnum
{
    const ROOMS = 'rooms';
    const GENERALS = 'generals';
    const CLASSES = 'classes';
    const TEACHERS = 'teachers';
    const LESSONS = 'lessons';
    const ADMIN = 'admin';
    const MODULES = 'modules';

    public static function isADataFile($dataFile)
    {
        return $dataFile == DataFileEnum::ROOMS ||
            $dataFile == DataFileEnum::GENERALS ||
            $dataFile == DataFileEnum::CLASSES ||
            $dataFile == DataFileEnum::LESSONS ||
            $dataFile == DataFileEnum::ADMIN ||
            $dataFile == DataFileEnum::MODULES ||
            $dataFile == DataFileEnum::TEACHERS;
    }
}//end class
