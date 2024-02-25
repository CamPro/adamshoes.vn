<?php

function siy_slider_banner($atts) {
	$str = '';
	$need_cache = $GLOBALS['smarty']->caching;
	$banner = array();

	if (file_exists(ROOT_PATH . DATA_DIR . '/flash_data.xml')) {
		if (!preg_match_all('/item_url="([^"]+)"\slink="([^"]+)"\stext="([^"]*)"\ssort="([^"]*)"/', file_get_contents(ROOT_PATH . DATA_DIR . '/flash_data.xml'), $list, PREG_SET_ORDER)) {
			preg_match_all('/item_url="([^"]+)"\slink="([^"]+)"\stext="([^"]*)"/', file_get_contents(ROOT_PATH . DATA_DIR . '/flash_data.xml'), $list, PREG_SET_ORDER);
		}
		if (!empty($list)) {
			foreach ($list as $key => $val) {
				if ($val[4] != '-1'){
					$val[4] = isset($val[4]) ? $val[4] : 0;
					$banner[] = array('src'=>$val[1],'url'=>$val[2],'text'=>$val[3],'sort'=>$val[4]);
                    
				}
			}
			foreach ($banner as $key => $val) {
				$sort[$key] = $val['sort'];
			}
			array_multisort($sort, SORT_ASC, $banner);
			$GLOBALS['smarty']->assign('banner', $banner);
			$form = (!empty($atts['form'])) ? $atts['form'] : 'library/siy_slider_banner.lbi';
			$str= $GLOBALS['smarty']->fetch($form);
		}

	}
	$GLOBALS['smarty']->caching = $need_cache;
	return $str;
}


function siy_home_banner($atts) {
	$str = '';
	$need_cache = $GLOBALS['smarty']->caching;
	$banner = array();

 	$list=$GLOBALS['db']->getRow("SELECT * FROM ".$GLOBALS['ecs']->table('ad')." WHERE position_id='22'");
		
		if (!empty($list)) {
			
				if ($list['enabled'] != '-1'){
					
					$banner[] = array('src'=>'data/afficheimg/'.$list['ad_code'],'url'=>$list['ad_link'],'text'=>$list['ad_name']);
				}
			 
			 
			 
			$GLOBALS['smarty']->assign('banner', $banner);
			$form = (!empty($atts['form'])) ? $atts['form'] : 'library/siy_home_banner.lbi';
			$str= $GLOBALS['smarty']->fetch($form);
		}

	 
	$GLOBALS['smarty']->caching = $need_cache;
	return $str;
}