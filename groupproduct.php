<?php

/**
 * @author Jerry Nguyen
 * @copyright 2017
 */

define('IN_ECS', true);

require(dirname(__FILE__) . '/includes/init.php');

if ((DEBUG_MODE & 2) != 2)
{
    $smarty->caching = false;
}

if(empty($_REQUEST['slug']))    
{
    ecs_header("Location: ./\n");
    exit;
}

$get=$db->getRow("SELECT * FROM ".$ecs->table('groupproduct')." WHERE group_slug ='".$_REQUEST['slug']."'");
if(empty($get))
{
    ecs_header("Location: ./\n");
    exit;
} 
 $cache_id = sprintf('%X', crc32('group-'. $_REQUEST['slug'] .'-' . $_CFG['lang'].'-'.$_COOKIE['client']));
 if (!$smarty->is_cached($display_mode, $cache_id))
 {
     $list_goods_result = array();
    if($list_goods= unserialize($get['data']))
    {
       
            foreach(explode(",",$list_goods) as $k=> $good_id) 
            {
                    $goods=get_goods_info($good_id);
                   
                    if ($goods['promote_price'] > 0 && $goods['promote_price']!=$goods['shop_price'])
                    { 
                         
                        $promote_price = bargain_price($goods['promote_price'], $goods['promote_start_date'], $goods['promote_end_date']);
                        
                        $goods['promote_price'] = $promote_price > 0 ? ($promote_price) : '';
                      
                        if($promote_price > 0){
                        $goods['rate_sale'] =  100-round(($promote_price/$goods['shop_price'])*100, 0);
                        }
                        $discount = $promote_price - $goods['shop_price'];
                    }
                    else
                    {
                        $goods['promote_price'] = '';
                        $discount = '';
                    }
                    $goods['shop_price']   = price_format($goods['shop_price']);
                    $goods['goods_desc_short']=nl2p($goods['goods_desc_short']);
                    $goods['goods_gift']=nl2p($goods['goods_gift']);
                    $goods['is_best']      = $goods['is_best'];
                    $goods['is_hot']       = $goods['is_hot'];
                    $goods['is_new']       = $goods['is_new'];
                    $goods['is_os']        = $goods['is_os'];
                    $goods['is_promote']   = $goods['is_promote'];
                    $goods['text_status']  = $goods['seller_note'];
                    $goods['goods_thumb2col']  = $goods['img_special'];
                    $goods['stock']        = $goods['goods_number'];
                    $goods['url']          = build_uri('goods', array('gid' => $goods['goods_id']), $goods['goods_name']);
                    $list_goods_result[]=$goods;
                     
            }
         
    }
    $position= assign_ur_here();
    $position['ur_here'].=' › <li property="itemListElement" typeof="ListItem"><a property="item" typeof="WebPage" href="g/'.$_REQUEST['slug'].'"><span property="name">' .

                                $get['group_name'] . '</span></a> <meta property="position" content="2"></li>';
     $smarty->assign('ur_here',      $position['ur_here']);  // 当前位置
     $smarty->assign('page_title',       $get['meta_title']); 
      $smarty->assign('goods_list',$list_goods_result);
     $smarty->assign('keywords',    htmlspecialchars($get['keywords']));
     $smarty->assign('description',    htmlspecialchars($get['description']));
     assign_template();
 }
 $smarty->display($display_mode, $cache_id);
?>