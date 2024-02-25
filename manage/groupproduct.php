<?php
define('IN_ECS', true);
require(dirname(__FILE__) . '/includes/init.php');
include_once(ROOT_PATH . '/includes/cls_image.php');
$image = new cls_image($_CFG['bgcolor']);
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
    admin_priv('goods_group');
    $smarty->assign('ur_here',     $_LANG['18_grouproduct']);
    $smarty->assign('full_page',   1);
    $list = get_topic_list();
    $smarty->assign('topic_list',   $list['item']);
    $smarty->assign('filter',       $list['filter']);
    $smarty->assign('record_count', $list['record_count']);
    $smarty->assign('page_count',   $list['page_count']);
    $sort_flag  = sort_flag($list['filter']);
    $smarty->assign($sort_flag['tag'], $sort_flag['img']);
    assign_query_info();
    $smarty->assign('action_link', array('text' => $_LANG['groupproduct_add'], 'href' => basename(__FILE__).'?act=add'));
    $smarty->display('groupproduct_list.htm');
}
/* 添加,编辑 */
if ($_REQUEST['act'] == 'add' || $_REQUEST['act'] == 'edit')
{
    admin_priv('goods_group');
    $isadd     = $_REQUEST['act'] == 'add';
    $smarty->assign('isadd', $isadd);
    $smarty->assign('base_url',$_CFG['static_path']);
    $group_id  = empty($_REQUEST['group_id']) ? 0 : intval($_REQUEST['group_id']);
    include_once(ROOT_PATH.'includes/fckeditor/fckeditor.php'); // 包含 html editor 类文件
    $smarty->assign('ur_here',     $_LANG['18_grouproduct']);
    $smarty->assign('action_link', list_link($isadd));
    $smarty->assign('cat_list',   cat_list(0, 1));
    $smarty->assign('brand_list', get_brand_list());
    $smarty->assign('cfg_lang',   $_CFG['lang']);
    $smarty->assign('list_layout',array(1=>'List Only Goods',2=>'List Banner + Goods',3=>'Full Goods'));
    $list_ads=$db->getAll('SELECT* FROM '.$ecs->table('ad').' where enabled="1"');
    $result_list_ads=array(0=>'---');
    foreach($list_ads as $ad)
        $result_list_ads[$ad['ad_id']]=$ad['ad_name'];
    $smarty->assign('list_ads',$result_list_ads);
    if (!$isadd)
    {
        $sql = "SELECT * FROM " . $ecs->table('groupproduct') . " WHERE group_id = '$group_id'";
        $topic = $db->getRow($sql);
        create_html_editor('group_note', $topic['group_note'],'FCintro','Normal', '100%', '300');
        require(ROOT_PATH . 'includes/cls_json.php');
        $json          = new JSON;
      #  $topic['data'] = addcslashes($topic['data'], "'");
        $topic['data'] = $json->encode(@unserialize($topic['data']));
        $list_goods=array();
       $topic['data']=str_replace('"','',$topic['data']);
       if($topic['data'])foreach(explode(",",$topic['data']) as $_goods_id)
       {
            $_goods_id=strip_tags($_goods_id);
          $sql = "SELECT * FROM " . $ecs->table('goods') . " WHERE goods_id = '$_goods_id'";
            $goods = $db->getRow($sql);
               if($goods)
                    $list_goods[]=array('id'=>$goods['goods_id'],'name'=>$goods['goods_sn'].'-'.$goods['goods_name']);
       }
       # $topic['data'] = addcslashes($topic['data'], "'");
        $smarty->assign('list_goods',$list_goods);
        $smarty->assign('topic', $topic);
        $smarty->assign('act',   "update");
    }
    else
    {
        $smarty->assign('topic', $topic);
        create_html_editor('topic_intro', $topic['giasoc_note'],'FCintro','Normal', '100%', '300');
        $smarty->assign('act', "insert");
    }
    $smarty->display('groupproduct_edit.htm');
}
elseif ($_REQUEST['act'] == 'insert' || $_REQUEST['act'] == 'update')
{
    admin_priv('goods_group');
    $is_insert = $_REQUEST['act'] == 'insert';
    $group_id  = empty($_POST['group_id']) ? 0 : intval($_POST['group_id']);
    require(ROOT_PATH . 'includes/cls_json.php');
    #$json       = new JSON;
   # $tmp_data   = $json->decode($_POST['topic_data']);
    $tmp_data=$_POST['products'];
    $data       = serialize($tmp_data);
    $home   = $_POST['home'];
    if($home) $home=1;else $home=0;
    $sort_order   = $_POST['sort_order'];
    $meta_title   = $_POST['meta_title'];
    $layout   = $_POST['layout'];
    $link   = $_POST['link'];
    $datalinks   = $_POST['links'];
    $banner   = $_POST['banner'];
    $keywords   = $_POST['keywords'];
    $url_seo   =  trim($_POST['url_seo']);
    $description= $_POST['description'];
    $img_link   =  trim($_POST['img_link']);
   /* $fname =  $image->get_namefile($_FILES['banner']['name']);
    $upload_img   = $image->upload_image($_FILES['banner'],'',$fname); // goods normal name
    if(empty($_FILES['banner']['image']) && !empty($_POST['oldimage']))
        $upload_img=$_POST['oldimage'];*/
    if (empty($url_seo)){
         sys_msg('Chưa nhập URL SEO');
    }
    if ($is_insert)
    {
        $sql = "INSERT INTO " . $ecs->table('groupproduct') . " (group_name,group_slug,data,group_note,sort_order,home,layout,link,links,meta_title,keywords,description, img_link,banner)" .
                "VALUES ('$_POST[topic_name]', '$url_seo','$data','$_POST[topic_intro]','$sort_order','$home','$layout','$link','$datalinks','$meta_title','$keywords','$description', '$img_link','$banner')";
    }
    else
    {
        $sql = "UPDATE " . $ecs->table('groupproduct') .
                "SET group_name='$_POST[topic_name]', group_slug='$url_seo',data='$data',layout='$layout',link='$link',links='$datalinks',banner='$upload_img',group_note='$_POST[topic_intro]',sort_order='$sort_order',home='$home',meta_title='$meta_title', keywords='$keywords', description='$description', banner = '$banner'" .
               " WHERE group_id='$group_id' LIMIT 1";
    }
    $db->query($sql);
    clear_cache_files();
    $links[] = array('href' => basename(__FILE__), 'text' =>  $_LANG['back_list']);
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
elseif ($_REQUEST['act'] == 'suggest')
{
    include_once(ROOT_PATH . 'includes/cls_json.php');
    $json = new JSON;
    $filters = $json->decode($_REQUEST['JSON']);
    $arr = get_goods_list($filters);
    $opt = array();
    foreach ($arr AS $key => $val)
    {
        $opt[] = array('value' => $val['goods_id'],
                       'text'  => $val['goods_sn'].' - '.$val['goods_name']);
    }
    echo json_encode(array('content'=>$opt,'error'=>0,'length'=>count($opt)));
    #make_json_result($opt,0,array('length'=>count($opt)));
}
elseif ($_REQUEST["act"] == "delete")
{
    admin_priv('goods_group');
    $sql = "DELETE FROM " . $ecs->table('groupproduct') . " WHERE ";
    if (!empty($_POST['checkboxs']))
    {
        $sql .= db_create_in($_POST['checkboxs'], 'group_id');
    }
    elseif (!empty($_GET['id']))
    {
        $_GET['id'] = intval($_GET['id']);
        $sql .= "group_id = '$_GET[id]'";
    }
    else
    {
        exit;
    }
    $db->query($sql);
    clear_cache_files();
    if (!empty($_REQUEST['is_ajax']))
    {
        $url = basename(__FILE__).'?act=query&' . str_replace('act=delete', '', $_SERVER['QUERY_STRING']);
        ecs_header("Location: $url\n");
        exit;
    }
    $links[] = array('href' => basename(__FILE__), 'text' =>  $_LANG['back_list']);
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
    $tpl = 'groupproduct_list.htm';
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
        $filter['sort_by']    = empty($_REQUEST['sort_by']) ? 'group_id' : trim($_REQUEST['sort_by']);
        $filter['sort_order'] = empty($_REQUEST['sort_order']) ? 'DESC' : trim($_REQUEST['sort_order']);
        $sql = "SELECT COUNT(*) FROM ".$GLOBALS['ecs']->table('groupproduct');
        $filter['record_count'] = $GLOBALS['db']->getOne($sql);
        /* 分页大小 */
        $filter = page_and_size($filter);
        $sql = "SELECT * FROM " .$GLOBALS['ecs']->table('groupproduct'). " ORDER BY $filter[sort_by] $filter[sort_order]";
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
        $topic['url']        = $GLOBALS['ecs']->url() . 'g/'. $topic['group_slug'];
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
    $href = basename(__FILE__).'?act=list';
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
