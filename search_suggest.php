<?php
define('IN_ECS', true);
require(dirname(__FILE__) . '/includes/init.php');
//Xác định xem có một yêu cầu ajax
$keywords   = !empty( $_POST['keywords'])   ? trim( $_POST['keywords'])     : '';
include_once('includes/cls_json.php');
$json = new JSON;
if($keywords!="")
{
    $res=	get_search_goods($keywords.' ');
    if(count($res)<5)
    {
       
      $res= array_merge($res,get_search_goods(explode(" ",$keywords))); 
    } 
}
if(!empty($res)){
  $content = '<ul>';
  $array_check_goods=array(); 
  foreach ($res as $row) if(!in_array($row['goods_id'],$array_check_goods)){$array_check_goods[$row['goods_id']]=$row['goods_id'];
    if ($row['promote_price'] > 0)
    {
        $promote_price = bargain_price($row['promote_price'], $row['promote_start_date'], $row['promote_end_date']);
        $goods[$idx]['promote_price'] = $promote_price > 0 ? price_format($promote_price) : '';
    }
    else
    {
        $goods[$idx]['promote_price'] = '';
    }
    $price = !empty($goods[$idx]['promote_price']) ? $goods[$idx]['promote_price'] : price_format($row['shop_price']);
    $row['url'] = build_uri('goods', array('gid' => $row['goods_id']), $row['goods_name']);
    $content .= '<li><a href="'.$row['url'].'"><img src="'.$row['goods_thumb'].'" alt="'.$row['goods_name'].'"><h3>'.$row['goods_name'].'</h3>';
    if($row['goods_number']==0 && $row['is_preoder']==0)
    {
        if($row['label_status']==1)
            $content.='<span class="price">Ngừng kinh doanh</span>';
        else
            $content.='<span class="price">Tin đồn</span>';
    }
    else
        $content.='<span class="price">'.$price.'</span>';
    $content.='</a></li>';
  }
  $content .= '</ul>';
  $error = 0;
}else{
  $content = '<p class="nosearch">Không tìm thấy kết quả nào phù hợp với từ khoá <strong>'.$keywords.'</strong></p>';
  $error = 1;
}
$result   = array('error' => $error, 'content' => $content,'key'=> $keywords);
die($json->encode($result));
function get_search_goods($keywords='')
{
    if(empty($keywords))    return false;
    if(is_array($keywords)  && count($keywords))
    {
        $like="(";
        foreach($keywords as $k=> $kw)
        {
            if($k) $like.=" AND ";
            $like.=" goods_name LIKE '%$kw%'    ";
        }
        $like.=")";
    }
    else
        $like=" goods_name LIKE '%$keywords%'    ";
   	$sql = "SELECT g.goods_id,g.goods_number,g.is_preoder,g.label_status, g.cat_id, g.goods_name, g.click_count, g.shop_price AS org_price, ".
                "IFNULL(mp.user_price, g.shop_price * '$_SESSION[discount]') AS shop_price, ".
                "g.promote_price, g.promote_start_date, g.promote_end_date, g.goods_thumb ".
            "FROM " .$GLOBALS['ecs']->table('goods'). " AS g ".
            "LEFT JOIN " . $GLOBALS['ecs']->table('member_price') . " AS mp ".
                    "ON mp.goods_id = g.goods_id AND mp.user_rank = '$_SESSION[user_rank]' ".
           // "WHERE g.is_delete = 0 AND g.is_alone_sale = 1 AND g.shop_price !='0.00' AND  goods_name LIKE '%$keywords%' ORDER BY g.click_count, g.goods_id LIMIT 5";
            "WHERE g.is_delete = 0 AND g.is_alone_sale = 1 AND g.shop_price !='0.00' AND  $like ORDER BY g.shop_price DESC, g.goods_id LIMIT 5";
            
    return $GLOBALS['db']->getAll($sql);
    
}
?>