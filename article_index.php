<?php
define('IN_ECS', true);
require(dirname(__FILE__) . '/includes/init.php');
if ((DEBUG_MODE & 2) != 2)
{
    $smarty->caching = true;
}
/* ???? */
clear_cache_files();
/*------------------------------------------------------ */
//-- INPUT
/*------------------------------------------------------ */
/* ?????? */
$page   = !empty($_REQUEST['page'])  && intval($_REQUEST['page'])  > 0 ? intval($_REQUEST['page'])  : 1;
/*------------------------------------------------------ */
//-- PROCESSOR
/*------------------------------------------------------ */
/* ???????ID */
$cache_id = sprintf('%X', crc32('article-index-' . $page . '-' . $_CFG['lang'].'-'.$_COOKIE['client']));
if (!$smarty->is_cached($display_mode, $cache_id))
{
    /* ??????????????????? */
    $cat_id=0;
    assign_template('a', array($cat_id));
    $position = assign_ur_here($cat_id);
    $smarty->assign('page_title',           $position['title']);     // ????
    $smarty->assign('ur_here',              $position['ur_here']);   // ????
    $smarty->assign('categories',           get_categories_tree(0)); // ???
    $smarty->assign('new_goods',            get_new_goods_mobile());
    /*Start SEO cusstom*/
        $smarty->assign('page_title','Tin công nghệ, đánh giá, mẹo hay đang chờ bạn');
    /*End SEO cusstom*/
    $smarty->assign('keywords',  'Tin tức công nghệ' );
    $smarty->assign('description', 'Tin tức công nghệ' );
    /* ?????? */
    $size   = isset($_CFG['article_page_size']) && intval($_CFG['article_page_size']) > 0 ? intval($_CFG['article_page_size']) : 20;
    $count  = get_article_count(0);
    $pages  = ($count > 0) ? ceil($count / $size) : 1;
    if ($page > $pages)
    {
        $page = $pages;
    }
    $pager['search']['id'] = 0;
    $keywords = '';
    $goon_keywords = ''; //??????????
    /* ?????? */
    if (isset($_REQUEST['keywords']))
    {
        $keywords = addslashes(htmlspecialchars(urldecode(trim($_REQUEST['keywords']))));
        $pager['search']['keywords'] = $keywords;
        $search_url = substr(strrchr($_POST['cur_url'], '/'), 1);
        $smarty->assign('search_value',    stripslashes(stripslashes($keywords)));
        $smarty->assign('search_url',       $search_url);
        $count  = get_article_count(0, $keywords);
        $pages  = ($count > 0) ? ceil($count / $size) : 1;
        if ($page > $pages)
        {
            $page = $pages;
        }
        $goon_keywords = urlencode($_REQUEST['keywords']);
    }
    $cat_id=-1;
    $smarty->assign('artciles_list',    get_cat_articles($cat_id, $page, $size ,$keywords));
    $smarty->assign('top5',    get_top5_articles($cat_id));
    $smarty->assign('promo_article_list',    get_promo_articles());
    $smarty->assign('top_view',    get_top10viewed_articles($cat_id));
    $catvieclam = array(16,17,18,19,20,21,22);
    if(in_array($cat_id, $catvieclam)){
        $layoutvieclam = true;
        $smarty->assign('catvl',    cat_vieclam(16));
    }
    $smarty->assign('layoutvieclam',    $layoutvieclam);
    $smarty->assign('cat_id',    $cat_id);
    /* ?? */
    assign_pager('article_index', 0, $count, $size, '', '', $page, $goon_keywords);
    assign_dynamic('article_index');
}
if(!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest')
    $smarty->assign('is_ajax',1);
$smarty->assign('feed_url',         ($_CFG['rewrite'] == 1) ? "feed-typearticle_index" . $cat_id . ".xml" : 'feed.php?type=article_index' . $cat_id); // RSS URL
$smarty->display($display_mode, $cache_id);
?>