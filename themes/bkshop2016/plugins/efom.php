<?php

/**
 * @author Jerry Nguyen
 * @copyright 2017
 */
function eismobile()
{
    $ua = strtolower($_SERVER['HTTP_USER_AGENT']);
    $uachar = "/(nokia|asus|sony|ericsson|microsoft|samsung|oppo|lg|philips|panasonic|alcatel|lenovo|iphone|htc|mobile|android)/i";
    
    if((!isset($_COOKIE['client']) && $client == 'mobile') || preg_match($uachar, $ua)) return true;return false;
}
function siy_home_group($atts)
{
    
    $str = '';
    $need_cache = $GLOBALS['smarty']->caching;
    $list_results= $GLOBALS['db']->getAll('SELECT * FROM  '.$GLOBALS['ecs']->table('groupproduct').' WHERE home=1 order by sort_order asc');
    if(eismobile()){ $is_mobile=1; }else $is_mobile=0;
    foreach($list_results as $group)
    { 
        if($list_goods= unserialize($group['data']))
        {
            $list_goods_result = array();
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
                    $goods['goods_gift']=nl2p(strip_tags($goods['goods_gift']));
                    $goods['is_best']      = $goods['is_best'];
                    $goods['is_hot']       = $goods['is_hot'];
                    $goods['is_new']       = $goods['is_new'];
                    $goods['is_os']        = $goods['is_os'];
                    $goods['is_promote']   = $goods['is_promote'];
                    $goods['seller_note']=nl2p($goods['seller_note']);
                    $goods['text_status']  = $goods['seller_note'];
                    $goods['goods_thumb2col']  = $goods['img_special'];
                    $goods['stock']        = $goods['goods_number'];
                    $goods['url']          = build_uri('goods', array('gid' => $goods['goods_id']), $goods['goods_name']);
                    $list_goods_result[]=$goods;
                    if($group['layout']==1)
                    {
                       
                        if($is_mobile    && $k==2)break;else if($k==3)break;
                    }
                    if($group['layout']==2)
                    {
                        if($is_mobile    && $k==5)break;else if($k==9)break;
                    }
                    if($group['layout']==3)
                            if($is_mobile    && $k==5)break;
            }
        }
        if($group['layout']==2)
        {
            $banner=$GLOBALS['db']->getRow('SELECT * FROM '.$GLOBALS['ecs']->table('ad').' where ad_id='.$group['banner']);
           $banner['ad_image']='data/afficheimg/'.$banner['ad_code'];
           if($banner['ad_link'])
           $banner['ad_link']=$GLOBALS['_CFG']['domain'].'/ads.php?ad_id='.$banner['ad_id'].'&uri='.$banner['ad_link'];
           $GLOBALS['smarty']->assign('banner',$banner);
        }
        
       
        $GLOBALS['smarty']->assign('is_mobile',$is_mobile);
        $GLOBALS['smarty']->assign('list_goods',$list_goods_result);
         
          
         
            $form='library/siy_efom_group_layout'.$group['layout'].'.lbi'; 
        $GLOBALS['smarty']->assign('group',$group); 
        $str.= $GLOBALS['smarty']->fetch($form);    
    }
    
    
     $GLOBALS['smarty']->caching = $need_cache;
    return 	$str  ;
}
function efom_count_goods_category($cat_id,$ext='')
{
    $children = get_children($cat_id);
    $where  = "g.is_on_sale = 1 AND g.is_alone_sale = 1 AND g.is_delete = 0 AND ($children OR " . get_extension_goods($children) . ')';
    return $GLOBALS['db']->getOne('SELECT COUNT(*) FROM ' . $GLOBALS['ecs']->table('goods') . " AS g WHERE $where $ext");
}
function efom_get_url_category($cat_id)
{
    $cat_url_seo=$GLOBALS['db']->getOne("select cat_url_seo from ". $GLOBALS['ecs']->table('category') ." where cat_id='$cat_id' limit 0,1");
    if( $cat_url_seo)
    {
                     
                    $slug= $GLOBALS['_CFG']['domain']. $cat_url_seo.'.html';
                  return $slug;
    }
}
function siy_statics_home()
{
     $str = '';
    $need_cache = $GLOBALS['smarty']->caching;
    
        
    
    $GLOBALS['smarty']->assign('dienthoai',number_format(efom_count_goods_category(2)));
    $GLOBALS['smarty']->assign('url_dienthoai', efom_get_url_category(2));
    $GLOBALS['smarty']->assign('laptop',number_format(efom_count_goods_category(1)));
    $GLOBALS['smarty']->assign('url_laptop', efom_get_url_category(1));
    $GLOBALS['smarty']->assign('mtb',number_format(efom_count_goods_category(3)));
    $GLOBALS['smarty']->assign('url_mtb', efom_get_url_category(3));
    $GLOBALS['smarty']->assign('phukien',number_format(efom_count_goods_category(5)));
    $GLOBALS['smarty']->assign('url_phukien', efom_get_url_category(5));
    
    $form='library/siy_efom_statics_home.lbi';
    $str.= $GLOBALS['smarty']->fetch($form); 
    
    
    $GLOBALS['smarty']->caching = $need_cache;
    return 	$str  ;
}
function nl2p($string)
{
    $paragraphs = '';

    foreach (explode("\n", $string) as $line) {
        if (trim($line)) {
            $paragraphs .= '<p>' . $line . '</p>';
        }
    }

    return $paragraphs;
}
function siy_efom_text($atts)
{
    if(!empty($atts['text']))
        return nl2p(   $atts['text']);
}
function siy_efom_comment_count($atts) {
	$id = !empty($atts['id']) ? intval($atts['id']) : 0;
	$type = !empty($atts['type']) ? intval($atts['type']) : 0;
	$count = $GLOBALS['db']->getOne('SELECT COUNT(*) FROM ' .$GLOBALS['ecs']->table('comment').
		" WHERE id_value = '$id' AND comment_type = '$type' AND status = 1 AND parent_id = 0");
        if($count)
        {
            return '<span class="meta_count"><i class="fa fa-question-circle" aria-hidden="true"></i>Có <strong>'.$count.'</strong> hỏi đáp</span>';
        }
        
	return '';
}
function siy_efom_heading_comment_count($atts) {
	$id = !empty($atts['id']) ? intval($atts['id']) : 0;
	$type = !empty($atts['type']) ? intval($atts['type']) : 0;
	$count = $GLOBALS['db']->getOne('SELECT COUNT(*) FROM ' .$GLOBALS['ecs']->table('comment').
		" WHERE id_value = '$id' AND comment_type = '$type' AND status = 1 AND parent_id = 0");
        if($count)
        {
            return 'Có <em class="number">'.$count.'</em>';
        }
        
	return '';
}
function siy_topic_banner($atts) {
    $str = '';
    $need_cache = $GLOBALS['smarty']->caching;
    $banner = array();

    $list=$GLOBALS['db']->getRow("SELECT * FROM ".$GLOBALS['ecs']->table('ad')." WHERE position_id='".$atts['id']."'");
        
        if (!empty($list)) {
            
                if ($list['enabled'] != '-1'){
                    
                    $banner[] = array('src'=>'data/afficheimg/'.$list['ad_code'],'url'=>$list['ad_link'],'text'=>$list['ad_name']);
                }
             
             
             foreach($banner as $bn)
             {
                $str.="<a href='".$bn['url']."'><img src='".$bn['src']."'/></a>";
             }
            #$GLOBALS['smarty']->assign('banner', $banner);
            #$form = (!empty($atts['form'])) ? $atts['form'] : 'library/siy_home_banner.lbi';
            #$str= $GLOBALS['smarty']->fetch($form);
        }

     
    $GLOBALS['smarty']->caching = $need_cache;
    return $str;
}
function get_status_label($goods)
{
    // tin don, ngung kinh doanh, moi ra mat,tam het hang, dat hang truoc   
    if($goods['on_sale'])
    {
        
       if($goods['preoder']==1    && $goods['goods_number']==0)  
       {
              return 3;// dat hang truoc
       }  
          
        if($goods['goods_number'] && $goods['label_status']==4)
        {
            
                return 2;         // tam het hang
        }
       
       
        
       
            
    }
    else
        if(!$goods['goods_number'])
        {
            if(!$goods['shop_price']=='0.00')
                return 0; // tin don
            
        }
            else
                if(!$goods['shop_price']!='0.00')
                    return 1;    // ngung kinh doanh
        /*else
            if($goods['shop_price'])
                return 2; //moi ra mat*/
}
function _list_text_status()
{
    return array('Tin đồn','Ngừng kinh doanh','Tạm hết hàng','Đặt hàng trước');
}
function siy_goods_label_status($goods)
{
    //on_sale,goods_number,preoder
    $goods=( $goods['goods']);
    if(isset($goods['is_on_sale']))
        $goods['on_sale']=$goods['is_on_sale'];
    
   $list_status=_list_text_status();  
   switch($status=get_status_label($goods))
   {
    # case 0:    return '<span class="labelstatus outstock">'.$list_status[$status].'</span>';break;
     case 1:    return '<span class="labelstatus outstock">'.$list_status[$status].'</span>';break;
     case 2:  return; break;  return '<span class="labelstatus preoder">'.$list_status[$status].'</span>';break;
     case 3:    return '<span class="labelstatus preoder">'.$list_status[$status].'</span>';break;
     case 4:    return '<span class="labelstatus preoder">'.$list_status[$status].'</span>';break;
   }
   
        
            
}
function get_status_label_item($goods)
{
    // tin don, ngung kinh doanh, moi ra mat,tam het hang, dat hang truoc  
    if($goods['stock']==0)
    {
        if($goods['is_preoder'])    return 3; // dat hang truoc
        
    } 
    else 
         if($goods['label_status']==4)
                    return  2;         // tam het hang
     return '';               
               
}
function siy_goods_tagss_status($goods)
{
    $goods=( $goods['goods']);
    $goods['goods_number']=$goods['stock'];
    $goods['shop_price']=$goods['zero_price'];
 #var_dump($goods);exit();
   $list_status=_list_text_status();
   $status=get_status_label_item($goods);
  
   if(empty($status))
   {
    
         if($goods['laisuat']!=1 && $goods['laisuat']!=null)
                return ' <label class="tagss installment">Trả góp 0%</label>';
           if($goods['is_new'])
           {
             
                if(!$goods['is_hot'])
                    return '<label class="tagstatus tagss new">Mới</label>';
                
                        
           } 
           else
                if($goods['is_hot'])
                    return '<label class="tagstatus tagss hot">Bán chạy</label>';
   
   }   
   else
   { 
    
        switch($status)
           {
            # case 0:    return '<label class="tagss cmsoon">'.$list_status[$status].'</label>';break;
             case 1:    return '<label class="tagss cmsoon">'.$list_status[$status].'</label>';break;
             case 2:  return ;break;  return '<label class="tagss outstock">'.$list_status[$status].'</label>';break;
             case 3:    return '<label class="tagss cmsoon">'.$list_status[$status].'</label>';break;
             case 4:    return '<label class="tagss cmsoon">'.$list_status[$status].'</label>';break;
           }
      

    }    
}
function siy_egoods_price($goods)
{
   # if(empty($goods['cat_root_id']))
    #    $cat_root_id=0;
   #  else   
    #    $cat_root_id=( $goods['cat_root_id']);
     $goods=( $goods['goods']);
    
     #if( $status=get_status_label_item($goods)==2   && $cat_root_id==1)
     {
      #  return ' <strong class="price">Liên hệ</strong>';
     }
     $html='<strong>';
        if($goods['promote_price'])
            $html.=$goods['promote_price'];
        else
            $html.=$goods['shop_price'];    
     $html.='</strong>';
     return $html;
       
}
function siy_efom_show_price_online($atts)
{
     $goods=( $atts['goods']);
     if( $goods['online_price'] !='000' || $goods['online_price'] !='0.00')
     {
        $price=intval(str_replace(array('.','đ'),'',$goods['online_price']));
        if($price)
        {


        $html='<span class="tag_price_online">Giảm <font>'.$price.'</font> khi mua online</span>';
       
        return $html;

        }
     }
}
?>