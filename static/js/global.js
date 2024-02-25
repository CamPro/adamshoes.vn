var loader = '<div class="loader">&nbsp;</div>', result = '<div class="result"></div>', status = '<div class="status"></div>', page = 'undefined', action = 'undefined';

function clearHistory() {
	document.cookie = 'ECS[history]='+escape('');
	$('#history').animate({height:'0',opacity:'0'}, 1000, '', function(){
		$('#history .tip').tipsy('hide');
		$('#history').hide();
	});
}

function orderQuery() {
	var order_input = $('#order_query input[name="order_sn"]');
	var order_sn = order_input.val(), reg = /^[\.0-9]+/;
	if (order_sn.length < 10 || ! reg.test(order_sn)) {
		order_input.focus().tipsy('show');
		return;
	}
	else {
		$.get(
			'user.php?act=order_query&order_sn=s',
			'order_sn=s' + order_sn,
			function(response){
				var order_result = $('#order_query .result');
				var res = $.evalJSON(response);
				if (res.message != '') {
					cAlert(res.message);
				}
				if (res.error == 0) {
					cAlert(res.content);
					$('form[name^=queryForm] a').click(function(){
						$(this).parents('form').submit();
						return false;
					});
				}
			},
			'text'
		);
	}
}

function submitVote()
{
	var type = $('#vote input[name="type"]').val(), vote_id = $('#vote input[name="id"]').val(), option = '';
	$('#vote input[name="option_id"]:checked').each(function() {
		var option_id = $(this).val();
		option += option_id + ',';
	});

	if (option == '') {
		$('#vote form').tipsy('show');
		return;
	} else {
		var vote_result = $('#vote .result');
		$('#vote .loader').css({visibility:'visible'}).fadeTo(0, 1000);
		$.post(
			'vote.php',
			{vote: vote_id, options: option, type: type},
			function(response){
				var res = $.evalJSON(response);
				if (res.message != '') {
					vote_result.css({display:'block'});
					vote_result.html(res.message);
				}
				if (res.error == 0) {
					$('#vote_inner').html(res.content);
				}
				$('#vote .loader').fadeTo(1000, 0);
			},
			'text'
		);
	}
}

function addEmailList() {
	var subscription_email = $('#subscription input[name="email"]');
	var email = subscription_email.val();
	if (!isValidEmail(email)) {
		subscription_email.focus().tipsy('show');
		return;
	}
	else {
		$('#subscription .loader').css({visibility:'visible'}).fadeTo(0, 1000);
		$.get(
			'user.php?act=email_list&job=add',
			'email=' + email,
			function(response){
				$('#subscription .result').css({display:'block',backgroundColor:'#97cf4d'}).html(response).animate({backgroundColor:'#fff'}, 1000);
				$('#subscription .loader').fadeTo(1000, 0);
			},
			'text'
		);
	}
}
function cancelEmailList()
{
	var subscription_email = $('#subscription input[name="email"]');
	var email = subscription_email.val();
	if (!isValidEmail(email)) {
		subscription_email.focus().tipsy('show');
		return;
	}
	else {
		var subscription_result = $('#subscription .result');
		var subscription_loader = $('#subscription .loader');
		subscription_loader.css({visibility:'visible'}).fadeTo(0, 1000);
		$.get(
			'user.php?act=email_list&job=del',
			'email=' + email,
			function(response){
				subscription_result.css({display:'block',backgroundColor:'#97cf4d'}).html(response).animate({backgroundColor:'#fff'}, 1000);
				subscription_loader.fadeTo(1000, 0);
			},
			'text'
		);
	}
}
function isValidEmail(email) {
	var filter = /^([\w-\.]+)@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.)|(([\w-]+\.)+))([a-zA-Z]{2,4}|[0-9]{1,3})(\]?)$/;
	return filter.test(email);
}

function getAttrSiy(area) {
	var attrList = new Array();
	area.find('input[name^="spec_"]:checked, select[name^="spec_"]').each(function(i) {
		attrList[i] = $(this).val();
	});
	return attrList;
}



