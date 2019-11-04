<?php

namespace App\Services;

use App\Models\Enum\DataFileEnum;

class DataFileManager
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
     * Get the content of a json file
     *
     * @param string $fileName
     * @param boolean $isBackup
     * @return mixed  content of the file
     */
    public static function getDataFile($fileName, $isBackup = false)
    {
        $file_path = DataFileManager::getDataFilePath($fileName, $isBackup);
        $jsonData = json_decode(file_get_contents($file_path));
        if($jsonData === null) {
            $backup = DataFileManager::getDataFile($fileName, true);
            $file_path = DataFileManager::getDataFilePath($fileName);
            file_put_contents($file_path,json_encode($backup, JSON_PRETTY_PRINT));
            return $backup;
        }
        return $jsonData;
    }

    /**
     * Write content into json file
     *
     * @param string $fileName
     * @param mixed $content
     * @return boolean
     */
    public static function writeDataFile($fileName, $content) {
        $contentToBackup = DataFileManager::getDataFile($fileName);
        $file_path = DataFileManager::getDataFilePath($fileName, true);
        $result = file_put_contents($file_path,json_encode($contentToBackup, JSON_PRETTY_PRINT));
        $file_path = DataFileManager::getDataFilePath($fileName);
        $result = file_put_contents($file_path,json_encode($content, JSON_PRETTY_PRINT));
        return $result != false;
    }

    /**
     * Get the full path of a file
     *
     * @param string $fileName
     * @param boolean $isBackup
     * @return string
     */
    private static function getDataFilePath($fileName, $isBackup = false){
      if (!DataFileEnum::isADataFile($fileName)) {
          throw new \Exception("The data file doesn't exist\nYou should use App\Models\Enum\DataFileEnum instead", 1);
      }
      $path = storage_path('db'. DIRECTORY_SEPARATOR . $fileName . ($isBackup ? '.backup' : '') . '.json');
      return $path;
    }

    /**
     * Return the last time each data file was modified
     * For cache invalidation purposes
     * @return stdClass object listing the modification times (one property for each file)
     */
    public static function getLastModifiedTime() {
        $files = new \stdClass();
        $files->rooms = filemtime(self::getDataFilePath(DataFileEnum::ROOMS));
        //$page->generals = filemtime(self::getDataFilePath(DataFileEnum::GENERALS));   //No more used ?
        $files->classes = filemtime(self::getDataFilePath(DataFileEnum::CLASSES));
        $files->teachers = filemtime(self::getDataFilePath(DataFileEnum::TEACHERS));
        $files->lessons = filemtime(self::getDataFilePath(DataFileEnum::LESSONS));
        //$page->admin = filemtime(self::getDataFilePath(DataFileEnum::ADMIN)); //Used only on server side
        $files->modules = filemtime(self::getDataFilePath(DataFileEnum::MODULES));
        return $files;
    }
}
