<?php
 
define('IN_ECS', true);
define('INIT_NO_USERS', true);
define('INIT_NO_SMARTY', true);

require(dirname(__FILE__) . '/includes/init.php');
require(ROOT_PATH . 'includes/cls_json.php');

header('Content-type: text/html; charset=' . EC_CHARSET);

$type   = !empty($_REQUEST['type'])   ? intval($_REQUEST['type'])   : 0;
$parent = !empty($_REQUEST['parent']) ? intval($_REQUEST['parent']) : 0;
if(isset($_REQUEST['showroom']))
{
     $sql = 'SELECT r.region_id, r.region_name FROM ' . $GLOBALS['ecs']->table('region') . 
            
            " r RIGHT JOIN ".$GLOBALS['ecs']->table('agency') ." a on a.city_id =  r.region_id
            
            WHERE r.region_type = '".$type."' AND parent_id = '".$parent."' group by a.city_id";

 

        $arr['regions'] = $GLOBALS['db']->GetAll($sql);
}
else
    $arr['regions'] = get_regions($type, $parent);
 
$arr['type']    = $type;
$arr['target']  = !empty($_REQUEST['target']) ? stripslashes(trim($_REQUEST['target'])) : '';
$arr['target']  = htmlspecialchars($arr['target']);

$json = new JSON;
echo $json->encode($arr);

?>