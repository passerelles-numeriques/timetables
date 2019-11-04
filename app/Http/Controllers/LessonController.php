<?php

namespace App\Http\Controllers;

use App\Services\DataFileManager;
use App\Models\Enum\DataFileEnum;
use App\Services\AdminService;
use Illuminate\Http\Request;

class LessonController extends Controller
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
     * Get lessons
     * Public access
     *
     * @return mixed list of lessons
     */
    public function getLessons(){
        try {
            $lessons = DataFileManager::getDataFile(DataFileEnum::LESSONS);
            return response()->json($lessons);
        } catch (\Exception $e) {
            return response($e->getMessage(), 500);
        }
    }

    /**
     * Update a lesson
     * Has to be admin
     *
     * @param Request $request
     * @return mixed  state of the request
     */
    public function editLesson(Request $request)
    {
        $user = $request->input('user');
        $password = $request->input('password');
        return AdminService::adminAction($user, $password, function () use ($request) {
            $lessonEdited = $request->json()->get('lesson');
            try {
                $lessons = DataFileManager::getDataFile(DataFileEnum::LESSONS);
                foreach ($lessons as $lesson) {
                  if($lesson->id == $lessonEdited['id']) {
                    $lesson->name = $lessonEdited['name'];
                    $lesson->module = $lessonEdited['module'];
                    $lesson->teacher = $lessonEdited['teacher'];
                    $lesson->hours = $lessonEdited['hours'];
                    $lesson->curriculum = $lessonEdited['curriculum'];
                  }
                }
                $info = DataFileManager::writeDataFile(DataFileEnum::LESSONS, $lessons);
                return response()->json($info);
            } catch (\Exception $e) {
                return response($e->getMessage(), 500);
            }
        });
    }

    /**
     * Add a new lesson
     * Has to be admin
     *
     * @param Request $request
     * @return mixed  state of the request
     */
    public function addLesson(Request $request)
    {
        $user = $request->input('user');
        $password = $request->input('password');
        return AdminService::adminAction($user, $password, function () use ($request) {
            $lessonEdited = $request->json()->get('lesson');
            try {
                $lessons = DataFileManager::getDataFile(DataFileEnum::LESSONS);
                $lessonEdited['id'] = count($lessons) == 0 ? 1 : $lessons[count($lessons) - 1]->id + 1;
                array_push($lessons, $lessonEdited);
                $info = DataFileManager::writeDataFile(DataFileEnum::LESSONS, $lessons);
                return response()->json($info);
            } catch (\Exception $e) {
                return response($e->getMessage(), 500);
            }
        });
    }

    /**
     * Remove a lesson
     * Has to be admin
     *
     * @param Request $request
     * @return mixed  state of the request
     */
    public function removeLesson(Request $request)
    {
        $user = $request->input('user');
        $password = $request->input('password');
        return AdminService::adminAction($user, $password, function () use ($request) {
            $lessonEdited = $request->json()->get('lesson');
            try {
                $lessons = DataFileManager::getDataFile(DataFileEnum::LESSONS);
                foreach ($lessons as $index => $lesson) {
                  if($lesson->id == $lessonEdited['id']) {
                    array_splice($lessons, $index, 1);
                  }
                }
                $info = DataFileManager::writeDataFile(DataFileEnum::LESSONS, $lessons);
                return response()->json($info);
            } catch (\Exception $e) {
                return response($e->getMessage(), 500);
            }
        });
    }
}
