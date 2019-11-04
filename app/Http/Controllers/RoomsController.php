<?php

namespace App\Http\Controllers;

use App\Services\DataFileManager;
use App\Models\Enum\DataFileEnum;
use App\Services\AdminService;
use Illuminate\Http\Request;


class RoomsController extends Controller
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
     * Get rooms
     * Public access
     *
     * @return mixed list of room
     */
    public function getRooms(){
        try {
            $rooms = DataFileManager::getDataFile(DataFileEnum::ROOMS);
            return response()->json($rooms);
        } catch (\Exception $e) {
            return response($e->getMessage(), 500);
        }
    }

    /**
     * Update a room
     * Has to be admin
     *
     * @param Request $request
     * @return mixed  state of the request
     */
    public function editRoom(Request $request)
    {
        $user = $request->input('user');
        $password = $request->input('password');
        return AdminService::adminAction($user, $password, function () use ($request) {
            $roomEdited = $request->json()->get('room');
            try {
                $rooms = DataFileManager::getDataFile(DataFileEnum::ROOMS);
                foreach ($rooms as $room) {
                  if($room->id == $roomEdited['id']) {
                    $room->name = $roomEdited['name'];
                    $room->google_id = $roomEdited['googleId'];
                  }
                }
                $info = DataFileManager::writeDataFile(DataFileEnum::ROOMS, $rooms);
                return response()->json($info);
            } catch (\Exception $e) {
                return response($e->getMessage(), 500);
            }
        });
    }

    /**
     * Add a new room
     * Has to be admin
     *
     * @param Request $request
     * @return mixed  state of the request
     */
    public function addRoom(Request $request)
    {
        $user = $request->input('user');
        $password = $request->input('password');
        return AdminService::adminAction($user, $password, function () use ($request) {
            $roomEdited = $request->json()->get('room');
            try {
                $rooms = DataFileManager::getDataFile(DataFileEnum::ROOMS);
                $roomEdited['id'] = count($rooms) == 0 ? 1 : $rooms[count($rooms) - 1]->id + 1;
                $roomJson =  new \stdClass();
                $roomJson->id = $roomEdited['id'];
                $roomJson->name = $roomEdited['name'];
                $roomJson->google_id = $roomEdited['googleId'];
                array_push($rooms, $roomJson);
                $info = DataFileManager::writeDataFile(DataFileEnum::ROOMS, $rooms);
                return response()->json($info);
            } catch (\Exception $e) {
                return response($e->getMessage(), 500);
            }
        });
    }

    /**
     * Remove a room
     * Has to be admin
     *
     * @param Request $request
     * @return mixed  state of the request
     */
    public function removeRoom(Request $request)
    {
        $user = $request->input('user');
        $password = $request->input('password');
        return AdminService::adminAction($user, $password, function () use ($request) {
            $roomEdited = $request->json()->get('room');
            try {
                $rooms = DataFileManager::getDataFile(DataFileEnum::ROOMS);
                foreach ($rooms as $index => $room) {
                  if($room->id == $roomEdited['id']) {
                    array_splice($rooms, $index, 1);
                  }
                }
                $info = DataFileManager::writeDataFile(DataFileEnum::ROOMS, $rooms);
                return response()->json($info);
            } catch (\Exception $e) {
                return response($e->getMessage(), 500);
            }
        });
    }
}
