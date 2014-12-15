<?php

require('lib/UserAccountManager.php');
require('lib/UserEmailVerification.php');

class User_Model extends Model
{
	public function __construct()
	{
		parent::__construct();
	}

    public function registerUserAccount()
    {


        // throw new HighException("parm".print_r($_POST,true));

        $params=array();
        $params['firstName']=new ParameterValidation(2,50);
        $params['lastName']=new ParameterValidation(2,50);
        $params['personalEmail']=new ParameterValidation(3,150);
        $params['accept_ta']=new ParameterValidation(1,1);
        $params['password']=new ParameterValidation(3,25);
        $params['c_password']=$params['password'];



        self::parseVars($_POST,$params);




        $r=$_POST;



        if (!($r['password'] === $r['c_password'])) throw new RegisterFailure("Passwords do not match");

        if (empty($r['accept_ta'])) throw new RegisterFailure("You need to read and accept the Terms and Conditions in order to join");

        if (!$this->db->beginTransaction()) throw new SQLFailure(null, "Failed to begin transaction");////1. begin SQL INSERTS


        $sth = $this->db->prepare("INSERT INTO UserAccount (timeRegistered) VALUES (:timeRegistered);");


        $timeRegistered = time();



        $sth->bindParam("timeRegistered", $timeRegistered, PDO::PARAM_INT, 1);


        if ($sth->execute()) {///2. create user account


            $user_ID = $this->db->lastInsertId();

            if (empty($user_ID) || $user_ID == 0) throw new HighException("Failed to obtain User ID");


            $sth = $this->db->prepare("INSERT INTO UserAuthDetail (user_ID,personalEmail,user_password,user_password_salt) VALUES (:user_ID,:personalEmail,:user_password,:user_password_salt)");


            $passwordSalt = Authentication::generateSalt();


            $saltedPassword = Authentication::generateSaltedPassword($r['password'], $passwordSalt);


            $sth->bindParam("user_ID", $user_ID, PDO::PARAM_INT);

            $sth->bindParam("personalEmail", $r['personalEmail'], PDO::PARAM_STR);

            $sth->bindParam("user_password", $saltedPassword, PDO::PARAM_STR);

            $sth->bindParam("user_password_salt", $passwordSalt, PDO::PARAM_STR);


            if ($sth->execute()) {///3.add login details for user account created


                $sth = $this->db->prepare("INSERT INTO PersonalDetail (user_ID,firstName,lastName,primaryEmail) VALUES (:user_ID,:firstName,:lastName,:primaryEmail)");

                $sth->bindParam("user_ID", $user_ID, PDO::PARAM_INT);

                $sth->bindParam("firstName", $r['firstName'], PDO::PARAM_STR);

                $sth->bindParam("lastName", $r['lastName'], PDO::PARAM_STR);

                $sth->bindParam("primaryEmail", $r['personalEmail'], PDO::PARAM_STR);///set primary contact email to the personal email by default.


                if ($sth->execute()) {////4. add personal details for user account created


                    if (!UserEmailVerification::sendEmailVerification($user_ID)) throw new EmailFailure("Failed to send verification email");


                    if ($this->db->commit()) {////5. register and save all user details to SQL DB

                        return r::json(SUCCESS,"Registered user:".$user_ID);

                    }///###REGISTER SUCCESS


                } else throw new SQLFailure($sth, "Failed to register personal details");

            } else throw new SQLFailure($sth, "Failed to register user login details");


        } else throw new SQLFailure($sth, "Failed to register User Account");

        r::json(0,"Registration failed");//###REGISTER FAILED

    }





    public function getUserAccountDetails($user_id) {

        if (is_null($user_id)) throw new LowException("User ID not specified");

        $uam=new UserAccountManager();
        return $uam->getUserAccountDetails($user_id);


    }
    public function updateUserAccountDetails($user_id) {



        $uam=new UserAccountManager();
        if (isset($_POST['primaryEmail'],$_POST['user_role'])&&!empty($_POST['primaryEmail'])&&!empty($_POST['user_role'])) {
            //update both primary email and user role together


            Authentication::authorizeAccessLevel(UserRole::USER_TEACHER);//for updating user role
            $uam->beginTransaction();

            $uam->updateUserPrimaryEmail($user_id,$_POST['primaryEmail']);
            $uam->updateUserRole($user_id,$_POST['user_role']);

            if ($uam->commit()) return r::json(SUCCESS,"Successfully updated User's primary email and user role");
            else throw new HighException("Failed to commit to updating all user info");


        }elseif (isset($_POST['primaryEmail'])&&!empty($_POST['primaryEmail'])) {
            if ($uam->updateUserPrimaryEmail($user_id,$_POST['primaryEmail'])) return r::json(SUCCESS,"Successfully updated user primary email");
        }elseif (isset($_POST['user_role'])&&!empty($_POST['user_role'])) {

            Authentication::authorizeAccessLevel(UserRole::USER_TEACHER);//for updating user role


            if ($uam->updateUserRole($user_id,$_POST['user_role'])) return r::json(SUCCESS,"Successfully updated user role");
        }else throw new PostDataFailure();



    }


}