(function($) {
$.fn.ChangePriceSiy = function() {
	var area = $(this);
	loadPrice(area);
	area.find('input[name^="spec_"], select[name^="spec_"]').change(function() {
		loadPrice(area);
	});
	area.find('input[name="number"]').keyup(function() {
		var number = area.find('input[name="number"]').val();
		if (number.length > 0) {
			loadPrice(area);
		}
	});
};
})(jQuery);

function loadPrice(area) {
	var attr = getAttrSiy(area);
	var number = area.find('input[name="number"]').val();
	if (number < 1) {
		var qty = '1';
	}
	else {
		var qty = number;
	};
	if(qty==undefined) qty=1;
	$.get(
		'goods.php',
		'act=price&id=' + goodsId + '&attr=' + attr + '&number=' + qty,
		function(response){
			var res = $.evalJSON(response);
			if (res.err_msg.length > 0) {
				$.fn.colorbox({html:'<div class="message_box">' + res.err_msg + '<p class="action"><a href="javascript:void(0);" class="button brighter_button" onclick="$.fn.colorbox.close(); return false;"><span>' + lang.confirm + '</span></a></p></div>'});
			}
			else {
				//area.find('[name="number"]').val(res.qty);
				area.find('.amount').html(res.result);
				$('#gdspricekm').html(res.result);
				$('#gdsprice').html(res.result);
			}
		},
		'text'
	);
};


function buy(id, num, parent,game) {
	var goods = new Object();
	var spec_arr = new Array();
	var fittings_arr = new Array();
	var number = 1;
	var form = $('#purchase_form');
	var quick = 0;

	if (form.length > 0) {
		spec_arr = getAttrSiy(form);
		var formNumber = form.find('[name="number"]').val();
		if (formNumber) {
			number = formNumber;
		}
		quick = 1;
	}
	if (num > 0) {
		number = num;
	}

	goods.quick    = quick;
	goods.spec     = spec_arr;
	goods.goods_id = id;
	goods.number   = number;
	goods.parent   = (typeof(parent) == 'undefined') ? 0 : parseInt(parent);
    //game: 0 = nothing | 1 : online | 2 : gia soc
    goods.game   = (typeof(parent) == 'undefined') ? 0 : parseInt(game);
    //console.log(goods.game);
    $("#loading_box").show();
	$.post(
		'flow.php?step=add_to_cart',
		{goods: $.toJSON(goods)},
		function(response){
			    $("#loading_box").hide();
			var res = $.evalJSON(response);
			if (res.error > 0) {
				if (res.error == 2) {
					$.fn.colorbox({html:'<div class="message_box mb_question">' + res.message + '<p class="action"><a href="user.php?act=add_booking&id=' + res.goods_id + '&spec=' + res.product_spec + '" class="button brighter_button"><span>' + lang.booking + '</span></a><a href="javascript:void(0);" class="tool_link" onclick="$.fn.colorbox.close(); return false;">' + lang.continue_browsing_products + '</a></p></div>'});
				}
				else if (res.error == 6) {
					openSpeSiy(res.message, res.goods_id, number, res.parent);
				}
				else {
					$.fn.colorbox({html:'<div class="message_box mb_info">' + res.message + '<p class="action"><a href="javascript:void(0);" class="button brighter_button" onclick="$.fn.colorbox.close(); return false;"><span>' + lang.confirm + '</span></a></p></div>'});
				}
			}
			else {
				$('#cart .label').html(res.content);
				loadCart('refresh');
				if (res.one_step_buy == '1') {
					location.href = 'gio-hang';
				}
				else {
					if ($('#page_flow').length > 0) {
						location.href = 'gio-hang';
					} else {
						window.location=$("base").attr("href")+'gio-hang';
						//$.fn.colorbox({html:'<div class="message_box mb_info">' + lang.add_to_cart_success + '<p class="action"><a href="gio-hang" class="button brighter_button"><span>' + lang.checkout_now + '</span></a><a href="javascript:void(0);" class="tool_link" onclick="$.fn.colorbox.close(); return false;">' + lang.continue_browsing_products + '</a></p></div>'});
					}
				}
			}
		},
		'text'
	);
}


