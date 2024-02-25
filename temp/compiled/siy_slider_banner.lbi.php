<?php if ($this->_var['banner']): ?>
<div class="customNavigation">
  <div class="owl-buttons"><div class="owl-prev">‹</div><div class="owl-next">›</div></div>
</div>
<div id="sync1" class="owl-carousel owl-theme">
    <?php $_from = $this->_var['banner']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }; $this->push_vars('', 'banner_0_53710700_1495021269');$this->_foreach['banner'] = array('total' => count($_from), 'iteration' => 0);
if ($this->_foreach['banner']['total'] > 0):
    foreach ($_from AS $this->_var['banner_0_53710700_1495021269']):
        $this->_foreach['banner']['iteration']++;
?>
    <?php if (($this->_foreach['banner']['iteration'] - 1) < 5): ?>
    <div class="item"><a href="<?php echo $this->_var['banner_0_53710700_1495021269']['url']; ?>" ><img src="<?php echo $this->_var['banner_0_53710700_1495021269']['src']; ?>" alt="<?php echo htmlspecialchars($this->_var['banner_0_53710700_1495021269']['text']); ?>" ></a></div>
    <?php endif; ?>
    <?php endforeach; endif; unset($_from); ?><?php $this->pop_vars();; ?>
</div>

<div id="sync2" class="owl-carousel owl-theme">
    <?php $_from = $this->_var['banner']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }; $this->push_vars('', 'banner_0_53726100_1495021269');$this->_foreach['banner'] = array('total' => count($_from), 'iteration' => 0);
if ($this->_foreach['banner']['total'] > 0):
    foreach ($_from AS $this->_var['banner_0_53726100_1495021269']):
        $this->_foreach['banner']['iteration']++;
?>
    <?php if (($this->_foreach['banner']['iteration'] - 1) < 5): ?>
    <div class="item"><h3><?php echo $this->_var['banner_0_53726100_1495021269']['text']; ?></h3></div>
     <?php endif; ?>
    <?php endforeach; endif; unset($_from); ?><?php $this->pop_vars();; ?>
</div>
<?php endif; ?>


