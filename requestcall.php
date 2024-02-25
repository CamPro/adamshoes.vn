<?php

/**
 * @author Jerry Nguyen
 * @copyright 2017
 */
define('IN_ECS', true);

require(dirname(__FILE__) . '/includes/init.php');

if(isset($_POST['tel']))
{ 
    
    $tel=  addslashes( $_POST['tel']);
     
    $ref=( $_POST['url']);
    $date=gmdate('Y-m-d H:i:s',time()+7*3600);
    $ip=$_SERVER["REMOTE_ADDR"];
   
    if(reismobile())$device='mobile';else $device='pc';
    $sql = "INSERT INTO ".$ecs->table('requestcall')."(tel, product,device, created, ip) ".

            "VALUES ('$tel', '$ref','$device', '$date','$ip')";
 
    $db->query($sql);
}
function reismobile()
{
    $ua = strtolower($_SERVER['HTTP_USER_AGENT']);
    $uachar = "/(nokia|asus|sony|ericsson|microsoft|samsung|oppo|lg|philips|panasonic|alcatel|lenovo|iphone|htc|mobile|android)/i";
    
    if((!isset($_COOKIE['client']) && $client == 'mobile') || preg_match($uachar, $ua)) return true;return false;
}
 
?>