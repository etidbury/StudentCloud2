<?php
/**
 * Created by PhpStorm.
 * User: Admin
 * Date: 07/12/2014
 * Time: 15:24
 */

class FDRecordManager extends DriveManager {


    private $db;


    public function __construct($user_id=null) {


        $this->db=new DB();


        $user_id = is_null($user_id)?Authentication::getUserID():$user_id;

        parent::__construct($user_id);

    }


    public function addFileRecord($FILE_POST,$user_id,$dir_id=-1) {


        //foreach($FILE_POST as $key=>$val) $FILE_POST[$key]=Sanitize::normalNoSpace($val);//todo: refine

        $file_name=$FILE_POST['name'];

        $file_ext = strtolower(substr($file_name, strrpos($file_name, ".") + 1,strlen($file_name)));

        $file_name = substr($file_name, 0, strpos($file_name, "."));

        $file_cache_size=$FILE_POST['size'];




        if (!$this->userHasEnoughStorage($file_cache_size)) throw new LowException("Not enough storage");


        $sth=$this->db->prepare("
            INSERT INTO FileRecords
            (
            dir_id,
            file_name,
            file_type,
            file_ext,
            file_cache_size,
            user_id
            ) VALUES (
            :dir_id,
            :file_name,
            :file_type,
            :file_ext,
            :file_cache_size,
            :user_id

        )");


        $sth->bindParam('dir_id',$dir_id,PDO::PARAM_INT);
        $sth->bindParam('file_name',$file_name,PDO::PARAM_STR);
        $sth->bindParam('file_type',$FILE_POST['type'],PDO::PARAM_STR);
        $sth->bindParam('file_ext',$file_ext,PDO::PARAM_STR);
        $sth->bindParam('file_cache_size',$file_cache_size,PDO::PARAM_INT);
        $sth->bindParam('user_id',$user_id,PDO::PARAM_INT);



        if ($sth->execute()) {




            $this->addFile($this->getLastInsertID(),$file_ext,$FILE_POST['tmp_name']);

            return r::jsonWithData("Successfully added file record: '{$file_name}.{$file_ext}'",$sth,true,true);
        }else throw new SQLFailure($sth);


    }
    private function userHasEnoughStorage($file_size) {
        return true;
    }


    public function getLastInsertID() {
        return $this->db->lastInsertId();
    }


    public function getDirName($dir_id) {

        if ($dir_id<=-1) return r::json(1,"Successfully obtained directory name",array("dir_name"=>"[Main Directory]"));


        $sth=$this->db->prepare("SELECT dir_name FROM DirectoryRecords WHERE dir_id=:dir_id");



        if ($sth->execute(array('dir_id'=>$dir_id))) {
            return r::jsonWithData("Successfully obtained directory name",$sth,true,true);
        }else throw new SQLFailure($sth);

    }
    public function getFileName($file_id) {

        $sth=$this->db->prepare("SELECT file_name FROM FileRecords WHERE file_id=:file_id");

        if ($sth->execute(array('file_id'=>$file_id))) {
            return r::jsonWithData("Successfully obtained file name",$sth,true,true);
        }else throw new SQLFailure($sth);

    }
    public function updateDirectoryName($dir_id,$newName) {

        $sth=$this->db->prepare("UPDATE DirectoryRecords SET dir_name = :newName WHERE dir_id=:dir_id LIMIT 1");

        $sth->bindParam('dir_id',$dir_id,PDO::PARAM_INT);
        $sth->bindParam('newName',$newName,PDO::PARAM_STR);

        if ($sth->execute()) {
            return r::jsonWithData("Successfully renamed directory to '{$newName}'",$sth,true,true);
        }else throw new SQLFailure($sth);
    }
    public function updateFileName($file_id,$newName) {


        $sth=$this->db->prepare("UPDATE FileRecords SET file_name = :newName WHERE file_id=:id LIMIT 1");

        $sth->bindParam('file_id',$file_id,PDO::PARAM_INT);
        $sth->bindParam('newName',$newName,PDO::PARAM_STR);

        if ($sth->execute()) {
            return r::jsonWithData("Successfully renamed file to '{$newName}'",$sth,false,true);
        }else throw new SQLFailure($sth);
    }

    public function getUserFileList($dir_id,$user_id,$from,$length,$shareStatus=ShareStatus::SHARE_PRIVATE) {


        if (is_null($shareStatus)||$shareStatus==ShareStatus::SHARE_PRIVATE) {
            $testshare=false;
            $sth=$this->db->prepare("


SELECT FileRecords.*, UNIX_TIMESTAMP(STR_TO_DATE(FileRecords.timestamp, '%Y-%m-%d %H:%i:%s')) AS unix_timestamp, Shares.share_status,Shares.verifiedBy,Shares.timestamp AS shared_timestamp FROM FileRecords

 LEFT JOIN Shares ON FileRecords.file_id=Shares.file_id

 WHERE FileRecords.dir_id=:dir_id AND FileRecords.user_id=:user_id LIMIT :limit_from,:limit_length


        ");



        }else {
            $testshare=true;



            $sth=$this->db->prepare("

SELECT FileRecords.*,Shares.share_status,Shares.verifiedBy,Shares.timestamp AS shared_timestamp, PersonalDetail.firstName AS uploaded_by FROM FileRecords

 LEFT JOIN Shares ON FileRecords.file_id=Shares.file_id

 LEFT JOIN PersonalDetail ON FileRecords.user_id=PersonalDetail.user_id


 WHERE FileRecords.dir_id=:dir_id AND FileRecords.user_id=:user_id AND Shares.share_status=:share_status LIMIT :limit_from,:limit_length


        ");



            $sth->bindParam('share_status',$shareStatus,PDO::PARAM_INT);
        }




        $sth->bindParam('dir_id',$dir_id,PDO::PARAM_INT);
        $sth->bindParam('user_id',$user_id,PDO::PARAM_INT);
        $sth->bindParam('limit_from',$from,PDO::PARAM_INT);
        $sth->bindParam('limit_length',$length,PDO::PARAM_INT);



        //throw new LowException("testshare:{$testshare} userid:{$user_id} dir_id:{$dir_id} limitfrom:{$from} limit_length:{$length}");

        if ($sth->execute()) {
            return r::jsonWithData("Successfully obtained user file list",$sth,true,true);
        }else throw new SQLFailure($sth);
    }
    public function deleteFiles($file_id_list=array()) {

        $sth=$this->db->prepare("

        DELETE FROM FileRecords WHERE file_id=:file_id LIMIT 1


");


        $no_queued=count($file_id_list);
        $no_delete_success=0;

        foreach($file_id_list as $file_id) {

            $sth->bindParam('file_id',$file_id,PDO::PARAM_INT);

            if ($sth->execute()) {
                if (self::deleteFile($file_id)) $no_delete_success++;
            }else break;


        }

        if ($no_delete_success<$no_queued) throw new HighException("Failed to delete ".($no_queued-$no_delete_success)." files");

        return r::json(SUCCESS,"Successfully deleted {$no_delete_success} files");

    }
} 