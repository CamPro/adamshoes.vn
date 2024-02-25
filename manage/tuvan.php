<?php


define('IN_ECS', true);

require(dirname(__FILE__) . '/includes/init.php');
/* 权限判断 */
admin_priv('feedback_priv');
$exc = new exchange($ecs->table("tuvan"), $db, 'tuvan_id', 'cname');

/*------------------------------------------------------ */
//-- 列出所有留言
/*------------------------------------------------------ */
if ($_REQUEST['act']=='list')
{
    assign_query_info();
    $msg_list = msg_list();

    $smarty->assign('msg_list',     $msg_list['msg_list']);
    $smarty->assign('filter',       $msg_list['filter']);
    $smarty->assign('record_count', $msg_list['record_count']);
    $smarty->assign('page_count',   $msg_list['page_count']);
    $smarty->assign('full_page',    1);
    $smarty->assign('sort_msg_id', '<img src="images/sort_desc.gif">');

    $smarty->assign('ur_here',      $_LANG['11_tuvan']);
    $smarty->assign('full_page',    1);
    $smarty->display('tuvan_list.htm');
}

/*------------------------------------------------------ */
//-- ajax显示留言列表
/*------------------------------------------------------ */
elseif ($_REQUEST['act'] == 'query')
{
    $msg_list = msg_list();

    $smarty->assign('msg_list',     $msg_list['msg_list']);
    $smarty->assign('filter',       $msg_list['filter']);
    $smarty->assign('record_count', $msg_list['record_count']);
    $smarty->assign('page_count',   $msg_list['page_count']);

    $sort_flag  = sort_flag($msg_list['filter']);
    $smarty->assign($sort_flag['tag'], $sort_flag['img']);

    make_json_result($smarty->fetch('tuvan_list.htm'), '', array('filter' => $msg_list['filter'], 'page_count' => $msg_list['page_count']));
}
/*------------------------------------------------------ */
//-- ajax 删除留言
/*------------------------------------------------------ */
elseif ($_REQUEST['act'] == 'remove')
{
    $tuvan_id = intval($_REQUEST['id']);

    /* 检查权限 */
    check_authz_json('feedback_priv');

    $cname = $exc->get_name($tuvan_id);
    if ($exc->drop($tuvan_id))
    {

        $sql = "DELETE FROM " . $ecs->table('tuvan') . " WHERE tuvan_id = '$tuvan_id' LIMIT 1";
        $db->query($sql, 'SILENT');

        admin_log(addslashes($cname), 'remove', 'message');
        $url = 'tuvan.php?act=query&' . str_replace('act=remove', '', $_SERVER['QUERY_STRING']);
        ecs_header("Location: $url\n");
        exit;
    }
    else
    {
        make_json_error($GLOBALS['db']->error());
    }
}

/*------------------------------------------------------ */
//-- 批量操作删除、允许显示、禁止显示用户评论
/*------------------------------------------------------ */
if ($_REQUEST['act'] == 'batch')
{
    admin_priv('feedback_priv');
    $action = isset($_POST['sel_action']) ? trim($_POST['sel_action']) : 'def';

    if (isset($_POST['checkboxes']))
    {
        switch ($action)
        {
           case 'remove':
                $db->query("DELETE FROM " . $ecs->table('tuvan') . " WHERE " . db_create_in($_POST['checkboxes'], 'tuvan_id'));
                break;
           default :
               break;
        }

        clear_cache_files();
        $action = ($action == 'remove') ? 'remove' : 'edit';
        admin_log('', $action, 'adminlog');

        $link[] = array('text' => $_LANG['back_list'], 'href' => 'tuvan.php?act=list');
        sys_msg(sprintf('Xóa thành công', count($_POST['checkboxes'])), 0, $link);
    }
    else
    {
        /* 提示信息 */
        $link[] = array('text' => $_LANG['back_list'], 'href' => 'tuvan.php?act=list');
        sys_msg('Vui lòng chọn ID', 0, $link);
    }
}


