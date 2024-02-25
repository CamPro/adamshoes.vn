<?php
define('IN_ECS', true);
require(dirname(__FILE__) . '/includes/init.php');

if ($_REQUEST['act'] == 'list')
{
	admin_priv('installment_manage'); //phanquyen
	#code here ...

	//admin_log('', 'list', 'installment');
	$list = get_tragop_list();
    $smarty->assign('full_page',   1);
    $smarty->assign('tragop',       $list['item']);
    $smarty->assign('filter',       $list['filter']);
    $smarty->assign('record_count', $list['record_count']);
    $smarty->assign('page_count',   $list['page_count']);

    $sort_flag  = sort_flag($list['filter']);
    $smarty->assign($sort_flag['tag'], $sort_flag['img']);

    assign_query_info();
	$smarty->assign('action_link', array('text' => $_LANG['add_installment'], 'href' => 'installment.php?act=add'));
    $smarty->assign('ur_here',      $_LANG['11_installment']);
	$smarty->display('installment_list.htm');

}elseif ($_REQUEST['act'] == 'add' || $_REQUEST['act'] == 'edit') {
	admin_priv('installment_manage'); //phanquyen
	#code here ...
	$isadd      = $_REQUEST['act'];
	$tragop_id  = empty($_REQUEST['tragop_id']) ? 0 : intval($_REQUEST['tragop_id']);


	$smarty->assign('cat_list', cat_list());
    $smarty->assign('brand_list', get_brand_list());

    assign_query_info();

	//admin_log('', 'list', 'installment');
        $smarty->assign('cfg_lang',   $_CFG['lang']);
	$smarty->assign('action_link', array('text' => $_LANG['11_installment'], 'href' => 'installment.php?act=list'));
	$smarty->assign('ur_here',      $_LANG['add_installment']);

	if($isadd == 'edit'){
		$smarty->assign('act',   "update");
	}else{
		$smarty->assign('act',   "insert");
	}

	$smarty->display('installment_add.htm');

}elseif ($_REQUEST['act'] == 'update' || $_REQUEST['act'] == 'insert') {
	admin_priv('installment_manage'); //phanquyen
	# code...
	$isupdate       = $_REQUEST['act'];
	$tragopname 	= !empty($_POST['tragop_name']) ? $_POST['tragop_name'] : '';
	$nhataichinh	= !empty($_POST['nhataichinh']) ? $_POST['nhataichinh'] : 'ACS';
	$laisuat 		= !empty($_POST['laisuat']) ? $_POST['laisuat'] : 0;
	$is_money		= !empty($_POST['is_money']) ? floatval($_POST['is_money']) : 0;
	$tratruoc		= !empty($_POST['tratruoc']) ? $_POST['tratruoc'] : 0;
	$kyhanvay		= !empty($_POST['kyhanvay']) ? floatval($_POST['kyhanvay']) : 6;
	$thutuc 		= !empty($_POST['thutuc']) ? $_POST['thutuc'] : 0;
    $thutuc_info    = !empty($_POST['thutuc_info']) ? $_POST['thutuc_info'] : '';
	$endday 		= !empty($_POST['endday']) ? local_strtotime($_POST['endday']) : 0;
	$goods_ids		= !empty($_POST['goods_id']) ? $_POST['goods_id'] : 0;

	$goods_id =  explode(',', $goods_ids);

	if($isupdate == 'update'){
		$sql = "UPDATE " .$ecs->table('tragop'). " SET goods_id = '$goods_ids', tragop_name = '$tragopname', nhataichinh = '$nhataichinh', tratruoc = '$tratruoc', laisuat = '$laisuat', kyhanvay = '$kyhanvay', is_money='$is_money', thutuc='$thutuc', thutuc_info = '$thutuc_info', endday='$endday' ".
				" WHERE tragop_id = '$tragop_id' LIMIT 1";
		$db->query($sql);
	}else{

		foreach ($goods_id as $gid) {
		$sql = "INSERT INTO " .$ecs->table('tragop'). " (goods_id, tragop_name, nhataichinh, tratruoc, laisuat, kyhanvay, is_money, thutuc, thutuc_info, endday)".
                " VALUES ('$gid', '$tragopname', '$nhataichinh', '$tratruoc', '$laisuat', '$kyhanvay', '$is_money', '$thutuc', '$thutuc_info', '$endday')";
        $db->query($sql);
        //set icon Tragop 0ls chi khi ls = 0
        /*if($laisuat == 0){
            $sql = "UPDATE " .$ecs->table('goods'). " SET is_os = 3 WHERE goods_id = '$gid' LIMIT 1";
            $db->query($sql);
        }*/




        }
	}

    $link[] = array('href' => 'installment.php?act=list', 'text' => $_LANG['list_installment']);
    sys_msg($_LANG['add_success'], 0, $link);

}elseif ($_REQUEST['act'] == 'get_goods'){
    $filter = &new stdclass;

    $filter->cat_id = intval($_GET['cat_id']);
    $filter->brand_id = intval($_GET['brand_id']);
    $filter->real_goods = -1;
    $arr = get_goods_list($filter);

    make_json_result($arr);
}elseif ($_REQUEST['act'] == 'delete'){
   admin_priv('installment_manage'); //phanquyen

    $sql = "DELETE FROM " . $ecs->table('tragop') . " WHERE ";

    if (!empty($_POST['checkboxes']))
    {
        $sql .= db_create_in($_POST['checkboxes'], 'tragop_id');
    }

    $db->query($sql);
    clear_cache_files();
    if (!empty($_REQUEST['is_ajax']))
    {
        $url = 'installment.php?act=query&' . str_replace('act=delete', '', $_SERVER['QUERY_STRING']);
        ecs_header("Location: $url\n");
        exit;
    }
    $links[] = array('href' => 'installment.php?act=list', 'text' =>  $_LANG['back_list']);
    sys_msg($_LANG['succed'], 0, $links);


}elseif ($_REQUEST["act"] == "query"){

    $tragop_list = get_tragop_list();
    $smarty->assign('tragop',   $tragop_list['item']);
    $smarty->assign('filter',       $tragop_list['filter']);
    $smarty->assign('record_count', $tragop_list['record_count']);
    $smarty->assign('page_count',   $tragop_list['page_count']);
    $smarty->assign('use_storage',  empty($_CFG['use_storage']) ? 0 : 1);

    $sort_flag  = sort_flag($tragop_list['filter']);
    $smarty->assign($sort_flag['tag'], $sort_flag['img']);

    $tpl = 'installment_list.htm';
    make_json_result($smarty->fetch($tpl), '',array('filter' => $tragop_list['filter'], 'page_count' => $tragop_list['page_count']));
}


