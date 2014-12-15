<?php
/**
 * Created by PhpStorm.
 * User: Admin
 * Date: 17/07/14
 * Time: 03:57
 */

class Header {
    private static $content_type="text/html";

    public static function setContentType($content_type) {
        self::$content_type=$content_type;

        header("Content-Type: ".$content_type);
    }
    public static function getContentType() {
        return self::$content_type;
    }
} 