/*------------------------------------------------------ */
//-- 回复留言
/*------------------------------------------------------ */
elseif ($_REQUEST['act']=='view')
{
    $tuvan_id   = isset($_GET['id']) ? intval($_GET['id']) : 0;
    if($tuvan_id == 0){
        sys_msg('ID không tồn tại', 1, array(), false);
        exit();
    }
    $db->query("UPDATE ".$ecs->table('tuvan')." SET status = 2 WHERE tuvan_id = $tuvan_id");

    $msg_list = msg_list();
    $smarty->assign('msg',   $msg_list['msg_list']);
    $smarty->assign('ur_here',     $_LANG['reply']);
    $smarty->assign('action_link', array('text' => $_LANG['11_tuvan'], 'href'=>'tuvan.php?act=list'));

    assign_query_info();
    $smarty->display('tuvan_info.htm');
}
elseif ($_REQUEST['act']=='action')
{
    if(isset($_POST['Submit'])){
        $tuvan_id = intval($_POST['tuvan_id']);
        $note = isset($_POST['msg_content']) ? $_POST['msg_content'] : '';
        if(!empty($note)){
            $db->query("UPDATE ".$ecs->table('tuvan')." SET note = '$note' WHERE tuvan_id = $tuvan_id");
        }
        clear_cache_files();
        $link[] = array('text' => $_LANG['back_list'], 'href' => 'tuvan.php?act=list');
        sys_msg('Cập nhật thành công !', 0, $link);
    }

}
elseif ($_REQUEST['act'] == 'export')
{
    $msg_list = msg_list();
    //var_dump($msg_list['msg_list']);
    export_csv($msg_list['msg_list'], 'tuvantg_list.csv');

}


/**
 *
 *
 * @access  public
 * @param
 *
 * @return void
 */
function msg_list()
{
    $filter['tuvan_id']   = isset($_GET['id']) ? intval($_GET['id']) : 0;
    $filter['sort_by']    = empty($_REQUEST['sort_by']) ? 't.add_time' : trim($_REQUEST['sort_by']);
    $filter['sort_order'] = empty($_REQUEST['sort_order']) ? 'DESC' : trim($_REQUEST['sort_order']);

    $filter['start_time'] = empty($_REQUEST['start_date']) ? '' : $_REQUEST['start_date'].' 00:00:00';
    $filter['end_time'] = empty($_REQUEST['end_date']) ? '' : $_REQUEST['end_date'].' 23:59:59';

    $filter['status'] = isset($_REQUEST['status']) ? intval($_REQUEST['status']) : 0;


    $where = '1';
    if ($filter['tuvan_id'] != 0)
    {
        $where .= " AND t.tuvan_id = '$filter[tuvan_id]'";
    }

    if ($filter['status'] != 0)
    {
        $where .= " AND t.status  = '$filter[status]'";
    }
    if ($filter['start_time'])
    {
        $filter['start_time'] = local_strtotime($filter['start_time']);
        $where .= " AND t.add_time >= '$filter[start_time]'";
    }
    if ($filter['end_time'])
    {
        $filter['end_time'] = local_strtotime($filter['end_time']);
        $where .= " AND t.add_time <= '$filter[end_time]'";
    }

    //
    $GLOBALS['smarty']->assign('status', $_REQUEST['status']);
    $GLOBALS['smarty']->assign('end_date', $_REQUEST['end_date']);
    $GLOBALS['smarty']->assign('start_date', $_REQUEST['start_date']);

    $sql = "SELECT count(*) FROM " .$GLOBALS['ecs']->table('tuvan'). " AS t" .
           " WHERE " . $where;
    $filter['record_count'] = $GLOBALS['db']->getOne($sql);

    $filter = page_and_size($filter);

    $sql = "SELECT t.tuvan_id, t.cname, t.cmobile, r.region_name, t.product, t.add_time, t.status,  t.note " .
            "FROM " . $GLOBALS['ecs']->table('tuvan') . " AS t ".
            "LEFT JOIN " . $GLOBALS['ecs']->table('region') . " AS r ON r.region_id=t.region_id ".
            "WHERE $where " .
            "ORDER by $filter[sort_by] $filter[sort_order] ".
            "LIMIT " . $filter['start'] . ', ' . $filter['page_size'];

    $msg_list = $GLOBALS['db']->getAll($sql);
    foreach ($msg_list AS $key => $value)
    {
        $msg_list[$key]['cname'] = $value['cname'];
        $msg_list[$key]['cmobile'] = "'".$value['cmobile'];
        $msg_list[$key]['region_name'] = $value['region_name'];
        $msg_list[$key]['product'] = $value['product'];
        $msg_list[$key]['note'] = $value['note'];
        $msg_list[$key]['status'] = $value['status'] == 1 ? 'Chưa xem' : 'Đã xem';
        $msg_list[$key]['add_time'] = local_date($GLOBALS['_CFG']['time_format'], $value['add_time']);
    }
    $arr = array('msg_list' => $msg_list, 'filter' => $filter, 'page_count' => $filter['page_count'], 'record_count' => $filter['record_count']);

    return $arr;
}


?>