function goituvan(id,goodsname, code, price, promo){
	//Show form Order
	var goods = new Object();
	var form = $('#formtragop');
	goods.goods_id = id;
	goods.number   = 1;

	goods.csex = form.find('[name="csex"]').val();
	goods.cname = form.find('[name="cname"]').val();
	goods.cmobile = form.find('[name="cmobile"]').val();
    goods.province = form.find('[name="province"]').val();
	goods.goodsname = goodsname;
	goods.gdscode = code;
	goods.giany = price;
	goods.giakm = promo;
	goods.quatang = '';
	goods.url = window.location.href;

     error = 0;
	if(goods.cname.length == ''){
        error = 1;
        $msg += '- Tên không được để trống <br/>';
    }
    var va_mobile = goods.cmobile.substring(0,1); //lay ki tu dau cua mobile
    if(goods.cmobile.length < 10 || goods.cmobile.length > 11 || va_mobile != 0){
        error = 1;
        $msg += '- Số điện thoại không hợp lệ <br/>';
    }
    if(goods.province == 0){
        error = 1;
        $msg += '- Chưa chọn tỉnh/TP <br/>';
    }

    if(error == 1){
       $.fn.colorbox({html:'<div class="message_box mb_info">'+$msg+'</div>'});
    }

    if(error == 0){
     	$.post(
    	'order.php?step=goituvan',
    	{goods: $.toJSON(goods)},
    	function(response){
    		var res = $.evalJSON(response);
    		$.fn.colorbox({html:'<div class="message_box mb_info">'+res.message+'</div>'});
            //reset form
            form.find('[name="cname"]').val('');
            form.find('[name="cmobile"]').val('');
            form.find('[name="province"]').prop('selectedIndex',0);
    	},
    	'text'
    	);
    }
}


function openSpeSiy(message, goods_id, num, parent)
{
	var html = '<div class="message_box" id="properties_box"><div class="properties_wrapper">';
	for (var spec = 0; spec < message.length; spec++) {
		var tips = '';
		if (message[spec]['attr_type'] == 2) {
			var tips = 'title="' + lang.multi_choice + '"';
		};
		html += '<dl class="properties clearfix" ' + tips + '><dt>' +  message[spec]['name'] + '：</dt>';
		if (message[spec]['attr_type'] == 1) {
			html += '<dd class="radio">';
			for (var val_arr = 0; val_arr < message[spec]['values'].length; val_arr++) {
				var check = '';
				var title = '';
				if (val_arr == 0) {
					var check = 'checked="checked"';
				}
				if (parseInt(message[spec]["values"][val_arr]["price"]) > 0) {
					var title = 'title="' + lang.increase + message[spec]["values"][val_arr]["format_price"] + '"';
				} else if (parseInt(message[spec]["values"][val_arr]["price"]) < 0) {
					var title = 'title="' + lang.reduce + message[spec]["values"][val_arr]["format_price"] + '"';
				}
				html += '<label for="spec_value_'+ message[spec]["values"][val_arr]["id"] +'" ' + title + '><input type="radio" name="spec_' + message[spec]["attr_id"] + '" value="' + message[spec]["values"][val_arr]["id"] + '" id="spec_value_' + message[spec]["values"][val_arr]["id"] + '" ' + check + ' />' + message[spec]["values"][val_arr]["label"] + '</label>';
			}
			html += '<input type="hidden" name="spec_list" value="' + val_arr + '" /></dd>';
		}
		else {
			html += '<dd class="checkbox">';
			for (var val_arr = 0; val_arr < message[spec]["values"].length; val_arr++) {
				var title = '';
				if (parseInt(message[spec]["values"][val_arr]["price"]) > 0) {
					var title = 'title="' + lang.increase + message[spec]["values"][val_arr]["format_price"] + '"';
				} else if (parseInt(message[spec]["values"][val_arr]["price"]) < 0) {
					var title = 'title="' + lang.reduce + message[spec]["values"][val_arr]["format_price"] + '"';
				}
				html += '<label for="spec_value_' + message[spec]["values"][val_arr]["id"] + '" ' + title + '><input type="checkbox" name="spec_' + message[spec]["attr_id"] + '" value="' + message[spec]["values"][val_arr]["id"] + '" id="spec_value_' + message[spec]["values"][val_arr]["id"] + '" />' + message[spec]["values"][val_arr]["label"] + '</label>';
			}
			html += '<input type="hidden" name="spec_list" value="' + val_arr + '" /></dd>';
		}
		html += "</dl>";
	}
	html += '</div><p class="action"><a href="javascript:submitSpeSiy(' + goods_id + ',' + num + ',' + parent + ')" class="buy button brighter_button"><span>' + lang.buy + '</span></a><a href="javascript:void(0);" class="tool_link" onclick="$.fn.colorbox.close(); return false;">' + lang.cancel + '</a></p></div>';

	$.fn.colorbox({scrolling:false,html: html});
	$('.properties').Formiy();
	$('.properties dl').tipsy({gravity: 'e',fade: true,html:true});
	$('.properties label').tipsy({gravity: 's',fade: true,html:true});
}

