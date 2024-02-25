<?php
define('IN_ECS', true);
require(dirname(__FILE__) . '/includes/init.php');
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
//-- 专题列表页面
/*------------------------------------------------------ */
if ($_REQUEST['act'] == 'list')
{
    admin_priv('giasoc_manage');
    $smarty->assign('ur_here',     $_LANG['17_giasoc']);
    $smarty->assign('full_page',   1);
    $list = get_topic_list();
    $smarty->assign('topic_list',   $list['item']);
    $smarty->assign('filter',       $list['filter']);
    $smarty->assign('record_count', $list['record_count']);
    $smarty->assign('page_count',   $list['page_count']);
    $sort_flag  = sort_flag($list['filter']);
    $smarty->assign($sort_flag['tag'], $sort_flag['img']);
    assign_query_info();
    $smarty->assign('action_link', array('text' => $_LANG['shock_add'], 'href' => 'giasoc.php?act=add'));
    $smarty->display('giasoc_list.htm');
}
/* 添加,编辑 */
if ($_REQUEST['act'] == 'add' || $_REQUEST['act'] == 'edit')
{
    admin_priv('giasoc_manage');
    $isadd     = $_REQUEST['act'] == 'add';
    $smarty->assign('isadd', $isadd);
    $giasoc_id  = empty($_REQUEST['giasoc_id']) ? 0 : intval($_REQUEST['giasoc_id']);
    include_once(ROOT_PATH.'includes/fckeditor/fckeditor.php'); // 包含 html editor 类文件
    $smarty->assign('ur_here',     $_LANG['17_giasoc']);
    $smarty->assign('action_link', list_link($isadd));
    $smarty->assign('cat_list',   cat_list(0, 1));
    $smarty->assign('brand_list', get_brand_list());
    $smarty->assign('cfg_lang',   $_CFG['lang']);

    if (!$isadd)
    {
        $sql = "SELECT * FROM " . $ecs->table('giasoc') . " WHERE giasoc_id = '$giasoc_id'";
        $topic = $db->getRow($sql);

        create_html_editor('topic_intro', $topic['giasoc_note'],'FCintro','Normal', '100%', '300');
        require(ROOT_PATH . 'includes/cls_json.php');
        $json          = new JSON;
        $topic['data'] = addcslashes($topic['data'], "'");
        $topic['data'] = $json->encode(@unserialize($topic['data']));
        $topic['data'] = addcslashes($topic['data'], "'");

        $smarty->assign('topic', $topic);
        $smarty->assign('act',   "update");
    }
    else
    {

        $smarty->assign('topic', $topic);
        create_html_editor('topic_intro', $topic['giasoc_note'],'FCintro','Normal', '100%', '300');
        $smarty->assign('act', "insert");
    }
    $smarty->display('giasoc_edit.htm');
}
elseif ($_REQUEST['act'] == 'insert' || $_REQUEST['act'] == 'update')
{
    admin_priv('giasoc_manage');
    $is_insert = $_REQUEST['act'] == 'insert';
    $giasoc_id  = empty($_POST['giasoc_id']) ? 0 : intval($_POST['giasoc_id']);


    require(ROOT_PATH . 'includes/cls_json.php');

    $json       = new JSON;
    $tmp_data   = $json->decode($_POST['topic_data']);
    $data       = serialize($tmp_data);

    $keywords   = $_POST['keywords'];
    $url_seo   =  trim($_POST['url_seo']);
    $description= $_POST['description'];
    $img_link   =  trim($_POST['img_link']);
    if (empty($url_seo)){
         sys_msg('Chưa nhập URL SEO');
    }
    if ($is_insert)
    {
        $sql = "INSERT INTO " . $ecs->table('giasoc') . " (giasoc_name,giasoc_slug,data,giasoc_note,keywords,description, img_link)" .
                "VALUES ('$_POST[topic_name]', '$url_seo','$data','$_POST[topic_intro]','$keywords','$description', '$img_link')";
    }
    else
    {
        $sql = "UPDATE " . $ecs->table('giasoc') .
                "SET giasoc_name='$_POST[topic_name]', giasoc_slug='$url_seo',data='$data',giasoc_note='$_POST[topic_intro]', keywords='$keywords', description='$description', img_link = '$img_link'" .
               " WHERE giasoc_id='$giasoc_id' LIMIT 1";

    }
    $db->query($sql);
    clear_cache_files();
    $links[] = array('href' => 'giasoc.php', 'text' =>  $_LANG['back_list']);
    sys_msg($_LANG['succed'], 0, $links);
}
elseif ($_REQUEST['act'] == 'get_goods_list')
{
    include_once(ROOT_PATH . 'includes/cls_json.php');
    $json = new JSON;
    $filters = $json->decode($_GET['JSON']);
    $arr = get_goods_list($filters);
    $opt = array();
    foreach ($arr AS $key => $val)
    {
        $opt[] = array('value' => $val['goods_id'],
                       'text'  => $val['goods_sn'].' - '.$val['goods_name']);
    }
    make_json_result($opt);
}
elseif ($_REQUEST["act"] == "delete")
{
    admin_priv('giasoc_manage');
    $sql = "DELETE FROM " . $ecs->table('giasoc') . " WHERE ";
    if (!empty($_POST['checkboxs']))
    {
        $sql .= db_create_in($_POST['checkboxs'], 'giasoc_id');
    }
    elseif (!empty($_GET['id']))
    {
        $_GET['id'] = intval($_GET['id']);
        $sql .= "giasoc_id = '$_GET[id]'";
    }
    else
    {
        exit;
    }
    $db->query($sql);
    clear_cache_files();
    if (!empty($_REQUEST['is_ajax']))
    {
        $url = 'giasoc.php?act=query&' . str_replace('act=delete', '', $_SERVER['QUERY_STRING']);
        ecs_header("Location: $url\n");
        exit;
    }
    $links[] = array('href' => 'giasoc.php', 'text' =>  $_LANG['back_list']);
    sys_msg($_LANG['succed'], 0, $links);
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
    $tpl = 'giasoc_list.htm';
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
        $filter['sort_by']    = empty($_REQUEST['sort_by']) ? 'giasoc_id' : trim($_REQUEST['sort_by']);
        $filter['sort_order'] = empty($_REQUEST['sort_order']) ? 'DESC' : trim($_REQUEST['sort_order']);
        $sql = "SELECT COUNT(*) FROM ".$GLOBALS['ecs']->table('giasoc');
        $filter['record_count'] = $GLOBALS['db']->getOne($sql);
        /* 分页大小 */
        $filter = page_and_size($filter);
        $sql = "SELECT * FROM " .$GLOBALS['ecs']->table('giasoc'). " ORDER BY $filter[sort_by] $filter[sort_order]";
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

        $topic['url']        = $GLOBALS['ecs']->url() . 'gia-soc/'. $topic['giasoc_slug'];
        $res[] = $topic;
    }
    $arr = array('item' => $res, 'filter' => $filter, 'page_count' => $filter['page_count'], 'record_count' => $filter['record_count']);
    return $arr;
}
/**
 * 列表链接
 * @param   bool    $is_add     是否添加（插入）
 * @param   string  $text       文字
 * @return  array('href' => $href, 'text' => $text)
 */
function list_link($is_add = true, $text = '')
{
    $href = 'giasoc.php?act=list';
    if (!$is_add)
    {
        $href .= '&' . list_link_postfix();
    }
    if ($text == '')
    {
        $text = $GLOBALS['_LANG']['shock_list'];
    }
    return array('href' => $href, 'text' => $text);
}

?>
