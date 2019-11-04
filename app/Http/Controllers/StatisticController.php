<?php

namespace App\Http\Controllers;

use App\Services\AdminService;
use Illuminate\Http\Request;
use App\Services\StatisticService;


class StatisticController extends Controller
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
     * Get the list of events from a google calendar filtered by the name of the lesson
     *
     * @param Request $request
     * @param int $idLesson
     * @return mixed list of google calendar event
     */
    public function getEventsByLesson(Request $request, $idLesson) {
        $user = $request->input('user');
        $password = $request->input('password');
        return AdminService::adminAction($user, $password, function () use ($request, $idLesson) {
            try {
                $googleData = StatisticService::getLessonStats($idLesson);
                return response()->json($googleData);
                $lessons = DataFileManager::getDataFile(DataFileEnum::LESSONS);
                return response()->json($lessons);
            } catch (\Exception $e) {
                return response($e->getMessage(), 500);
            }
        });
    }
}
