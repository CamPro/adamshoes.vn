<?php echo $this->_var['render']['before_html_footer']; ?>
<script src="<?php echo $this->_var['option']['static_path']; ?>/static/asset_mobile/js/jquery.min.1.8.3.js"></script>
<!--[if lt IE 9]>
<script type="text/javascript" src="<?php echo $this->_var['option']['static_path']; ?>static/js/selectivizr.js"></script>
<![endif]-->
<script  type="text/javascript" src="<?php echo $this->_var['option']['static_path']; ?>static/js/plugins.js"></script>
<script  type="text/javascript" src="<?php echo $this->_var['option']['static_path']; ?>static/js/lang.<?php echo $this->_var['option']['lang']; ?>.js"></script>
<script  type="text/javascript" src="<?php echo $this->_var['option']['static_path']; ?>static/js/global.js"></script>
<script  type="text/javascript" src="<?php echo $this->_var['option']['static_path']; ?>static/js/init.js"></script>

<script  type="text/javascript" src="<?php echo $this->_var['option']['static_path']; ?>static/js/custom.js"></script>
<?php if ($this->_var['pname'] == 'index'): ?>
<script  type="text/javascript" src="<?php echo $this->_var['option']['static_path']; ?>static/asset_mobile/js/owl.carousel.min.js"></script>
<script>
    $(document).ready(function() {

      var sync1 = $("#sync1");
      var sync2 = $("#sync2");

      sync1.owlCarousel({
        singleItem : true,
        slideSpeed : 300,
        lazyLoad : true,
        autoPlay : 5000,
        navigation: false,
        pagination:false,
        afterAction : syncPosition,
        responsiveRefreshRate : 200,
      });

      // Custom Navigation Events
      $(".owl-next").click(function(){
        sync1.trigger('owl.next');
      })
      $(".owl-prev").click(function(){
        sync1.trigger('owl.prev');
      })

      sync2.owlCarousel({
        items :5,
        pagination:false,
        responsiveRefreshRate : 100,
        afterInit : function(el){
          el.find(".owl-item").eq(0).addClass("synced");
        }
      });

      function syncPosition(el){
        var current = this.currentItem;
        $("#sync2")
          .find(".owl-item")
          .removeClass("synced")
          .eq(current)
          .addClass("synced")
        if($("#sync2").data("owlCarousel") !== undefined){
          center(current)
        }
      }

      $("#sync2").on("click", ".owl-item", function(e){
        e.preventDefault();
        var number = $(this).data("owlItem");
        sync1.trigger("owl.goTo",number);
      });

      function center(number){
        var sync2visible = sync2.data("owlCarousel").owl.visibleItems;
        var num = number;
        var found = false;
        for(var i in sync2visible){
          if(num === sync2visible[i]){
            var found = true;
          }
        }

        if(found===false){
          if(num>sync2visible[sync2visible.length-1]){
            sync2.trigger("owl.goTo", num - sync2visible.length+2)
          }else{
            if(num - 1 === -1){
              num = 0;
            }
            sync2.trigger("owl.goTo", num);
          }
        } else if(num === sync2visible[sync2visible.length-1]){
          sync2.trigger("owl.goTo", sync2visible[1])
        } else if(num === sync2visible[0]){
          sync2.trigger("owl.goTo", num-1)
        }

      }

});
</script>
<?php endif; ?>
<?php if ($this->_var['pname'] == 'jobs'): ?>
<script  type="text/javascript" src="<?php echo $this->_var['option']['static_path']; ?>static/js/tab.js"></script>
<script  type="text/javascript" src="<?php echo $this->_var['option']['static_path']; ?>static/asset_mobile/js/owl.carousel.min.js"></script>
<script>
    $(document).ready(function() {
        $("#homeslide").owlCarousel({
          navigation : true, // Show next and prev buttons
          slideSpeed : 300,
          paginationSpeed : 400,
          singleItem:true
          //items : 1,
  });

    });
</script>
<?php endif; ?>
<?php if ($this->_var['pname'] == 'category'): ?>
    <script  type="text/javascript" src="<?php echo $this->_var['option']['static_path']; ?>static/asset_mobile/js/owl.carousel.min.js"></script>
    <script>
        $(document).ready(function() {
            $("#owl-category").owlCarousel({
                items : 2,
                lazyLoad : true,
                autoPlay : 5000,
                navigation : false,
                pagination : true,
                responsiveClass: false
            });

        });
    </script>
