<?php

// required password to write database
// leave empty to do not require password at all (not recommended)
// TODO change following option

require_once(__DIR__ . '/../config.php');

// Path to kdbx databases
define("BASE_PATH", __DIR__ . "/../../store/databases");
define("BACK_PATH", __DIR__ . "/../../store/backup");


function require_authorization() {
    if (defined("PASSWORD")){
        $valid_password = PASSWORD;

        $password = !empty($_SERVER["HTTP_AUTHORIZATION"]) ? $_SERVER["HTTP_AUTHORIZATION"] : "";

        if (empty($password) || $password !== $valid_password) {
            header('HTTP/1.0 401 Unauthorized');
            die ("Not authorized PASSWORD");
        }else{
            return true;
        }

    }elseif(defined("OAUTH_SERVER")){
        if (isset($_SESSION['oauth_gitlab']) &&  $_SESSION['oauth_gitlab'] ){
            return true;
        }else{
            die ("Not authorized OAUTH");
            header("Location: auth/gitlab.php");
        }
    }
    die();
}

function clean_filename($file) {
    $file = basename($file);
    $file = preg_replace("/[^\w\s\d\-_~,;\[\]\(\)\.]/", '', $file);
    return $file;
}

function validate_file($path) {
    if (!file_exists($path) || is_dir($path)) {
        header('HTTP/1.0 404 Not Found');
        die("Not Found");
    }
}

if (isset($_GET["path"])) {
    if (REQUIRE_AUTH_READING)
        require_authorization();

    $files = glob(BASE_PATH . "/*.kdbx");
    $list = [];
    foreach($files as $file) {
        $list[] = [
            "name" => basename($file),
            "path" => basename($file),
            "rev" => filemtime($file),
            "dir" => false
        ];
    }
    header("Content-Type: text/json");
    print json_encode($list);
    return;
}

if (isset($_GET["file"])) {
    if (REQUIRE_AUTH_READING)
        require_authorization();

    $file = BASE_PATH . "/" . clean_filename($_GET["file"]);
    validate_file($file);

    header("Content-Type: application/binary");
    clearstatcache();
    header('Last-Modified: '.gmdate('D, d M Y H:i:s', filemtime($file)).' GMT', true, 200);
    echo file_get_contents($file);
    return;
}

if (isset($_GET["stat"])) {
    if (REQUIRE_AUTH_READING)
        require_authorization();

    $file = BASE_PATH . "/" . clean_filename($_GET["stat"]);
    validate_file($file);

    clearstatcache();
    header('Last-Modified: '.gmdate('D, d M Y H:i:s', filemtime($file)).' GMT', true, 200);
    echo "";
    return;
}

if (isset($_GET["save"])) {
    require_authorization();

    $file = BASE_PATH . "/" . clean_filename($_GET["save"]);
    $backfile = BACK_PATH . "/" . clean_filename($_GET["save"]).'~'.date("Y-m-d_H-i-s");
    validate_file($file);

    $rev = $_GET["rev"];

    clearstatcache();
    $current_rev = gmdate('D, d M Y H:i:s', filemtime($file)).' GMT';

    if ($current_rev !== $rev) {
        header('HTTP/1.0 500 Revision mismatch');
        return;
    }

    $contents = file_get_contents("php://input");
    //error_log(disk_free_space(BACK_PATH));
    // TODO autoclean store backups
    
    if (strlen($contents) > 0){
        copy($file, $backfile);
        file_put_contents($file, $contents);

        $log="$file updated by ".(isset($_SESSION['username'])?$_SESSION['username']:'PASSWORD')." at ".date("Y-m-d_H-i-s").PHP_EOL;
        file_put_contents(BACK_PATH . "/saves.log",$log,FILE_APPEND);
        error_log($log);
    }

    clearstatcache();
    header('Last-Modified: '.gmdate('D, d M Y H:i:s', filemtime($file)).' GMT', true, 200);
    echo "";
    return;
}



