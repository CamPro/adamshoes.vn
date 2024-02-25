<?php

define('IN_ECS', true);

require(dirname(__FILE__) . '/includes/init.php');

require_once(ROOT_PATH . "includes/fckeditor/fckeditor.php");

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

    admin_priv('comment_priv');

    $smarty->assign('ur_here',      $_LANG['05_comment_manage']);

    $smarty->assign('full_page',    1);

    $list = get_comment_list();

    $start_date = isset($_POST['start_date']) ? $_POST['start_date'] : '';

    $end_date = isset($_POST['end_date']) ? $_POST['end_date'] : '';

    $smarty->assign('start_date',  $start_date );

    $smarty->assign('end_date', $end_date);

    $smarty->assign('comment_list', $list['item']);

    $smarty->assign('filter',       $list['filter']);

    $smarty->assign('record_count', $list['record_count']);

    $smarty->assign('page_count',   $list['page_count']);

    $sort_flag  = sort_flag($list['filter']);

    $smarty->assign($sort_flag['tag'], $sort_flag['img']);

    assign_query_info();



    $page = $_mobile ? 'comment_list_mobile.htm' : 'comment_list.htm';

    $smarty->display($page);

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

        $sql  = "SELECT comment_id, user_name,telephone, content, add_time FROM " .$ecs->table('comment'). " WHERE parent_id = 0 $where ";

        $res  = $db->getAll($sql);

        foreach ($res as $row) {

            //Get Admin Answer for each ask

            $sql  = "SELECT user_name, content, add_time FROM " .$ecs->table('comment'). " WHERE parent_id = $row[comment_id] ";

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

        export_csv($arr, 'comment_list.csv');

}

/*------------------------------------------------------ */

//-- 翻页、搜索、排序

/*------------------------------------------------------ */

if ($_REQUEST['act'] == 'query')

{

    $list = get_comment_list();

    $smarty->assign('comment_list', $list['item']);

    $smarty->assign('filter',       $list['filter']);

    $smarty->assign('record_count', $list['record_count']);

    $smarty->assign('page_count',   $list['page_count']);

    $sort_flag  = sort_flag($list['filter']);

    $smarty->assign($sort_flag['tag'], $sort_flag['img']);

    make_json_result($smarty->fetch('comment_list.htm'), '',

        array('filter' => $list['filter'], 'page_count' => $list['page_count']));

}

/*------------------------------------------------------ */

//-- 回复用户评论(同时查看评论详情)

/*------------------------------------------------------ */

if ($_REQUEST['act']=='reply')

{

    /* 检查权限 */

    admin_priv('comment_priv');

    $comment_info = array();

    $reply_info   = array();

    $id_value     = array();

    /* 获取评论详细信息并进行字符处理 */

    $sql = "SELECT * FROM " .$ecs->table('comment'). " WHERE comment_id = '$_REQUEST[id]'";

    $comment_info = $db->getRow($sql);

    $comment_info['add_time'] = local_date($_CFG['time_format'], $comment_info['add_time']);

    /* 获得评论回复内容 */

    $sql = "SELECT * FROM ".$ecs->table('comment'). " WHERE parent_id = '$_REQUEST[id]'";

    $reply_info = $db->getRow($sql);

    if (empty($reply_info))

    {

        $reply_info['content']  = '';

        $reply_info['add_time'] = '';

    }

    else

    {

        $reply_info['add_time'] = local_date($_CFG['time_format'], $reply_info['add_time']);

    }

    /* 获取管理员的用户名和Email地址 */

    $sql = "SELECT user_name, fullname, email FROM ". $ecs->table('admin_user').

           " WHERE user_id = '$_SESSION[admin_id]'";

    $admin_info = $db->getRow($sql);

    /* 取得评论的对象(文章或者商品) */

    if ($comment_info['comment_type'] == 0)

    {

        $sql = "SELECT goods_name FROM ".$ecs->table('goods').

               " WHERE goods_id = '$comment_info[id_value]'";

        $id_value = $db->getOne($sql);

    }

    else

    {

        $sql = "SELECT title FROM ".$ecs->table('article').

               " WHERE article_id='$comment_info[id_value]'";

        $id_value = $db->getOne($sql);

    }

    create_html_editor('content', $reply_info['content'], 'FKCcontent', 'Basic', '80%', '200');

    /* 模板赋值 */

    $smarty->assign('msg',          $comment_info); //评论信息

    $smarty->assign('admin_info',   $admin_info);   //管理员信息

    $smarty->assign('reply_info',   $reply_info);   //回复的内容

    $smarty->assign('id_value',     $id_value);  //评论的对象

    $smarty->assign('send_fail',   !empty($_REQUEST['send_ok']));

    $smarty->assign('ur_here',      $_LANG['comment_info']);

    $smarty->assign('action_link',  array('text' => $_LANG['05_comment_manage'],

    'href' => 'comment_manage.php?act=list'));

    /* 页面显示 */

    assign_query_info();

    $page = $_mobile ? 'comment_info_mobile.htm' : 'comment_info.htm';

    $smarty->display($page);

}