<?php endif; ?>
<?php if ($this->_var['pname'] == 'article'): ?>
<script  type="text/javascript" src="<?php echo $this->_var['option']['static_path']; ?>static/js/warranty.min.js"></script>
<?php endif; ?>

<script type="text/javascript">
	(function(){
		$(window).scroll(function(){
			if( $(window).scrollTop() == 0 ) {
				$('#back-top').stop(false,true).fadeOut(600);
			}else{
				$('#back-top').stop(false,true).fadeIn(600);
			}
		});
		$('#back-top').click(function(){
			$('body,html').animate({scrollTop:0},300);
			return false;
		})
		$.fn.delayKeyup = function(callback, ms){
		    var timer = 0;
		    var el = $(this);
		    $(this).keyup(function(){
		    clearTimeout (timer);
		    timer = setTimeout(function(){
		        callback(el)
		        }, ms);
		    });
		    return $(this);
		};

		$('#suggest').delayKeyup(function(el){
		    keywords = el.val();
		    $.post(
				'search_suggest.php',
				{keywords: keywords},
				function(response){
					var res = $.evalJSON(response);
					var show = $('#search-site .search-suggest');
					show.css('display','block');
					show.html(res.content);
				},
				'text'
			);
		},1000);


	})(jQuery);
	</script>

<?php if ($this->_var['option']['compare_enabled'] && ( $this->_var['pname'] == 'index' || $this->_var['pname'] == 'category' || $this->_var['pname'] == 'search' || $this->_var['pname'] == 'brand' )): ?>
<script  type="text/javascript" src="<?php echo $this->_var['option']['static_path']; ?>static/js/compare.js"></script>
<script>
	$('label.compr').click(function (e) {
	  e.preventDefault(); //fix khi kich nut se nhay link
	});
	function fixDiv() {var $cache = $('div.breadandfilter'); if ($(window).scrollTop() > 180) $cache.css({'position': 'fixed','z-index': '100','min-width': '1200px','top': '0'}); else $cache.css({'position': 'relative','top': 'auto'}); } $(window).scroll(fixDiv); fixDiv();
</script>
<?php endif; ?>

<?php if ($this->_var['pname'] == 'goods' && $this->_var['buytragop'] != 1): ?>

    <script  type="text/javascript" src="<?php echo $this->_var['option']['static_path']; ?>static/asset_mobile/js/owl.carousel.min.js"></script>
    <script>
        $(document).ready(function() {
        var gallery = $("#galleryowl");
    	gallery.owlCarousel({
    	      navigation : true, // Show next and prev buttons
    	      slideSpeed : 300,
    	      paginationSpeed : 400,
    	      singleItem:true
    	});

    });
    </script>
    <script type="text/javascript">
    	$('#viewmore').click(function(b){
    		b.preventDefault();
    		$('div.product_description').toggleClass('short_view');
    		$('#more').toggleClass('hidden');
    		$('#short').toggleClass('hidden');

    	});
    </script>
    <script type="text/javascript">
    var goodsId = <?php echo $this->_var['goods_id']; ?>;
    $(document).ready(function() {
    	// stick croll
    	var stick = $('ul.stickybar');
      if(stick.html()!=undefined)
      {


      
    	var top = stick.offset().top;
    	var footerTop = $('footer').offset().top;
    	var maxY = footerTop - stick.outerHeight();
    	$(window).scroll(function(evt) {
            var y = $(this).scrollTop();
            if (y > top) {
                if (y < maxY) {
                    stick.css({'position': 'fixed'});
                } else {
                    stick.css({'position': 'relative'});
                }
            } else {
                 stick.css({'position': 'relative'});
            }
        });

        }
        //stick click
        $('#liSpec').click(function(f){
        	 $(this).parent('.stickybar').find('li.actbox').removeClass('actbox');
        	 $(this).addClass('actbox');
    		 $('html, body').animate({scrollTop: $("aside.tableparameter").offset().top}, 600);
    		f.preventDefault();
    	});
    	$('#liTip').click(function(g){
    		$(this).parent('.stickybar').find('li.actbox').removeClass('actbox');
        	 $(this).addClass('actbox');
    		 $('html, body').animate({scrollTop: $("aside.characteristics").offset().top}, 600);
    		g.preventDefault();
    	});
    	$('#liAcc').click(function(h){
    		 $(this).parent('.stickybar').find('li.actbox').removeClass('actbox');
        	 $(this).addClass('actbox');
    		 $('html, body').animate({scrollTop: $("div.boxaccess").offset().top}, 600);
    		h.preventDefault();
    	});
    	$('#liCpr').click(function(j){
    		$(this).parent('.stickybar').find('li.actbox').removeClass('actbox');
        	 $(this).addClass('actbox');
    		$('html, body').animate({scrollTop: $("#description").offset().top}, 600);
    		j.preventDefault();
    	});
    	$('#liCmt').click(function(k){
    		$(this).parent('.stickybar').find('li.actbox').removeClass('actbox');
        	 $(this).addClass('actbox');
    		$('html, body').animate({scrollTop: $("#comment").offset().top}, 600);
    		k.preventDefault();
    	});

       // console.log(top+'-'+footerTop);
      //
    	$('#purchase_form').ChangePriceSiy();
    	// Xem them noi dung mo table
    	$('#btn-viewmore').click(function(e){
    		$('div.product_description').toggleClass('short_view');
    		$('#view1').toggleClass('hidden');
    		$('#view2').toggleClass('hidden');
    		e.preventDefault();
    	});
    	$('#detail-spec').click(function(f){
    		$("body").after('<div class="fixparameter"></div>');
        $(".fullparameter,.closebtn").show();
    		f.preventDefault();
    	});
  $(".closebtn").click(function(e){
      e.preventDefault();
      $(".fixparameter").remove();
      $(".fullparameter,.closebtn").hide();
  });
    });
    </script>
    
    <?php if ($this->_var['parent_catid'] == 1 || $this->_var['parent_catid'] == 2 || $this->_var['parent_catid'] == 3 || $this->_var['parent_catid'] == 4 || $this->_var['parent_catid'] == 9 || $this->_var['parent_catid'] == 91 || $this->_var['parent_catid'] == 131): ?>
    <script type="text/javascript" src="<?php echo $this->_var['option']['static_path']; ?>static/js/getStock.js"></script>
    <?php endif; ?>
    
    <?php if ($this->_var['goods']['preoder'] || $this->_var['show_preorder'] == 1): ?>
        <script  type="text/javascript" src="<?php echo $this->_var['option']['static_path']; ?>static/js/preorder.desktop.js"></script>
    <?php endif; ?>
