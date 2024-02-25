<?php
define('IN_ECS', true);
require(dirname(__FILE__) . '/includes/init.php');
$smarty->caching = false;

if ($_REQUEST['step'] == 'cart'){

    //lay thong tin san pham
    $goods_list = array();
    $total = 0;
    foreach ($_SESSION['cart'] as $k => $v) {
        $sql = "SELECT goods_id, goods_sn, goods_name, shop_price, promote_price, online_price, promote_start_date, promote_end_date, goods_thumb, goods_gift FROM " . $ecs->table('goods') ." WHERE goods_id = $k LIMIT 1";
        $res = $db->getRow($sql);

        if ($res['promote_price'] > 0)
        {
            $promote_price = bargain_price($res['promote_price'], $res['promote_start_date'], $res['promote_end_date']);
            $total += $promote_price;
            $goods_list[$k]['promote_price']   = $promote_price;
            $goods_list[$k]['promote_price_f'] = price_format($promote_price);
        }else{
            $promote_price = 0;
            $total += $res['shop_price'];


        }

        $goods_list[$k]['goods_id']        = $res['goods_id'];
        $goods_list[$k]['goods_sn']        = $res['goods_sn'];
        $goods_list[$k]['goods_name']      = $res['goods_name'];
        $goods_list[$k]['shop_price']      = $res['shop_price'];
        $goods_list[$k]['shop_price_f']    = price_format($res['shop_price']);
        $goods_list[$k]['url']             = build_uri('goods', array('gid'=>$res['goods_id']), $res['goods_name']);
        $goods_list[$k]['gift']            = $res['goods_gift'];
        $goods_list[$k]['thumb']           = $res['goods_thumb'];
        $goods_list[$k]['soluong']         = $v;
        if($res['online_price'] > 0){
            $goods_list[$k]['online_price'] = $res['online_price'];
        }


        $properties = get_goods_properties($k);
        $goods_list[$k]['specification'] = $properties['spe'];
    }

    //var_dump($goods_list);
    $smarty->assign('goods_list', $goods_list);
    $smarty->assign('total', price_format($total));

    // dia chi shop
    $region = get_regions(1,1);
    $region_list = array();
    foreach ($region as $row) {
        $region_list[$row['region_id']] = $row['region_name'];
    }
    $smarty->assign('region_list', $region_list);
    $smarty->display($display_mode);
}
elseif ($_REQUEST['step'] == 'done'){
    if(isset($_POST['btnorder'])){
        $goods = $_POST['Cart'];
        $customer = $_POST['Consegine'];

        print_r($goods);
        print_r($customer);
    }
}
elseif ($_REQUEST['step'] == 'del_item'){
    include_once(ROOT_PATH .'includes/cls_json.php');
    $_POST['goods']=strip_tags(urldecode($_POST['goods']));
    $_POST['goods'] = json_str_iconv($_POST['goods']);

    $result = array('error' => 0, 'message' => '', 'content' => '', 'goods_id' => '');
    $json  = new JSON;
    $goods = $json->decode($_POST['goods']);

    //xoa item trong gio hang
    unset ($_SESSION['cart'][$goods->goods_id]);

    $result['goods_id'] = $goods->goods_id;
    die($json->encode($result));

}

 ?>