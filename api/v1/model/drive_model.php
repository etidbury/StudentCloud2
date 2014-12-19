<?php

require('lib/DriveManager.php');
require('lib/FDRecordManager.php');

class Drive_Model extends Model {

    public function __construct() {
        parent::__construct();

    }
    
    public function postUploadFile($dir_id=-1) {


        $user_id=Authentication::getUserID();


        $fdr=new FDRecordManager();
        if (isset($_FILES['file']["name"],$_FILES['file']["size"],$_FILES['file']["tmp_name"])) {
            //
            return $fdr->addFileRecord($_FILES['file'],$user_id,$dir_id);

        }else throw new PostDataFailure(print_r($_FILES,true));

    }
    public function getUserFileList($dir_id=-1,$user_id=0,$from=0,$length=20,$shareStatus=ShareStatus::SHARE_PRIVATE) {

        $fdr=new FDRecordManager();

        $user_id=empty($user_id)?Authentication::getUserID():$user_id;

        return $fdr->getUserFileList($dir_id,$user_id,$from,$length,$shareStatus);

    }
    public function deleteFiles() {

        $fdr=new FDRecordManager();

        $file_id_list=array();
        if (isset($_POST['file_id'])&&is_array($_POST['file_id'])) {
            foreach($_POST['file_id'] as $file_id) {
                if (!empty($file_id)) $file_id_list[]=$file_id;
            }
        }else throw new PostDataFailure();

        return $fdr->deleteFiles($file_id_list);
    }

    public function renameFile() {
        $fdr=new FDRecordManager();

        $params=array();
        $params['file_name']=new ParameterValidation(1,150);

        parent::parseVars($_POST,$params);

        if (isset($_POST['file_id'])&&!empty($_POST['file_id'])) {
            return $fdr->updateFileName($_POST['file_id'],$_POST['file_name']);
        }else throw new PostDataFailure("Invalid file ID specified");
    }






    public function updateMultipleFileSharingStatus() {
        $fds=new FDSharingManager();


        $params=array();
        $params['share_status']=new ParameterValidation(1,15);

        parent::parseVars($_POST,$params);

        $file_id_list=array();
        if (isset($_POST['file_id'])&&is_array($_POST['file_id'])) {
            foreach($_POST['file_id'] as $file_id) {
                if (!empty($file_id)) $file_id_list[]=$file_id;
            }
        }else throw new PostDataFailure();

        return $fds->updateMultipleFileSharingStatus($file_id_list,$_POST['share_status']);



    }


}