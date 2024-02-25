<div class="group_goods group2">
	<div class="group_head">

		<h4><?php echo $this->_var['group']['group_name']; ?></h4>
		<a href="<?php echo $this->_var['group']['link']; ?>" class="viewmorelinks">(Xem tất cả)</a>
        <div class="more_links"><?php echo $this->_var['group']['links']; ?></div>
	</div>
	<div class="group_list">
        <div class="qcleft">
       <a href="<?php echo $this->_var['banner']['ad_link']; ?>"> <img src="<?php echo $this->_var['banner']['ad_image']; ?>"/></a>
        </div>
		<ul class="cate">
		<?php $_from = $this->_var['list_goods']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }; $this->push_vars('', 'goods_0_57809100_1495021269');if (count($_from)):
    foreach ($_from AS $this->_var['goods_0_57809100_1495021269']):
?>
		<li class="igoods">

				 <a href="<?php echo $this->_var['goods_0_57809100_1495021269']['url']; ?>" title="<?php echo htmlspecialchars($this->_var['goods_0_57809100_1495021269']['goods_name']); ?>">
             
            <img width="170" height="170" alt="<?php echo htmlspecialchars($this->_var['goods_0_57809100_1495021269']['goods_name']); ?>" src="<?php echo $this->_var['option']['static_path']; ?><?php echo $this->_var['goods_0_57809100_1495021269']['goods_thumb']; ?>">
            <?php 
$k = array (
  'name' => 'efom_show_price_online',
  'goods' => $this->_var['goods_0_57809100_1495021269'],
);
$plugin = 'siy_'.$k['name'];
if (function_exists($plugin)) {
   echo $plugin($k);
} else {
   echo "<!-- error: ".$k['name']." not installed -->";
}
?>
            <div class="box_ginfo">
<h3><?php echo $this->_var['goods_0_57809100_1495021269']['goods_name']; ?></h3>
              <?php if ($this->_var['is_mobile'] == 1): ?>     
                <?php if ($this->_var['goods_0_57809100_1495021269']['stock'] == 0 && $this->_var['goods_0_57809100_1495021269']['is_preoder'] == 1): ?>
                <label class="tagss cmsoon">Đặt trước</label>
            <?php elseif ($this->_var['goods_0_57809100_1495021269']['stock'] == 0 && $this->_var['goods_0_57809100_1495021269']['is_preoder'] == 0): ?>
                <label class="tagss outstock">Tạm hết hàng</label>
            <?php else: ?>
                <?php if ($this->_var['goods_0_57809100_1495021269']['laisuat'] != 1 && $this->_var['goods_0_57809100_1495021269']['laisuat'] != NULL): ?>
                <label class="tagss installment">Trả góp 0%</label>
                <?php else: ?>
                    <?php if ($this->_var['goods_0_57809100_1495021269']['is_new'] == 1 && $this->_var['goods_0_57809100_1495021269']['is_hot'] == 0): ?><label class="tagss new">Mới</label><?php endif; ?>
                    <?php if ($this->_var['goods_0_57809100_1495021269']['is_new'] == 0 && $this->_var['goods_0_57809100_1495021269']['is_hot'] == 1): ?><label class="tagss hot">Bán chạy</label><?php endif; ?>
                <?php endif; ?>
            <?php endif; ?>
            <?php endif; ?>


            
            <div class="price_box">
            <strong><?php if ($this->_var['goods_0_57809100_1495021269']['promote_price']): ?><?php echo $this->_var['goods_0_57809100_1495021269']['promote_price']; ?><?php else: ?><?php echo $this->_var['goods_0_57809100_1495021269']['shop_price']; ?><?php endif; ?></strong>

             <?php if ($this->_var['is_mobile'] == 0): ?>     
              <?php if ($this->_var['goods_0_57809100_1495021269']['stock'] == 0 && $this->_var['goods_0_57809100_1495021269']['is_preoder'] == 1): ?>
                <label class="tagss cmsoon">Đặt trước</label>
            <?php elseif ($this->_var['goods_0_57809100_1495021269']['stock'] == 0 && $this->_var['goods_0_57809100_1495021269']['is_preoder'] == 0): ?>
                <label class="tagss outstock">Tạm hết hàng</label>
            <?php else: ?>
                <?php if ($this->_var['goods_0_57809100_1495021269']['laisuat'] != 1 && $this->_var['goods_0_57809100_1495021269']['laisuat'] != NULL): ?>
                <label class="tagss installment">Trả góp 0%</label>
                <?php else: ?>
                    <?php if ($this->_var['goods_0_57809100_1495021269']['is_new'] == 1 && $this->_var['goods_0_57809100_1495021269']['is_hot'] == 0): ?><label class="tagss new">Mới</label><?php endif; ?>
                    <?php if ($this->_var['goods_0_57809100_1495021269']['is_new'] == 0 && $this->_var['goods_0_57809100_1495021269']['is_hot'] == 1): ?><label class="tagss hot">Bán chạy</label><?php endif; ?>
                <?php endif; ?>
            <?php endif; ?>

             <?php endif; ?>


            </div>
            </div>
            
          <!--  <div class="promotion">
               <?php if ($this->_var['goods_0_57809100_1495021269']['text_status']): ?><span><?php echo $this->_var['goods_0_57809100_1495021269']['text_status']; ?></span> <?php endif; ?>
            </div>-->
            <?php if ($this->_var['category'] == 1 || $this->_var['category'] == 100): ?> 
            <figure class="bginfo">
                <?php echo $this->_var['goods_0_57809100_1495021269']['goods_desc_short']; ?>
            </figure>
            <?php else: ?>
            <figure class="bginfo">
                 <span class="name"><?php echo $this->_var['goods_0_57809100_1495021269']['goods_name']; ?></span>
                
                <strong><?php if ($this->_var['goods_0_57809100_1495021269']['promote_price']): ?><?php echo $this->_var['goods_0_57809100_1495021269']['promote_price']; ?><?php else: ?><?php echo $this->_var['goods_0_57809100_1495021269']['shop_price']; ?><?php endif; ?></strong>
                
                 <div class="desc">
              <?php echo $this->_var['goods_0_57809100_1495021269']['goods_desc_short']; ?>
              </div>
              	 
            </figure>
            <?php endif; ?>
        	 </a>

		</li>
		<?php endforeach; endif; unset($_from); ?><?php $this->pop_vars();; ?>
		</ul>
	</div>
</div>