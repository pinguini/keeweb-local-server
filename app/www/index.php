<?php
require_once('../vendor/autoload.php');
require_once(__DIR__ . '/../config.php');




if ((isset($_SESSION['oauth_gitlab']) and $_SESSION['oauth_gitlab'] ) or defined("PASSWORD")){
    echo file_get_contents('index.html');
}else{
    $link = (   (  (isset($_SERVER['HTTPS'])         && $_SERVER['HTTPS'] === 'on' ) 
                || (isset($_SERVER['HTTP_X_SCHEME']) && $_SERVER['HTTP_X_SCHEME'] == 'https' )
                || (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] == 'https' )
                )
                ? "https" : "http") . "://" . $_SERVER['HTTP_HOST'] . 
                $_SERVER['REQUEST_URI'];
  
    header('Location: auth/gitlab.php?'.http_build_query(["return"=>$link]));
    echo '<a href="auth/gitlab.php">auth</a>';
}

?>
