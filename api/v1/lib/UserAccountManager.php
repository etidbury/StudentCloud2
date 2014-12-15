<?php
/**
 * Created by PhpStorm.
 * User: Admin
 * Date: 14/12/2014
 * Time: 17:39
 */

class UserAccountManager {


    private $db;


    public function __construct() {



        $this->db=new DB();


    }

    public function getUserAccountDetails($user_id) {

        $sth=$this->db->prepare("


SELECT

PersonalDetail.firstName,
PersonalDetail.lastName,
PersonalDetail.primaryEmail,
PersonalDetail.lastUpdated,
UserAccount.timeRegistered

FROM UserAccount LEFT JOIN PersonalDetail ON UserAccount.user_ID=PersonalDetail.user_ID WHERE UserAccount.user_ID=:user_id


");


        $sth->bindParam('user_id',$user_id,PDO::PARAM_INT);

        if ($sth->execute()) {
            return r::jsonWithData("Successfully obtained user details",$sth,true,true);
        }else throw new SQLFailure($sth);

        
    }
    public function updateUserRole($user_id,$new_user_role) {
        $sth=$this->db->prepare("

            UPDATE UserAuthDetail SET user_role=:new_user_role WHERE user_ID=:user_id LIMIT 1


        ");


        $sth->bindParam('user_id',$user_id,PDO::PARAM_INT);
        $sth->bindParam('new_user_role',$user_role,PDO::PARAM_INT);

        if ($sth->execute()) {
            return true;
        }else throw new SQLFailure($sth);

        return false;
    }

    public function beginTransaction() {
        return $this->db->beginTransaction();
    }
    public function commit() {
        return $this->db->commit();
    }
    public function updateUserPrimaryEmail($user_id,$new_primaryEmail) {
        $sth=$this->db->prepare("

            UPDATE PersonalDetail SET primaryEmail=:new_primaryEmail WHERE user_ID=:user_id LIMIT 1


        ");


        $sth->bindParam('user_id',$user_id,PDO::PARAM_INT);
        $sth->bindParam('new_primaryEmail',$new_primaryEmail,PDO::PARAM_STR);

        if ($sth->execute()) {
            return true;
        }else throw new SQLFailure($sth);

        return false;
    }

}