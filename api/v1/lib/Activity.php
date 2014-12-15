<?php

/* TODO: replace die methods with exceptions and handlers */

class Activity {

    public static function add($activity_type='general',$activity_description) {//true=successfully logged -- false=failed to log
        if (defined('ACTIVITY_LOG')) {
            if (!file_exists(ACTIVITY_LOG)) die('Failed to log activity: Activity Log file not found!');


            $prev_activity_log=file_get_contents(ACTIVITY_LOG);



            $log_text="\n- ".$activity_type.": ".$activity_description. " # ". DATESTAMP;
            if (file_put_contents(ACTIVITY_LOG,$prev_activity_log.$log_text)) {
                return true;
            }else return false;///TODO: Throw and handle an exception


        }else die ("Failed to log activity: Directory of Activity Log not specified!");



    }


} 