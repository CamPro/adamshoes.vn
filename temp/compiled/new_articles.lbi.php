<div class="homenews">
<figure>
    <h2><a href="/tin-tuc/tin-cong-nghe/9">Tin công nghệ</a></h2>
    <a href="/tin-tuc/tin-cong-nghe/9" class="readmore">Đọc thêm <i class="fa fa-angle-right"></i></a>
    <div class="clear clearfix"></div>
</figure>
<ul>
    <?php $_from = $this->_var['new_articles']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }; $this->push_vars('k', 'article');$this->_foreach['new_articles'] = array('total' => count($_from), 'iteration' => 0);
if ($this->_foreach['new_articles']['total'] > 0):
    foreach ($_from AS $this->_var['k'] => $this->_var['article']):
        $this->_foreach['new_articles']['iteration']++;
?>
    <?php if ($this->_var['k'] < 3): ?>
    <li class="item<?php echo $this->_var['k']; ?>">
       <a href="<?php echo $this->_var['article']['url']; ?>" title="<?php echo htmlspecialchars($this->_var['article']['title']); ?>">
            <img src="<?php echo $this->_var['article']['thumb']; ?>" alt="<?php echo htmlspecialchars($this->_var['article']['title']); ?>" width="100" height="70">
            <h3><?php echo htmlspecialchars($this->_var['article']['title']); ?></h3>
            <span><?php echo $this->_var['article']['add_time']; ?></span>
            <label> • <?php echo $this->_var['article']['cat_name']; ?></label>
        </a>
    </li>
    <?php endif; ?>
    <?php endforeach; endif; unset($_from); ?><?php $this->pop_vars();; ?>
</ul>
</div>

