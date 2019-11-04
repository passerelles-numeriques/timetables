<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\DataFileManager;
use App\Models\Enum\DataFileEnum;
use App\Services\AdminService;
use App\Services\LessonService;

class ModuleController extends Controller
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
     * Get modules
     * Public access
     *
     * @return mixed list of module
     */
    public function getModules()
    {
        try {
            $modules = DataFileManager::getDataFile(DataFileEnum::MODULES);
            return response()->json($modules);
        } catch (\Exception $e) {
            return response($e->getMessage(), 500);
        }
    }

    /**
     * Get a consolidated view of modules, lessons, and teachers, e.g.:
     *      Module 1
     *          Lesson A -> Teacher X
     *          Lesson B -> Teacher Y 
     * Public access
     *
     * @return mixed list of module
     */
    public function getModulesLessonsTeachers()
    {
        $modules = DataFileManager::getDataFile(DataFileEnum::MODULES);
        $lessons = DataFileManager::getDataFile(DataFileEnum::LESSONS);
        $teachers = DataFileManager::getDataFile(DataFileEnum::TEACHERS);
        $merged = [];
        foreach ($modules as $module) {
            //List the lessons of the module
            $lessonsInModule = [];
            foreach ($lessons as $lesson) {
                if ($lesson->module == $module->id) {
                    //Attach the teacher of the lesson
                    $lessonInModule = clone $lesson;
                    foreach ($teachers as $teacher) {
                        if ($lesson->teacher == $teacher->id) {
                            $lessonInModule->teacher = clone $teacher;
                        }
                    }
                    //Attach the module object
                    $lessonInModule->module = clone $module;
                    $lessonsInModule[] = $lessonInModule;
                }
            }

            usort($lessonsInModule, function ($a, $b)
                {
                    return strcmp($a->name, $b->name);
                });

            //Append to the combined list of modules
            $merged[$module->id] = [
                "name" => $module->name,
                "startDate" => $module->startDate,
                "endDate" => $module->endDate,
                "lessons" => $lessonsInModule,
            ];
        }
        return response()->json($merged);
    }

    /**
     * Update a module
     * Has to be admin
     *
     * @param Request $request
     * @return mixed  state of the request
     */
    public function editModule(Request $request)
    {
        $user = $request->input('user');
        $password = $request->input('password');
        return AdminService::adminAction($user, $password, function () use ($request) {
            $moduleEdited = $request->json()->get('module');
            try {
                $modules = DataFileManager::getDataFile(DataFileEnum::MODULES);
                foreach ($modules as $module) {
                  if($module->id == $moduleEdited['id']) {
                    $module->name = $moduleEdited['name'];
                    $module->startDate = $moduleEdited['startDate'];
                    $module->endDate = $moduleEdited['endDate'];
                  }
                }
                $info = DataFileManager::writeDataFile(DataFileEnum::MODULES, $modules);
                return response()->json($info);
            } catch (\Exception $e) {
                return response($e->getMessage(), 500);
            }
        });
    }

    /**
     * Add a new module
     * Has to be admin
     *
     * @param Request $request
     * @return mixed  state of the request
     */
    public function addModule(Request $request)
    {
        $user = $request->input('user');
        $password = $request->input('password');
        return AdminService::adminAction($user, $password, function () use ($request) {
            $moduleEdited = $request->json()->get('module');
            try {
                $modules = DataFileManager::getDataFile(DataFileEnum::MODULES);
                $moduleEdited['id'] = count($modules) == 0 ? 1 : $modules[count($modules) - 1]->id + 1;
                array_push($modules, $moduleEdited);
                $info = DataFileManager::writeDataFile(DataFileEnum::MODULES, $modules);
                return response()->json($info);
            } catch (\Exception $e) {
                return response($e->getMessage(), 500);
            }
        });
    }

    /**
     * Remove a module
     * Has to be admin
     *
     * @param Request $request
     * @return mixed  state of the request
     */
    public function removeModule(Request $request)
    {
        $user = $request->input('user');
        $password = $request->input('password');
        return AdminService::adminAction($user, $password, function () use ($request) {
            $moduleEdited = $request->json()->get('module');
            try {
                if(LessonService::haveLessonLinkToModule($moduleEdited['id'])) {
                    $errorInfo = new \stdClass();
                    $errorInfo->connected = true;
                    return response()->json($errorInfo);
                }
                $modules = DataFileManager::getDataFile(DataFileEnum::MODULES);
                foreach ($modules as $index => $module) {
                  if($module->id == $moduleEdited['id']) {
                    array_splice($modules, $index, 1);
                  }
                }
                $info = DataFileManager::writeDataFile(DataFileEnum::MODULES, $modules);
                return response()->json($info);
            } catch (\Exception $e) {
                return response($e->getMessage(), 500);
            }
        });
    }
}
