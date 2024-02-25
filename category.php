<?php

define('IN_ECS', true);

require(dirname(__FILE__) . '/includes/init.php');

if ((DEBUG_MODE & 2) != 2)
{
    $smarty->caching = true;
}
if(isset($_COOKIE['dev'])){
    $smarty->caching = false;
}
/*------------------------------------------------------ */
//-- INPUT
/*------------------------------------------------------ */
/* 获得请求的分类 ID */
if (isset($_REQUEST['id']))
{
    $cat_id = intval($_REQUEST['id']);
}
elseif (isset($_REQUEST['category']))
{
    $cat_id = intval($_REQUEST['category']);
}
/* start  By Nobj */
elseif(isset($_REQUEST['caturl']))
{
    $define_url=trim($_REQUEST['caturl']);
    $cat_id=$db->getOne("select cat_id from ". $ecs->table('category') ." where cat_url_seo='$define_url' limit 0,1");
    $cat_id=$cat_id ? $cat_id : 0;
}
elseif(empty($cat_id)){
    ecs_header("Location: ./\n");
}
/* end  By Nobj */
else
{
    /* 如果分类ID为0，则返回首页 */
    ecs_header("Location: ./\n");

    exit;
}
/* 初始化分页信息 */
$page = isset($_REQUEST['page'])   && intval($_REQUEST['page'])  > 0 ? intval($_REQUEST['page'])  : 1;
$start = isset($_REQUEST['start'])   && intval($_REQUEST['start'])  > 0 ? intval($_REQUEST['start'])  : 0;
$size = isset($_CFG['page_size'])  && intval($_CFG['page_size']) > 0 ? intval($_CFG['page_size']) : 10;
//$brand = isset($_REQUEST['brand']) && intval($_REQUEST['brand']) > 0 ? intval($_REQUEST['brand']) : 0;
if(isset($_REQUEST['hang']))
{
    if(intval($_REQUEST['hang']))
    {
       $brand  = intval($_REQUEST['hang']);
   }else{
         $bpamra =htmlspecialchars(trim($_REQUEST['hang']));
         if($bpamra != ''){
            $brand=$db->getOne("select brand_id from ". $ecs->table('brand') ." where url_seo='$bpamra' limit 0,1");
         }else{
             $brand = 0;
         }
   }
}else{
    $brand = 0;
}
/*start seo custom*/
    //redirect link brand ve danh muc tuong tu
    if($brand)
    {

         //ds map list id chuyen tu brand ve danh muc con
        $mapping_category=array(
                        1=>array(
                                    3=>107,
                                    2=>108,
                                    4=>104,
                                    108=>100,
                                    5=>105,
                                    17=>106,
                                    13=>103,
                                    105=>109
                                ),
                         2=>array(
                                    2=>111,
                                    1=>110,
                                    11=>114,
                                    4=>113,
                                    66=>115,
                                    8=>112,
                                    50=>116,
                                    103=>118,
                                    82=>117,
                                    112=>130,
                                    107=>119,
                                    102=>120,
                                    51=>121

                                ),
                         3 =>array(
                                15=>129,
                                3=>125,
                                2=>122,
                                13=>126,
                                4=>123,
                                1=>128,
                                50=>124,

                                ),
                         35 => array(
                                        36=>132,
                                        85=>133,
                                        30=>134
                                ),
                          36 => array(
                                    19=>135,
                                    86=>136,
                                    73=>137,
                                    18=>138,
                                    21=>140,
                                    80=>223

                          ),
                          38=>array(
                                24=>142,
                                111=>143,
                                18=>144,
                                10=>145,
                                57=>152,
                                97=>153,

                          )     ,
                          39=>array(
                            29=>146,
                            35=>147,
                            5=>148,
                            55>149,
                            68=>150,
                            26=>151,
                            68=>154,
                            36=>155,


                          ),
                          45=>array(
                            35=>156,
                            71=>157,
                            86=>158,
                            55=>159,
                            93=>160,
                            57=>161,
                            36=>162
                          ),
                          37=>array(
                                55=>165,
                                35=>163,
                                85=>164,
                                68=>166,
                                26=>167,
                                93=>168,
                                54=>169,
                                32=>170
                          ),
                          40=>array(
                                29=>177,
                                104=>178,
                                41=>179,
                                26=>180,
                                54=>181,
                                94=>182,
                                53=>183
                          ),
                          41=>array(
                                29=>184,
                                85=>185,
                                104=>186,
                                26=>187,
                                41=>188,
                                36=>189,
                                109=>190,
                                40=>191,
                                68=>192,
                          ),
                          42=>array(
                            2=>193,
                            85=>194,
                            64=>195,
                            27=>196,
                            26=>197,
                            36=>198,
                            30=>199,
                            68=>200,
                          ),
                          46=>array(
                            22=>201,
                            73=>202,
                            23=>203,
                            1=>204,
                            18=>205,
                            10=>206,
                            21=>207,
                            42=>208,
                            68=>209
                          ),

                          95=>array(
                                114=>210,
                                73=>211,
                                92=>212,
                                113=>213,
                          ),
                          90=>array(
                            106=>214,
                          ),
                          52=>array(
                            29=>215,
                            4=>216,
                            96=>217,
                            57=>218,
                            2=>219,
                            85=>220,
                            87=>221,
                            36=>222
                          )

                        );

           if(isset($mapping_category[$cat_id][$brand]))
           {

                $cat_url_seo=$db->getOne("select cat_url_seo from ". $ecs->table('category') ." where cat_id='{$mapping_category[$cat_id][$brand]}' limit 0,1");
                if( $cat_url_seo)
                {

                    $slug= $_CFG['domain']. $cat_url_seo.'.html';
                    ecs_header("Location:  $slug\n");
                }
                exit();

           }
    }

