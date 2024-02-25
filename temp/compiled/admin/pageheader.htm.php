<!DOCTYPE html>

<html>

<head>

<meta charset="utf-8" />

<title><?php if ($this->_var['ur_here']): ?><?php echo $this->_var['ur_here']; ?> - <?php endif; ?><?php echo $this->_var['lang']['cp_home']; ?></title>

<meta name="robots" content="noindex, nofollow">

<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />

<link href="<?php echo $this->_var['mydomain']; ?>/<?php echo $this->_var['admin_path']; ?>/static/css/main.css" rel="stylesheet" type="text/css" />

<link type="image/x-icon" href="<?php echo $this->_var['mydomain']; ?>/<?php echo $this->_var['admin_path']; ?>/static/img/favicon.png" rel="shortcut icon" />

<?php echo $this->smarty_insert_scripts(array('files'=>'../js/transport.js,common.js,../js/utils.js')); ?>

<script language="JavaScript">
function keepLogin() {
    var xmlhttp = new XMLHttpRequest();

    xmlhttp.onreadystatechange = function() {
        if (xmlhttp.readyState == XMLHttpRequest.DONE ) {
           if (xmlhttp.status == 200) {
               
           }
           else if (xmlhttp.status == 400) {
              
           }
           else {
              
           }
        }
    };

    xmlhttp.open("POST", "<?php echo $this->_var['mydomain']; ?>efom/esession.php", true);
    xmlhttp.send();
}
setInterval("keepLogin();",30000); 



<?php $_from = $this->_var['lang']['js_languages']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }; $this->push_vars('key', 'item');if (count($_from)):
    foreach ($_from AS $this->_var['key'] => $this->_var['item']):
?>

var <?php echo $this->_var['key']; ?> = "<?php echo $this->_var['item']; ?>";

<?php endforeach; endif; unset($_from); ?><?php $this->pop_vars();; ?>



</script>

</head>

<body>

<?php if ($_SESSION['admin_id']): ?>

<div id="header"><div class="container">

	<div id="logo"><a href="index.php?act=main" title="<?php echo $this->_var['lang']['admin_home']; ?>"><?php echo sub_str($this->_var['option']['shop_name'],20); ?><em><?php echo $this->_var['lang']['admin_panel']; ?></em></a></div>

	<div id="top_nav">

		<a href="message.php?act=list" class="message"><?php echo $this->_var['lang']['view_message']; ?></a>

		<a href="index.php?act=clear_cache" class="cache"><?php echo $this->_var['lang']['clear_cache']; ?></a>

		<a href="../" target="_blank" class="view_front"><?php echo $this->_var['lang']['preview']; ?></a>

		<a href="privilege.php?act=modif"><?php echo sub_str($_SESSION['admin_name'],10); ?></a>

		<a href="privilege.php?act=logout" class="signout"><?php echo $this->_var['lang']['signout']; ?></a>

	</div>

</div></div>

<div id="nav"><div class="container">

	<ul class="level_1">

		<li class="level_1 home"><p class="level_1"><a href="index.php?act=main" class="level_1" title="<?php echo $this->_var['lang']['admin_home']; ?>"><?php echo $this->_var['lang']['admin_home']; ?></a></p></li>

	<?php $_from = $this->_var['main_menu']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }; $this->push_vars('k', 'menu');if (count($_from)):
    foreach ($_from AS $this->_var['k'] => $this->_var['menu']):
?>

		<li class="level_1" onmouseover="this.className='hover level_1'" onmouseout="this.className='level_1'">

			<p class="level_1"><?php if ($this->_var['menu']['action']): ?><a href="<?php echo $this->_var['menu']['action']; ?>"><?php echo $this->_var['menu']['label']; ?></a><?php else: ?><em><?php echo $this->_var['menu']['label']; ?></em><?php endif; ?></p>

			<?php if ($this->_var['menu']['children']): ?>

			<ul class="level_2">

				<?php $_from = $this->_var['menu']['children']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }; $this->push_vars('', 'child');if (count($_from)):
    foreach ($_from AS $this->_var['child']):
?>

				<li class="level_2"><a href="<?php echo $this->_var['child']['action']; ?>" class="level_2"><?php echo $this->_var['child']['label']; ?></a></li>

				<?php endforeach; endif; unset($_from); ?><?php $this->pop_vars();; ?>

			</ul>

			<?php endif; ?>

		</li>

	<?php endforeach; endif; unset($_from); ?><?php $this->pop_vars();; ?>

	</ul>



</div></div>

<?php endif; ?>

<div id="content"><div class="container"><div class="content_wrapper">

	<div class="main_title_wrapper clearfix">

		<h1 id="search_id"><?php if ($this->_var['ur_here']): ?><?php echo $this->_var['ur_here']; ?><?php else: ?><?php echo $this->_var['lang']['admin_panel']; ?><?php endif; ?></h1>

		<p class="action">

			<?php if ($this->_var['action_link']): ?><a href="<?php echo $this->_var['action_link']['href']; ?>" class="button"><?php echo $this->_var['action_link']['text']; ?></a><?php endif; ?>

			<?php if ($this->_var['action_link2']): ?><a href="<?php echo $this->_var['action_link2']['href']; ?>" class="button"><?php echo $this->_var['action_link2']['text']; ?></a><?php endif; ?>

		</p>

	</div>

