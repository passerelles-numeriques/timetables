<?php

namespace App\Services;

use App\Models\Enum\DataFileEnum;
use App\Services\DataFileManager;
class GoogleCalendarService
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
     * Get events from google calendar
     *
     * @param string $calendarId
     * @param mixed $startDate
     * @param mixed $endDate
     * @param boolean $showClass
     * @param boolean $detailledName
     * @param string $query
     * @return mixed  list of events
     */
    public static function getCalendarEvents($calendarId, $startDate, $endDate, $showClass, $detailledName=true, $query=null)
    {
        $client = new \GuzzleHttp\Client();
        $url = 'https://www.googleapis.com/calendar/v3/calendars/';
        $url .= $calendarId;
        $url .= '/events?maxResults=100000&singleEvents=true&showDeleted=False';
        $url .= '&timeZone=' . config('app.GOOGLE_CAL_TMZ');
        $url .= '&key=' . config('app.GOOGLE_API_KEY');
        if(isset($startDate) && isset($endDate)){

          $startFormat = date("Y-m-d\TH:i:s+07:00", is_numeric($startDate) ? $startDate : strtotime($startDate));
          $endFormat = date("Y-m-d\TH:i:s+07:00",is_numeric($endDate) ? $endDate : strtotime($endDate));

          $url .= '&timeMin=' . urlencode($startFormat) . '&timeMax=' . urlencode($endFormat);
        }
        if(isset($query)) {
          $url.= '&q=' . $query;
        }
        $res = $client->request('GET', $url);
        if ($res->getStatusCode() == 200) {
            $googleCalendar = json_decode($res->getBody());
            $events = [];
            $i = 1;
            foreach ($googleCalendar->items as $event) {
              $itemCalendar = new \stdClass();
              $title = $event->summary;
              if($detailledName && isset($event->location)) {
                $title .= ', ' . $event->location;
              }
              if($detailledName && $showClass === true && isset($event->organizer) && isset($event->organizer->displayName)) {
                $classes = DataFileManager::getDataFile(DataFileEnum::CLASSES);
                $organizer = '';
                foreach ($classes as $class) {
                  if($class->google_id == $event->organizer->email) {
                    $organizer = $class->name;
                  }
                }
                $title .= ', ' . $organizer;
              }
              $title = str_replace('PNC-T-','',$title);
              $title = str_replace('PNC-R-','',$title);
              $title = str_replace('undefined','*/*',$title);
              $itemCalendar->title = $title;
              $itemCalendar->start = $event->start->dateTime;
              $itemCalendar->end = $event->end->dateTime;
              $itemCalendar->allDay = false;
              array_push($events, $itemCalendar);
              $i++;
            }
            return $events;
        } else {
          throw new \Exception("Error Processing Request", 1);
            return [];
        }
    }
}