/*end seo custom*/
$price_max = isset($_REQUEST['den']) && intval($_REQUEST['den']) > 0 ? intval($_REQUEST['den']) : 0;
$price_min = isset($_REQUEST['gia']) && intval($_REQUEST['gia']) > 0 ? intval($_REQUEST['gia']) : 0;
$filter_attr_str = isset($_REQUEST['filter_attr']) ? htmlspecialchars(trim($_REQUEST['filter_attr'])) : '0';

$filter_attr_str = trim(urldecode($filter_attr_str));
$filter_attr_str = preg_match('/^[\d\.]+$/',$filter_attr_str) ? $filter_attr_str : '';
$filter_attr = empty($filter_attr_str) ? '' : explode('.', $filter_attr_str);


/* 排序、显示方式以及类型 */
$default_display_type = $_CFG['show_order_type'] == '0' ? 'list' : ($_CFG['show_order_type'] == '1' ? 'grid' : 'text');
$default_sort_order_method = $_CFG['sort_order_method'] == '0' ? 'DESC' : 'ASC';
//$default_sort_order_type   = $_CFG['sort_order_type'] == '0' ? 'goods_id' : ($_CFG['sort_order_type'] == '1' ? 'shop_price' : 'last_update');
$default_sort_order_type  = 'sort_order';
$sort  = (isset($_REQUEST['sort'])  && in_array(trim(strtolower($_REQUEST['sort'])), array('goods_id', 'shop_price', 'last_update'))) ? trim($_REQUEST['sort'])  : $default_sort_order_type;
$order = (isset($_REQUEST['order']) && in_array(trim(strtoupper($_REQUEST['order'])), array('ASC', 'DESC')))                              ? trim($_REQUEST['order']) : $default_sort_order_method;
$display  = (isset($_REQUEST['display']) && in_array(trim(strtolower($_REQUEST['display'])), array('list', 'grid', 'text'))) ? trim($_REQUEST['display'])  : (isset($_COOKIE['ECS']['display']) ? $_COOKIE['ECS']['display'] : $default_display_type);
$display  = in_array($display, array('list', 'grid', 'text')) ? $display : 'text';
$s = (isset($_GET['s']))    ? $_GET['s']:'';

setcookie('ECS[display]', $display, gmtime() + 86400 * 7);
/*------------------------------------------------------ */
//-- PROCESSOR
/*------------------------------------------------------ */

/* 页面的缓存ID */
$cache_id = sprintf('%X', crc32($cat_id . '-' . $display . '-' . $sort  .'-' . $order  .'-' . $page . '-' . $size . '-' . $_SESSION['user_rank'] . '-' .
    $_CFG['lang'] .'-'. $brand. '-' . $price_max . '-' .$price_min . '-'.$s .'-'.$start.'-'. $filter_attr_str.'-'.$_COOKIE['client']));