function submitSpeSiy(goods_id, num, parent)
{
	var goods = new Object();
	var spec_arr = new Array();
	var fittings_arr = new Array();
	var number = 1;
	var area = $('#properties_box');
	var quick = 1;

	if (num > 0) {
		number = num;
	}

	var spec_arr = getAttrSiy(area);

	goods.quick    = quick;
	goods.spec     = spec_arr;
	goods.goods_id = goods_id;
	goods.number   = number;
	goods.parent   = (typeof(parent) == "undefined") ? 0 : parseInt(parent);

	$.post(
		'flow.php?step=add_to_cart',
		{goods: $.toJSON(goods)},
		function(response){
			var res = $.evalJSON(response);
			if (res.error > 0) {
				if (res.error == 2) {
					$.fn.colorbox({html:'<div class="message_box mb_question">' + res.message + '<p class="action"><a href="user.php?act=add_booking&id=' + res.goods_id + '&spec=' + res.product_spec + '" class="button brighter_button"><span>' + lang.booking + '</span></a><a href="javascript:void(0);" class="tool_link" onclick="$.fn.colorbox.close(); return false;">' + lang.continue_browsing_products + '</a></p></div>'});
				}
				else if (res.error == 6) {
					openSpeSiy(res.message, res.goods_id, number, res.parent);
				}
				else {
					$.fn.colorbox({html:'<div class="message_box mb_info">' + res.message + '<p class="action"><a href="javascript:void(0);" class="button brighter_button" onclick="$.fn.colorbox.close(); return false;"><span>' + lang.confirm + '</span></a></p></div>'});
				}
			}
			else {
				$('#cart .label').html(res.content);
				loadCart('refresh');
				if (res.one_step_buy == '1') {
					location.href = 'gio-hang';
				}
				else {
					if ($('#page_flow').length > 0) {
						location.href = 'gio-hang';
						//window.location.reload();
					} else {
						$.fn.colorbox({html:'<div class="message_box mb_info">' + lang.add_to_cart_success + '<p class="action"><a href="flow.php?step=cart" class="button brighter_button"><span>' + lang.checkout_now + '</span></a><a href="javascript:void(0);" class="tool_link" onclick="$.fn.colorbox.close(); return false;">' + lang.continue_browsing_products + '</a></p></div>'});
					};
				}
			}
		},
		'text'
	);
}

