<?php

define('IN_ECS', true);

require(dirname(__FILE__) . '/includes/init.php');



if ($_REQUEST['act'] == 'list')

{

    admin_priv('order_view');

    $smarty->assign('ur_here', $_LANG['02_order_list']);

     $smarty->assign('full_page',        1);



    $order_list = order_list();



    //get Province

   $province = get_regions(1,1);

   $smarty->assign('province',   $province);



    $smarty->assign('order_list',   $order_list['orders']);

    $smarty->assign('filter',       $order_list['filter']);

    $smarty->assign('record_count', $order_list['record_count']);

    $smarty->assign('page_count',   $order_list['page_count']);

    $smarty->assign('sort_order_time', '<img src="images/sort_desc.gif">');



    /* 显示模板 */

    assign_query_info();

   $page = $_mobile == 1 ? 'order_list_mobile.htm' : 'order_list.htm';

   $smarty->display($page);



}

elseif ($_REQUEST['act'] == 'export')

{

    $smarty->caching = false;

    $order_type = isset($_REQUEST['order_type']) ? intval($_REQUEST['order_type']) : 0;

    $customer_province = isset($_REQUEST['customer_province']) ? intval($_REQUEST['customer_province']) : '';



    $start_date = empty($_REQUEST['start_date']) ? '' : $_REQUEST['start_date'].' 00:00:00';

    $end_date = empty($_REQUEST['end_date']) ? '' : $_REQUEST['end_date'].' 23:59:59';



    if(empty($start_date) || empty($end_date)){

        sys_msg('Vui lòng chọn lại thời gian', 1, array(), false);

    }



    $start_date = local_strtotime($start_date);

    $end_date = local_strtotime($end_date);



    $where = '1';

    if ($order_type)

    {

        $where .= " AND o.order_type  = $order_type";

    }

     if ($customer_province)

    {

        $where .= " AND c.customer_province = $customer_province";

    }

    if(!empty($start_date) && !empty($end_date)){

        $where .= " AND o.add_time >= $start_date AND o.add_time <= $end_date";

    }



    $sql = "SELECT o.order_id, o.order_type, o.order_code, o.add_time, o.order_status, o.order_note, o.admin_note, c.customer_name, c.customer_mobile, c.customer_address, r.region_name ".

               " FROM " .$ecs->table('customer'). " AS c".

               " LEFT JOIN " .$ecs->table('region'). " AS r ON r.region_id=c.customer_province ".

               " LEFT JOIN " .$ecs->table('order'). " AS o ON o.customer_id=c.customer_id ".

               " WHERE $where";

    $row = $db->getAll($sql);

    foreach ($row AS $key => $value)

    {



        $row[$key]['order_id'] = $value['order_id'];

        $row[$key]['order_code'] = $value['order_code'];



        $sql = "SELECT product_name FROM ". $GLOBALS['ecs']->table('order_product'). " WHERE order_id = $value[order_id]";

        $res = $GLOBALS['db']->getAll($sql);



        $produc_name = '';

        foreach ($res as $p) {

           $produc_name .= $p['product_name']."\n";

        }

        $row[$key]['customer_mobile'] = "'".$value['customer_mobile'];

        $row[$key]['order_goods'] = $produc_name;



        $row[$key]['order_type'] = get_type_order($value['order_type']);

        $row[$key]['order_status'] = get_order_status($value['order_status']);



        $row[$key]['add_time'] = local_date($GLOBALS['_CFG']['time_format'], $value['add_time']);



    }

    //var_dump($row);

    export_csv($row, 'order_list.csv');



}

elseif ($_REQUEST['act'] == 'info')