if (!$smarty->is_cached($display_mode, $cache_id))
{
    /* 如果页面没有被缓存则重新获取页面的内容 */

    $children = get_children($cat_id);

    $cat = get_cat_info($cat_id);   // 获得分类的相关信息
     /*sap xep theo gia tu thap den cao neu khong phai la danh muc co parent id =0*/
    if($cat['parent_id'])
    {
         if(empty($_REQUEST['sort']))
         {
            $sort='shop_price';
            $order='ASC';
         }
    }
    if (!empty($cat))
    {
        $smarty->assign('keywords',    htmlspecialchars($cat['keywords']));

        if(!empty($cat['keywords'])){
            $tags = array();
            $tags = explode(',', $cat['keywords']);
            $smarty->assign('tags',  $tags);
        }

        $smarty->assign('description', htmlspecialchars($cat['cat_desc']));
        $smarty->assign('cat_style',   htmlspecialchars($cat['style']));
    }
    else
    {
        /* 如果分类不存在则返回首页 */
        ecs_header("Location: ./\n");

        exit;
    }

    /* 赋值固定内容 */
    if ($brand > 0)
    {
        $sql = "SELECT brand_name FROM " .$GLOBALS['ecs']->table('brand'). " WHERE brand_id = '$brand'";
        $brand_name = $db->getOne($sql);
    }
    else
    {
        $brand_name = '';
    }

    /* 获取价格分级 */
    if ($cat['grade'] == 0  && $cat['parent_id'] != 0)
    {
        $cat['grade'] = get_parent_grade($cat_id); //如果当前分类级别为空，取最近的上级分类
    }

    if ($cat['grade'] > 1)
    {


        $sql = "SELECT min(g.shop_price) AS min, max(g.shop_price) as max ".
               " FROM " . $ecs->table('goods'). " AS g ".
               " WHERE ($children OR " . get_extension_goods($children) . ') AND g.is_delete = 0 AND g.is_on_sale = 1 AND g.is_alone_sale = 1  ';
               //获得当前分类下商品价格的最大值、最小值

        $row = $db->getRow($sql);

        // 取得价格分级最小单位级数，比如，千元商品最小以100为级数
        $price_grade = 0.0001;
        for($i=-2; $i<= log10($row['max']); $i++)
        {
            $price_grade *= 10;
        }

        //跨度
        $dx = ceil(($row['max'] - $row['min']) / ($cat['grade']) / $price_grade) * $price_grade;
        if($dx == 0)
        {
            $dx = $price_grade;
        }

        for($i = 1; $row['min'] > $dx * $i; $i ++);

        for($j = 1; $row['min'] > $dx * ($i-1) + $price_grade * $j; $j++);
        $row['min'] = $dx * ($i-1) + $price_grade * ($j - 1);

        for(; $row['max'] >= $dx * $i; $i ++);
        $row['max'] = $dx * ($i) + $price_grade * ($j - 1);

        $sql = "SELECT (FLOOR((g.shop_price - $row[min]) / $dx)) AS sn, COUNT(*) AS goods_num  ".
               " FROM " . $ecs->table('goods') . " AS g ".
               " WHERE ($children OR " . get_extension_goods($children) . ') AND g.is_delete = 0 AND g.is_on_sale = 1 AND g.is_alone_sale = 1 '.
               " GROUP BY sn ";

        $price_grade = $db->getAll($sql);

        foreach ($price_grade as $key=>$val)
        {
            $temp_key = $key + 1;
            $price_grade[$temp_key]['goods_num'] = $val['goods_num'];
            $price_grade[$temp_key]['start'] = $row['min'] + round($dx * $val['sn']);
            $price_grade[$temp_key]['end'] = $row['min'] + round($dx * ($val['sn'] + 1));
            $price_grade[$temp_key]['price_range'] = price_format($price_grade[$temp_key]['start']) . '&nbsp;-&nbsp;' . price_format($price_grade[$temp_key]['end']);
            $price_grade[$temp_key]['formated_start'] = price_format($price_grade[$temp_key]['start']);
            $price_grade[$temp_key]['formated_end'] = price_format($price_grade[$temp_key]['end']);
            $price_grade[$temp_key]['url'] = build_uri('category', array('cid'=>$cat_id, 'bid'=>$brand, 'price_min'=>$price_grade[$temp_key]['start'], 'price_max'=> $price_grade[$temp_key]['end'], 'filter_attr'=>$filter_attr_str), $cat['cat_name']);

            /* 判断价格区间是否被选中 */
            if (isset($_REQUEST['gia']) && $price_grade[$temp_key]['start'] == $price_min && $price_grade[$temp_key]['end'] == $price_max)
            {
                $price_grade[$temp_key]['selected'] = 1;
            }
            else
            {
                $price_grade[$temp_key]['selected'] = 0;
            }
        }

        $price_grade[0]['start'] = 0;
        $price_grade[0]['end'] = 0;
        $price_grade[0]['price_range'] = $_LANG['all_attribute'];
        $price_grade[0]['url'] = build_uri('category', array('cid'=>$cat_id, 'bid'=>$brand, 'price_min'=>0, 'price_max'=> 0, 'filter_attr'=>$filter_attr_str), $cat['cat_name']);
        $price_grade[0]['selected'] = empty($price_max) ? 1 : 0;

        $smarty->assign('price_grade',     $price_grade);

    }


    /* 品牌筛选 */

    $sql = "SELECT b.brand_id, b.brand_name, COUNT(*) AS goods_num ".
            "FROM " . $GLOBALS['ecs']->table('brand') . "AS b, ".
                $GLOBALS['ecs']->table('goods') . " AS g LEFT JOIN ". $GLOBALS['ecs']->table('goods_cat') . " AS gc ON g.goods_id = gc.goods_id " .
            "WHERE g.brand_id = b.brand_id AND ($children OR " . 'gc.cat_id ' . db_create_in(array_unique(array_merge(array($cat_id), array_keys(cat_list($cat_id, 0, false))))) . ") AND b.is_show = 1 " .
            " AND g.is_on_sale = 1 AND g.is_alone_sale = 1 AND g.is_delete = 0 ".
            "GROUP BY b.brand_id HAVING goods_num > 0 ORDER BY b.brand_name, b.sort_order, b.brand_id ASC";

    $brands = $GLOBALS['db']->getAll($sql);

    foreach ($brands AS $key => $val)
    {
        $temp_key = $key + 1;
        $brands[$temp_key]['brand_name'] = $val['brand_name'];
        $brands[$temp_key]['url'] = build_uri('category', array('cid' => $cat_id, 'bid' => $val['brand_id'], 'price_min'=>$price_min, 'price_max'=> $price_max, 'filter_attr'=>$filter_attr_str), $cat['cat_name']);

        /* 判断品牌是否被选中 */
        if ($brand == $brands[$key]['brand_id'])
        {
            $brands[$temp_key]['selected'] = 1;
        }
        else
        {
            $brands[$temp_key]['selected'] = 0;
        }
    }

    $brands[0]['brand_name'] = $_LANG['all_attribute'];
    $brands[0]['url'] = build_uri('category', array('cid' => $cat_id, 'bid' => 0, 'price_min'=>$price_min, 'price_max'=> $price_max, 'filter_attr'=>$filter_attr_str), $cat['cat_name']);
    $brands[0]['selected'] = empty($brand) ? 1 : 0;

    $smarty->assign('brands', $brands);


    /*Start seo custom*/
        /*Lay sub-category cua danh muc hien tai*/
     $list_sub_cat =array();
     $cat_check_root=$cat;
     do
     {
        if($cat_check_root['parent_id']){
            $root_cat=$cat_check_root['parent_id'];
            $cat_check_root = get_cat_info($cat_check_root['parent_id']);
             
        }
            
        else{
             $root_cat=$cat_id;   
                break;
        }
            
            
            
     }while($cat_check_root['parent_id']);
        
#           $cat_id=$root_cat;
    #if($root_cat==5)
    #    $parent_id=$cat_id;
    #else
        $parent_id=$root_cat;
  
    $list_sub_cat =   $db->getAll("SELECT cat_id,cat_name,cat_url_seo FROM ".$ecs->table('category')." where parent_id='".$parent_id."' and is_show='1' order by sort_order asc");
    $result_sub_cat = array();
    $result_sub_cat[0]['cat_name']=$_LANG['all_attribute'];
    $result_sub_cat[0]['cat_url_seo'] = build_uri('category', array('cid' => $root_cat, 'bid' => 0), $_LANG['all_attribute']);
    $result_sub_cat[0]['selected'] = 0;
    $result_sub_cat[0]['all'] = 1 ;
    $k=1;
    foreach($list_sub_cat as $sub_cat)
    {
        $result_sub_cat[$k]['cat_name']=$sub_cat['cat_name'];
        $result_sub_cat[$k]['cat_url_seo'] = build_uri('category', array('cid' => $sub_cat['cat_id'], 'bid' => 0), $_LANG['all_attribute']);
        $result_sub_cat[$k]['selected']=($cat_id==$sub_cat['cat_id'])?1:0;
        $result_sub_cat[$k]['all']=0;
        $k++;
    }

     $smarty->assign('list_sub_cat',$result_sub_cat);
     $show_sub=0;
     if(in_array($root_cat,array(1,2,3,35,36,38,39,45,37,40,41,42,46,95,90,52,5)))
        $show_sub=1;

     $smarty->assign('show_sub',$show_sub);
     
    //$smarty->assign('cat_root_info',$cat = get_cat_info($root_cat); )
     $smarty->assign('cat_root_id',$root_cat);
    /*END seo custom*/


    /* 属性筛选 */
    $ext = ''; //商品查询条件扩展
    if ($cat['filter_attr'] > 0)
    {
        $cat_filter_attr = explode(',', $cat['filter_attr']);       //提取出此分类的筛选属性
        $all_attr_list = array();

        foreach ($cat_filter_attr AS $key => $value)
        {
            $sql = "SELECT a.attr_name FROM " . $ecs->table('attribute') . " AS a, " . $ecs->table('goods_attr') . " AS ga, " . $ecs->table('goods') . " AS g WHERE ($children OR " . get_extension_goods($children) . ") AND a.attr_id = ga.attr_id AND g.goods_id = ga.goods_id AND g.is_delete = 0 AND g.is_on_sale = 1 AND g.is_alone_sale = 1 AND a.attr_id='$value'";
            if($temp_name = $db->getOne($sql))
            {
                $all_attr_list[$key]['filter_attr_name'] = $temp_name;

                $sql = "SELECT a.attr_id, MIN(a.goods_attr_id ) AS goods_id, a.attr_value AS attr_value FROM " . $ecs->table('goods_attr') . " AS a, " . $ecs->table('goods') .
                       " AS g" .
                       " WHERE ($children OR " . get_extension_goods($children) . ') AND g.goods_id = a.goods_id AND g.is_delete = 0 AND g.is_on_sale = 1 AND g.is_alone_sale = 1 '.
                       " AND a.attr_id='$value' ".
                       " GROUP BY a.attr_value";

                $attr_list = $db->getAll($sql);

                $temp_arrt_url_arr = array();

                for ($i = 0; $i < count($cat_filter_attr); $i++)        //获取当前url中已选择属性的值，并保留在数组中
                {
                    $temp_arrt_url_arr[$i] = !empty($filter_attr[$i]) ? $filter_attr[$i] : 0;
                }
        
                $temp_arrt_url_arr[$key] = 0;                           //“全部”的信息生成
                $temp_arrt_url = implode('.', $temp_arrt_url_arr);
                $all_attr_list[$key]['attr_list'][0]['attr_value'] = $_LANG['all_attribute'];
                $all_attr_list[$key]['attr_list'][0]['url'] = build_uri('category', array('cid'=>$cat_id, 'bid'=>$brand, 'price_min'=>$price_min, 'price_max'=>$price_max, 'filter_attr'=>$temp_arrt_url), $cat['cat_name']);
                $all_attr_list[$key]['attr_list'][0]['selected'] = 1;
                $check=0;
                foreach ($attr_list as $k => $v)
                {
                    $temp_key = $k + 1;
                    $temp_arrt_url_arr[$key] = $v['goods_id'];       //为url中代表当前筛选属性的位置变量赋值,并生成以‘.’分隔的筛选属性字符串
                    $temp_arrt_url = implode('.', $temp_arrt_url_arr);

                    $all_attr_list[$key]['attr_list'][$temp_key]['attr_value'] = $v['attr_value'];
                    $all_attr_list[$key]['attr_list'][$temp_key]['url'] = build_uri('category', array('cid'=>$cat_id, 'bid'=>$brand, 'price_min'=>$price_min, 'price_max'=>$price_max, 'filter_attr'=>$temp_arrt_url), $cat['cat_name']);

                    if (!empty($filter_attr[$key]) AND $filter_attr[$key] == $v['goods_id'])
                    {
                        $all_attr_list[$key]['attr_list'][$temp_key]['selected'] = 1;
                        $check=1;
                    }
                    else
                    {
                        $all_attr_list[$key]['attr_list'][$temp_key]['selected'] = 0;
                    }
                }
                if($check)
                {
                    
                    $all_attr_list[$key]['attr_list'][0]['selected'] = 0;
                }
                    
            }

        }

        $smarty->assign('filter_attr_list',  $all_attr_list);
        /* 扩展商品查询条件 */
        if (!empty($filter_attr))
        {
            $ext_sql = "SELECT DISTINCT(b.goods_id) FROM " . $ecs->table('goods_attr') . " AS a, " . $ecs->table('goods_attr') . " AS b " .  "WHERE ";
            $ext_group_goods = array();

            foreach ($filter_attr AS $k => $v)                      // 查出符合所有筛选属性条件的商品id */
            {
                if (is_numeric($v) && $v !=0 &&isset($cat_filter_attr[$k]))
                {
                    $sql = $ext_sql . "b.attr_value = a.attr_value AND b.attr_id = " . $cat_filter_attr[$k] ." AND a.goods_attr_id = " . $v;
                    $ext_group_goods = $db->getColCached($sql);
                    $ext .= ' AND ' . db_create_in($ext_group_goods, 'g.goods_id');
                }
            }
        }
    }

    assign_template('c', array($cat_id));

    $position = assign_ur_here($cat_id, $brand_name);
   /*Start EFOM*/
   $additon_title='';
   $root_cat_name=$db->getOne('SELECT cat_name FROM '.$ecs->table('category').' where cat_id  = '.$root_cat);
   if(  (isset($filter_attr) && is_array($filter_attr) ) || isset($_GET['gia'])    )
   {

    

    if($cat['cat_id']!=$root_cat)
     $root_cat_name.=' '.$cat['cat_name'];

        $meta_title=$root_cat_name;
        $meta_attr=' ';
        $meta_keywords='';
        $meta_desc='';
    if(isset($_GET['gia']))
    {
      $meta_desc.= $meta_keywords.= $meta_title.=  sprintf($_LANG['category_meta_price'],number_format($_GET['gia']),number_format($_GET['den']));

    }

     foreach($filter_attr as $field_id)
     {
        if($field_id)
        {


        $sql_attr="SELECT attr.attr_name, goods_attr.attr_value FROM ".$ecs->table('goods_attr')." goods_attr ";
        $sql_attr.="INNER JOIN ".$ecs->table('attribute')." attr on attr.attr_id = goods_attr.attr_id";
        $sql_attr.=" WHERE goods_attr.goods_attr_id  = ".$field_id;
        $result_attr = $db->getRow($sql_attr);
        if($result_attr)
        {
            $meta_attr.=$result_attr['attr_name'].' '.$result_attr['attr_value'].' ';
            if($meta_keywords)$meta_keywords.=',';
            $meta_keywords.= $root_cat_name.' '.$result_attr['attr_name'].' '.$result_attr['attr_value'].',' .$root_cat_name.' '.$result_attr['attr_name'].' '.$result_attr['attr_value'].' Bách Khoa shop';
        }


        }

     }
     $cat['meta_title']=$meta_title.$meta_attr.' - Bachkhoashop.com';
     switch($root_cat)
     {
        case 1:  $smarty->assign('description', sprintf( $_LANG['category_laptop_desc'],$root_cat_name,$meta_desc.$meta_attr,$meta_desc.$meta_attr));break;
        case 2:  $smarty->assign('description', sprintf( $_LANG['category_dt_desc'],$root_cat_name,$meta_desc.$meta_attr,$meta_desc.$meta_attr));break;
        case 3:  $smarty->assign('description', sprintf( $_LANG['category_mtb_desc'],$root_cat_name,$meta_desc.$meta_attr,$meta_desc.$meta_attr));break;
        case 5:  $smarty->assign('description', sprintf( $_LANG['category_pk_desc'],$root_cat_name.$meta_desc.$meta_attr));break;
     }
     $smarty->assign('keywords',$meta_keywords);
   }

   if(isset($_GET['page']))
    $additon_title=' - Trang '.$_GET['page'];
    if($cat['meta_title'])
        $smarty->assign('page_title',       $cat['meta_title'].$additon_title);    // 页面标题
    else
        $smarty->assign('page_title',       $position['title'].$additon_title);    // 页面标题
    $smarty->assign('current_cat_id',$cat['cat_id']);
    $cat_url= $_CFG['domain']. build_uri('category', array('cid' => $cat_id, 'bid' => 0, 'price_min'=>0, 'price_max'=> 0, 'filter_attr'=>$filter_attr_str), $_LANG['all_attribute']) ;
    $smarty->assign('canonical',$cat_url);
    $is_installment =0;
    if(isset($_GET['s'])&& $_GET['s']=='tra-gop-0-phan-tram')
    {
        $is_installment=1;
        
    }
        
   $smarty->assign('is_installment',$is_installment);     
    $smarty->assign('cat_url',$cat_url);
    /*End EFOM*/
    $smarty->assign('ur_here',          $position['ur_here']);
    $smarty->assign('root_cat_name',$root_cat_name);
    $smarty->assign('categories',       get_categories_tree($cat_id)); // 分类树
    $smarty->assign('show_marketprice', $_CFG['show_marketprice']);
    $smarty->assign('category',         $cat_id);
    $smarty->assign('cat_name',         $cat['cat_name']);
    $smarty->assign('brand_id',         $brand);
    $smarty->assign('price_max',        $price_max);
    $smarty->assign('price_min',        $price_min);
    $smarty->assign('filter_attr',      $filter_attr_str);

    // array cat acc
    $accarr = array(5,34,35,36,37,38,39,40,41,42,43,44,45,46,52,90,95, 6,53,54,55,56,64,65,97, 66,67,68,69,96); // id mang phu kien,ko border,
    $cat_pcmvp = array(4,8,29,30,31,32,70,71,93,94,101,224,225,226);
    $cat_net = array(7,57,58,59,60,61,62,63);
    $cat_doitra = array(9,49,50,131);
    $size = 20; //default page size
    $col=5;
    
    if($cat_id == 1){
        $size = 34;
        $col=4;
    }
    if(in_array($root_cat,array(2,3)))  $size=20;
    if(in_array($root_cat,array(1)))  $size=16;
    if (in_array($cat_id, $accarr) || in_array($root_cat,$accarr)) // Cat sub rieng Acc
    {
        
        $smarty->assign('isacc', 1);
        $size = 18;
        $col=6;
    }
    if($_COOKIE['client']=='mobile') $col=2;
    $smarty->assign('col',$col);
     if (in_array($cat_id, $cat_pcmvp)) // Cat sub rieng Phan Mem
    {
        $smarty->assign('ispcmvp', 1);
    }
     if (in_array($cat_id, $cat_net)) // Cat sub rieng Phan Mem
    {
        $smarty->assign('isnetwork', 1);
    }
     if (in_array($cat_id, $cat_doitra)) // Cat sub rieng Phan Mem
    {
        $smarty->assign('isdoitra', 1);
    }


     if ($cat_id == 73)  //  co cau hinh chinh
    {
        $smarty->assign('iskmtragop', 1);
    }


    if ($brand > 0)
    {
        $arr['all'] = array('brand_id'  => 0,
                        'brand_name'    => $GLOBALS['_LANG']['all_goods'],
                        'brand_logo'    => '',
                        'goods_num'     => '',
                        'url'           => build_uri('category', array('cid'=>$cat_id), $cat['cat_name'])
                    );
    }
    else
    {
        $arr = array();
    }

    $brand_list = array_merge($arr, get_brands($cat_id, 'category'));

    $smarty->assign('data_dir',    DATA_DIR);
    $smarty->assign('promotion_info', get_promotion_info());


    /* 调查 */
    $vote = get_vote();
    if (!empty($vote))
    {
        $smarty->assign('vote_id',     $vote['id']);
        $smarty->assign('vote',        $vote['content']);
    }
    $smarty->assign('best_goods',      get_category_recommend_goods('best', $children, $brand, $price_min, $price_max, $ext));

    if($is_installment){  $ext.=" AND tg.laisuat = 0 "; }
    $count = get_cagtegory_goods_count($children, $brand, $price_min, $price_max, $ext);
     
    $max_page = ($count> 0) ? ceil($count / $size) : 1;
    if ($page > $max_page)
    {
        $page = $max_page;
    }
    
    if($start)
    {
        $page=0;
    }
    
   $goodslist = category_get_goods($children, $brand, $price_min, $price_max, $ext, $size, $page, $sort, $order,$start);
  
   //xu ly them san pham phu hop de lap day neu san pham do la tieu bieu
   $goodslist = checkFullFeaturedGrid( $goodslist, $children, $brand, $price_min, $price_max, $ext, $size, $page, $sort, $order,$start,$col);

    if($display == 'grid')
    {
        if(count($goodslist) % 2 != 0)
        {
            $goodslist[] = array();
        }
    }
    if($cat_id == 9){
         
       $smarty->assign('total_mobile_used', get_cagtegory_goods_count("g.cat_id IN ('49')", $brand, $price_min, $price_max, $ext));
        $smarty->assign('mobile_used', get_goods_by_catid(49));
        $smarty->assign('total_tablet_used', get_cagtegory_goods_count("g.cat_id IN ('50')", $brand, $price_min, $price_max, $ext));
        $smarty->assign('tablet_used', get_goods_by_catid(50));
        $smarty->assign('total_laptop_used', get_cagtegory_goods_count("g.cat_id IN ('131')", $brand, $price_min, $price_max, $ext));
        $smarty->assign('laptop_used', get_goods_by_catid(131));
    }
    
    $smarty->assign('size',$size);
    
    $smarty->assign('goods_list',       $goodslist);
    /*start seo*/
    $smarty->assign('total_full',$count);
    $smarty->assign('total',$count-count($goodslist));
    
    if($_REQUEST)
    {
        $query_string=array();        
        foreach($_REQUEST as $key =>$value)if(!in_array($key,array('caturl','start','page','total')))
        {
            $query_string[]=$key.'='.$value;
        }
        $query_string= implode("&",$query_string);
        $smarty->assign('query_string','?'.$query_string);
         
    }
        
    /*end seo*/
    
    $smarty->assign('category',         $cat_id);
    $smarty->assign('cat',$cat);
    $smarty->assign('script_name', 'category');
    
    assign_pager('category',            $cat_id, $count, $size, $sort, $order, $page, '', $brand, $price_min, $price_max, $display, $filter_attr_str); // 分页
    assign_dynamic('category'); // 动态内容
}
if(!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
     if($_COOKIE['client']=='mobile')
        $display_mode='library/goods_list_item_mobile.lbi';
     else
        $display_mode='library/goods_list_item.lbi';
}
 
