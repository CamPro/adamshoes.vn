<?php
define('IN_ECS', true);
require(dirname(__FILE__) . '/includes/init.php');

if ($_REQUEST['act'] == 'sync_stock')
{
	admin_log('', 'update', 'map_stock');
	$smarty->assign('full_page',   1);
	$smarty->display('map_stock.htm');
}
?>