function collect(id)
{
	$.get(
		'user.php?act=collect',
		'id=' + id,
		function(response){
			var res = $.evalJSON(response);
			$.fn.colorbox({html:'<div class="message_box mb_info">' + res.message + '<p class="action"><a href="javascript:void(0);" class="button brighter_button" onclick="$.fn.colorbox.close(); return false;"><span>' + lang.confirm + '</span></a></p></div>'});
		},
		'text'
	);
}


function addPackageToCart(id) {
	var package_info = new Object();
	var number       = 1;

	package_info.package_id = id
	package_info.number     = number;

	$.post(
		'flow.php?step=add_package_to_cart',
		{package_info: $.toJSON(package_info)},
		function(response){
			var res = $.evalJSON(response);
			if (res.error > 0) {
				if (res.error == 2) {
					$.fn.colorbox({html:'<div class="message_box mb_question">' + res.message + '<p class="action"><a href="user.php?act=add_booking&id=' + res.goods_id + '" class="button brighter_button"><span>' + lang.booking + '</span></a><a href="javascript:void(0);" class="tool_link" onclick="$.fn.colorbox.close(); return false;">' + lang.cancel + '</a></p></div>'});
				}
				else {
					$.fn.colorbox({html:'<div class="message_box mb_info">' + res.message + '<p class="action"><a href="javascript:void(0);" class="button brighter_button" onclick="$.fn.colorbox.close(); return false;"><span>' + lang.confirm + '</span></a></p></div>'});
				}
			}
			else {
				$('#cart .label').html(res.content);
				loadCart('refresh');
				if (res.one_step_buy == '1') {
					location.href = 'flow.php?step=checkout';
				}
				else {
					$.fn.colorbox({html:'<div class="message_box mb_info">' + lang.add_to_cart_success + '<p class="action"><a href="flow.php?step=cart" class="button brighter_button"><span>' + lang.checkout_now + '</span></a><a href="javascript:void(0);" class="tool_link" onclick="$.fn.colorbox.close(); return false;">' + lang.continue_browsing_products + '</a></p></div>'});
				}
			}
		},
		'text'
	);
}

function fittings_to_flow(goodsId,parentId)
{
	var goods        = new Object();
	var spec_arr     = new Array();
	var number       = 1;
	goods.spec     = spec_arr;
	goods.goods_id = goodsId;
	goods.number   = number;
	goods.parent   = parentId;

	$.post(
		'flow.php?step=add_to_cart',
		{goods: $.toJSON(goods)},
		function(response){
			var res = $.evalJSON(response);
			if (res.error > 0) {
				if (res.error == 2) {
					$.fn.colorbox({html:'<div class="message_box mb_question">' + res.message + '<p class="action"><a href="user.php?act=add_booking&id=' + res.goods_id + '&spec=' + res.product_spec + '" class="button brighter_button"><span>' + lang.booking + '</span></a><a href="javascript:void(0);" class="tool_link" onclick="$.fn.colorbox.close(); return false;">' + lang.continue_browsing_products + '</a></p></div>'});
				}
				else if (res.error == 6) {
					openSpeSiy(res.message, res.goods_id, number, res.parent);
				}
				else {
					$.fn.colorbox({html:'<div class="message_box mb_info">' + res.message + '<p class="action"><a href="javascript:void(0);" class="button brighter_button" onclick="$.fn.colorbox.close(); return false;"><span>' + lang.confirm + '</span></a></p></div>'});
				}
			} else {
				location.href = 'gio-hang';
			}
		},
		'text'
	);
}


function validAndTip(obj){if(obj.val().length > 0){obj.tipsy('hide');}return false;}
function validAndTipNext(obj){if(obj.val().length > 0){obj.next().tipsy('hide');}else{obj.next().tipsy('show');}return false;}

