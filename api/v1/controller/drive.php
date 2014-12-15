<?php

class Drive extends Controller {


	function __construct() {
		parent::__construct();
	}
	
	function files($dir_id=-1,$user_id=0,$from=0,$length=20,$shareStatus=ShareStatus::SHARE_PRIVATE) {

       echo $this->routeCRUD(array(
            "POST"=>array("postUploadFile",array("dir_id"=>$dir_id)),
            "GET"=>array("getUserFileList",array(
                "dir_id"=>$dir_id,
                "user_id"=>$user_id,
                "from"=>$from,
                "length"=>$length,
                "shareStatus"=>$shareStatus
            )),
            "DELETE"=>"deleteFiles",
            "PUT"=>"renameFile"
        ));



	}
   function share() {
       echo $this->routeCRUD(array(
           "PUT"=>"updateMultipleFileSharingStatus"
       ));
   }


}