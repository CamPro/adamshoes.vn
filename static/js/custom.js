!function(t,o){var i=t(o),e={gap:0,horizontal:!1,isFixed:t.noop},r=function(t){for(var o,i=["","-webkit-","-moz-","-ms-","-o-"];o=i.pop();)if(t.style.cssText="position:"+o+"sticky",""!==t.style.position)return!0;return!1};t.fn.fixer=function(o){o=t.extend({},e,o);var n=o.horizontal,s=n?"left":"top";return this.each(function(){var e=this.style,p=t(this),a=p.parent();return r(this)?void(e[s]=o.gap+"px"):void i.on("scroll",function(){var t=i[n?"scrollLeft":"scrollTop"](),r=p[n?"outerWidth":"outerHeight"](),u=a.offset()[s],f=a[n?"outerWidth":"outerHeight"]();t>=u-o.gap&&f+u-o.gap>=t+r?(e.position="fixed",e[s]=o.gap+"px",o.isFixed()):u>t?(e.position="absolute",e[s]=0):(e.position="absolute",e[s]=f-r+"px")}).resize()})}}(jQuery,this);
//var mangso = ['không','một','hai','ba','bốn','năm','sáu','bảy','tám','chín'];
var mangso = [0,1,2,3,4,5,6,7,8,9];
function dochangchuc(so,daydu)
{
    var chuoi = "";
    chuc = Math.floor(so/10);
    donvi = so%10;
    if (chuc>1) {
        chuoi = " " + mangso[chuc] + " mươi";
        if (donvi==1) {
            chuoi += " mốt";
        }
    } else if (chuc==1) {
        chuoi = " mười";
        if (donvi==1) {
            chuoi += " một";
        }
    } else if (daydu && donvi>0) {
        chuoi = " lẻ";
    }
    if (donvi==5 && chuc>=1) {
        chuoi += " lăm";
    } else if (donvi>1||(donvi==1&&chuc==0)) {
        chuoi += " " + mangso[ donvi ];
    }
    return chuoi;
}
function docblock(so,daydu)
{
    var chuoi = "";
    tram = Math.floor(so/100);
    so = so%100;
    if (daydu || tram>0) {
        chuoi = " " + mangso[tram] + " trăm";
        chuoi += dochangchuc(so,true);
    } else {
        chuoi = dochangchuc(so,false);
    }
    return chuoi;
}
function dochangtrieu(so,daydu)
{
    var chuoi = "";
    trieu = Math.floor(so/1000000);
    so = so%1000000;
    if (trieu>0) {
        chuoi = docblock(trieu,daydu) + " triệu";
        daydu = true;
    }
    nghin = Math.floor(so/1000);
    so = so%1000;
    if (nghin>0) {
        chuoi += docblock(nghin,daydu) + " nghìn";
        daydu = true;
    }
    if (so>0) {
        chuoi += docblock(so,daydu);
    }
    return chuoi;
}
function docso(so)
{
        if (so==0) return ' ' + mangso[0];
    var chuoi = "", hauto = "";
    do {
        ty = so%1000000000;
        so = Math.floor(so/1000000000);
        if (so>0) {
            chuoi = dochangtrieu(ty,true) + hauto + chuoi;
        } else {
            chuoi = dochangtrieu(ty,false) + hauto + chuoi;
        }
        hauto = " tỷ";
    } while (so>0);
    return chuoi;
}

var th = ['','thousand','million', 'billion','trillion'];

var dg = ['zero','one','two','three','four', 'five','six','seven','eight','nine']; 

var tn = ['ten','eleven','twelve','thirteen', 'fourteen','fifteen','sixteen', 'seventeen','eighteen','nineteen'];

var tw = ['twenty','thirty','forty','fifty', 'sixty','seventy','eighty','ninety']; 


function num2words(s)