{

    if (isset($_REQUEST['order_id']))

    {

        $order_id = intval($_REQUEST['order_id']);

        $sql = "SELECT o.order_id, o.order_type, o.order_code, o.add_time, o.order_status, o.order_note, o.admin_note,o.order_total,o.order_ship_fee,order_subtotal, c.customer_name, c.customer_mobile, c.customer_address, r.region_name ".

               " FROM " .$ecs->table('customer'). " AS c".

               " LEFT JOIN " .$ecs->table('region'). " AS r ON r.region_id=c.customer_province ".

               " LEFT JOIN " .$ecs->table('order'). " AS o ON o.customer_id=c.customer_id ".

               " WHERE o.order_id = $order_id";

        $order = $db->getRow($sql);

    }

     else

    {

        die('invalid parameter');

    }

     if (empty($order))

    {

        die('order does not exist');

    }

    admin_priv('order_view');



    $order['order_type'] = get_type_order($order['order_type']);

    $order['order_status_num'] = $order['order_status'];

    $order['order_status'] = get_order_status($order['order_status']);

    $order['add_time'] = local_date($GLOBALS['_CFG']['time_format'], $order['add_time']);



    // goods list

    $sql = "SELECT * FROM " . $ecs->table('order_product'). " WHERE order_id = $order_id";

    $res = $db->getAll($sql);



    $goods_list = array();

    foreach ($res as $idx => $row) {

       $goods_list[$idx]['product_name'] = $row['product_name'];

       $goods_list[$idx]['product_sn'] = $row['product_sn'];

       $goods_list[$idx]['product_url'] = $row['product_url'];

       $goods_list[$idx]['product_gift'] = $row['product_gift'];

       $goods_list[$idx]['product_attr'] = $row['product_attr'];

       $goods_list[$idx]['product_number'] = $row['product_number'];

       $goods_list[$idx]['product_price'] = price_format($row['product_price']);

    }



    //get Action admin

    $action = array();

    $sql = "SELECT action_user, order_status, action_note, log_time FROM " . $ecs->table('order_action'). " WHERE order_id = $order_id ORDER BY log_time DESC";

    $res = $db->getAll($sql);

    //print_r($res);

    foreach ($res as $key => $val) {

        $action[$key]['action_user']  = $val['action_user'];

        $action[$key]['order_status'] = get_order_status($val['order_status']);

        $action[$key]['action_note']  = $val['action_note'];

        $action[$key]['add_time'] = local_date($GLOBALS['_CFG']['time_format'], $val['log_time']);

    }

    $smarty->assign('action', $action);

    $smarty->assign('goods_list', $goods_list);
    $order['order_subtotal']=price_format($order['order_subtotal']);
    $order['order_ship_fee']=price_format($order['order_ship_fee']);
    $order['order_total']=price_format($order['order_total']);
    $smarty->assign('order', $order);

    $smarty->assign('ur_here', $_LANG['order_info']);

    $smarty->assign('action_link', array('href' => 'order.php?act=list', 'text' => $_LANG['02_order_list']));



    assign_query_info();

    $page = $_mobile == 1 ? 'order_info_mobile.htm' : 'order_info.htm';

    $smarty->display($page);



}

elseif ($_REQUEST['act'] == 'comfirm')

{

    if(isset($_POST['sendsms'])){

        require(ROOT_PATH . '/includes/SpeedSMSAPI.php');

        $phones = array();

        $phones[] = $_POST['customer_mobile'];

        $order_code = $_POST['order_code'];

        $order_status = $_POST['order_status'];

        $order_id = !empty($_POST['order_id']) ? intval($_POST['order_id']) : 0;

        $content = "Bachkhoashop.com - Dat hang thanh cong! Ma don hang: $order_code, Xin cam on.";



        $sms = new SpeedSMSAPI();

        if($order_status == 2){

            $return = $sms->sendSMS($phones, $content);

        }



        $url = "order.php?act=info&order_id=$order_id";

        $link[] = array('href' => $url, 'text' => $_LANG['order_info']);



        $sms_status = $return['status'] == 'success' ? 'Gửi SMS thành công' : 'Gửi SMS thất bại';

        $log_time = gmtime();

        //acion admin

        $username = $_SESSION['admin_name'];

        $sql = "INSERT INTO " .$ecs->table('order_action'). "(order_id, action_user, order_status, action_note, log_time)".

                                    " VALUES ('$order_id', '$username', '$order_status','$sms_status', '$log_time')";

        $db->query($sql);



        sys_msg($sms_status, 0, $link);



    }else{

        $admin_note = !empty($_POST['admin_note']) ? $_POST['admin_note'] : '';

        $order_id = !empty($_POST['order_id']) ? intval($_POST['order_id']) : 0;

        $order_status = !empty($_POST['new_order_status']) ? intval($_POST['new_order_status']) : 0;

        $log_time = gmtime();



        if($order_status == 0){

            sys_msg('Chưa chọn trạng thái xác nhận', 1, array(), false);

        }



       $sql = "UPDATE " .$ecs->table('order'). " SET order_status = '$order_status', admin_note = '$admin_note' WHERE order_id = '$order_id'";

       $db->query($sql);



       //order Action

       $username = $_SESSION['admin_name'];

       $sql = "INSERT INTO " .$ecs->table('order_action'). "(order_id, action_user, order_status, action_note, log_time)".

                                    " VALUES ('$order_id', '$username', '$order_status', '$admin_note', '$log_time')";

       $db->query($sql);



       $url = "order.php?act=info&order_id=$order_id";

       $link[] = array('href' => $url, 'text' => $_LANG['order_info']);

       sys_msg('Xác nhận thành công', 0, $link);

    }





}

elseif ($_REQUEST['act'] == 'query')