/*------------------------------------------------------ */

//-- 处理 回复用户评论

/*------------------------------------------------------ */

if ($_REQUEST['act']=='action')

{

    admin_priv('comment_priv');

    /* 获取IP地址 */

    $ip     = real_ip();

    /* 获得评论是否有回复 */

    $sql = "SELECT comment_id, content, parent_id FROM ".$ecs->table('comment').

           " WHERE parent_id = '$_REQUEST[comment_id]'";

    $reply_info = $db->getRow($sql);

    if (!empty($reply_info['content']))

    {

        /* 更新回复的内容 */

        $sql = "UPDATE ".$ecs->table('comment')." SET ".

               "email     = '$_POST[email]', ".

               "user_name = '$_POST[user_name]', ".

               "content   = '$_POST[content]', ".

               "add_time  =  '" . gmtime() . "', ".

               "ip_address= '$ip', ".

               "status    = 0".

               " WHERE comment_id = '".$reply_info['comment_id']."'";

    }

    else

    {

        /* 插入回复的评论内容 */

        $sql = "INSERT INTO ".$ecs->table('comment')." (comment_type, id_value, email, user_name , ".

                    "content, add_time, ip_address, status, parent_id) ".

               "VALUES('$_POST[comment_type]', '$_POST[id_value]','$_POST[email]', " .

                    "'$_POST[user_name]','$_POST[content]','" . gmtime() . "', '$ip', '0', '$_POST[comment_id]')";

    }

    $db->query($sql);

    /* 更新当前的评论状态为已回复并且可以显示此条评论 */

    $sql = "UPDATE " .$ecs->table('comment'). " SET status = 1 WHERE comment_id = '$_POST[comment_id]'";

    $db->query($sql);

    /* 邮件通知处理流程 */

    if (!empty($_POST['send_email_notice']) or isset($_POST['remail']))

    {

        //获取邮件中的必要内容

        $sql = 'SELECT user_name, email, content ' .

               'FROM ' .$ecs->table('comment') .

               " WHERE comment_id ='$_REQUEST[comment_id]'";

        $comment_info = $db->getRow($sql);

        /* 设置留言回复模板所需要的内容信息 */

        $template    = get_mail_template('recomment');

        $smarty->assign('user_name',   $comment_info['user_name']);

        $smarty->assign('recomment', $_POST['content']);

        $smarty->assign('comment', $comment_info['content']);

        $smarty->assign('shop_name',   "<a href='".$ecs->url()."'>" . $_CFG['shop_name'] . '</a>');

        $smarty->assign('send_date',   date('Y-m-d'));

        $content = $smarty->fetch('str:' . $template['template_content']);

        /* 发送邮件 */

        if (send_mail($comment_info['user_name'], $comment_info['email'], $template['template_subject'], $content, $template['is_html']))

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

    admin_log(addslashes($_LANG['reply']), 'edit', 'users_comment');

    ecs_header("Location: comment_manage.php?act=reply&id=$_REQUEST[comment_id]&send_ok=$send_ok\n");

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

        $sql = "UPDATE " .$ecs->table('comment'). " SET status = 1 WHERE comment_id = '$_REQUEST[id]'";

        $db->query($sql);

        //add_feed($_REQUEST['id'], COMMENT_GOODS);

        /* 清除缓存 */

        clear_cache_files();

        ecs_header("Location: comment_manage.php?act=reply&id=$_REQUEST[id]\n");

        exit;

    }

    else

    {

        /* 禁止评论显示 */

        $sql = "UPDATE " .$ecs->table('comment'). " SET status = 0 WHERE comment_id = '$_REQUEST[id]'";

        $db->query($sql);

        /* 清除缓存 */

        clear_cache_files();

        ecs_header("Location: comment_manage.php?act=reply&id=$_REQUEST[id]\n");

        exit;

    }

}

/*------------------------------------------------------ */

//-- 删除某一条评论

/*------------------------------------------------------ */

elseif ($_REQUEST['act'] == 'remove')

{

    check_authz_json('comment_priv');

    $id = intval($_GET['id']);

    $sql = "DELETE FROM " .$ecs->table('comment'). " WHERE comment_id = '$id'";

    $res = $db->query($sql);

    if ($res)

    {

        $db->query("DELETE FROM " .$ecs->table('comment'). " WHERE parent_id = '$id'");

    }

    admin_log('', 'remove', 'ads');

    $url = 'comment_manage.php?act=query&' . str_replace('act=remove', '', $_SERVER['QUERY_STRING']);

    ecs_header("Location: $url\n");

    exit;

}

