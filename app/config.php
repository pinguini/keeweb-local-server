<?php


if (!isset($_SESSION)){
    session_start();
}

if (false !== getenv('REQUIRE_AUTH_READING'))
    if (in_array(getenv('REQUIRE_AUTH_READING'),["0","false"])){
        define("REQUIRE_AUTH_READING", false);
    }else{
        define("REQUIRE_AUTH_READING", true);
    }
else
    define("REQUIRE_AUTH_READING", true); 


if (false !== getenv('PASSWORD')){
    define("PASSWORD", getenv('PASSWORD'));
}elseif(false !== getenv('OAUTH_CLIENT') && false !== getenv('OAUTH_SECRET') && false !== getenv('OAUTH_SERVER')){
    define("OAUTH_CLIENT", getenv('OAUTH_CLIENT'));
    define("OAUTH_SECRET", getenv('OAUTH_SECRET'));
    define("OAUTH_SERVER", getenv('OAUTH_SERVER'));
}else{
    error_log("Wrong config!!!");
    error_log("neither PASSWORD neither OAUTH_CLIENT OAUTH_SECRET OAUTH_SERVER defined");
    die("incorrect auth");
}
