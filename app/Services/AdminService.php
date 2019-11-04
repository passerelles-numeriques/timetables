<?php

namespace App\Services;

use App\Models\Enum\DataFileEnum;
use App\Services\DataFileManager;
class AdminService
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
     * Function for authentification
     *
     * @param string $user
     * @param string $password
     * @return boolean
     */
    public static function isAdmin(string $user = null, string $password = null)
    {
      if($user == null || $password == null) {
        return false;
      }
      try {
        $admin = DataFileManager::getDataFile(DataFileEnum::ADMIN);
        return $user == $admin->name && password_verify($password, $admin->pswd);

      } catch (\Exception $e) {
        return false;
      }
    }

    /**
     * Wrapper function when an authentification is needed
     *
     * @param string $user
     * @param string $password
     * @param callable $action
     * @return mixed   result of the action if connected, response error otherwise
     */
    public static function adminAction(string $user = null, string $password = null, $action) {
      if (AdminService::isAdmin($user, $password)) {
          return $action();
      } else {
        return response("You are not logged as an admin", 401);
      }
    }
}