/*------------------------------------------------------ */

//-- 批量删除用户评论

/*------------------------------------------------------ */

if ($_REQUEST['act'] == 'batch')

{

    admin_priv('comment_priv');

    $action = isset($_POST['sel_action']) ? trim($_POST['sel_action']) : 'deny';

    if (isset($_POST['checkboxes']))

    {

        switch ($action)

        {

            case 'remove':

                $db->query("DELETE FROM " . $ecs->table('comment') . " WHERE " . db_create_in($_POST['checkboxes'], 'comment_id'));

                $db->query("DELETE FROM " . $ecs->table('comment') . " WHERE " . db_create_in($_POST['checkboxes'], 'parent_id'));

                break;

           case 'allow' :

               $db->query("UPDATE " . $ecs->table('comment') . " SET status = 1  WHERE " . db_create_in($_POST['checkboxes'], 'comment_id'));

               break;

           case 'deny' :

               $db->query("UPDATE " . $ecs->table('comment') . " SET status = 0  WHERE " . db_create_in($_POST['checkboxes'], 'comment_id'));

               break;

           default :

               break;

        }

        clear_cache_files();

        $action = ($action == 'remove') ? 'remove' : 'edit';

        admin_log('', $action, 'adminlog');

        $link[] = array('text' => $_LANG['back_list'], 'href' => 'comment_manage.php?act=list');

        sys_msg(sprintf($_LANG['batch_drop_success'], count($_POST['checkboxes'])), 0, $link);

    }

    else

    {

        /* 提示信息 */

        $link[] = array('text' => $_LANG['back_list'], 'href' => 'comment_manage.php?act=list');

        sys_msg($_LANG['no_select_comment'], 0, $link);

    }

}

/**

 * 获取评论列表

 * @access  public

 * @return  array

 */

function get_comment_list()

{

    $filter['start_time'] = empty($_REQUEST['start_date']) ? '' : $_REQUEST['start_date'].' 00:00:00';

    $filter['end_time'] = empty($_REQUEST['end_date']) ? '' : $_REQUEST['end_date'].' 23:59:59';

    $filter['sort_by']      = empty($_REQUEST['sort_by']) ? 'add_time' : trim($_REQUEST['sort_by']);

    $filter['sort_order']   = empty($_REQUEST['sort_order']) ? 'DESC' : trim($_REQUEST['sort_order']);

    if ($filter['start_time'])

    {

        $filter['start_time'] = local_strtotime($filter['start_time']);

        $where .= " AND add_time >= '$filter[start_time]'";

    }

    if ($filter['end_time'])

    {

        $filter['end_time'] = local_strtotime($filter['end_time']);

        $where .= " AND add_time <= '$filter[end_time]'";

    }

    $sql = "SELECT count(*) FROM " .$GLOBALS['ecs']->table('comment'). " WHERE parent_id = 0 $where";

    $filter['record_count'] = $GLOBALS['db']->getOne($sql);

    /* 分页大小 */

    $filter = page_and_size($filter);

    /* 获取评论数据 */

    $arr = array();

    $sql  = "SELECT * FROM " .$GLOBALS['ecs']->table('comment'). " WHERE parent_id = 0 $where " .

            " ORDER BY $filter[sort_by] $filter[sort_order] ".

            " LIMIT ". $filter['start'] .", $filter[page_size]";

    $res  = $GLOBALS['db']->query($sql);

    while ($row = $GLOBALS['db']->fetchRow($res))

    {
        if($row['comment_type']==5) 
        {
            $row['title']='Promotion';
        }
        else
        {
            
        
        $sql = ($row['comment_type'] == 0) ?

            "SELECT goods_name FROM " .$GLOBALS['ecs']->table('goods'). " WHERE goods_id='$row[id_value]'" :

            "SELECT title FROM ".$GLOBALS['ecs']->table('article'). " WHERE article_id='$row[id_value]'";

        $row['title'] = $GLOBALS['db']->getOne($sql);
        }
      $sql = "SELECT COUNT(*) FROM " .$GLOBALS['ecs']->table('comment'). " WHERE parent_id = '$row[comment_id]'";

       $row['is_reply'] =  ($GLOBALS['db']->getOne($sql) > 0) ?  1 : 0;

        $row['add_time'] = local_date($GLOBALS['_CFG']['time_format'], $row['add_time']);

        $arr[] = $row;

    }

    $filter['start_date'] = $_POST['start_date'];

    $filter['end_date']   = $_POST['start_date'];

    $arr = array('item' => $arr, 'filter' => $filter, 'page_count' => $filter['page_count'], 'record_count' => $filter['record_count']);

    return $arr;

}

?>