<?php

define('IN_ECS', true);
require(dirname(__FILE__) . '/includes/init.php');
if ((DEBUG_MODE & 2) != 2)
{
    $smarty->caching = true;
}


/*------------------------------------------------------ */
//-- INPUT
/*------------------------------------------------------ */

$_REQUEST['id'] = isset($_REQUEST['id']) ? intval($_REQUEST['id']) : 0;
$article_id     = $_REQUEST['id'];
if(isset($_REQUEST['cat_id']) && $_REQUEST['cat_id'] < 0)
{
    $article_id = $db->getOne("SELECT article_id FROM " . $ecs->table('article') . " WHERE cat_id = '".intval($_REQUEST['cat_id'])."' ");
}

/*------------------------------------------------------ */
//-- PROCESSOR
/*------------------------------------------------------ */

$cache_id = sprintf('%X', crc32($_REQUEST['id'] . '-' . $_CFG['lang'].'-'.$_COOKIE['client']));

if (!$smarty->is_cached($display_mode, $cache_id))
{
    /* 文章详情 */
    $article = get_article_info($article_id);

    if (empty($article))
    {
        ecs_header("Location: ./\n");
        exit;
    }

    if (!empty($article['link']) && $article['link'] != 'http://' && $article['link'] != 'https://')
    {
        ecs_header("location:$article[link]\n");
        exit;
    }

    $smarty->assign('article_categories',   article_categories_tree(8)); //文章分类树
    $smarty->assign('categories',       get_categories_tree());  // 分类树
    //$smarty->assign('hot_goods',        get_recommend_goods('hot'));        // 热点文章
    //$smarty->assign('promotion_goods',  get_promote_goods());    // 特价商品
    $smarty->assign('id',               $article_id);
    $smarty->assign('username',         $_SESSION['user_name']);
    $smarty->assign('email',            $_SESSION['email']);
    $smarty->assign('type',            '1');

    /* 验证码相关设置 */
    if ((intval($_CFG['captcha']) & CAPTCHA_COMMENT) && gd_version() > 0)
    {
        $smarty->assign('enabled_captcha', 1);
        $smarty->assign('rand',            mt_rand());
    }
    $parent_id = get_parent_catid($_REQUEST['id']);

    $smarty->assign('parent_id',      $parent_id);

    $smarty->assign('article',      $article);
    $smarty->assign('article_rela',    get_related_articles($_REQUEST['id']));
    $smarty->assign('goods_rela',    article_related_goods($_REQUEST['id']));

    //$smarty->assign('new_articles',    index_get_new_articles());
    $smarty->assign('keywords',     htmlspecialchars($article['keywords']));
    $smarty->assign('description', htmlspecialchars($article['description']));
    $smarty->assign('request_uri',        ltrim($_SERVER['REQUEST_URI'],"/"));

    $catlist = array();
    foreach(get_article_parent_cats($article['cat_id']) as $k=>$v)
    {
        $catlist[] = $v['cat_id'];
    }

    assign_template('a', $catlist);

    $position = assign_ur_here($article['cat_id'], '');
    $smarty->assign('page_title',   $position['title']);    // 页面标题
    $smarty->assign('ur_here',      $position['ur_here']);  // 当前位置
    $smarty->assign('comment_type', 1);

    /* sp lien quan */
    /*$sql = "SELECT a.goods_id, g.goods_name " .
            "FROM " . $ecs->table('goods_article') . " AS a, " . $ecs->table('goods') . " AS g " .
            "WHERE a.goods_id = g.goods_id " .
            "AND a.article_id = '$_REQUEST[id]' ";
    $smarty->assign('goods_list', $db->getAll($sql));*/

    /* 上一篇下一篇文章 */
    $next_article = $db->getRow("SELECT article_id, title FROM " .$ecs->table('article'). " WHERE article_id > $article_id AND cat_id=$article[cat_id] AND is_open=1 LIMIT 1");
    if (!empty($next_article))
    {
        $next_article['url'] = build_uri('article', array('aid'=>$next_article['article_id']), $next_article['title']);
        $smarty->assign('next_article', $next_article);
    }

    $prev_aid = $db->getOne("SELECT max(article_id) FROM " . $ecs->table('article') . " WHERE article_id < $article_id AND cat_id=$article[cat_id] AND is_open=1");
    if (!empty($prev_aid))
    {
        $prev_article = $db->getRow("SELECT article_id, title FROM " .$ecs->table('article'). " WHERE article_id = $prev_aid");
        $prev_article['url'] = build_uri('article', array('aid'=>$prev_article['article_id']), $prev_article['title']);
        $smarty->assign('prev_article', $prev_article);
    }
    $db->query('UPDATE ' . $ecs->table('article') . " SET viewed = viewed + 1 WHERE article_id = '$_REQUEST[id]'");
    
    $smarty->assign('top5',    get_top5_articles($article['cat_id']));
    $smarty->assign('promo_article_list',    get_promo_articles());
    $smarty->assign('top_view',    get_top10viewed_articles($article['cat_id']));
    assign_dynamic('article');
}
if(isset($article) && $article['cat_id'] > 3)
{
    $smarty->display($display_mode, $cache_id);
}
else
{   $display_mode =  (isset($_COOKIE['client']) && $_COOKIE['client'] == 'mobile') ? 'article_pro_mobile.dwt' : 'article_pro.dwt';
    $smarty->display($display_mode, $cache_id);
}

