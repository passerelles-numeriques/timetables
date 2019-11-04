<?php

namespace App\Http\Controllers;

use App\Services\DataFileManager;
use App\Models\Enum\DataFileEnum;
use App\Services\AdminService;
use Illuminate\Http\Request;


class ClassesController extends Controller
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
     * Get classes
     * Has to be admin
     *
     * @param Request $request
     * @return mixed list of class
     */
    public function getClasses(Request $request){
        $user = $request->input('user');
        $password = $request->input('password');
        return AdminService::adminAction($user, $password, function () {
            try {
                $classes = DataFileManager::getDataFile(DataFileEnum::CLASSES);
                return response()->json($classes);
            } catch (\Exception $e) {
                return response($e->getMessage(), 500);
            }
        });
    }

    /**
     * Update a class
     * Has to be admin
     *
     * @param Request $request
     * @return mixed  state of the request
     */
    public function editClass(Request $request)
    {
        $user = $request->input('user');
        $password = $request->input('password');
        return AdminService::adminAction($user, $password, function () use ($request) {
            $classEdited = $request->json()->get('classRoom');
            try {
                $classes = DataFileManager::getDataFile(DataFileEnum::CLASSES);
                foreach ($classes as $class) {
                  if($class->id == $classEdited['id']) {
                    $class->name = $classEdited['name'];
                    $class->google_id = $classEdited['googleId'];
                  }
                }
                $info = DataFileManager::writeDataFile(DataFileEnum::CLASSES, $classes);
                return response()->json($info);
            } catch (\Exception $e) {
                return response($e->getMessage(), 500);
            }
        });
    }

    /**
     * Add a new class
     * Has to be admin
     *
     * @param Request $request
     * @return mixed  state of the request
     */
    public function addClass(Request $request)
    {
        $user = $request->input('user');
        $password = $request->input('password');
        return AdminService::adminAction($user, $password, function () use ($request) {
            $classEdited = $request->json()->get('classRoom');
            try {
                $classes = DataFileManager::getDataFile(DataFileEnum::CLASSES);
                $classEdited['id'] = count($classes) == 0 ? 1 : $classes[count($classes) - 1]->id + 1;
                $classJson =  new \stdClass();
                $classJson->id = $classEdited['id'];
                $classJson->name = $classEdited['name'];
                $classJson->google_id = $classEdited['googleId'];
                array_push($classes, $classJson);
                $info = DataFileManager::writeDataFile(DataFileEnum::CLASSES, $classes);
                return response()->json($info);
            } catch (\Exception $e) {
                return response($e->getMessage(), 500);
            }
        });
    }

    /**
     * Remove a class
     * Has to be admin
     *
     * @param Request $request
     * @return mixed  state of the request
     */
    public function removeClass(Request $request)
    {
        $user = $request->input('user');
        $password = $request->input('password');
        return AdminService::adminAction($user, $password, function () use ($request) {
            $classEdited = $request->json()->get('classRoom');
            try {
                $classes = DataFileManager::getDataFile(DataFileEnum::CLASSES);
                foreach ($classes as $index => $class) {
                  if($class->id == $classEdited['id']) {
                    array_splice($classes, $index, 1);
                  }
                }
                $info = DataFileManager::writeDataFile(DataFileEnum::CLASSES, $classes);
                return response()->json($info);
            } catch (\Exception $e) {
                return response($e->getMessage(), 500);
            }
        });
    }
}