$smarty->display($display_mode, $cache_id);

/*------------------------------------------------------ */
//-- PRIVATE FUNCTION
/*------------------------------------------------------ */

/**
 * 获得分类的信息
 *
 * @param   integer $cat_id
 *
 * @return  void
 */
function get_cat_info($cat_id)
{
    return $GLOBALS['db']->getRow('SELECT cat_id,cat_name,meta_title, keywords, cat_desc, style, grade, filter_attr, parent_id FROM ' . $GLOBALS['ecs']->table('category') .
        " WHERE cat_id = '$cat_id'");
}

/**
 * 获得分类下的商品
 *
 * @access  public
 * @param   string  $children
 * @return  array
 */
function category_get_goods($children, $brand, $min, $max, $ext, $size, $page, $sort, $order,$start=0)
{
    $display = $GLOBALS['display'];
    $where = "g.is_on_sale = 1 AND g.is_alone_sale = 1 AND ".
            "g.is_delete = 0 AND ($children OR " . get_extension_goods($children) . ')';

    if ($brand > 0)
    {
        $where .=  "AND g.brand_id=$brand ";
    }

    if ($min > 0)
    {
        $where .= " AND g.shop_price >= $min ";
    }
    //kiem tra khong cho hien san pham co gia =0 hooac sp khong kinh doanh
    $where.=' AND g.shop_price !="0.00"   and if(g.goods_number = 0 and is_preoder = 0,0,1) = 1  ';    
    
    if ($max > 0)
    {
        $where .= " AND g.shop_price <= $max ";
    }

    /* 获得商品列表 */
    //tg.laisuat,
    $field_laisuat='(SELECT laisuat FROM ' . $GLOBALS['ecs']->table('tragop') . ' tg where tg.goods_id = g.goods_id limit 1) as laisuat ,';
    $sql = 'SELECT g.goods_id,g.label_status,  g.is_preoder, g.goods_name, g.goods_sn, g.goods_name_style, g.market_price, g.sort_order, g.goods_desc_short, g.is_hot, g.is_best, g.is_new, g.is_promote, g.is_os,  g.is_special, g.goods_number, g.shop_price AS org_price, ' .
                "IFNULL(mp.user_price, g.shop_price * '$_SESSION[discount]') AS shop_price, g.promote_price, g.online_price, g.goods_type, " .
                $field_laisuat.
                'g.promote_start_date, g.promote_end_date, g.goods_brief, g.seller_note, g.img_special, g.goods_thumb , g.goods_img ,
                 IF(g.label_status =4 ,3,IF( g.goods_number =0 AND   is_preoder = 1,2,1)) as field_sort   
                
                ' .
            'FROM ' . $GLOBALS['ecs']->table('goods') . ' AS g ' .
            'LEFT JOIN ' . $GLOBALS['ecs']->table('member_price') . ' AS mp ' .
                "ON mp.goods_id = g.goods_id AND mp.user_rank = '$_SESSION[user_rank]' " .
          //  ' LEFT JOIN ' . $GLOBALS['ecs']->table('tragop') . ' AS tg ON tg.goods_id = g.goods_id '.
            "WHERE $where $ext   ORDER BY field_sort asc ,  $sort $order";
   
    if($page)
    {
               
               $res = $GLOBALS['db']->selectLimit($sql, $size, ($page - 1) * $size);
    }         
        
    else
    {

        $res = $GLOBALS['db']->selectLimit($sql, $size, $start );
    }
    
   
           


    
    while ($row = $GLOBALS['db']->fetchRow($res))
    {
        if ($row['promote_price'] > 0)
        {
            $promote_price = bargain_price($row['promote_price'], $row['promote_start_date'], $row['promote_end_date']);
            if($promote_price > 0){
            $arr[$row['goods_id']]['rate_sale'] =  100-round(($promote_price/$row['shop_price'])*100, 0);
            }
             $discount = $promote_price - $row['shop_price'];
        }
        else
        {
            $promote_price = 0;
            $discount = 0;
        }

        if ($row['online_price'] > 0){
            $arr[$row['goods_id']]['online_raw'] = $row['online_price'];
            $arr[$row['goods_id']]['online_price'] = price_format($row['online_price']);
        }


        $arr[$row['goods_id']]['goods_id']         = $row['goods_id'];
        if($display == 'grid')
        {
            $arr[$row['goods_id']]['goods_name']       = $GLOBALS['_CFG']['goods_name_length'] > 0 ? sub_str($row['goods_name'], $GLOBALS['_CFG']['goods_name_length']) : $row['goods_name'];
        }
        else
        {
            $arr[$row['goods_id']]['goods_name']       = $row['goods_name'];
        }
        $arr[$row['goods_id']]['name']             = $row['goods_name'];
        $arr[$row['goods_id']]['codehts']          = $row['goods_sn'];
        $arr[$row['goods_id']]['goods_brief']      = $row['goods_brief'];
        $arr[$row['goods_id']]['is_best']      = $row['is_best'];
        $arr[$row['goods_id']]['is_hot']       = $row['is_hot'];
        $arr[$row['goods_id']]['is_new']       = $row['is_new'];
        $arr[$row['goods_id']]['is_promote']   = $promote_price > 0 ? $row['is_promote'] : 0;
        $arr[$row['goods_id']]['is_special']   = $row['is_special'];
        $arr[$row['goods_id']]['discount']   = price_format($discount);
        $arr[$row['goods_id']]['is_os']        = $row['is_os'];
        $arr[$row['goods_id']]['label_status'] = $row['label_status'];
        $arr[$row['goods_id']]['is_preoder']   = $row['is_preoder'];
        $arr[$row['goods_id']]['stock']        = $row['goods_number'];
        $arr[$row['goods_id']]['text_status']  = $row['seller_note'];
        $arr[$row['goods_id']]['laisuat']      = $row['laisuat'] = NULL ? '' : $row['laisuat'] ;
        //$arr[$row['goods_id']]['short_desc']   = nl2br($row['goods_desc_short']);

        if(!empty($row['goods_desc_short'])){
            $cauhinh = explode("\n", str_replace("\r", '', $row['goods_desc_short']));
            $thongso = '';
            foreach ($cauhinh as $val) {
                $thongso .= '<span>'.$val.'</span>';
            }
            $arr[$row['goods_id']]['short_desc'] = $thongso;
        }else{
            $arr[$row['goods_id']]['short_desc'] = '';
        }
        $arr[$row['goods_id']]['goods_style_name'] = add_style($row['goods_name'],$row['goods_name_style']);
        $arr[$row['goods_id']]['market_price']     = price_format($row['market_price']);
        $arr[$row['goods_id']]['shop_price']       = price_format($row['shop_price']);
        $arr[$row['goods_id']]['zero_price']       = $row['shop_price'];
        $arr[$row['goods_id']]['type']             = $row['goods_type'];
        $arr[$row['goods_id']]['promote_price']    = ($promote_price > 0) ? price_format($promote_price) : '';
        $arr[$row['goods_id']]['goods_thumb']      = get_image_path($row['goods_id'], $row['goods_thumb'], true);
        $arr[$row['goods_id']]['goods_img']        = get_image_path($row['goods_id'], $row['goods_img']);
        $arr[$row['goods_id']]['goods_thumb2col']  = $row['img_special'];
        $arr[$row['goods_id']]['url']              = build_uri('goods', array('gid'=>$row['goods_id']), $row['goods_name']);
         

    }
   

    return $arr;
}

