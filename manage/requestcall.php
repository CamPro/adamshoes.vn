<?php

define('IN_ECS', true);

require(dirname(__FILE__) . '/includes/init.php');

#require_once(ROOT_PATH . "includes/fckeditor/fckeditor.php");

/* act操作项的初始化 */
 
if (empty($_REQUEST['act']))

{

    $_REQUEST['act'] = 'list';

}

else

{

    $_REQUEST['act'] = trim($_REQUEST['act']);

}

/*------------------------------------------------------ */

//-- 获取没有回复的评论列表

/*------------------------------------------------------ */

if ($_REQUEST['act'] == 'list')

{

    /* 检查权限 */

    admin_priv('requestcall_priv');

    $smarty->assign('ur_here',      $_LANG['05_requestcall']);

    $smarty->assign('full_page',    1);

    $list = get_requestcall_list();

    $created = isset($_POST['created']) ? $_POST['created'] : '';

    $device = isset($_POST['device']) ? $_POST['device'] : '';
    
    $status = isset($_POST['status']) ? $_POST['status'] : '';
    
    $smarty->assign('created',  $created );

    $smarty->assign('device', $device);
    
    $smarty->assign('status', $status);
 
    $smarty->assign('requestcall_list', $list['item']);

    $smarty->assign('filter',       $list['filter']);

    $smarty->assign('record_count', $list['record_count']);

    $smarty->assign('page_count',   $list['page_count']);
    
    $smarty->assign('list_status',   $list['filter']['list_status']);
     
    $sort_flag  = sort_flag($list['filter']);

    $smarty->assign($sort_flag['tag'], $sort_flag['img']);

    assign_query_info();



    $page = 'requestcall_list.htm';

    $smarty->display($page);

}

if ($_GET['act'] == 'updateStatus')
{
    $id=$_GET['id'];
    $status == $_POST['status'][$id];
    
    $sql = "UPDATE " .$ecs->table('requestcall'). " SET status = '".$_POST['status'][$id]."' WHERE id = '$id'";
    
;    $db->query($sql);
    if(isset($_SERVER["HTTP_REFERER"]))
    { 
        ecs_header("Location:".$_SERVER["HTTP_REFERER"]);
    }
        
    else
       ecs_header("Location:requestcall.php?act=list");     
    exit();
}
if ($_REQUEST['act'] == 'export'){

        $start_date = empty($_REQUEST['start_date']) ? '' : local_strtotime($_REQUEST['start_date']);

        $end_date = empty($_REQUEST['end_date']) ? '' : local_strtotime($_REQUEST['end_date']);

        if(empty($start_date) || empty($end_date)){

            sys_msg('Vui lòng chọn lại thời gian', 1, array(), false);

        }

        if(!empty($start_date) && !empty($end_date)){

            $where = " AND add_time >= '$start_date' AND add_time <= '$end_date'";

        }

        $arr = array();

        $sql  = "SELECT id, user_name,telephone, content, add_time FROM " .$ecs->table('requestcall'). " WHERE parent_id = 0 $where ";

        $res  = $db->getAll($sql);

        foreach ($res as $row) {

            //Get Admin Answer for each ask

            $sql  = "SELECT user_name, content, add_time FROM " .$ecs->table('requestcall'). " WHERE parent_id = $row[id] ";

            $answer  = $db->getRow($sql);

            $answer['add_time'] = local_date($GLOBALS['_CFG']['time_format'], $answer['add_time']);

            $row['user_name'] = $row['user_name'];
            
            $row['telephone'] = $row['telephone'];

            $row['content']   =  $row['content'];

            $row['add_time']  = local_date($GLOBALS['_CFG']['time_format'], $row['add_time']);

            $row['answer'] = $answer['user_name'];

            $str = html_entity_decode($answer['content'], ENT_QUOTES, 'UTF-8');

            $row['content_answer'] = strip_tags($str);

            $row['time_answer'] = $answer['add_time'];

            $arr[] = $row;

        }

       // var_dump($arr);

        export_csv($arr, 'requestcall_list.csv');

}

/*------------------------------------------------------ */

//-- 翻页、搜索、排序

/*------------------------------------------------------ */


if ($_REQUEST['act'] == 'query')

{

    $list = get_requestcall_list();

    $smarty->assign('requestcall_list', $list['item']);

    $smarty->assign('filter',       $list['filter']);

    $smarty->assign('record_count', $list['record_count']);

    $smarty->assign('page_count',   $list['page_count']);

    $sort_flag  = sort_flag($list['filter']);

    $smarty->assign($sort_flag['tag'], $sort_flag['img']);

    make_json_result($smarty->fetch('requestcall_list.htm'), '',

        array('filter' => $list['filter'], 'page_count' => $list['page_count']));

}