<?php else: ?>
    <script  type="text/javascript" src="<?php echo $this->_var['option']['static_path']; ?>static/js/tragop.v2.min.js"></script>
<?php endif; ?>

<?php if ($this->_var['pname'] == 'group_buy' && $_GET['id'] > 0): ?>
<script type="text/javascript">
if ($('.properties').length) {
	$('.properties').Formiy();
	$('.properties dl').tipsy({gravity: 'e',fade: true,html:true});
	$('.properties label').tipsy({gravity: 's',fade: true,html:true});
}
</script>
<?php endif; ?>
<?php if ($this->_var['pname'] == 'snatch'): ?>
<?php if ($this->_var['id']): ?>
<script type="text/javascript">
setInterval('newPrice(' + <?php echo $this->_var['id']; ?> + ')', 8000);
</script>
<?php endif; ?>
<?php endif; ?>
<?php if ($this->_var['pname'] == 'activity'): ?>
<script type="text/javascript">
var link_item = $(location.hash);
if (link_item.length == 1 && link_item.siblings().length > 0) {
	link_item.addClass('current').siblings().hide();
	link_item.parent().append('<a href="javascript:void(0);" class="show_all button"><span>'+lang.show_all+'</span></a>');
	link_item.parent().find('.show_all').click(function(){
		link_item.addClass('bright_current').siblings().show();
		$(this).hide();
	});
};
</script>
<?php endif; ?>

<?php echo $this->_var['render']['after_html_footer']; ?>
</div>

<div id="quick_support">
  <a data-tip="Chat với tư vấn viên" data-mode="above" href="https://www.messenger.com/t/bachkhoashopcom" target="_blank">Tư vấn</a>
</div>




<div id="fb-root"></div>
<script>(function(d, s, id) {
  var js, fjs = d.getElementsByTagName(s)[0];
  if (d.getElementById(id)) return;
  js = d.createElement(s); js.id = id;
  js.src = "//connect.facebook.net/en_GB/sdk.js#xfbml=1&version=v2.4&appId=679139665490899";
  fjs.parentNode.insertBefore(js, fjs);
}(document, 'script', 'facebook-jssdk'));</script>
</body>
</html>
