$(document).ready(function(){
	var s=0;
	$(".viewmoreinfo").click(function(){
		if(s==1)
		{
			$(".viewmoreinfo .fa-caret-up").hide();
			$(".viewmoreinfo .fa-caret-down").css('display','inline-block');
			$("ul.colfoot").hide();
			s=0;
		}
		else
		{
			$(".viewmoreinfo .fa-caret-down").hide();
			$(".viewmoreinfo .fa-caret-up").css('display','inline-block');
			$("ul.colfoot").show();
			s=1;
		}
		
		return false;
	});
	$("#pagi_ajax a").click(function(){
    var	$this=$(this);
    	$("#loading_box").show();$this.hide();
    	var $form=$(this).closest('form');
        $form.find("input[name='start']").val(getLengthItem());$form.find("input[name='page']").val(2);
    	$.ajax({
    		url:$form.attr("action"),
    		type:"GET",
            
    		data:$form.serialize(),
    		success:function(data){
    			$total =	parseInt($this.find("font").html());
    			$total_all = parseInt($("input[name='total']").val());
                 $page=parseInt($form.find("input[name='page']").val());
               
                $form.find("input[name='page']").val($page+1);
                var $all_item = 0;
                if($("ul.cate li").length)    
                {
    			     $("ul.cate li:last-child").after(data);	
                     $all_item=$("ul.cate li").length;
                }
                else
                    if($("ul.box-product li").length)
                    {
                        $("ul.box-product li:last-child").after(data);     
                        $all_item=$("ul.box-product li").length;
                    }    
                     
    			if($all_item < $total_all )
    			{
    				//$form.find("input[name='page']").val(parseInt($form.find("input[name='page']").val())+1);	
    				$this.css("display","inline-block");	
    				$this.find("font").html($total_all-$all_item);
    			}
    			$("#loading_box").hide();
    		}
    	});
    	return false;

    });
    $(".requestcall a").click(function(){
            $form=$(this).closest("form");
            var $tel=0;
            $tel=$form.find("input[name='tel']").val();

            if($tel.length<10 || $tel.length > 11 || $tel[0]!="0")
            {
                alert("Số điện thoại không hợp lệ");
            }
            else
            {
                
                $form.find("input[type='hidden']").val(window.location.href);
                $.ajax({
                        url:$("base").attr("href")+"requestcall.php",
                        data:$form.serialize(),
                        type:"POST",
                        success:function(data){
                                $form.hide();
                                $form.find("input[name='tel']").val('');
                                $(".request-msg").fadeIn();
                        }
                });
            }
            return false;
        });
    if($(".frmpagisize").length)
        $(".frmpagisize").each(function(){
            $form=$(this);
            $size=parseInt($form.data('size'));
            $total=parseInt($form.find("input[name='total']").val());
            $form.find("a font").html($total-$size);

            $form.find("a").click(function(e){
               $("#loading_box").show();     
               $page    =parseInt($form.find("input[name='pager']").val());
               $form.find("a").hide();
               $.get(
                    'comment.php?act=gotopage',
                    'page=' + $form.find("input[name='pager']").val() + '&id=' + $form.find("input[name='product']").val() + '&type=' + $form.find("input[name='type']").val(),
                    function(response){
                        var res = $.evalJSON(response);
                         $form.after('<div class="tmpajax" style="display:none">'+res.content+'</div>');
                        
                        $list_result= $(".tmpajax .comment_list").html();
                        $form.find(".tmpajax").remove();
                        
                        $("ul.comment_list li.item_comment:last-child").after($list_result);
                        $form.find("input[name='pager']").val($page+1);
                        $count_page = parseInt($form.find("a font").html());
                        $left=$count_page - parseInt($form.data("size")) ;
                        if($left>0)
                        {
                            $form.find("a font").html( $left);$form.find("a").show();
                        }
                        $("#loading_box").hide();
                    },
                    'text'
                );
                return false;
            }); 
        });
     if($(".frmpagilist").length)
        $(".frmpagilist").each(function(){
            $form=$(this);
            $size=parseInt($form.data('size'));
            $total=parseInt($form.find("input[name='total']").val());
            $form.find("a font").html($total-$size);

            $form.find("a").click(function(e){
               $("#loading_box").show();     
               $page    =parseInt($form.find("input[name='pager']").val());
               $form.find("a").hide();
               $.get(
                    window.location,
                    'page=' + $form.find("input[name='pager']").val(),
                    function(response){
                        $("ul.newslist li:last-child").after(response);
                        $form.find("input[name='pager']").val($page+1);
                        $count_page = parseInt($form.find("a font").html());
                        $left=$count_page - parseInt($form.data("size")) ;
                        if($left>0)
                        {
                            $form.find("a font").html( $left);$form.find("a").show();
                        }
                        $("#loading_box").hide();
                    },
                    'text'
                );
                return false;
            }); 
        });
});
 
function getLengthItem()
{
    if($("ul.cate li").length)    
    {
                     
        return $("ul.cate li").length;
    }
    else
        if($("ul.box-product li").length){
     
         return $("ul.box-product li").length;
    }  
    return 0;
}