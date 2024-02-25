<div class="group_goods group1">
	<div class="group_head">

		<h4><?php echo $this->_var['group']['group_name']; ?></h4>
		<!--<a href="<?php echo $this->_var['group']['link']; ?>" class="viewmorelinks">Xem thêm <i class="fa fa-angle-right"></i></a>-->
	</div>
	<div class="group_list">
		<ul class="cate">
		<?php $_from = $this->_var['list_goods']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }; $this->push_vars('', 'goods');if (count($_from)):
    foreach ($_from AS $this->_var['goods']):
?>
		<li class="igoods">
<a href="<?php echo $this->_var['goods']['url']; ?>" title="<?php echo htmlspecialchars($this->_var['goods']['goods_name']); ?>">
				 <div class="gimage">
             
            <img width="170" height="170" alt="<?php echo htmlspecialchars($this->_var['goods']['goods_name']); ?>" src="<?php echo $this->_var['option']['static_path']; ?><?php echo $this->_var['goods']['goods_thumb']; ?>">
             
            <?php if ($this->_var['category'] == 1 || $this->_var['category'] == 100): ?> 
            <figure class="bginfo">
                <?php echo $this->_var['goods']['goods_desc_short']; ?>
            </figure>
            <?php else: ?>
            <figure class="bginfo">
               
                 <div class="desc">
                 
              <?php echo $this->_var['goods']['goods_desc_short']; ?>
              </div>
                
                
            </figure>
            <?php endif; ?>
            </div>
            <div class="box_ginfo">
           
            <h3><?php echo $this->_var['goods']['goods_name']; ?></h3>
             <?php if ($this->_var['is_mobile'] == 1): ?>     
                <?php if ($this->_var['goods']['stock'] == 0 && $this->_var['goods']['is_preoder'] == 1): ?>
                <label class="tagss cmsoon">Đặt trước</label>
            <?php elseif ($this->_var['goods']['stock'] == 0 && $this->_var['goods']['is_preoder'] == 0): ?>
                <label class="tagss outstock">Tạm hết hàng</label>
            <?php else: ?>
                <?php if ($this->_var['goods']['laisuat'] != 1 && $this->_var['goods']['laisuat'] != NULL): ?>
                <label class="tagss installment">Trả góp 0%</label>
                <?php else: ?>
                    <?php if ($this->_var['goods']['is_new'] == 1 && $this->_var['goods']['is_hot'] == 0): ?><label class="tagss new">Mới</label><?php endif; ?>
                    <?php if ($this->_var['goods']['is_new'] == 0 && $this->_var['goods']['is_hot'] == 1): ?><label class="tagss hot">Bán chạy</label><?php endif; ?>
                <?php endif; ?>
            <?php endif; ?>
            <?php endif; ?>
            <div class="price_box">
            <strong><?php if ($this->_var['goods']['promote_price']): ?><?php echo $this->_var['goods']['promote_price']; ?><?php else: ?><?php echo $this->_var['goods']['shop_price']; ?><?php endif; ?></strong>

                <?php if ($this->_var['is_mobile'] == 0): ?>    
                    <?php if ($this->_var['goods']['stock'] == 0 && $this->_var['goods']['is_preoder'] == 1): ?>
                    <label class="tagss cmsoon">Đặt trước</label>
                <?php elseif ($this->_var['goods']['stock'] == 0 && $this->_var['goods']['is_preoder'] == 0): ?>
                    <label class="tagss outstock">Tạm hết hàng</label>
                <?php else: ?>
                    <?php if ($this->_var['goods']['laisuat'] != 1 && $this->_var['goods']['laisuat'] != NULL): ?>
                    <label class="tagss installment">Trả góp 0%</label>
                    <?php else: ?>
                        <?php if ($this->_var['goods']['is_new'] == 1 && $this->_var['goods']['is_hot'] == 0): ?><label class="tagss new">Mới</label><?php endif; ?>
                        <?php if ($this->_var['goods']['is_new'] == 0 && $this->_var['goods']['is_hot'] == 1): ?><label class="tagss hot">Bán chạy</label><?php endif; ?>
                    <?php endif; ?>
                <?php endif; ?>
            <?php endif; ?>
            </div>
 </div>
            
            <div class="promotion">
               <?php if ($this->_var['goods']['text_status']): ?><span><?php echo $this->_var['goods']['text_status']; ?></span> <?php endif; ?>
            </div>
            
            <div class="gift"><?php echo $this->_var['goods']['seller_note']; ?></div>
        	 
</a>
<div class="good_tool">
            <a   href="<?php echo $this->_var['goods']['url']; ?>" class="quick_buy">Mua</a>
            <a   href="<?php echo $this->_var['goods']['url']; ?>/tra-gop" title="Mua trả góp <?php echo $this->_var['goods']['goods_name']; ?>" class="installment_buy">Mua trả góp</a>
            </div>
		</li>
		<?php endforeach; endif; unset($_from); ?><?php $this->pop_vars();; ?>
		</ul>
	</div>
</div>