<?php echo $this->fetch('library/html_header.lbi'); ?>


<?php echo $this->fetch('library/page_header.lbi'); ?>
<section>
	<?php echo $this->_var['render']['before_main']; ?>
	<div class="wrap_bnhome <?php if ($this->_var['homenew']): ?> wrapnew <?php endif; ?>clearfix">
	<aside class="bannerhome ">
	<?php echo $this->fetch('library/index_ad.lbi'); ?>
	</aside>
	<aside class="homeright">
		<?php echo $this->fetch('library/new_articles.lbi'); ?>
        <?php 
$k = array (
  'name' => 'home_banner',
);
$plugin = 'siy_'.$k['name'];
if (function_exists($plugin)) {
   echo $plugin($k);
} else {
   echo "<!-- error: ".$k['name']." not installed -->";
}
?>
	</aside>
	</div>
    

        <?php 
$k = array (
  'name' => 'home_group',
);
$plugin = 'siy_'.$k['name'];
if (function_exists($plugin)) {
   echo $plugin($k);
} else {
   echo "<!-- error: ".$k['name']." not installed -->";
}
?>

    <?php 
$k = array (
  'name' => 'statics_home',
);
$plugin = 'siy_'.$k['name'];
if (function_exists($plugin)) {
   echo $plugin($k);
} else {
   echo "<!-- error: ".$k['name']." not installed -->";
}
?>
     

	

	<?php echo $this->_var['render']['after_main']; ?>
</section>
<?php if ($this->_var['searchkeywords']): ?>
<div class="keyword"><em><?php echo $this->_var['lang']['hot_search']; ?></em>
        <?php $_from = $this->_var['searchkeywords']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }; $this->push_vars('', 'val');if (count($_from)):
    foreach ($_from AS $this->_var['val']):
?>
        <a title="<?php echo $this->_var['val']; ?>" href="tim-kiem?keywords=<?php echo urlencode($this->_var['val']); ?>">• <?php echo $this->_var['val']; ?></a>
        <?php endforeach; endif; unset($_from); ?><?php $this->pop_vars();; ?>
</div>
<?php endif; ?>


<?php echo $this->fetch('library/page_footer.lbi'); ?>
<?php echo $this->fetch('library/html_footer.lbi'); ?>