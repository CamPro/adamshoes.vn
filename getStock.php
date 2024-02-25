<?php
date_default_timezone_set('Asia/Ho_Chi_Minh');
define('IN_ECS', true);
define('INIT_NO_USERS', true);
define('INIT_NO_SMARTY', true);

require(dirname(__FILE__) . '/includes/init.php');
require(ROOT_PATH . 'includes/cls_json.php');

header('Content-type: text/html; charset=' . EC_CHARSET);

/*if(isset($_POST)){ */
    $province_id    = isset($_POST['province_id'])   ? intval($_POST['province_id'])   : 0;
    $city_id        = isset($_POST['city_id']) ? intval($_POST['city_id']) : 0;
    $codehts        = isset($_POST['codehts']) ? $_POST['codehts'] : '0';
    //custom seo    
    $store        = isset($_POST['store']) ? $_POST['store'] : '0';
    //end custom seo
    $arr = array();

    //Lay storeid tai location dang chon
    if($city_id > 0){
        $cityWhere = "AND city_id = '$city_id'";
    }
    $sql = "SELECT agency_desc, storeid FROM " . $ecs->table('agency') . " WHERE region_id = '$province_id ' $cityWhere";
    $agency = $db->getAll($sql);

    if(empty($agency)){
        $arr['shoplist'] = '<li class="noshop">Chưa có cửa hàng tại nơi bạn chọn</li>';
    }else{
        $arr['shoplist'] = '<strong>Siêu thị BKS gần bạn</strong><ul>';
        foreach ($agency as $store) {
            $product = json_decode(GetStockByStoreId($codehts, $store['storeid']));
//custom seo            //neu ton kho >0 thi chi nhanh nao cung co hang het
            if((!empty($product[0]->productInfo[0]) && $product[0]->productInfo[0]->totalStock > 0) || $store){
                $arr['shoplist'] .= '<li>'.$store['agency_desc'].' <span>Còn hàng</span></li>';
            }else{
                
                $arr['shoplist'] .= '<li>'.$store['agency_desc'].' <span class="outstock">Nhận hàng sau 2 - 7 ngày</span></li>';
            }
        }
        $arr['shoplist'] .= '</ul>';
    }

    $json = new JSON;
    echo $json->encode($arr);
    exit;
/*}*/



/*
============DANH SACH KHO TP 1==========
Ha tinh:    c490aa1e-9084-49f7-853c-1e0fbb4c2bbb
QuangBinh:  9ad73720-517c-408a-8cbd-055c26ee55f5
Dong ha:    c97ef626-3823-4d9c-9f0c-cf08b2cefc09
03HV:       98e0ed28-5217-42dd-8b35-54800a3c2f48
113HN:      1eb97218-738e-4a36-839d-4796ceb9a3ef
kho 115:    0801ed3c-6905-415c-9023-78723f87cb51
Luxury:     177d4231-cf17-48a4-9226-49f8dfab68b3
CopTKy:     424e5b5a-3998-4c74-89f3-d2ff89efd59f
CopQNgai:   9972152c-c25a-4023-b6b1-770196c9ad0b
CopQNhon:   edc61e55-51e8-4d32-8755-fd2afd609f20
CopPhuYen:  ef6bba43-cf98-497c-9407-5f6a06cbfb3a
CopBuonMe:  84daec89-d5e0-4604-9b6d-5afc7193dc4b
CopPleiku:  afe83b45-c4fe-4bc9-8756-5bf15d9717ec
Lotte:      fcf87bb1-3b3c-459c-8e9b-428608624ba5

*/


/**
 * Get stock by Store ID
 * @param $productCode  string
 * @param $stroreID     string
 * @return json array
 */
function GetStockByStoreId($productCode, $storesID){
    $data = array(
        "storeId" => $storesID,
        "productCode" => $productCode
    );
    $data1 = array(
        $data
    );
    $url ="http://bkc.htsoft.vn:9005/ActionService.svc/GetStockByStoreId";
    $post = json_encode($data1);
    return sendPostData($url, $post);
}

/**
 * sendPostData
 * @param $url      string
 * @param $post     json array
 * @return json array
 */
function sendPostData($url, $post){
  $ch = curl_init($url);
  curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
  curl_setopt($ch, CURLOPT_POST, true);
  curl_setopt($ch, CURLOPT_POSTFIELDS,$post);
  curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
  curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows; U; Windows NT 5.1; en-GB; rv:1.8.1.6) Gecko/20070725 Firefox/2.0.0.6');
  curl_setopt($ch, CURLOPT_HTTPHEADER, array(
        'Content-Type: application/json',
        'Content-Length: ' . strlen($post),
        'ClientTag: bkc150803'
        )
  );

  $result = curl_exec($ch);
  curl_close($ch);  // Seems like good practice
  return $result;
}