<?php

define('IN_ECS', true);
define('INIT_NO_USERS', true);
define('INIT_NO_SMARTY', true);

require(dirname(__FILE__) . '/includes/init.php');
require(ROOT_PATH . 'includes/cls_json.php');

header('Content-type: text/html; charset=' . EC_CHARSET);

$region_id   = !empty($_REQUEST['region_id'])   ? intval($_REQUEST['region_id'])   : 0;
$city_id   = !empty($_REQUEST['city_id'])   ? intval($_REQUEST['city_id'])   : 0;
if($city_id > 0){
    $cityWhere = "AND city_id = '$city_id'";
}
$sql = "SELECT agency_id, agency_name, agency_desc FROM " . $ecs->table('agency') . " WHERE region_id = '$region_id ' $cityWhere";
$agency = $db->getAll($sql);

$json = new JSON;
echo $json->encode($agency);

?>