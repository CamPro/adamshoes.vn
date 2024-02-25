<?php
/**
 * $Author:Nobj Nguyen
 */
define('IN_ECS', true);
require(dirname(__FILE__) . '/includes/init.php');
require('includes/lib_goods.php');

if ($_REQUEST['act'] == 'list')
{
    admin_priv('topic_manage');
    #code here ...

    //admin_log('', 'list', 'installment');
    $list = get_topic_list();
    $smarty->assign('full_page',   1);
    $smarty->assign('topic',       $list['item']);
    $smarty->assign('filter',       $list['filter']);
    $smarty->assign('record_count', $list['record_count']);
    $smarty->assign('page_count',   $list['page_count']);

    $sort_flag  = sort_flag($list['filter']);
    $smarty->assign($sort_flag['tag'], $sort_flag['img']);

    assign_query_info();
    $smarty->assign('action_link', array('text' => $_LANG['topic_add'], 'href' => 'topic.php?act=add'));
    $smarty->assign('ur_here',      $_LANG['09_topic']);
    $smarty->display('langding_page_list.htm');

}elseif ($_REQUEST['act'] == 'add' || $_REQUEST['act'] == 'edit')
{
    admin_priv('topic_manage');

    $topic_id  = empty($_REQUEST['topic_id']) ? 0 : intval($_REQUEST['topic_id']);
    include_once(ROOT_PATH.'includes/fckeditor/fckeditor.php');


    $smarty->assign('cat_list', cat_list());
    $smarty->assign('brand_list', get_brand_list());

    assign_query_info();

    $smarty->assign('action_link', array('text' => $_LANG['09_topic'], 'href' => 'langdingpage.php?act=list'));
    $smarty->assign('ur_here',     $_LANG['09_topic']);

    if(!$isadd == 'add'){

        $sql = "SELECT * FROM " . $ecs->table('topic') . " WHERE topic_id = '$topic_id'";
        $topic = $db->getRow($sql);
        $topic['start_time'] = local_date('Y-m-d', $topic['start_time']);
        $topic['end_time']   = local_date('Y-m-d', $topic['end_time']);

        $smarty->assign('topic', $topic);
        $smarty->assign('act',   "update");

    }else{
        $smarty->assign('act',   "insert");
        $topic = array('title' => '', 'topic_type' => 0, 'url' => 'http://');
        $smarty->assign('topic', $topic);
    }
    create_html_editor('topic_intro', $topic['intro'],'html_header','Normal', '100%', '300');
    create_html_editor('topic_footer', $topic['intro'],'html_footer','Normal', '100%', '300');


    $smarty->display('langding_page_info.htm');

}
elseif ($_REQUEST['act'] == 'update' || $_REQUEST['act'] == 'insert')
{

}
elseif ($_REQUEST["act"] == "query")
{
    $topic_list = get_topic_list();
    $smarty->assign('topic_list',   $topic_list['item']);
    $smarty->assign('filter',       $topic_list['filter']);
    $smarty->assign('record_count', $topic_list['record_count']);
    $smarty->assign('page_count',   $topic_list['page_count']);
    $smarty->assign('use_storage',  empty($_CFG['use_storage']) ? 0 : 1);

    /* 排序标记 */
    $sort_flag  = sort_flag($topic_list['filter']);
    $smarty->assign($sort_flag['tag'], $sort_flag['img']);

    $tpl = 'langding_page_list.htm';
    make_json_result($smarty->fetch($tpl), '',array('filter' => $topic_list['filter'], 'page_count' => $topic_list['page_count']));
}


/**
 * 获取专题列表
 * @access  public
 * @return void
 */
function get_topic_list()
{
    $result = get_filter();
    if ($result === false)
    {
        /* 查询条件 */
        $filter['sort_by']    = empty($_REQUEST['sort_by']) ? 'topic_id' : trim($_REQUEST['sort_by']);
        $filter['sort_order'] = empty($_REQUEST['sort_order']) ? 'DESC' : trim($_REQUEST['sort_order']);

        $sql = "SELECT COUNT(*) FROM ".$GLOBALS['ecs']->table('topic');
        $filter['record_count'] = $GLOBALS['db']->getOne($sql);

        /* 分页大小 */
        $filter = page_and_size($filter);

        $sql = "SELECT * FROM " .$GLOBALS['ecs']->table('topic'). " ORDER BY $filter[sort_by] $filter[sort_order]";

        set_filter($filter, $sql);
    }
    else
    {
        $sql    = $result['sql'];
        $filter = $result['filter'];
    }

    $query = $GLOBALS['db']->selectLimit($sql, $filter['page_size'], $filter['start']);

    $res = array();

    while($topic = $GLOBALS['db']->fetch_array($query)){
        $topic['start_time'] = local_date('Y-m-d',$topic['start_time']);
        $topic['end_time']   = local_date('Y-m-d',$topic['end_time']);
        $topic['url']        = $GLOBALS['ecs']->url() . 'khuyen-mai/'. $topic['url_seo'];
        $res[] = $topic;
    }

    $arr = array('item' => $res, 'filter' => $filter, 'page_count' => $filter['page_count'], 'record_count' => $filter['record_count']);

    return $arr;
}

 ?>