function submitComment(form) {
	var form = $(form);
	var comment = {
		user_name: $('[name=user_name]',form).val(),
		//email: $('[name=email]',form).val(),
		telephone:$('[name=telephone]',form).val(),
		content: $('[name=content]',form).val(),
		type: $('[name=cmt_type]',form).val(),
		id: $('[name=id]',form).val(),
		captcha: $('[name=captcha]',form).val()
	}
	$('input[type=text], textarea').tipsy({gravity: 's', fade: true, trigger: 'manual'}).keyup(function(){validAndTip($(this));});
	/*if (comment.email.length > 0) {
		if (!isValidEmail(comment.email)) {
			$('[name=email]',form).attr('original-title', lang.error_email_format).tipsy('show');
			return false;
		}
	} else {
		$('[name=email]',form).attr('original-title', lang.error_email_required).tipsy('show');
		return false;
	}*/
	if (comment.telephone.length > 0) {
	}
	if (comment.content.length == 0) {
		$('[name=content]',form).attr('original-title', lang.error_comment_content_required).tipsy('show');
		return false;
	}
    if (comment.content.length < 6) {
        $('[name=content]',form).attr('original-title', lang.error_comment_minlenght).tipsy('show');
        return false;
    }
     if (comment.content.length > 501) {
        $('[name=content]',form).attr('original-title', lang.error_comment_maxlenght).tipsy('show');
        return false;
    }
	if ($('[name=captcha]',form).length > 0 && comment.captcha.length == 0) {
		$('[name=captcha]',form).attr('original-title', lang.error_captcha_required).tipsy('show');
		return false;
	}
	$.post(
		'comment.php',
		{cmt: $.toJSON(comment)},
		function(response){
			var res = $.evalJSON(response);

			if (res.message) {
				$.fn.colorbox({html:'<div class="message_box mb_info">' + res.message + '<p class="action"><a href="javascript:void(0);" class="button" onclick="$.fn.colorbox.close(); return false;"><span>' + lang.confirm + '</span></a></p></div>'});
			}

			if (res.error == 0) {
				$('#cmcontent').val(''); // reset empty content
				$('#comment_wrapper').html(res.content);
				//globalInt();
                //notify("Hệ thống thông báo", "Có bình luận mới, admin xem trả lời nhé");
			}
		},
		'text'
	);
	return false;
}



/* 评论的翻页 */
function gotoPage(page, id, type) {
	$.get(
		'comment.php?act=gotopage',
		'page=' + page + '&id=' + id + '&type=' + type,
		function(response){
			var res = $.evalJSON(response);
			$('#comment_wrapper').html(res.content);
			$('.rank_star').rating({
				focus: function(value, link){
					var tip = $('#star_tip');
					tip[0].data = tip[0].data || tip.html();
					tip.html(link.title || 'value: '+value);
				},
				blur: function(value, link){
					var tip = $('#star_tip');
					$('#star_tip').html(tip[0].data || '');
				}
			});
		},
		'text'
	);
}

/* 购买记录的翻页 */
function gotoBuyPage(page, id) {
	$.get(
		'goods.php?act=gotopage',
		'page=' + page + '&id=' + id,
		function(response){
			var res = $.evalJSON(response);
			$('#bought_wrap').html(res.result);
		},
		'text'
	);
}

/* =user */
function sendHashMail() {
	$.get(
		'user.php?act=send_hash_mail',
		'',
		function(response){
			var res = $.evalJSON(response);
			$.fn.colorbox({html:'<div class="message_box mb_info">' + res.message + '<p class="action"><a href="javascript:void(0);" class="button brighter_button" onclick="$.fn.colorbox.close(); return false;"><span>' + lang.confirm + '</span></a></p></div>'});
			$('#comment_wrapper').html(res.content);
		},
		'text'
	);
}