/*------------------------------------------------------ */
//-- PRIVATE FUNCTION
/*------------------------------------------------------ */
function get_parent_catid($id){
    $cat_id =  $GLOBALS['db']->getOne("SELECT cat_id FROM " . $GLOBALS['ecs']->table('article') . " WHERE article_id = '".$id."' ");
    $parent_id =  $GLOBALS['db']->getOne("SELECT parent_id FROM " . $GLOBALS['ecs']->table('article_cat') . " WHERE cat_id = '".$cat_id."' ");
    return $parent_id;
}


/**
 * 获得指定的文章的详细信息
 *
 * @access  private
 * @param   integer     $article_id
 * @return  array
 */
function get_article_info($article_id)
{
    /* 获得文章的信息 */
    $sql = "SELECT a.*, IFNULL(AVG(r.comment_rank), 0) AS comment_rank ".
            "FROM " .$GLOBALS['ecs']->table('article'). " AS a ".
            "LEFT JOIN " .$GLOBALS['ecs']->table('comment'). " AS r ON r.id_value = a.article_id AND comment_type = 1 ".
            "WHERE a.is_open = 1 AND a.article_id = '$article_id' GROUP BY a.article_id";
    $row = $GLOBALS['db']->getRow($sql);

    if ($row !== false)
    {
        $row['comment_rank'] = ceil($row['comment_rank']);
        $row['add_time']     = timeAgo(date('d-m-Y H:i:s', $row['add_time']));

        if (empty($row['author']) || $row['author'] == '_SHOPHELP')
        {
            $row['author'] = $GLOBALS['_CFG']['shop_name'];
        }
        if(!empty($row['keywords'])){
            $row['tags'] = explode(', ', $row['keywords']);
        }
        $row['thumb']  = !empty($row['article_thumb']) ? $row['article_thumb'] : 'images/no_picture.gif';


    }

    return $row;
}

