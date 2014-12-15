<?php
class FileHandleException extends Exception {

}
class FileHandle {

    private static $instance;
    private $user_id;
    private $username;
    private $db;
    private $response="";

    public function __construct() {
        $this->user_id = Authentication::init()->getUserID();
        $this->user_type = Authentication::init()->getUserType();
        $this->username = Authentication::init()->getUsername();
        $this->db=new DB();
    }


    private function getParentDirID($dir_id) {
        tl::add("getParentDirID: ".$dir_id);
        $param=array('dir_id'=>$dir_id,"user_id"=>$this->user_id);
        $sth = $this->db->prepare('

        SELECT dir_parent_id FROM DirectoryRecords WHERE dir_id=:dir_id AND user_id=:user_id LIMIT 1


        ');

        $sth->execute($param);

        return ($sth->fetchColumn()>-1)?$sth->fetchColumn():-1;
    }
    public function getBreadcrumb($dir_id) {
        $bc=array();

        $lastID=$dir_id;


        $bc[]=$this->username;//add username to breadcrumb
        $bc[]=$this->getDirName($lastID);

        tl::add("getBreadcrumb: go through list...");
        while (floatval($this->getParentDirID($lastID))>-1) {
            $bc[]=$this->getDirName($this->getParentDirID($lastID));

            $lastID = $this->getParentDirID($lastID);
            tl::add("getBreadcrumb: ".$this->getDirName($lastID));
        }



        return implode("/",$bc);

    }

    private function isRegisteredDir($dir) {
        if ($this->user_id>-1) {
            //check directory specified with the SQL database
            return true;
        }



    }


    public function getFileList($dir_id) {

        $output=array();

        $param=array('dir_parent_id'=>$dir_id,"user_id"=>$this->user_id);

        $sth = $this->db->prepare('
        SELECT DirectoryRecords.*,DateModifiedRecords.timestamp AS dateModified,
        (SELECT COUNT(*) FROM FileRecords WHERE dir_id=:dir_parent_id) AS fileCount FROM DirectoryRecords
        LEFT JOIN DateModifiedRecords ON DateModifiedRecords.dir_id=DirectoryRecords.dir_id WHERE DirectoryRecords.dir_parent_id=:dir_parent_id AND DirectoryRecords.user_id=:user_id');
        $sth->setFetchMode(PDO::FETCH_ASSOC);
        $sth->execute($param);


        $output['dir'] = $sth->fetchAll();

        $sth = $this->db->prepare('
        SELECT FileRecords.*,DateModifiedRecords.timestamp AS dateModified,Shares.share_status AS shareStatus FROM FileRecords
LEFT JOIN DateModifiedRecords ON DateModifiedRecords.file_id=FileRecords.file_id
LEFT JOIN Shares ON Shares.file_id=FileRecords.file_id
WHERE FileRecords.dir_id=:dir_parent_id AND FileRecords.user_id=:user_id');
        $sth->setFetchMode(PDO::FETCH_ASSOC);
        $sth->execute($param);

        $output['file'] = $sth->fetchAll();

        return $output;
    }




    /************************ADDING FILES#**********************************/
    public function addFiles($dir_id=-1,$fileDetails) {
        $fd=$fileDetails;//shorthand


        if ($this->isRegisteredDir($dir_id)) {
            $this->updateDateModified('dir',$dir_id);

        }else return false;//TODO: throw error


        $availableSpace=$this->getAvailableUserSpace();

        if ($this->checkFileExists($fd['name'],$fd['type'])===-1) {


            $this->response="A internal server error has occurred!";//TODO: THROW EXCEPTION

            return false;
        }elseif ($this->checkFileExists($fd['name'],$fd['type'])) {
            $this->response="A file in this name already exists!";

            return false;
        }



        if ($fd['size']>$availableSpace) {
            $this->response="You do not have enough space for this file! (File size: ".self::convertSize($fd['size']).") Space left: ". self::convertSize($availableSpace)."";
            return false;
        }





        if (!$this->checkSupportedFile($fd['type'])||empty($fd['ext'])) {
            $this->response="File is not yet supported!";
            return false;
        }









        $file_id=$this->addFileRecord($dir_id,$fd['name'],$fd['type'],$fd['ext'],$fd['size']);

        if ($file_id<=-1) {
            $this->response="An internal server error has occurred!";
            return false;
        }

        if ($this->uploadFile($dir_id,$file_id,$fd['ext'], $fd['tmp'])) {

            return true;

        }else {
            /******* Undo changes *********/
            if (!$this->deleteFileRecord($file_id)) {

                //TODO: throw exception
            }


            /************----**************/

            $this->response="Server Error: Failed to upload file at this time!";
            return false;

        }



    }
    public function deleteFile($file_id) {
        $params=array("file_id"=>$file_id);

        tl::add("deleteFile called");

        $sth=$this->db->prepare("SELECT file_ext FROM FileRecords WHERE file_id=:file_id");

        if ($sth->execute($params)) {
            $file_ext=$sth->fetchColumn();
            tl::add("deleteFile ext:".$file_ext);


            if ($this->deleteFileRecord($file_id)) {

                tl::add("deleteFile deletefilerecord");
                if ($this->unlinkFile($file_id,$file_ext)) {
                    tl::add("deleteFile unlinkfile");
                    return array("file_count"=>1);
                }else return false;//todo: throw exception
            }else return false;//todo: throw exception

        }else return false;//todo: throw exception



    }
    public function deleteDirectory($dir_id) {
        $params=array("dir_id"=>$dir_id);
        $deleted=array();
        $deleted['dir_count']=0;
        $deleted['file_count']=0;




        $sth=$this->db->prepare("SELECT file_id FROM FileRecords WHERE dir_id=:dir_id");

        if ($sth->execute($params)) {
            while ($f=$sth->fetch(PDO::FETCH_BOUND)) {
                tl::add("dd delete file : ".$f[0]);
                $deleted['file_count']++;

            }


            $sth=$this->db->prepare("SELECT dir_id FROM DirectoryRecords WHERE parent_dir_id=:dir_id");

            if ($sth->execute($params)) {
                while ($d=$sth->fetch(PDO::FETCH_BOUND)) {
                    tl::add("dd delete dir : ".$d[0]);
                    $deleted['dir_count']++;

                }


                tl::add("delete JSON:-----".json_encode($deleted));
                return $deleted;

            }


        }else {
            return false;
            //TODO: THROW EXCEPTION
        }




    }
    private function unlinkFile($file_id,$file_ext) {
        $uid=$this->user_id;

        $fileDir="s/".$uid."/";//S_URL

        if (!file_exists($fileDir)) return false;//TODO: throw exception on fail

        if (unlink($fileDir.$file_id.".".$file_ext)) {
            return true;

        }else return false;

        tl::add("upload directory:".$uploadDir.$file_id.".".$file_ext);

    }
    private function deleteFileRecord($file_id) {

        $params=array("file_id"=>$file_id);

        $sth=$this->db->prepare("DELETE FROM FileRecords WHERE file_id=:file_id LIMIT 1");

        if ($sth->execute($params)) {
            return true;
        }else return false;
    }
    private function addFileRecord($dir_id,$file_name,$file_type,$file_ext,$file_size) {//returns the file_id of the new file record
        $params=array(
            "dir_id"=>$dir_id,

            "file_name"=>$file_name,
            "file_type"=>$file_type,
            "file_ext"=>$file_ext,
            "file_size"=>$file_size,
            "user_id"=>$this->user_id


        );//TODO: change to PDO::bindParams


        $sth=$this->db->prepare("INSERT INTO FileRecords (dir_id,file_name,file_type,file_ext,file_size,user_id) VALUES (:dir_id,:file_name,:file_type,:file_ext,:file_size,:user_id)");

        if ($sth->execute($params)) {
            $getID=$this->db->prepare("SELECT file_id FROM FileRecords WHERE file_name=:file_name LIMIT 1");

            if ($getID->execute(array("file_name"=>$file_name))) {

                $id=$getID->fetchColumn();
                if ($id>-1) {
                    return $id;
                }else return -1;

            }else {
                //TODO: THROW EXCEPTION
                return -1;

            }



        }else {

            //TODO: THROW EXCEPTION
            return -1;

        }



        return -1;

    }
    private function checkFileExists($file_name,$file_type) {

        $sth=$this->db->prepare("SELECT COUNT(*) FROM FileRecords WHERE user_id=:user_id AND file_name=:file_name AND file_type=:file_type");

        if ($sth->execute(array("user_id"=>$this->user_id,"file_name"=>$file_name,"file_type"=>$file_type))) {
            if ($sth->fetchColumn()>0) {
                return true;
            }else {
                return false;
            }
        }else {
            //TODO: THROW EXCEPTION
            return -1;

        }

    }
    private function checkDirExists($dir_name,$dir_id) {

        $sth=$this->db->prepare("SELECT COUNT(*) FROM DirectoryRecords WHERE user_id=:user_id AND dir_name=:dir_name AND dir_id=:dir_id");

        if ($sth->execute(array("user_id"=>$this->user_id,"dir_name"=>$dir_name,"dir_id"=>$dir_id))) {
            if ($sth->fetchColumn()>0) {
                return true;
            }else {
                return false;
            }
        }else {
            //TODO: THROW EXCEPTION
            return true;

        }

    }
    private function uploadFile($dir_id,$file_id,$file_ext,$file_tmp) {

        if ($file_id<=0) return false;//TODO: throw exception

        $uploadDir="s/".$this->user_id."/";//S_URL

        if (!file_exists($uploadDir)) mkdir($uploadDir);//TODO: throw exception on fail

        if (move_uploaded_file($file_tmp,$uploadDir.$file_id.".".$file_ext)) {
            return true;

        }//TODO: UPDATE HERE

        tl::add("upload directory:".$uploadDir.$file_id.".".$file_ext);

       return false;


    }
    private function checkSupportedFile($file_type) {

        //TODO: CHECK SUPPORT OF FILE
        return true;


    }
    public static function convertSize($size) {
        if (floatval($size)<=0) return false;
        $divide=1024;

        $sLabel=array('b','kb','mb','gb');

        $origSize=$size;
        $i=0;
        while ($origSize>=$divide&&$i<5) {
            $origSize=round($origSize/$divide);
            $i++;
        }
        return $origSize.$sLabel[$i];
    }
    public function getAvailableUserSpace($returnFriendlySize=false) {

        $params=array("user_id"=>$this->user_id);
        $sth=$this->db->prepare("
        SELECT SUM(FileRecords.file_size) AS space_used,users.space_allocated FROM users LEFT JOIN FileRecords ON users.user_id=FileRecords.user_id WHERE users.user_id=:user_id

        ");

        if (!$sth->execute($params)) {
            //todo: throw exception
            return -1;
        }

        $data=$sth->fetch(PDO::FETCH_ASSOC);

        $allocatedSpace=floatval($data['space_allocated']);
        $usedSpace=floatval($data['space_used']);

        $spaceLeft=floor($allocatedSpace-$usedSpace);
        tl::add("check space:".$allocatedSpace."- used:".$usedSpace." left:".$spaceLeft);

        $spaceLeft=$spaceLeft>0?$spaceLeft:0;

        if ($returnFriendlySize) {
            return $this->convertSize($spaceLeft);

        }else return $spaceLeft;



    }

    /************************#ADDING FILES**********************************/
    public static function init() {
        if (is_null(self::$instance)) {


            self::$instance=new FileHandle();
        }
        return self::$instance;
    }
    public function getResponse() {
        return $this->response;
    }
}

