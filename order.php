<?php

define('IN_ECS', true);


require(dirname(__FILE__) . '/includes/init.php');
require(ROOT_PATH . 'includes/lib_order.php');
$smarty->caching = false;

if ($_REQUEST['step'] == 'preorder')
{
    include_once(ROOT_PATH .'includes/cls_json.php');
    $_POST['goods']=strip_tags(urldecode($_POST['goods']));
    $_POST['goods'] = json_str_iconv($_POST['goods']);


    $_POST['customer']=strip_tags(urldecode($_POST['customer']));
    $_POST['customer'] = json_str_iconv($_POST['customer']);

    $json  = new JSON;

    $goods = $json->decode($_POST['goods']);
    $customer = $json->decode($_POST['customer']);

    $goods->goods_url = rtrim($goods->goods_url,'/dat-truoc');

    //insert customer Table
    $sql = "INSERT INTO " . $ecs->table('customer') . "(customer_name, customer_mobile, customer_address, customer_sex, customer_province)" .
                                              " VALUES ('$customer->name', '$customer->mobile', '', '$customer->sex', '')";
    $db->query($sql);
    $customer_id = $db->insert_id();
    //Xu li order_code, order type

     $error_no = 0;
     do
     {
     // Lay customer_id + 1 de duoc ID moi
    $order_code = get_order_sn();
    // $order_type: 0|PreOder, 1|Order, 2|ShockOrder
    // Insert Order Table
         $sql = "INSERT INTO " . $ecs->table('order') . "(order_code, order_type, customer_id,  add_time, order_device, order_note, shipmethod, paymethod)" .
                                               " VALUES ('$order_code', '3', '$customer_id', '".gmtime()."', '', '', '', '')";
         $db->query($sql);

        $error_no = $db->errno();

        if ($error_no > 0 && $error_no != 123)
        {
            die($db->errorMsg());
        }
    }
    while ($error_no == 123);
    // Khoi tao id Don hang moi
    $order_id = $db->insert_id();
    // Insert Order Table
    $sql = "INSERT INTO " . $ecs->table('order_product') . "(order_id, giasoc_id, goods_id, product_sn, product_url, product_name, product_price, product_number, product_gift, product_attr)" .
                                               " VALUES ('$order_id', '', '$goods->goods_id', '$goods->goods_sn', '$goods->goods_url', '$goods->goods_name', '$goods->goods_rprice', 1, '$goods->goods_gift', '$goods->goodsattr')";

    $res = $db->query($sql);

     if(!empty($res)){
        $msg_success = 'Đặt trước thành công !<br/>Chúng tôi sẽ liên hệ ngay khi có hàng.';
        $result = array('error' => 0, 'message' => $msg_success, 'link' => $goods->goods_url);
        //SMS Auto for CSKH 1:Thuong|2:Gia soc|3:Dattruoc
        $allow_sendsms = false;
        if($allow_sendsms == true){
            //require(ROOT_PATH . '/includes/SpeedSMSAPI.php');
            $phones = array('0905775772'); //0905775772
            //$phones[] = $customer->mobile;
            $csex  = $customer->sex == 1 ?'a.':'c.';
            $cname = $customer->name;
            $sp = substr($goods->goods_name,0,50);

            $content = 'Preorder '.$csex.$cname.', '.$customer->mobile.', '.$sp;
            $content = utf8convert($content); //chuyen qua ko dau
            $sms = new SpeedSMSAPI();
            $sms->sendSMS($phones, $content);
        }


    }else{
        $msg_error= 'Đặt hàng không thành công ! <br/> Vui lòng kiểm tra lại thông tin.';
        $result = array('error' => 1, 'message' => $msg_error);
    }

    die($json->encode($result));
}
elseif ($_REQUEST['step'] == 'goituvan')
{
    include_once(ROOT_PATH .'/includes/cls_json.php');

    $_POST['goods']=strip_tags(urldecode($_POST['goods']));
    $_POST['goods'] = json_str_iconv($_POST['goods']);

    $result = array('error' => 0, 'message' => '', 'content' => '', 'goods_id' => '');
    $json  = new JSON();

    if (empty($_POST['goods']))
    {
        $result['error'] = 1;
        die($json->encode($result));
    }
    $goods = $json->decode($_POST['goods']);
    $goods->url = rtrim($goods->url,'/tra-gop');
    // chen vao tablet feedback
    $sql = "INSERT INTO " . $ecs->table('tuvan') . "(cname, cmobile, region_id, product, add_time)" .
            " VALUES ('$goods->cname', '$goods->cmobile', '$goods->province', '$goods->goodsname', '" . gmtime() . "')";
    $db->query($sql);

    $time = date('H:i:s');
    $timeclose = '18:00:00';
    $timeopen  = '08:00:00';
    //send SMS ngoai gio hanh chinh
    if($time >= $timeclose && $time <= $timeopen){
        $kh = utf8convert($goods->cname);
        $tinh = $db->getOne("SELECT region_name FROM ".$ecs->table('region')." WHERE region_id = $goods->province");
        $sp = substr($goods->goodsname,0,50);
        $phones = array('0938331100'); //0938331100
        $content = "KH {$kh} - {$goods->cmobile} - {$tinh}, Tu van TG {$sp}";
        $content = utf8convert($content);
        /*include_once(ROOT_PATH . '/includes/SpeedSMSAPI.php');
        $sms = new SpeedSMSAPI();
        $return = $sms->sendSMS($phones, $content);*/
        //$return['status'].$return['code'].$return['message']
        // End send sms
    }

    $msg_success = 'Bạn đăng ký mua trả góp sản phẩm <br/>'.$goods->goodsname.' thành công !<br/>Chúng tôi sẽ gọi lại tư vấn, làm hồ sơ ngay khi đọc tin.';
    $result = array('message' => $msg_success);

    die($json->encode($result));
}
elseif ($_REQUEST['step'] == 'add_cart'){
    include_once('includes/cls_json.php');
    $_POST['goods']=strip_tags(urldecode($_POST['goods']));
    $_POST['goods'] = json_str_iconv($_POST['goods']);

    $result = array('error' => 0, 'message' => '', 'content' => '', 'goods_id' => '');
    $json  = new JSON;

    if (empty($_POST['goods']))
    {
        $result['error'] = 1;
        die($json->encode($result));
    }
    $goods = $json->decode($_POST['goods']);

    //tao cookie
    $gid = array();
    $gid[] = $goods->goods_id;
    setcookie('pid', $gid , time()+ (84000*30));

    $result['message'] = 'Đã thêm vào giỏ hàng';
    die($json->encode($result));
}



 ?>

