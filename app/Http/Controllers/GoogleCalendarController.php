<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\GoogleCalendarService;

class GoogleCalendarController extends Controller
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
     * Get the list of events from a google calendar
     *
     * @param Request $request
     * @param string $idGoogleCalendar
     * @return mixed list of google calendar event
     */
    public function getGoogleCalendarEvents(Request $request, $idGoogleCalendar)
    {
      try {
          $startDate = $request->input('start');
          $endDate = $request->input('end');
          $showClasses = true;
          $showClassInput = $request->input('showClasses');
          if(isset($showClassInput)){
            $showClasses = $showClassInput === 'true';
          }
          $googleData = GoogleCalendarService::getCalendarEvents($idGoogleCalendar, $startDate, $endDate, $showClasses);
          return response()->json($googleData);
      } catch (\Exception $e) {
          return response($e->getMessage(), 500);
      }
    }
}
