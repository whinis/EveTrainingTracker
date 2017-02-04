<?php
/**
 * Created by PhpStorm.
 * User: John
 * Date: 2/3/2017
 * Time: 11:23 AM
 */
include "lib/class_config.php";
include "lib/Curl.php";
include "lib/ccpOAuth.php";
include "lib/class_db.php";
include "lib/class_smarty.php";
$db = new db();
$db->loadByParams(
    config::get("db.host"),
    config::get("db.user"),
    config::get("db.pass"),
    config::get("db.database"),
    config::getOrDefault("db.port",3306)
);

$CURL =new Curl();
$oAuth = new ccpOAuth(
    config::get("SSO.loginURL"),
    config::get("SSO.clientID"),
    config::get("SSO.secret"),
    config::get("SSO.hostURL"),
    $CURL
);
$v=[];
