<?php

define('IN_ECS', true);

require(dirname(__FILE__) . '/includes/init.php');

if ((DEBUG_MODE & 2) != 2)
{
    $smarty->caching = false;
}
if($_REQUEST['topic_id']){
  $topic_id  = empty($_REQUEST['topic_id']) ? 0 : intval($_REQUEST['topic_id']);
}else{
    $slug =  empty($_REQUEST['slug']) ? '' : $_REQUEST['slug'];
    if($slug == ''){
        $topic_id = 0;
    }else{
        $topic_id=$db->getOne("select topic_id from ". $ecs->table('topic') ." where url_seo='$slug' limit 0,1");
    }
}


$sql = "SELECT template FROM " . $ecs->table('topic') .
        "WHERE topic_id = '$topic_id' and  " . gmtime() . " >= start_time and " . gmtime() . "<= end_time";

$topic = $db->getRow($sql);

if(empty($topic))
{
    /* 如果没有找到任何记录则跳回到首页 */
    ecs_header("Location: ./\n");
    exit;
}

$templates = empty($topic['template']) ? 'topic.dwt' : $topic['template'];

$cache_id = sprintf('%X', crc32($_SESSION['user_rank'] . '-' . $_CFG['lang'] . '-' . $topic_id.'-'.$_COOKIE['client']));

if (!$smarty->is_cached($display_mode, $cache_id))
{
    $sql = "SELECT * FROM " . $ecs->table('topic') . " WHERE topic_id = '$topic_id'";

    $topic = $db->getRow($sql);
    $topic['data'] = addcslashes($topic['data'], "'");
    $tmp = @unserialize($topic["data"]);
    $arr = (array)$tmp;

    $goods_id = array();

    foreach ($arr AS $key=>$value)
    {
        foreach($value AS $k => $val)
        {
            $opt = explode('|', $val);
            $arr[$key][$k] = $opt[1];
            $goods_id[] = $opt[1];
        }
    }

    $sql = 'SELECT g.goods_id, g.cat_id, g.goods_name, g.goods_name_style, g.market_price, g.tragop_price, g.is_new, g.is_best, g.is_hot, g.is_os, g.is_special, g.shop_price AS org_price, ' .
                "IFNULL(mp.user_price, g.shop_price * '$_SESSION[discount]') AS shop_price, g.promote_price, " .
                'g.promote_start_date, g.promote_end_date, g.goods_brief, g.seller_note,  g.img_special,  g.goods_desc_short, g.goods_thumb , g.goods_img ' .
                'FROM ' . $GLOBALS['ecs']->table('goods') . ' AS g ' .
                'LEFT JOIN ' . $GLOBALS['ecs']->table('member_price') . ' AS mp ' .
                "ON mp.goods_id = g.goods_id AND mp.user_rank = '$_SESSION[user_rank]' " .
                "WHERE g.is_on_sale = 1 AND " . db_create_in($goods_id, 'g.goods_id');

    $res = $GLOBALS['db']->query($sql);

    $sort_goods_arr = array();

    while ($row = $GLOBALS['db']->fetchRow($res))
    {
        if ($row['promote_price'] > 0)
        {
            $promote_price = bargain_price($row['promote_price'], $row['promote_start_date'], $row['promote_end_date']);
            $row['promote_price'] = $promote_price > 0 ? price_format($promote_price) : '';
            if($promote_price > 0){
                $row['rate_sale'] =  100-round(($promote_price/$row['shop_price'])*100, 0);

                $discount = $promote_price - $row['shop_price'];
                $row['discount'] = price_format($discount);

            }

        }
        else
        {
            $row['promote_price'] = '';
        }

        if($row['cat_id'] == 1 || $row['cat_id'] == 2 || $row['cat_id'] == 3){
            $row['istragop'] = 1;
        }
        $row['shop_price'] =  $row['shop_price'] > 0 ? price_format($row['shop_price']) : 0 ;
        $row['tragop_price'] =  $row['tragop_price'] > 0 ? price_format($row['tragop_price']) : $row['shop_price'] ;
        $row['is_hot']       = $row['is_hot'];
        $row['is_best']      = $row['is_best'];
        $row['is_special']   = $row['is_special'];
        $row['is_os']        = $row['is_os'];
        $row['goods_thumb2col']  = $row['img_special'];
        $row['url']              = build_uri('goods', array('gid'=>$row['goods_id']), $row['goods_name']);
        $row['goods_style_name'] = add_style($row['goods_name'], $row['goods_name_style']);
        $row['short_name']       = $GLOBALS['_CFG']['goods_name_length'] > 0 ?
                                    sub_str($row['goods_name'], $GLOBALS['_CFG']['goods_name_length']) : $row['goods_name'];
        $row['goods_thumb']      = get_image_path($row['goods_id'], $row['goods_thumb'], true);
        $row['short_style_name'] = add_style($row['short_name'], $row['goods_name_style']);
        $row['short_desc']   = nl2br($row['goods_desc_short']);
        $row['text_status']   = $row['seller_note'];
        foreach ($arr AS $key => $value)
        {
            foreach ($value AS $val)
            {
                if ($val == $row['goods_id'])
                {
                    $key = $key == 'default' ? $_LANG['all_goods'] : $key;
                    $sort_goods_arr[$key][] = $row;
                }
            }
        }
    }
    $sort_goods_arr =  checkFullFeaturedGrid($sort_goods_arr,5);
    /* 模板赋值 */
    assign_template();
    $position = assign_ur_here();
    #$smarty->assign('page_title',       $topic['title'].' | '.$position['title']);       // 页面标题
    $smarty->assign('page_title',       $topic['title']);       // 页面标题
    $smarty->assign('ur_here',          $position['ur_here'] . ' > ' . $topic['title']);     // 当前位置
    $smarty->assign('show_marketprice', $_CFG['show_marketprice']);
    $smarty->assign('sort_goods_arr',   $sort_goods_arr);          // 商品列表
    $smarty->assign('topic',            $topic);                   // 专题信息
    $smarty->assign('keywords',         $topic['keywords']);       // 专题信息
    $smarty->assign('description',      $topic['description']);    // 专题信息
    $smarty->assign('title_pic',        $topic['title_pic']);      // 分类标题图片地址
    $smarty->assign('base_style',       '#' . $topic['base_style']);     // 基本风格样式颜色
    $smarty->assign('request_uri',        ltrim($_SERVER['REQUEST_URI'],"/"));

    //$template_file = empty($topic['template']) ? 'topic.dwt' : $topic['template'];
}
$smarty->display($display_mode, $cache_id);
/*start seo*/
function checkFullFeaturedGrid( $goodslist,$col=5)
{
    $cell = 0;
    $prev_key = 0;
    foreach($goodslist as $key =>$goods)
    {   
        if($cell>=$col) 
            $cell=1;
        else
            $cell++;     
         if($goods['is_special'])
         {
            
            if($cell+1>$col) 
            { 
                 
               /* if(!$goodslist[$prev_key]['is_special'])
                {
                    $tmp = $goodslist[$key] ;
                    $goodslist[$key]=$goodslist[$prev_key];
                    $goodslist[$prev_key]=$tmp;
                }
                else*/
                    $goodslist[$key]['is_special']=0;
            }
                
            else
            {
                $cell++;$goodslist[$key]['is_special']=1;
            }
                
            
         }
        
         $prev_key=$key;
          
            
    }
     
    
     
     
        
    return $goodslist;
}
/*end seo*/

?>