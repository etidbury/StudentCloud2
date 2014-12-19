<?php
/**
 * Created by PhpStorm.
 * User: Admin
 * Date: 07/12/2014
 * Time: 16:45
 */

class DriveManager {

    private static $s_dir="s/";
    private $user_id;


    public function __construct($user_id=null) {


        if (!empty($user_id)) $this->setUserID($user_id);

    }

    private function setUserID($user_id) {

        //todo: authorize moderator access?
        //todo: validate id
        $this->user_id=$user_id;
    }
    public function getUserID() {
        return $this->user_id;
    }

    private function createUserStorageDirectory() {
        if (!mkdir(self::$s_dir.$this->getUserID())) throw new HighException("Failed to make storage directory for user");//TODO: throw exception on fail
    }
    private function getUserStorageDirectory() {

        $dir=self::$s_dir.$this->getUserID()."/";

        if (!file_exists($dir)) $this->createUserStorageDirectory();


        return $dir;



    }

    private static function isSupportedFile($file_ext) {

        //TODO: CHECK SUPPORT OF FILE
        return true;


    }


    public function addFile($file_id,$file_ext, $file_tmp) {

        if ($file_id<=0) throw new HighException("Invalid file ID specified");



        if (!self::isSupportedFile($file_ext)) throw new HighException("Specified file is not supported");




        if (move_uploaded_file($file_tmp,$this->getUserStorageDirectory().$file_id.".".$file_ext)) {
            tl::add("DriveManager::addFile --> ".$this->getUserStorageDirectory().$file_id.".".$file_ext);
            return true;

        }



        return false;

    }

    public function deleteFile($file_id) {

        $fileDir=$this->getUserStorageDirectory().$file_id;

        if (!file_exists($fileDir)) return false;//TODO: throw exception on fail

        if (unlink($fileDir)) {
            tl::add("DriveManager::deleteFile --> ".$fileDir);

            return true;

        }

        return false;


    }

} 