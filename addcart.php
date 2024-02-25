<?php
define('IN_ECS', true);
require(dirname(__FILE__) . '/includes/init.php');
$smarty->caching = false;

$goods_id = isset($_REQUEST['gid'])  ? intval($_REQUEST['gid']) : 0;

if(isset($_SESSION['cart'][$goods_id]))
{
 $qty = $_SESSION['cart'][$goods_id] + 1;
}
else
{
 $qty=1;
}
$_SESSION['cart'][$goods_id]=$qty;

ecs_header("Location: /gio-hang");
exit;
 ?>