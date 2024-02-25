<?php if ($this->_var['banner']): ?>
 <div class="homebanner">
    <?php $_from = $this->_var['banner']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }; $this->push_vars('', 'banner_0_54229800_1495021269');$this->_foreach['banner'] = array('total' => count($_from), 'iteration' => 0);
if ($this->_foreach['banner']['total'] > 0):
    foreach ($_from AS $this->_var['banner_0_54229800_1495021269']):
        $this->_foreach['banner']['iteration']++;
?>
    <?php if (($this->_foreach['banner']['iteration'] - 1) < 6): ?>
    <div class="item"><a href="<?php echo $this->_var['banner_0_54229800_1495021269']['url']; ?>" ><img src="<?php echo $this->_var['banner_0_54229800_1495021269']['src']; ?>" alt="<?php echo htmlspecialchars($this->_var['banner_0_54229800_1495021269']['text']); ?>" ></a></div>
    <?php endif; ?>
    <?php endforeach; endif; unset($_from); ?><?php $this->pop_vars();; ?>
 </div>
<?php endif; ?>