{

    /* 检查权限 */

    admin_priv('order_view');



    $order_list = order_list();



    $smarty->assign('order_list',   $order_list['orders']);

    $smarty->assign('filter',       $order_list['filter']);

    $smarty->assign('record_count', $order_list['record_count']);

    $smarty->assign('page_count',   $order_list['page_count']);

    $sort_flag  = sort_flag($order_list['filter']);

    $smarty->assign($sort_flag['tag'], $sort_flag['img']);

    make_json_result($smarty->fetch('order_list.htm'), '', array('filter' => $order_list['filter'], 'page_count' => $order_list['page_count']));

}

elseif ($_REQUEST['act'] == 'operate')

{

    admin_priv('order_os_edit');



    if (isset($_POST['remove']))

    {

        admin_priv('order_remove');

        $order_id = $_POST['checkboxes'];

        $where_order = db_create_in($order_id, 'order_id');

        $sql = "SELECT customer_id FROM " .$ecs->table('order'). " WHERE $where_order";

        $res = $db->getAll($sql);

        $customer_id = array();

        foreach ($res as $key => $val) {

             $customer_id[] = $val['customer_id'];

        }

        $where_customer = db_create_in($customer_id, 'customer_id');



        $db->query("DELETE FROM ".$ecs->table('order'). " WHERE $where_order");

        $db->query("DELETE FROM ".$ecs->table('order_product'). " WHERE  $where_order");

        $db->query("DELETE FROM ".$ecs->table('customer'). " WHERE  $where_customer");



        admin_log($order_id, 'remove', 'order');



    }

    elseif(isset($_POST['merge'])){

        $order_id = $_POST['checkboxes'];

        $first_order = $order_id[0];

        //print_r($order_id);

         // Gop khi chon > 2 don hang

        $tt_m = count($order_id);

        if($tt_m >= 2){

            $mobile = get_mobile($first_order); // lay don hang dau tien

            //i=1 thi chi so sanh cac phan tu con lai trong mang voi phan tu dau tien

            for ($i=1; $i < $tt_m; $i++) {

                $mobile2 = get_mobile($order_id[$i]); // lay don hang dau tien

                if($mobile == $mobile2){

                    $db->query("UPDATE ".$ecs->table('order_product') . " SET order_id = $first_order WHERE order_id = $order_id[$i] ");

                    //Xo don hang

                    $db->query("DELETE FROM ".$ecs->table('order'). " WHERE order_id = $order_id[$i]");

                }else{

                    sys_msg('Đơn hàng không khớp số mobile', 1, array(), false);

                }

            }

         }else{

            sys_msg('Chỉ gộp khi chọn từ 2 đơn hàng', 1, array(), false);

         }

        //admin_log($order_id, 'merge', 'order');

    }

    elseif(isset($_REQUEST['order_id'])){

         admin_priv('order_remove');

         $order_id =  intval($_REQUEST['order_id']);

         $where_order = " order_id = '$order_id'";

         $customer_id = $db->getOne("SELECT customer_id FROM " .$ecs->table('order'). " WHERE order_id = '$order_id'");

         $where_customer = " customer_id = '$customer_id'";



        $db->query("DELETE FROM ".$ecs->table('order'). " WHERE $where_order");

        $db->query("DELETE FROM ".$ecs->table('order_product'). " WHERE $where_order");

        $db->query("DELETE FROM ".$ecs->table('customer'). " WHERE $where_customer");



    }



    clear_cache_files();



    if (!empty($_REQUEST['is_ajax']))

    {

        $url = 'order.php?act=query&' . str_replace('act=operate', '', $_SERVER['QUERY_STRING']);

        ecs_header("Location: $url\n");

        exit;

    }



    $links[] = array('href' => 'order.php?act=list', 'text' =>  $_LANG['02_order_list']);

    sys_msg($_LANG['act_ok'], 0, $links);

}

function get_mobile($id)

{

    $sql = "SELECT c.customer_mobile ".

              " FROM " .$GLOBALS['ecs']->table('customer'). " AS c".

              " LEFT JOIN " .$GLOBALS['ecs']->table('order'). " AS o ON o.customer_id=c.customer_id ".

              " WHERE o.order_id = $id";

    return  $GLOBALS['db']->getOne($sql);

}



