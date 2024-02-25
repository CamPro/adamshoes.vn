<?php

/**
 * @author Jerry Nguyen
 * @copyright 2017
 */

 if (!defined('IN_ECS'))
{
    die('Hacking attempt');
}

if(!empty($smarty))
 $smarty->assign('month',date('m'));
//include    "helper.php";

?>