/* =snatch */
function bid()
{
	var form = $('#snatch_form');
	var id = form.find('input[name="snatch_id"]').val();
	var priceInput = form.find('input[name="price"]');
	var price = priceInput.val();
	priceInput.tipsy({gravity: 'w', fade: true, trigger: 'manual'}).focusout(function() {
		$(this).tipsy('hide');
	}).keypress(function() {
		$(this).tipsy('hide');
	});;
	if (price == '') {
		priceInput.attr('original-title', lang.error_price_required).tipsy('show');
		return;
	} else {
		var reg = /^[\.0-9]+/;
		if ( ! reg.test(price)) {
			priceInput.attr('original-title', lang.error_price).tipsy('show');
			return;
		} else {
			$.post(
				'snatch.php?act=bid',
				{id: id, price: price},
				function(response){
					var res = $.evalJSON(response);
					if (res.error == 0) {
						$('#snatch_wrapper').html(res.content);
					} else {
						$.fn.colorbox({html:'<div class="message_box mb_info">' + res.content + '<p class="action"><a href="javascript:void(0);" class="button brighter_button" onclick="$.fn.colorbox.close(); return false;"><span>' + lang.confirm + '</span></a></p></div>'});
					}
				},
				'text'
			);
		}
	}
}

function newPrice(id) {
	$.get(
		'snatch.php?act=new_price_list',
		'id=' + id,
		function(response){
			$('#price_list').html(response);
			$('#price_list').find('.bd').css({backgroundColor:'#ffc'}).animate({backgroundColor:'#fff'}, 1000);
		},
		'text'
	);
}

function regionChanged(obj, type, selName) {
	var parent = obj.options[obj.selectedIndex].value;
	loadRegions(parent, type, selName);
}
function loadRegions(parent, type, target) {
	var target = $('#'+target+'');
	target.after(loader).next('.loader').css({visibility:'visible'}).fadeTo(0, 1000);
	//target.nextAll('select').css('display','none');
	$.get(
		'region.php',
		'type=' + type + '&target=' + target + "&parent=" + parent,
		function(response){
			var res = $.evalJSON(response);
			target.next('.loader').fadeTo(500, 0, function(){
				$(this).remove();
			});
			target.find('option[value!="0"]').remove();
			if (res.regions.length == 0) {
				target.css('display','none');
				target.nextAll('select').css('display','none');
			} else {
				target.css('display','');
				for (i = 0; i < res.regions.length; i ++ ) {
					target.append('<option value="' + res.regions[i].region_id + '">' + res.regions[i].region_name + '</option>');
				}
			};
		},
		'text'
	);
}
/* order moi */

$("#selProvinces").change(function(){
    loadShop();
});

$('input[type=radio][name=shipmethod]').change(function() {
    var addf = $("#address");
    var shop = $("#buildshop");

    if (this.value == 2) {
        addf.show();
        shop.hide();
    }else{
        addf.hide();
        shop.show();
    }
});

function loadShop(){
    var region_id = $("#selProvinces").val();
    var city_id   = $("#selCities").val();
    var shipmethod   = $('input[type=radio][name=shipmethod]:checked').val();
    if(region_id > 0 && shipmethod == 1){
        $.get(
            'shop.php',
            'region_id='+ region_id + '&city_id='+city_id,
            function(response){
                var data = $.evalJSON(response);
                if(data.length > 0){
                    html = '';
                    $(data).each(function(i, val){
                       html += '<label for="'+val.agency_id+'"><input name="address2" value="'+val.agency_desc+'"  id="'+val.agency_id+'"  type="radio"> '+val.agency_desc+'</label>';
                    });

                    $('#buildshop').html(html);
                }else{
                   $('#buildshop').html('<p style="color:red">Chưa có Shop tại nơi bạn chọn</p>');
                }

            },
            'text'
        );
    }//end if
}