/*------------------------------------------------------ */

//-- 回复用户评论(同时查看评论详情)

/*------------------------------------------------------ */

 

/*------------------------------------------------------ */

//-- 处理 回复用户评论

/*------------------------------------------------------ */

if ($_REQUEST['act']=='action')

{

    admin_priv('requestcall_priv');

    /* 获取IP地址 */

    $ip     = real_ip();

    /* 获得评论是否有回复 */

    $sql = "SELECT id, content, parent_id FROM ".$ecs->table('requestcall').

           " WHERE parent_id = '$_REQUEST[id]'";

    $reply_info = $db->getRow($sql);

    if (!empty($reply_info['content']))

    {

        /* 更新回复的内容 */

        $sql = "UPDATE ".$ecs->table('requestcall')." SET ".

               "email     = '$_POST[email]', ".

               "user_name = '$_POST[user_name]', ".

               "content   = '$_POST[content]', ".

               "add_time  =  '" . gmtime() . "', ".

               "ip_address= '$ip', ".

               "status    = 0".

               " WHERE id = '".$reply_info['id']."'";

    }

    else

    {

        /* 插入回复的评论内容 */

        $sql = "INSERT INTO ".$ecs->table('requestcall')." (requestcall_type, id_value, email, user_name , ".

                    "content, add_time, ip_address, status, parent_id) ".

               "VALUES('$_POST[requestcall_type]', '$_POST[id_value]','$_POST[email]', " .

                    "'$_POST[user_name]','$_POST[content]','" . gmtime() . "', '$ip', '0', '$_POST[id]')";

    }

    $db->query($sql);

    /* 更新当前的评论状态为已回复并且可以显示此条评论 */

    $sql = "UPDATE " .$ecs->table('requestcall'). " SET status = 1 WHERE id = '$_POST[id]'";

    $db->query($sql);

    /* 邮件通知处理流程 */

    if (!empty($_POST['send_email_notice']) or isset($_POST['remail']))

    {

        //获取邮件中的必要内容

        $sql = 'SELECT user_name, email, content ' .

               'FROM ' .$ecs->table('requestcall') .

               " WHERE id ='$_REQUEST[id]'";

        $requestcall_info = $db->getRow($sql);

        /* 设置留言回复模板所需要的内容信息 */

        $template    = get_mail_template('rerequestcall');

        $smarty->assign('user_name',   $requestcall_info['user_name']);

        $smarty->assign('rerequestcall', $_POST['content']);

        $smarty->assign('requestcall', $requestcall_info['content']);

        $smarty->assign('shop_name',   "<a href='".$ecs->url()."'>" . $_CFG['shop_name'] . '</a>');

        $smarty->assign('send_date',   date('Y-m-d'));

        $content = $smarty->fetch('str:' . $template['template_content']);

        /* 发送邮件 */

        if (send_mail($requestcall_info['user_name'], $requestcall_info['email'], $template['template_subject'], $content, $template['is_html']))

        {

            $send_ok = 0;

        }

        else

        {

            $send_ok = 1;

        }

    }

    /* 清除缓存 */

    clear_cache_files();

    /* 记录管理员操作 */

    admin_log(addslashes($_LANG['reply']), 'edit', 'users_requestcall');

    ecs_header("Location: requestcall.php?act=reply&id=$_REQUEST[id]&send_ok=$send_ok\n");

    exit;

}

/*------------------------------------------------------ */

//-- 更新评论的状态为显示或者禁止

/*------------------------------------------------------ */

if ($_REQUEST['act'] == 'check')

{

    if ($_REQUEST['check'] == 'allow')

    {

        /* 允许评论显示 */

        $sql = "UPDATE " .$ecs->table('requestcall'). " SET status = 1 WHERE id = '$_REQUEST[id]'";

        $db->query($sql);

        //add_feed($_REQUEST['id'], requestcall_GOODS);

        /* 清除缓存 */

        clear_cache_files();

        ecs_header("Location: requestcall.php?act=reply&id=$_REQUEST[id]\n");

        exit;

    }

    else

    {

        /* 禁止评论显示 */

        $sql = "UPDATE " .$ecs->table('requestcall'). " SET status = 0 WHERE id = '$_REQUEST[id]'";

        $db->query($sql);

        /* 清除缓存 */

        clear_cache_files();

        ecs_header("Location: requestcall.php?act=reply&id=$_REQUEST[id]\n");

        exit;

    }

}

