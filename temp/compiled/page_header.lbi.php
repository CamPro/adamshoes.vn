<header class="clearfix">
        <div class="htop">
	        <div class="wrap-main">
		        <a href="<?php echo $this->_var['hu']; ?>" id="logo" title="<?php echo $this->_var['shop_name']; ?>"><i class="iconbkit-logo"></i></a>
		        <?php if ($this->_var['pname'] == 'article' || $this->_var['pname'] == 'article_cat'): ?>
		        <form id="search-site" method="post" action="tin-tuc/tin-cong-nghe/9" autocomplete="off">
		             <input class="topinput" type="text" name="keywords" tabindex="1" value="<?php echo $this->_var['search_value']; ?>" required placeholder="Tìm kiếm tin tức" />
		            <button class="btntop" type="submit" tabindex="2"><i class="iconbkit-topsearch"></i></button>
		             <input name="id" type="hidden" value="<?php echo $this->_var['cat_id']; ?>" />
       				 <input name="cur_url" id="cur_url" type="hidden" value="" />
		        </form>
		        <?php else: ?>
			        <form id="search-site" method="get" action="tim-kiem" autocomplete="off">
			            <input class="topinput" type="text" id="suggest" name="keywords" tabindex="1" required value="<?php echo htmlspecialchars($this->_var['search_keywords']); ?>" placeholder="Bạn cần tìm gì hôm nay" />
			            <button class="btntop" type="submit" tabindex="2"><i class="iconbkit-topsearch"></i></button>
			            <div class="search-suggest"></div>
			        </form>
		        <?php endif; ?>
		        <span class="htop-r">
		           <span><strong>1900 63.64.72</strong>
						<p>Tổng đài tư vấn khách hàng</p>
		           </span>
		            <span><strong><a href="<?php echo $this->_var['mydomain']; ?>he-thong-sieu-thi" title="Xem 14 siêu thị" class="vm-suppermarket">Xem 14 siêu thị</a></strong>
						<p>Mở cửa từ 08:00 - 22:00</p>
		           </span>
		           <span class="hpromotion"><strong><a href="<?php echo $this->_var['mydomain']; ?>khuyen-mai/dien-thoai.html" title="Khuyến mãi">Khuyến mãi</a></strong>

						<p class="yellow">Tháng <?php echo $this->_var['month']; ?></p>
		           </span>

		        </span>
	        </div>
        </div>
        <div class="hbot">
        	<div class="wrap-main">
        		<nav>
		        <?php 
$k = array (
  'name' => 'nav',
  'type' => 'middle',
);
$plugin = 'siy_'.$k['name'];
if (function_exists($plugin)) {
   echo $plugin($k);
} else {
   echo "<!-- error: ".$k['name']." not installed -->";
}
?>
		        </nav>
        	</div>
        </div>

</header>