{  
    
    s = s.toString(); 

    s = s.replace(/[\, ]/g,''); 

    if (s==0 || s=='') 
    {
        return 'zero '; 
    }
    
    if (s != parseFloat(s)) return 'not a number'; 
     
    var x = s.indexOf('.'); 

    if (x == -1) x = s.length; 

    if (x > 15) return 'too big'; 

    var n = s.split(''); 
    
    var str = ''; 

    var sk = 0; 

    for (var i=0; i < x; i++) 

    {

        if ((x-i)%3==2) 

        {

            if (n[i] == '1') 

            {

                str += tn[Number(n[i+1])] + ' '; 

                i++; 

                sk=1;

            }

            else if (n[i]!=0) 

            {

                str += tw[n[i]-2] + ' ';

                sk=1;

            }

        }

        else if (n[i]!=0) 

        {

            str += dg[n[i]] +' '; 

            if ((x-i)%3==0) str += 'hundred ';

            sk=1;

        }

        if ((x-i)%3==1)

        {

            if (sk) str += th[(x-i-1)/3] + ' ';

            sk=0;

        }

    }

    if (x != s.length)

    {

        var y = s.length; 

        str += 'point '; 

        for (var i=x+1; i<y; i++) str += dg[n[i]] +' ';

    }

    return str.replace(/\s+/g,' ');

}
$(document).ready(function() {
    topic();docsoTien();
    $("#pagi_ajax a").click(function(){
    var	$this=$(this);
    	$("#loading_box").show();$this.hide();
    	var $form=$(this).closest('form');
        $form.find("input[name='start']").val(getLengthItem());
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
                if($("ul.cate li.iproduct").length)    
                {
    			     $("ul.cate li.iproduct:last-child").after(data);	
                     $all_item=$("ul.cate li.iproduct").length;
                }
                else
                    if($("ul.box-product li.iproduct").length)
                    {
                        $("ul.box-product li.iproduct:last-child").after(data);     
                        $all_item=$("ul.box-product li.iproduct").length;
                    }    
                else
                    if($("ul.box-product li.itemacc").length)
                    {
                        $("ul.box-product li.itemacc:last-child").after(data);     
                        $all_item=$("ul.box-product li.itemacc").length;
                    }    
                     
    			if($all_item < $total_all )
    			{
    				$form.find("input[name='current']").val($all_item);	
    				$this.css("display","inline-block");	

    				$this.find("font").html($total_all-$all_item);
    			}
    			$("#loading_box").hide();
    		}
    	});
    	return false;

    });
    if($(".fixright").html())
    {
        
        
        $(".fixright").fixer({
            gap: 10
        });
    }
    $(window).scroll(function(){
       $(".mainfixright").height($("aside.characteristics").height()-$(".box_cauhinh").height()-20);
       $(".fixright").width($("aside.tableparameter").width());
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
});
function getLengthItem()
{
    if($("ul.cate li.iproduct").length)    
    {
                     
        return $("ul.cate li.iproduct").length;
    }
    else
        if($("ul.box-product li.iproduct").length){
     
         return $("ul.box-product li.iproduct").length;
    }  
    return 0;
}
function goodquickbuy()
{
	return false;
}
function goodinstallment()
{
	return false;
}
function topic()
{
    var col=5;
    $("#page_topic ul.cate").each(function(){
         $ul_height =$(this).outerHeight();   
        $item_height =  $(this).find("li.igoods").outerHeight();

        if(($ul_height-10) >$item_height*2)
        {
            var c=0;
            $(this).find("li.igoods").each(function(){
                if($(this).hasClass("feature")) c+=2;else c+=1;
                if(c>col*2)
                        $(this).hide();
            });

            $(this).after("<center class='clear'><a href='#' onclick='viewMoreTopic(this);return false;' class='view_more_topic'>Xem thêm <i class='fa fa-angle-down'></i></a></center>")
        }
    });
}
function viewMoreTopic(e)
{
    $(e).closest("center").prev().find("li.igoods").show();
    
    $(e).hide();
}
function docsoTien()
{

    $('.tag_price_online').each(function() {


            var so =  ($(this).find("font").html());
           
            
            so=so.replace(/,/g,"");
            so=so.replace(/\./g,"");
            
                var  v_doc_so = docso(so);
                var stchu = v_doc_so.substring(2,1).toUpperCase()+v_doc_so.substring(2, v_doc_so.length);
            
            $(this).find("font").html(stchu);
            
        });

}