/*------------------------------------------------------ */

//-- 删除某一条评论

/*------------------------------------------------------ */

elseif ($_REQUEST['act'] == 'remove')

{

    check_authz_json('requestcall_priv');

    $id = intval($_GET['id']);

    $sql = "DELETE FROM " .$ecs->table('requestcall'). " WHERE id = '$id'";

    $res = $db->query($sql);

     
    admin_log('', 'remove', 'requestcall');

   $url = 'requestcall.php?act=query';

    ecs_header("Location: $url\n");

    exit;

}

/*------------------------------------------------------ */

//-- 批量删除用户评论

/*------------------------------------------------------ */

if ($_REQUEST['act'] == 'batch')

{

    admin_priv('requestcall_priv');

    $action = isset($_POST['sel_action']) ? trim($_POST['sel_action']) : 'deny';

    if (isset($_POST['checkboxes']))

    {

        switch ($action)

        {

            case 'remove':

                $db->query("DELETE FROM " . $ecs->table('requestcall') . " WHERE " . db_create_in($_POST['checkboxes'], 'id'));

                $db->query("DELETE FROM " . $ecs->table('requestcall') . " WHERE " . db_create_in($_POST['checkboxes'], 'parent_id'));

                break;

           case 'allow' :

               $db->query("UPDATE " . $ecs->table('requestcall') . " SET status = 1  WHERE " . db_create_in($_POST['checkboxes'], 'id'));

               break;

           case 'deny' :

               $db->query("UPDATE " . $ecs->table('requestcall') . " SET status = 0  WHERE " . db_create_in($_POST['checkboxes'], 'id'));

               break;

           default :

               break;

        }

        clear_cache_files();

        $action = ($action == 'remove') ? 'remove' : 'edit';

        admin_log('', $action, 'adminlog');

        $link[] = array('text' => $_LANG['back_list'], 'href' => 'requestcall.php?act=list');

        sys_msg(sprintf($_LANG['batch_drop_success'], count($_POST['checkboxes'])), 0, $link);

    }

    else

    {

        /* 提示信息 */

        $link[] = array('text' => $_LANG['back_list'], 'href' => 'requestcall.php?act=list');

        sys_msg($_LANG['no_select_requestcall'], 0, $link);

    }

}

/**

 * 获取评论列表

 * @access  public

 * @return  array

 */

function get_requestcall_list()

{

    $filter['status'] = empty($_REQUEST['status']) ? '0' : $_REQUEST['status'];

    $filter['device'] = empty($_REQUEST['device']) ? '' : $_REQUEST['device'];
    
    $filter['created'] = empty($_REQUEST['created']) ? '' : $_REQUEST['created'];

    $filter['sort_by']      = empty($_REQUEST['sort_by']) ? 'created' : trim($_REQUEST['sort_by']);

    $filter['sort_order']   = empty($_REQUEST['sort_order']) ? 'DESC' : trim($_REQUEST['sort_order']);

    if ($filter['created'])

    {

        $filter['created'] = local_strtotime($filter['created']);

        $where .= " AND add_time >= '$filter[created]'";

    }

    if ($filter['status'])

    {

      

        $where .= " AND status = '$filter[status]'";

    }
    
    if ($filter['device'])

    {

      

        $where .= " AND device = '$filter[device]'";

    }

    $sql = "SELECT count(*) FROM " .$GLOBALS['ecs']->table('requestcall'). " WHERE 1=1  $where";

    $filter['record_count'] = $GLOBALS['db']->getOne($sql);

    /* 分页大小 */

    $filter = page_and_size($filter);

    /* 获取评论数据 */

    $arr = array();

    $sql  = "SELECT * FROM " .$GLOBALS['ecs']->table('requestcall'). " WHERE 1=1  $where " .

            " ORDER BY $filter[sort_by] $filter[sort_order] ".

            " LIMIT ". $filter['start'] .", $filter[page_size]";
   
    $res  = $GLOBALS['db']->query($sql);
    $list_status=array(1=>'Pending',2=>"Đã gọi",3=>"Không gọi được");
    while ($row = $GLOBALS['db']->fetchRow($res))

    {
        $row['status_id']=$row['status']; 
        $row['status']=$list_status[$row['status']];
        $arr[] = $row;

    } 

    $filter['created'] = $_POST['created'];

    $filter['device']   = $_POST['device'];
    
    $filter['status']   = $_POST['status'];
    
    $filter['list_status']=$list_status;

    $arr = array('item' => $arr, 'filter' => $filter, 'page_count' => $filter['page_count'], 'record_count' => $filter['record_count']);

    return $arr;

}

?>