<?php
define('IN_ECS', true);
require(dirname(__FILE__) . '/includes/init.php');
$jmod = isset($_REQUEST['jmod']) ? trim($_REQUEST['jmod']) : '';

$cache_id = sprintf('%X', crc32($_SESSION['user_rank'] . '-' . $_CFG['lang'] . '-vieclam-'.$_COOKIE['client']));

if (!$smarty->is_cached($display_mode, $cache_id))
{
    assign_template();
    $position = assign_ur_here();


    // info page
    if($jmod == 'quy-trinh'){
        $page_title = 'Quy trình tuyển dụng | bachkhoashop.com';
    }elseif ($jmod == 'ung-tuyen') {
        $page_title = 'Ứng tuyển | bachkhoashop.com';
    }else{
         $page_title = 'Cơ hội việc làm tại Bachkhoashop.com';
    }

    $smarty->assign('jmod',          $jmod );

    $smarty->assign('page_title',       $page_title);
    $smarty->assign('ur_here',          'Việc làm');
    $smarty->assign('keywords',         'việc làm, tuyển dụng, bach khoa shop');
    $smarty->assign('description',      'Thông tin việc làm Bachkhoashop.com');

}
$smarty->display($display_mode, $cache_id);

 ?>