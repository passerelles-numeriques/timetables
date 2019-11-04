<?php

namespace App\Http\Controllers;

use App\Services\DataFileManager;
use App\Models\Enum\DataFileEnum;
use App\Services\AdminService;
use Illuminate\Http\Request;

class TeacherController extends Controller
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
     * Get teachers
     * Public access
     *
     * @return mixed list of teacher
     */
    public function getTeachers(){
        try {
            $teachers = DataFileManager::getDataFile(DataFileEnum::TEACHERS);
            return response()->json($teachers);
        } catch (\Exception $e) {
            return response($e->getMessage(), 500);
        }
    }

    /**
     * Update a teacher
     * Has to be admin
     *
     * @param Request $request
     * @return mixed  state of the request
     */
    public function editTeacher(Request $request)
    {
        $user = $request->input('user');
        $password = $request->input('password');
        return AdminService::adminAction($user, $password, function () use ($request) {
            $teacherEdited = $request->json()->get('teacher');
            try {
                $teachers = DataFileManager::getDataFile(DataFileEnum::TEACHERS);
                foreach ($teachers as $teacher) {
                  if($teacher->id == $teacherEdited['id']) {
                    $teacher->name = $teacherEdited['name'];
                    $teacher->google_id = $teacherEdited['googleId'];
                  }
                }
                $info = DataFileManager::writeDataFile(DataFileEnum::TEACHERS, $teachers);
                return response()->json($info);
            } catch (\Exception $e) {
                return response($e->getMessage(), 500);
            }
        });
    }

    /**
     * Add a new teacher
     * Has to be admin
     *
     * @param Request $request
     * @return mixed  state of the request
     */
    public function addTeacher(Request $request)
    {
        $user = $request->input('user');
        $password = $request->input('password');
        return AdminService::adminAction($user, $password, function () use ($request) {
            $teacherEdited = $request->json()->get('teacher');
            try {
                $teachers = DataFileManager::getDataFile(DataFileEnum::TEACHERS);
                $teacherEdited['id'] = count($teachers) == 0 ? 1 : $teachers[count($teachers) - 1]->id + 1;
                $teacherJson =  new \stdClass();
                $teacherJson->id = $teacherEdited['id'];
                $teacherJson->name = $teacherEdited['name'];
                $teacherJson->google_id = $teacherEdited['googleId'];
                array_push($teachers, $teacherJson);
                $info = DataFileManager::writeDataFile(DataFileEnum::TEACHERS, $teachers);
                return response()->json($info);
            } catch (\Exception $e) {
                return response($e->getMessage(), 500);
            }
        });
    }

    /**
     * Remove a teacher
     * Has to be admin
     *
     * @param Request $request
     * @return mixed  state of the request
     */
    public function removeTeacher(Request $request)
    {
        $user = $request->input('user');
        $password = $request->input('password');
        return AdminService::adminAction($user, $password, function () use ($request) {
            $teacherEdited = $request->json()->get('teacher');
            try {
                $teachers = DataFileManager::getDataFile(DataFileEnum::TEACHERS);
                foreach ($teachers as $index => $teacher) {
                  if($teacher->id == $teacherEdited['id']) {
                    array_splice($teachers, $index, 1);
                  }
                }
                $info = DataFileManager::writeDataFile(DataFileEnum::TEACHERS, $teachers);
                return response()->json($info);
            } catch (\Exception $e) {
                return response($e->getMessage(), 500);
            }
        });
    }
}
