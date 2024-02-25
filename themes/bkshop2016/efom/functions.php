<?php

/**
 * @author Jerry Nguyen
 * @copyright 2017
 */
//redirectDomain();
function redirectDomain()
{
    $protocol = (@$_SERVER["HTTPS"] == "on") ? "https://" : "http://";

    if (substr($_SERVER['HTTP_HOST'], 0, 4) !== 'www.') {
        header('Location: '.$protocol.'www.'.$_SERVER['HTTP_HOST'].''.$_SERVER['REQUEST_URI']);
        exit;
    }
}
function isHttp()
{

}

?>