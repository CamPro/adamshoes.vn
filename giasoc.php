<?php
define('IN_ECS', true);
require(dirname(__FILE__) . '/includes/init.php');

// if ((DEBUG_MODE & 2) != 2)
// {
//     $smarty->caching = true;
// }
$smarty->caching = false;

if($_REQUEST['giasoc_id']){
  $giasoc_id  = empty($_REQUEST['giasoc_id']) ? 0 : intval($_REQUEST['giasoc_id']);
}else{
    $slug =  empty($_REQUEST['slug']) ? '' : $_REQUEST['slug'];
    if($slug == ''){
        $giasoc_id = 0;
    }else{
        $giasoc_id=$db->getOne("select giasoc_id from ". $ecs->table('giasoc') ." where giasoc_slug='$slug' limit 0,1");
    }
}

$cache_id = sprintf('%X', crc32($_SESSION['user_rank'] . '-' . $_CFG['lang'] . '-gia-soc-'.$_COOKIE['client']));

if (!$smarty->is_cached($display_mode, $cache_id))
{
    $sql = "SELECT * FROM " . $ecs->table('giasoc') . " WHERE giasoc_id = '$giasoc_id'";

    $giasoc = $db->getRow($sql);
    $giasoc['data'] = addcslashes($giasoc['data'], "'");
    $tmp = @unserialize($giasoc["data"]);
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

    $sql = 'SELECT g.goods_id, g.goods_sn, g.giasoc_id, g.goods_name, g.goods_name_style, g.market_price, g.is_new, g.is_best, g.is_hot, g.is_os, g.is_special, g.shop_price AS org_price, ' .
                "IFNULL(mp.user_price, g.shop_price * '$_SESSION[discount]') AS shop_price, g.promote_price,  g.online_price, " .
                'g.promote_start_date, g.promote_end_date, g.goods_brief, g.seller_note,  g.img_special,  g.goods_desc_short, g.goods_thumb , g.goods_img ' .
                'FROM ' . $GLOBALS['ecs']->table('goods') . ' AS g ' .
                'LEFT JOIN ' . $GLOBALS['ecs']->table('member_price') . ' AS mp ' .
                "ON mp.goods_id = g.goods_id AND mp.user_rank = '$_SESSION[user_rank]' " .
                "WHERE " . db_create_in($goods_id, 'g.goods_id');

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
            }

            if ($row['online_price'] > 0){
                $row['online_raw'] = $promote_price - $row['online_price'];
                $row['online_price'] = price_format($row['online_raw']);
            }

        }
        else
        {
            $row['promote_price'] = '';

            if ($row['online_price'] > 0){
                $row['online_raw'] = $row['shop_price'] - $row['online_price'];
                $row['online_price'] = price_format($row['online_raw']);
            }
        }



        $row['raw_price'] = $promote_price > 0 ?  $promote_price : $row['shop_price'];

        $row['shop_price'] =  $row['shop_price'] > 0 ? price_format($row['shop_price']) : 0 ;

        $row['goods_id']     = $row['goods_id'];
        $row['goods_sn']     = $row['goods_sn'];
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

    //get list da mua
    $sql = "SELECT p.product_name, o.add_time, c.customer_name, c.customer_mobile,c.customer_address, r.region_name " .
    " FROM " . $GLOBALS['ecs']->table('customer') . " AS c " .
    " LEFT JOIN " .$GLOBALS['ecs']->table('region'). " AS r ON r.region_id=c.customer_province ".
    " LEFT JOIN " .$GLOBALS['ecs']->table('order'). " AS o ON o.customer_id=c.customer_id ".
    " LEFT JOIN " .$GLOBALS['ecs']->table('order_product'). " AS p ON p.order_id= o.order_id ".
    " WHERE p.giasoc_id = $giasoc_id AND o.order_type = 2 ORDER BY o.add_time DESC ";
    $arr = $db->getAll($sql);
    $customer = array();
    foreach ($arr AS $key => $value)
    {
        $customer[$key]['customer_name']   =  $value['customer_name'];
        $xxxx = substr($value['customer_mobile'],  0, 4);
        $duoi = ltrim($value['customer_mobile'], $xxxx);
        $customer[$key]['customer_mobile'] = 'xxxx'. $duoi;
        $customer[$key]['goods_name']      =  $value['product_name'];
        $customer[$key]['add_time'] = local_date($GLOBALS['_CFG']['time_format'], $value['add_time']);
    }
    $smarty->assign('total_customer', count($customer));
    $smarty->assign('customer', $customer);

    assign_template();
    $position = assign_ur_here();
    $smarty->assign('page_title',       $giasoc['giasoc_name'].' | '.$position['title']);       // 页面标题
    $smarty->assign('ur_here',          $position['ur_here'] . ' > ' . $giasoc['giasoc_name']);     // 当前位置
    $smarty->assign('show_marketprice', $_CFG['show_marketprice']);
    $smarty->assign('sort_goods_arr',   $sort_goods_arr);          // 商品列表
    $smarty->assign('giasoc',            $giasoc);                   // 专题信息
    $smarty->assign('keywords',         $giasoc['keywords']);       // 专题信息
    $smarty->assign('description',      $giasoc['description']);    // 专题信息
    $smarty->assign('request_uri',        ltrim($_SERVER['REQUEST_URI'],"/"));

}
$smarty->display($display_mode, $cache_id);

 ?>