function get_related_articles($id)
{
    $sql = 'SELECT a.article_id, a.title, a.file_url, a.article_thumb, a.article_small_thumb, a.open_type, a.add_time ' .
            'FROM ' . $GLOBALS['ecs']->table('article_rela') . ' AS ar ' .
            " LEFT JOIN ".$GLOBALS['ecs']->table('article') . ' AS a ' .
            " ON a.article_id = ar.article_id_rela" .
            " WHERE ar.article_id = '$id' AND a.is_open = 1 " .
            ' ORDER BY a.add_time DESC';
    $res = $GLOBALS['db']->query($sql);

    $arr = array();
    while ($row = $GLOBALS['db']->fetchRow($res))
    {
        $row['url']         = $row['open_type'] != 1 ?
            build_uri('article', array('aid'=>$row['article_id']), $row['title']) : trim($row['file_url']);
        $row['add_time']    = timeAgo(date('d-m-Y H:i:s', $row['add_time']));
        $row['article_thumb']    = $row['article_thumb'];
        $row['article_small_thumb']    = $row['article_small_thumb'];
        $row['title'] = $row['title'];
        $row['short_title'] = $GLOBALS['_CFG']['article_title_length'] > 0 ?
        sub_str($row['title'], $GLOBALS['_CFG']['article_title_length']) : $row['title'];

        $arr[] = $row;
    }

    return $arr;
}
/**
 * 获得文章关联的商品
 *
 * @access  public
 * @param   integer $id
 * @return  array
 */
function article_related_goods($id)
{
    $sql = 'SELECT g.goods_id, g.goods_gift, g.seller_note, g.goods_name, g.is_preoder, g.goods_number, g.goods_thumb, g.goods_img, g.shop_price AS org_price, ' .
                "IFNULL(mp.user_price, g.shop_price * '$_SESSION[discount]') AS shop_price, ".
                'g.market_price, g.promote_price, g.promote_start_date, g.promote_end_date ' .
            'FROM ' . $GLOBALS['ecs']->table('goods_article') . ' ga ' .
            'LEFT JOIN ' . $GLOBALS['ecs']->table('goods') . ' AS g ON g.goods_id = ga.goods_id ' .
            "LEFT JOIN " . $GLOBALS['ecs']->table('member_price') . " AS mp ".
                    "ON mp.goods_id = g.goods_id AND mp.user_rank = '$_SESSION[user_rank]' ".
            "WHERE ga.article_id = '$id' AND g.is_on_sale = 1 AND g.is_alone_sale = 1 AND g.is_delete = 0";
    $res = $GLOBALS['db']->query($sql);

    $arr = array();
    while ($row = $GLOBALS['db']->fetchRow($res))
    {
        $arr[$row['goods_id']]['goods_id']      = $row['goods_id'];
        $arr[$row['goods_id']]['goods_name']    = $row['goods_name'];
        $arr[$row['goods_id']]['note']    = $row['seller_note'];
        $arr[$row['goods_id']]['is_preoder']   = $row['is_preoder'];
        $arr[$row['goods_id']]['stock']        = $row['goods_number'];
        $arr[$row['goods_id']]['short_name']   = $GLOBALS['_CFG']['goods_name_length'] > 0 ?
        sub_str($row['goods_name'], $GLOBALS['_CFG']['goods_name_length']) : $row['goods_name'];
        $arr[$row['goods_id']]['goods_thumb']   = get_image_path($row['goods_id'], $row['goods_thumb'], true);
        $arr[$row['goods_id']]['goods_img']     = get_image_path($row['goods_id'], $row['goods_img']);
        $arr[$row['goods_id']]['market_price']  = price_format($row['market_price']);
        $arr[$row['goods_id']]['shop_price']    = price_format($row['shop_price']);
        $arr[$row['goods_id']]['gift']          = $row['goods_gift'];
        $arr[$row['goods_id']]['url']           = build_uri('goods', array('gid' => $row['goods_id']), $row['goods_name']);

        if ($row['promote_price'] > 0)
        {
            $arr[$row['goods_id']]['promote_price'] = bargain_price($row['promote_price'], $row['promote_start_date'], $row['promote_end_date']);
            $arr[$row['goods_id']]['formated_promote_price'] = $arr[$row['goods_id']]['promote_price'] > 0 ? price_format($arr[$row['goods_id']]['promote_price']) : '';
        }
        else
        {
            $arr[$row['goods_id']]['promote_price'] = '';
        }
    }

    return $arr;
}

?>