function order_list(){

    $result = get_filter();

    if ($result === false)

    {

        $filter['customer_mobile'] = empty($_POST['mobile']) ? '' : trim($_POST['mobile']);

        $filter['order_status'] = isset($_REQUEST['order_status']) ? intval($_REQUEST['order_status']) : 0;

        $filter['order_type'] = isset($_REQUEST['order_type']) ? intval($_REQUEST['order_type']) : 0;

        $filter['customer_province'] = isset($_REQUEST['customer_province']) ? intval($_REQUEST['customer_province']) : 0;

        $filter['sort_by']      = empty($_REQUEST['sort_by']) ? 'add_time' : trim($_REQUEST['sort_by']);

        $filter['sort_order']   = empty($_REQUEST['sort_order']) ? 'DESC' : trim($_REQUEST['sort_order']);



        $filter['start_time'] = empty($_REQUEST['start_date']) ? '' : $_REQUEST['start_date'].' 00:00:00';

        $filter['end_time'] = empty($_REQUEST['end_date']) ? '' : $_REQUEST['end_date'].' 23:59:59';



        $where = 'WHERE 1 ';

         if ($filter['customer_mobile'])

        {

            $where .= " AND c.customer_mobile = '$filter[customer_mobile]'";

        }



        if ($filter['customer_province'])

        {

            $where .= " AND c.customer_province = '$filter[customer_province]'";

        }

        if ($filter['order_status'] != 0)

        {

            $where .= " AND o.order_status  = '$filter[order_status]'";

        }

        if ($filter['order_type'])

        {



            $where .= " AND o.order_type  = '$filter[order_type]'";

        }

        if ($filter['start_time'])

        {

            $filter['start_time'] = local_strtotime($filter['start_time']);

            $where .= " AND o.add_time >= '$filter[start_time]'";

        }

        if ($filter['end_time'])

        {

            $filter['end_time'] = local_strtotime($filter['end_time']);

            $where .= " AND o.add_time <= '$filter[end_time]'";

        }

        // render again input selected

        $GLOBALS['smarty']->assign('customer_province', $_REQUEST['customer_province']);

        $GLOBALS['smarty']->assign('order_type', $_REQUEST['order_type']);

        $GLOBALS['smarty']->assign('end_date', $_REQUEST['end_date']);

        $GLOBALS['smarty']->assign('start_date', $_REQUEST['start_date']);



        $sql = "SELECT COUNT(*) FROM " . $GLOBALS['ecs']->table('order') . " AS o ".

                    " LEFT JOIN " .$GLOBALS['ecs']->table('customer'). " AS c ON c.customer_id=o.customer_id ". $where;



        $filter['record_count']   = $GLOBALS['db']->getOne($sql);

        $filter = page_and_size($filter);



        $arr = array();

            $sql = "SELECT o.order_id, o.order_type, o.order_code, o.add_time, o.order_status, c.customer_name, c.customer_mobile,c.customer_address, r.region_name " .

                    " FROM " . $GLOBALS['ecs']->table('customer') . " AS c " .

                    " LEFT JOIN " .$GLOBALS['ecs']->table('region'). " AS r ON r.region_id=c.customer_province ".

                    " LEFT JOIN " .$GLOBALS['ecs']->table('order'). " AS o ON o.customer_id=c.customer_id ". $where .

                    " ORDER BY $filter[sort_by] $filter[sort_order] ".

                    " LIMIT " . ($filter['page'] - 1) * $filter['page_size'] . ",$filter[page_size]";



        set_filter($filter, $sql);



    }else{

        $sql    = $result['sql'];

        $filter = $result['filter'];

    }



    $row = $GLOBALS['db']->getAll($sql);



    foreach ($row AS $key => $value)

    {



        $row[$key]['order_id'] = $value['order_id'];

        $row[$key]['order_code'] = $value['order_code'];



        $sql = "SELECT product_name FROM ". $GLOBALS['ecs']->table('order_product'). " WHERE order_id = $value[order_id]";

        $res = $GLOBALS['db']->getAll($sql);



        $row[$key]['order_goods'] = $res;



        $row[$key]['order_type'] = get_type_order($value['order_type']);

        $row[$key]['order_status'] = get_order_status($value['order_status']);



        $row[$key]['otype'] = $value['order_type'];

        $row[$key]['ostatus'] = $value['order_status'];


        $timegmt=7*3600;
        $row[$key]['add_time'] = local_date($GLOBALS['_CFG']['time_format'], $value['add_time']-$timegmt);



    }



    $arr = array('orders' => $row, 'filter' => $filter, 'page_count' => $filter['page_count'], 'record_count' => $filter['record_count']);

    return $arr;



}



function get_type_order($type){

    if($type == 2){

           $typename  =  $GLOBALS['_LANG']['order_type'][2];

    }elseif ($type == 3) {

        $typename  =  $GLOBALS['_LANG']['order_type'][3];

    }else{

        $typename  =  $GLOBALS['_LANG']['order_type'][1];

    }

    return  $typename;

}



function get_order_status($order_status){

    if($order_status == 2){

           $text_status  =  $GLOBALS['_LANG']['status'][1];

    }elseif ($order_status == 3) {

        $text_status  =  $GLOBALS['_LANG']['status'][2];

    }elseif ($order_status == 4) {

       $text_status  =  $GLOBALS['_LANG']['status'][3];

    }

    else{

        //chua xac nhan = 1

       $text_status  =  $GLOBALS['_LANG']['status'][0];

    }

    return $text_status;

}



 ?>