/**
 * 获得分类下的商品总数
 *
 * @access  public
 * @param   string     $cat_id
 * @return  integer
 */
function get_cagtegory_goods_count($children, $brand = 0, $min = 0, $max = 0, $ext='')
{
    $where  = "g.is_on_sale = 1 AND g.is_alone_sale = 1 AND g.is_delete = 0 AND ($children OR " . get_extension_goods($children) . ')';

    if ($brand > 0)
    {
        $where .=  " AND g.brand_id = $brand ";
    }

    if ($min > 0)
    {
        $where .= " AND g.shop_price >= $min ";
    }
    $where.=' AND g.shop_price !="0.00"   and if(g.goods_number = 0 and is_preoder = 0,0,1) = 1 ';    
    
    if ($max > 0)
    {
        $where .= " AND g.shop_price <= $max ";
    }
    //$join = ' LEFT JOIN ' . $GLOBALS['ecs']->table('tragop') . ' AS tg ON tg.goods_id = g.goods_id ';
    /* 返回商品总数 */
    return $GLOBALS['db']->getOne('SELECT COUNT(*) FROM ' . $GLOBALS['ecs']->table('goods') . " AS g $join WHERE $where $ext     ");
}

/**
 * 取得最近的上级分类的grade值
 *
 * @access  public
 * @param   int     $cat_id    //当前的cat_id
 *
 * @return int
 */
