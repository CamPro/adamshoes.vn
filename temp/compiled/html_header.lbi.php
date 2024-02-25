
<!doctype html>
<!--[if IE 7]><html lang="vi" class="no-js ie7"><![endif]-->
<!--[if IE 8]><html lang="vi" class="no-js ie8"><![endif]-->
<!--[if IE 9]><html lang="vi" class="no-js ie9"><![endif]-->
<!--[if (gt IE 9)|!(IE)]><!--><html lang="vi" class="no-js"><!--<![endif]-->
<head>
	<meta charset="utf-8">
	<?php if ($this->_var['pname'] != 'respond' && $this->_var['message']['back_url']): ?><meta http-equiv="refresh" content="3;url=<?php echo $this->_var['message']['back_url']; ?>"><?php endif; ?>
	<base href="<?php echo $this->_var['mydomain']; ?>">
	<meta content="INDEX,FOLLOW" name="robots" />
    <meta http-equiv="content-language" content="vi" />
    <link rel="alternate" href="<?php echo $this->_var['mydomain']; ?>" hreflang="vi-vn" />
    <meta name="viewport" content="width=device-width" />
    <meta name="copyright" content="<?php echo $this->_var['shop_name']; ?>" />
    <meta name="author" content="<?php echo $this->_var['shop_name']; ?>" />
    <meta http-equiv="audience" content="General" />
    <meta name="resource-type" content="Document" />
    <meta name="distribution" content="Global" />
    <meta name="revisit-after" content="1 days" />
    <meta name="GENERATOR" content="<?php echo $this->_var['shop_name']; ?>" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1" />
    <link rel="shortcut icon" href="<?php echo $this->_var['option']['static_path']; ?>static/img/favicon.png">
	<link rel="apple-touch-icon-precomposed" href="<?php echo $this->_var['option']['static_path']; ?>static/img/website_icon.png">
    <link rel="publisher" href="<?php echo $this->_var['shop_name']; ?>" />
    <link rel="author" href="<?php echo $this->_var['shop_name']; ?>" />
    <?php if ($this->_var['description']): ?><meta name="description" content="<?php echo $this->_var['description']; ?>"/><?php endif; ?>
    <?php if ($this->_var['keywords']): ?><meta name="keywords" content="<?php echo $this->_var['keywords']; ?>"/><?php endif; ?>

    <meta property="og:site_name" content="bachkhoashop.com" />
    <meta property="og:type" content="website" />
    <meta property="og:locale" content="vi_VN" />
    <meta property="fb:app_id" content="679139665490899" />

    <!-- <meta http-equiv="x-dns-prefetch-control" content="on">
    <link rel="dns-prefetch" href="https://cdn.bachkhoashop.com/">
    <link rel="dns-prefetch" href="https://cdn2.bachkhoashop.com/">
    <link rel="dns-prefetch" href="https://cdn3.bachkhoashop.com/"> -->

	<!--[if IE]>
	<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
	<script type="text/javascript" src="<?php echo $this->_var['option']['static_path']; ?>static/js/html5shiv-printshiv.min.js"></script>
	<![endif]-->
	<?php if ($this->_var['pname'] == 'goods'): ?>
		<!--<title><?php echo htmlspecialchars($this->_var['goods']['goods_title']); ?> | bachkhoashop.com</title>-->
		<title><?php echo htmlspecialchars($this->_var['goods']['goods_title']); ?></title>
		<meta itemprop="image" content="<?php echo $this->_var['option']['static_path']; ?><?php echo $this->_var['goods']['goods_img']; ?>" />
		<meta property="og:url" itemprop="url" content="<?php echo $this->_var['option']['static_path']; ?><?php echo $this->_var['request_uri']; ?>/?utm_source=facebook&utm_campaign=newsfeed" />
		<meta property="og:title" content="<?php echo $this->_var['page_title']; ?> | bachkhoashop.com" />
		<meta property="og:description" content="<?php echo $this->_var['description']; ?>" />
		<meta property="og:image" content="<?php echo $this->_var['option']['static_path']; ?><?php echo $this->_var['goods']['goods_img']; ?>" />
		<link rel="canonical" href="<?php echo $this->_var['option']['static_path']; ?><?php echo $this->_var['request_uri']; ?>" />
		<meta name="RATING" content="GENERAL" />

	<?php elseif ($this->_var['pname'] == 'article'): ?>
		<title><?php if ($this->_var['article']['meta_title'] != ''): ?><?php echo $this->_var['article']['meta_title']; ?><?php else: ?><?php echo htmlspecialchars($this->_var['article']['title']); ?> | bachkhoashop.com<?php endif; ?></title>
		<meta property="og:url" itemprop="url" content="<?php echo $this->_var['option']['static_path']; ?><?php echo $this->_var['request_uri']; ?>?utm_source=facebook&utm_campaign=newsfeed" />
	    <meta property="og:title"   content="<?php echo htmlspecialchars($this->_var['article']['title']); ?>"/>
	    <meta property="og:description" content="<?php echo $this->_var['description']; ?>" />
	    <meta property="og:image"  content="<?php echo $this->_var['option']['static_path']; ?><?php echo $this->_var['article']['article_thumb']; ?>"/>
	    <meta property="article:modified_time" content="<?php echo $this->_var['article']['add_time']; ?>" />
	    <meta property="article:published_time" content="<?php echo $this->_var['article']['add_time']; ?>" />
	    <meta property="article:author" content="<?php echo $this->_var['article']['author']; ?>" />
	    <meta property="article:section" content="Tin tức" />
	<?php elseif ($this->_var['pname'] == 'index'): ?>
		
		<title><?php echo $this->_var['page_title']; ?></title>
        <meta property="og:title"   content="<?php echo $this->_var['page_title']; ?>"/>
        <meta property="og:description" content="<?php echo $this->_var['description']; ?>" />
        <meta property="og:image"  content="<?php echo $this->_var['option']['static_path']; ?>static/img/logo-bks-300.png"/>
	<?php else: ?>
		<title><?php echo $this->_var['page_title']; ?></title>
        <meta property="og:title"   content="<?php echo $this->_var['page_title']; ?>"/>
        <meta property="og:description" content="<?php echo $this->_var['description']; ?>" />
        <meta property="og:image"  content="<?php echo $this->_var['option']['static_path']; ?>static/img/logo-bks-300.png"/>
	<?php endif; ?>
	<?php if ($this->_var['canonical']): ?>
	<link rel="canonical" href="<?php echo $this->_var['canonical']; ?>" />
	<?php endif; ?>
	<?php echo $this->_var['render']['before_html_header']; ?>

	<link href="static/awesome/css/font-awesome.min.css" rel="stylesheet" />
	<?php if ($this->_var['pname'] == 'goods' || $this->_var['pname'] == 'category' || $this->_var['pname'] == 'index'): ?>
	<link href="<?php echo $this->_var['option']['static_path']; ?>static/asset_mobile/css/owl.carousel.css" rel="stylesheet" />
	<?php endif; ?>
	<?php if ($this->_var['pname'] == 'article' || $this->_var['pname'] == 'article_cat'): ?>
		<?php if ($this->_var['layoutvieclam']): ?>
		<link href="<?php echo $this->_var['option']['static_path']; ?>static/asset_mobile/css/owl.carousel.css" rel="stylesheet" />
	    <?php endif; ?>
    <?php endif; ?>
	<link rel="stylesheet" href="<?php echo $this->_var['option']['static_path']; ?>static/css/style.mini.2016.css">
	<link rel="stylesheet" href="<?php echo $this->_var['option']['static_path']; ?>static/css/custom.css">

	<?php if ($this->_var['cat_style']): ?><link rel="stylesheet" href="<?php echo $this->_var['cat_style']; ?>"><?php endif; ?>
	<?php if ($this->_var['topic']['css']): ?><style type="text/css"><?php echo $this->_var['topic']['css']; ?></style><?php endif; ?>
	<?php echo $this->_var['render']['after_html_header']; ?>
	<!--[if lt IE 10]>
	<script type="text/javascript" src="<?php echo $this->_var['option']['static_path']; ?>static/js/PIE.js"></script>
	<![endif]-->
	<?php if ($this->_var['pname'] == 'index'): ?>
	<style>
		footer, .homenews{background: #fff;}
		.ads_tophome{width: 100%;display: block;clear: both;max-height: 100px;overflow: hidden;background: #fff;}
		.ads_tophome span{width:40px;height:40px;display: block;position: absolute;top: 5px;right: 5px;z-index: 99999;color: blue;cursor: pointer;}
	</style>

	<?php endif; ?>
    	<?php echo $this->_var['stats_code']; ?>
        <script type="application/ld+json">
        {
          "@context": "http://schema.org",
          "@type": "WebSite",
          "url": "http://www.bachkhoashop.com/",
          "potentialAction": {
            "@type": "SearchAction",
            "target": "http://www.bachkhoashop.com/tim-kiem?keywords={search_term_string}",
            "query-input": "required name=search_term_string"
          }
        }
    </script>

</head>
<body id="page_<?php echo $this->_var['pname']; ?>" class="bg_<?php echo $this->_var['steporder']; ?>"  <?php if ($this->_var['pname'] == 'agency'): ?> onload="initialize(16.0617829,108.2108503, '<strong>Bách Khoa IT Mart</strong><br>113-117 Hàm Nghi, TP Đà Nẵng')"<?php endif; ?>>
<div id="container">
