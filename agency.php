<?php
define('IN_ECS', true);
require(dirname(__FILE__) . '/includes/init.php');
$cache_id = sprintf('%X', crc32($_SESSION['user_rank'] . '-' . $_CFG['lang']));

if (!$smarty->is_cached($display_mode, $cache_id))
{
	assign_template();
	$position = assign_ur_here(0, 'Hệ thống chi nhánh');
	$smarty->assign('page_title',       $position['title']);   
	$smarty->assign('ur_here',          $position['ur_here']);
	$smarty->assign('keywords',     'Hệ thống chi nhánh, he thong, chi nhanh, bkitmart');
	$smarty->assign('description', 'Hệ thống chi nhánh Bách Khoa IT Mart');
	$smarty->assign('helps',            get_shop_help());
}
$smarty->display($display_mode, $cache_id);

?>