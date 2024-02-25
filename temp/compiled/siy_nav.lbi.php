<?php if ($this->_var['nav']): ?>
	<?php $_from = $this->_var['nav']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }; $this->push_vars('', 'nav_0_53278200_1495021269');$this->_foreach['nav'] = array('total' => count($_from), 'iteration' => 0);
if ($this->_foreach['nav']['total'] > 0):
    foreach ($_from AS $this->_var['nav_0_53278200_1495021269']):
        $this->_foreach['nav']['iteration']++;
?>
	 	<a class="<?php echo $this->_var['nav_0_53278200_1495021269']['class']; ?> <?php if ($this->_var['nav_0_53278200_1495021269']['active'] || $this->_var['nav_0_53278200_1495021269']['children']): ?>current<?php endif; ?>" href="<?php echo $this->_var['nav_0_53278200_1495021269']['url']; ?>" <?php if ($this->_var['nav_0_53278200_1495021269']['opennew']): ?> target="_blank"<?php endif; ?>><i class="iconbkit-<?php echo $this->_var['nav_0_53278200_1495021269']['class']; ?>"></i><?php echo $this->_var['nav_0_53278200_1495021269']['name']; ?></a>
		<?php endforeach; endif; unset($_from); ?><?php $this->pop_vars();; ?>
<?php endif; ?>