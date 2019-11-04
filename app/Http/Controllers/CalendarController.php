<?php

namespace App\Http\Controllers;

use App\Services\DataFileManager;
use App\Models\Enum\DataFileEnum;

class CalendarController extends Controller
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
     * Get all the generals event type
     *
     * @return mixed  list of general event type
     */
    public function getGenerals()
    {
      try {
        $generals = DataFileManager::getDataFile(DataFileEnum::GENERALS);
        return response()->json($generals);
      } catch (\Exception $e) {
        return response($e->getMessage(), 500);
      }
    }

    /**
     * Get all the rooms
     *
     * @return mixed  list of room
     */
    public function getRooms()
    {
      try {
        $rooms = DataFileManager::getDataFile(DataFileEnum::ROOMS);
        return response()->json($rooms);
      } catch (\Exception $e) {
        return response($e->getMessage(), 500);
      }
    }

    /**
     * Get all the classes
     *
     * @return mixed  list of class
     */
    public function getClasses()
    {
      $classes = DataFileManager::getDataFile(DataFileEnum::CLASSES);
      return response()->json($classes);
    }

    /**
     * Get all the teachers
     *
     * @return mixed  list of teacher
     */
    public function getTeachers()
    {
      $teachers = DataFileManager::getDataFile(DataFileEnum::TEACHERS);
      return response()->json($teachers);
    }
}
