<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It is a breeze. Simply tell Lumen the URIs it should respond to
| and give it the Closure to call when that URI is requested.
|
*/

$router->group(['prefix' => 'api'], function () use ($router) {
    // If new feature is needed
    $router->group(['prefix' => 'v1'], function () use ($router) {
        // Get informations for calendars (teachers, rooms, classes)
        $router->group(['prefix' => 'calendar'], function () use ($router) {
            $router->get('generals', [
                'as' => 'generals', 'uses' => 'CalendarController@getGenerals'
            ]);
            $router->get('classes', [
                'as' => 'classes', 'uses' => 'CalendarController@getClasses'
            ]);
            $router->get('teachers', [
                'as' => 'teachers', 'uses' => 'CalendarController@getTeachers'
            ]);
            $router->get('rooms', [
                'as' => 'rooms', 'uses' => 'CalendarController@getRooms'
            ]);
        });
        // Application specific routes
        $router->group(['prefix' => 'app'], function () use ($router) {
          $router->get('files/modified', [
            'as' => 'files', 'uses' => 'AppController@getModificationTimes'
        ]);
      });
        // Retrieve google calendar events
        $router->group(['prefix' => 'google'], function () use ($router) {
            $router->get('events/{id}', [
              'as' => 'googleEvents', 'uses' => 'GoogleCalendarController@getGoogleCalendarEvents'
          ]);
        });
        // Admin part
        $router->group(['prefix' => 'admin'], function () use ($router) {
            // Auth
            $router->get('/', [
              'as' => 'isAdmin', 'uses' => 'AdminController@isAdmin'
            ]);
            // Modules management
            $router->group(['prefix' => 'modules'], function () use ($router) {
                $router->get('/', [
                  'as' => 'modules', 'uses' => 'ModuleController@getModules'
                ]);
                $router->put('/', [
                  'as' => 'editModule', 'uses' => 'ModuleController@editModule'
                ]);
                $router->post('/', [
                  'as' => 'addModule', 'uses' => 'ModuleController@addModule'
                ]);
                $router->delete('/', [
                  'as' => 'addModule', 'uses' => 'ModuleController@removeModule'
                ]);
                $router->get('/combined', [
                  'as' => 'combined', 'uses' => 'ModuleController@getModulesLessonsTeachers'
                ]);
            });
            // Lessons management
            $router->group(['prefix' => 'lessons'], function () use ($router) {
                $router->get('/', [
                  'as' => 'lessons', 'uses' => 'LessonController@getLessons'
                ]);
                $router->put('/', [
                  'as' => 'editLesson', 'uses' => 'LessonController@editLesson'
                ]);
                $router->post('/', [
                  'as' => 'addLesson', 'uses' => 'LessonController@addLesson'
                ]);
                $router->delete('/', [
                  'as' => 'addLesson', 'uses' => 'LessonController@removeLesson'
                ]);
            });
            // Teachers management
            $router->group(['prefix' => 'teachers'], function () use ($router) {
                $router->get('/', [
                  'as' => 'teachers', 'uses' => 'TeacherController@getTeachers'
                ]);
                $router->put('/', [
                  'as' => 'editTeacher', 'uses' => 'TeacherController@editTeacher'
                ]);
                $router->post('/', [
                  'as' => 'addTeacher', 'uses' => 'TeacherController@addTeacher'
                ]);
                $router->delete('/', [
                  'as' => 'addTeacher', 'uses' => 'TeacherController@removeTeacher'
                ]);
            });
            // Rooms management
            $router->group(['prefix' => 'rooms'], function () use ($router) {
                $router->get('/', [
                  'as' => 'rooms', 'uses' => 'RoomsController@getRooms'
                ]);
                $router->put('/', [
                  'as' => 'editRoom', 'uses' => 'RoomsController@editRoom'
                ]);
                $router->post('/', [
                  'as' => 'addRoom', 'uses' => 'RoomsController@addRoom'
                ]);
                $router->delete('/', [
                  'as' => 'addRoom', 'uses' => 'RoomsController@removeRoom'
                ]);
            });
            // Classes management
            $router->group(['prefix' => 'classes'], function () use ($router) {
                $router->get('/', [
                  'as' => 'classes', 'uses' => 'ClassesController@getClasses'
                ]);
                $router->put('/', [
                  'as' => 'editClass', 'uses' => 'ClassesController@editClass'
                ]);
                $router->post('/', [
                  'as' => 'addClass', 'uses' => 'ClassesController@addClass'
                ]);
                $router->delete('/', [
                  'as' => 'addClass', 'uses' => 'ClassesController@removeClass'
                ]);
            });
            // Retrieve information about lesson statistics
            $router->group(['prefix' => 'stats'], function () use ($router) {
                $router->get('events/{id}', [
                  'as' => 'eventsLesson', 'uses' => 'StatisticController@getEventsByLesson'
                ]);
            });
        });
    });
});

$router->get('/{route:.*}/', function ()  {
  return view('index');
});
