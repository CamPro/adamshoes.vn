<div class="group_goods group3 acchome">
	<div class="group_head">

		<h4><?php echo $this->_var['group']['group_name']; ?></h4>
		<a href="<?php echo $this->_var['group']['link']; ?>" class="viewmorelinks">(Xem tất cả)</a>
        <div class="more_links"><?php echo $this->_var['group']['links']; ?></div>
	</div>
	<div class="group_list ">
       
		<ul class="cate">
		<?php $_from = $this->_var['list_goods']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }; $this->push_vars('', 'goods_0_64576100_1495021269');if (count($_from)):
    foreach ($_from AS $this->_var['goods_0_64576100_1495021269']):
?>
		<li class="igoods">

				<a href="<?php echo $this->_var['goods_0_64576100_1495021269']['url']; ?>" title="<?php echo $this->_var['goods_0_64576100_1495021269']['goods_name']; ?>"> 
            <?php if ($this->_var['goods_0_64576100_1495021269']['is_special'] == 1): ?>
                <?php echo $this->_var['goods_0_64576100_1495021269']['goods_thumb2col']; ?>
            <?php else: ?>
            <img width="170" height="170" alt="<?php echo htmlspecialchars($this->_var['goods_0_64576100_1495021269']['goods_name']); ?>" src="<?php echo $this->_var['option']['static_path']; ?><?php echo $this->_var['goods_0_64576100_1495021269']['goods_thumb']; ?>">
            <?php endif; ?>
            <h3><?php echo $this->_var['goods_0_64576100_1495021269']['goods_name']; ?></h3>
            <div class="price_box">
            <strong><?php if ($this->_var['goods_0_64576100_1495021269']['promote_price']): ?><?php echo $this->_var['goods_0_64576100_1495021269']['promote_price']; ?><del><?php echo $this->_var['goods_0_64576100_1495021269']['shop_price']; ?></del><?php else: ?><?php echo $this->_var['goods_0_64576100_1495021269']['shop_price']; ?><?php endif; ?></strong>

            </div>

            
            <div class="promotion">
               <?php if ($this->_var['goods_0_64576100_1495021269']['text_status']): ?><span><?php echo $this->_var['goods_0_64576100_1495021269']['text_status']; ?></span> <?php endif; ?>
            </div>
             
        	 </a>

		</li>
		<?php endforeach; endif; unset($_from); ?><?php $this->pop_vars();; ?>
		</ul>
	</div>
</div>