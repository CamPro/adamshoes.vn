<?php

/**
 * @author Jerry Nguyen
 * @copyright 2017
 */
 if (!defined('IN_ECS'))
{
    die('Hacking attempt');
}
/*redirectCategory();
redirectAricle($db,$ecs);*/
function redirectMainDomain()
{
    $protocol = (@$_SERVER["HTTPS"] == "on") ? "https://" : "http://";

    if (substr($_SERVER['HTTP_HOST'], 0, 4) !== 'www.') {
        header('Location: '.$protocol.'www.'.$_SERVER['HTTP_HOST'].''.$_SERVER['REQUEST_URI']);
        exit;
    }
}
function redirectCategory()
{
     if(  !isset(  $_GET['caturl']))    return ;

     if(   strpos( $_SERVER['REQUEST_URI'] ,'caturl' ))
     {

        $protocol = (@$_SERVER["HTTPS"] == "on") ? "https://" : "http://";'';
        $url = $protocol.$_SERVER['HTTP_HOST'].'/'.$_GET['caturl'].'.html';

       eredirect($url);
     }



}
function redirectAricle()
{
   if(   strpos( $_SERVER['REQUEST_URI'] ,'article.php' ))
   {


     #$article_id = $db->getOne("SELECT article_id,url_seo FROM " . $ecs->table('article') . " where article_id = '".intval($_REQUEST['id'])."' ");
    eredirect( build_uri('article', array('aid'=>$_REQUEST['id'])));
        exit();
   }

}
function eredirect($link='')
{
    if($link)
    {


        header("HTTP/1.1 301 Moved Permanently");
        header("Location: ".$link);
    }
}

?>