function get_tragop_list()
{
    $result = get_filter();
    if ($result === false)
    {
        /* 查询条件 */
        $filter['sort_by']    = empty($_REQUEST['sort_by']) ? 'tragop_id' : trim($_REQUEST['sort_by']);
        $filter['sort_order'] = empty($_REQUEST['sort_order']) ? 'DESC' : trim($_REQUEST['sort_order']);

        $sql = "SELECT COUNT(*) FROM ".$GLOBALS['ecs']->table('tragop');
        $filter['record_count'] = $GLOBALS['db']->getOne($sql);

        /* 分页大小 */
        $filter = page_and_size($filter);

        $sql = "SELECT t.tragop_id, g.goods_name, t.tragop_name, t.nhataichinh, t.endday FROM " .$GLOBALS['ecs']->table('tragop'). " AS t, " .$GLOBALS['ecs']->table('goods'). " AS g WHERE t.goods_id = g.goods_id ORDER BY $filter[sort_by] $filter[sort_order]";

        set_filter($filter, $sql);
    }
    else
    {
        $sql    = $result['sql'];
        $filter = $result['filter'];
    }

    $query = $GLOBALS['db']->selectLimit($sql, $filter['page_size'], $filter['start']);

    $res = array();

    while($tragop = $GLOBALS['db']->fetch_array($query)){
        $tragop['endday']   = local_date('d-m-Y',$tragop['endday']);
        $res[] = $tragop;
    }

    $arr = array('item' => $res, 'filter' => $filter, 'page_count' => $filter['page_count'], 'record_count' => $filter['record_count']);

    return $arr;
}

?>