$('#submitOrder').submit(function(){
    frm = $(this);
    var customer_name     = frm.find('[name="fullname"]').val();
    var customer_mobile   = frm.find('[name="mobile"]').val();
    var customer_address  = frm.find('[name="address"]').val();
    var customer_address2 = frm.find('[name="address2"]:checked').val();
    var customer_ship     = frm.find('[name="shipmethod"]:checked').val();
    var customer_province = $("#selProvinces").val();
    var customer_city     = $("#selCities").val();
    var msg = new Array();
    var err = false;
    var vmobile = customer_mobile.substring(0,1); //lay ki tu dau cua mobile


    if(customer_name == '' ||   customer_mobile  =='' || customer_province == '' || customer_city == ''){
        msg.push('Bạn chưa chọn hoặc điền đủ thông tin<br/>');
        err = true;
    }
    if(customer_ship == 1 && customer_address2 == ''){
        msg.push('Bạn chưa chọn địa chỉ shop nhận hàng<br/>');
        err = true;
    }
    if(customer_province == 0 && customer_city == 0){
        msg.push('Bạn chưa chọn Tỉnh/TP hoặc Quận/huyện<br/>');
        err = true;
    }
    if(customer_ship == 2 && customer_address == ''){
        msg.push('Bạn chưa điền địa chỉ nhận hàng<br/>');
        err = true;
    }
    if(customer_mobile.length > 0 && (customer_mobile.length < 10 || customer_mobile.length > 11 || vmobile != 0)){
        msg.push('Số điện thoại không hợp lệ <br/>');
        err = true;
    }
    if (err) {
        message = msg.join("\n<br/>");
        //cAlert(message);
        $("#showerror").html(message);
    }
    return ! err;
});

function loadCart(act) {
	var cart = $('#cart');
	if (cart.hasClass('cart_space')) {
		if ($('.list', cart).length == 0) {
			cart.append('<div class="list"><div class="arrow"></div><div class="inner"><p class="cart_loading">'+lang.loading+'</p></div></div>');
		}
		if (act == 'show') {
			$('.list', cart).show();
		} else if (act == 'refresh') {
			$('.inner', cart).html('<p class="cart_loading">'+lang.loading+'</p>');
		}
		if ($('ul', cart).length == 0 && $('.cart_empty', cart).length == 0) {
			$.post(
				'cart.php',
				'',
				function(response){
					var res = $.evalJSON(response);
					$('.inner', cart).html(res.list);
					$('.label em', cart).text(res.number);
				},
				'text'
			);
		}
	}
}

function cartDrop(id) {
	var cart = $('#cart');
	$.get(
		'cart.php?act=drop',
		'id=' + id,
		function(response){
			if ($('#page_flow').length > 0) {
				if (action == 'checkout') {
					location.href = 'flow.php?step=checkout';
				} else {
					location.href = 'flow.php?step=cart';
				}
			} else {
				var res = $.evalJSON(response);
				$('.inner', cart).html(res.list);
				$('.label em', cart).text(res.number);
			}
		},
		'text'
	);
}


function cAlert(content) {
	$.fn.colorbox({transition:'none',html:'<div class="message_box mb_info">' + content + '<p class="action"><a href="javascript:void(0);" class="button brighter_button" onclick="$.fn.colorbox.close(); return false;"><span>' + lang.confirm + '</span></a></p></div>'});
}

function cLink(content, link) {
    $.fn.colorbox({transition:'none',html:'<div class="message_box mb_info">' + content + '<p class="action"><a href="'+link+'" class="button brighter_button"><span>OK</span></a></p></div>'});
}

function submitTag() {
	var tag = $('#tag_form input[name="tag"]').val();
	var goods_id = $('#tag_form input[name="goods_id"]').val();
	if (tag.length > 0 && parseInt(goods_id) > 0) {
		$.post(
			'user.php?act=add_tag',
			{id: goods_id, tag: tag},
			function(response){
				var res = $.evalJSON(response);
				if (res.error > 0) {
					cAlert(res.message);
				} else {
					var tags = res.content;
					var html = '';
					for (i = 0; i < tags.length; i++) {
						html += '<a href="search.php?keywords='+tags[i].word+'" class="item">' +tags[i].word + '<em>' + tags[i].count + '</em></a>';
					}
					$('#tags').html(html);
				}
			},
			'text'
		);
	}
}