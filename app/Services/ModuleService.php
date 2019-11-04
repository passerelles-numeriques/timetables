<?php

namespace App\Services;

use App\Models\Enum\DataFileEnum;
use App\Services\DataFileManager;
class ModuleService
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
     * Get module from his id
     *
     * @param int $moduleId
     * @return void
     */
    public static function getModule($moduleId)
    {
        $modules = DataFileManager::getDataFile(DataFileEnum::MODULES);
        foreach ($modules as $module) {
            if($module->id == $moduleId) {
                return $module;
            }
        }
        throw new \Exception("This id doesn't exist", 1);
    }
}
