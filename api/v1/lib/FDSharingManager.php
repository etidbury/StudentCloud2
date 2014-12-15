<?php
/**
 * Created by PhpStorm.
 * User: Admin
 * Date: 07/12/2014
 * Time: 16:15
 */




class FDSharingManager {

    private $db;


    public function __construct() {
        $this->db=new DB();

    }
/*    public function getRecentSharingList() {

        $sth=$this->db->prepare("

        SELECT Shares.*,FileRecords.file_name,FileRecords.file_size,FileRecords.file_ext,FileRecords.user_id FROM Shares
LEFT JOIN FileRecords ON Shares.file_id=FileRecords.file_id

WHERE Shares.share_status=:SHARE_PUBLIC

        ");



        if ($sth->execute(array("SHARE_PUBLIC"=>ShareStatus::SHARE_PUBLIC))) {
            return r::jsonWithData("Successfully obtained list of recent files that have been shared",$sth,false,true);
        }else throw new SQLFailure($sth);


    }


    public function getRecentRequestSharingList() {

        $sth=$this->db->prepare("

       SELECT Shares.*,FileRecords.file_name,FileRecords.file_size,FileRecords.file_ext FROM Shares
LEFT JOIN FileRecords ON Shares.file_id=FileRecords.file_id

WHERE Shares.share_status=:SHARE_REQUEST_PUBLIC

        ");




        if ($sth->execute(array("SHARE_REQUEST_PUBLIC"=>ShareStatus::SHARE_REQUEST_PUBLIC))) {
            return r::jsonWithData("Successfully obtained list of recent file sharing requests",$sth,false,true);
        }else throw new SQLFailure($sth);
    }*/


    public function updateMultipleFileSharingStatus($file_id_list=array(), ShareStatus $shareStatus) {

        $sth=$this->db->prepare('

            REPLACE INTO Shares SET file_id= :file_id ,share_status=:share_status,timestamp=NOW()


        ');


        $sth->bindParam('share_status',$share_status,PDO::PARAM_INT);


        $no_queued=count($file_id_list);
        $no_success=0;

        foreach($file_id_list as $file_id) {

            $sth->bindParam('file_id',$file_id,PDO::PARAM_INT);

            if ($sth->execute()) {
                $no_success++;
            }else break;


        }

        if ($no_success<$no_queued) throw new HighException("Failed to share ".($no_queued-$no_success)." files");

        return r::json(SUCCESS,"Successfully deleted {$no_success} files");

    }
} 