function get_parent_grade($cat_id)
{
    static $res = NULL;

    if ($res === NULL)
    {
        $data = read_static_cache('cat_parent_grade');
        if ($data === false)
        {
            $sql = "SELECT parent_id, cat_id, grade ".
                   " FROM " . $GLOBALS['ecs']->table('category');
            $res = $GLOBALS['db']->getAll($sql);
            write_static_cache('cat_parent_grade', $res);
        }
        else
        {
            $res = $data;
        }
    }

    if (!$res)
    {
        return 0;
    }

    $parent_arr = array();
    $grade_arr = array();

    foreach ($res as $val)
    {
        $parent_arr[$val['cat_id']] = $val['parent_id'];
        $grade_arr[$val['cat_id']] = $val['grade'];
    }

    while ($parent_arr[$cat_id] >0 && $grade_arr[$cat_id] == 0)
    {
        $cat_id = $parent_arr[$cat_id];
    }

    return $grade_arr[$cat_id];

}

/*start seo*/
function checkFullFeaturedGrid( $goodslist, $children, $brand, $price_min, $price_max, $ext, $size, $page, $sort, $order,$start,$col=5)
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
     
    
     
    if($cell<$col  )
    {
        if(isset($_GET['current'])  && $_GET['current'])    $start=$_GET['current'];    
        $goodslist_ext  =     category_get_goods($children, $brand, $price_min, $price_max, $ext, ($col-$cell), 0, $sort, $order,$start+count($goodslist));
        
        if(count($goodslist_ext))foreach($goodslist_ext as $k =>$goods)
        {
            
            $goods['is_special']=0;
             $goodslist[$k]=$goods;
        }
            
    }
        
    return $goodslist;
}
/*end seo*/


?>