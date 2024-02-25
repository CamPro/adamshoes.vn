
    var fprice = $('#gdsprice').attr('fprice');
    function preorder(type){
        //sanpham
        var form = $("#purchase_form");
        var user = $("#preorder_form");
        var goods = new Object();
        goods.goods_rprice = $('#gdsprice').attr('fprice'); //raw price
        goods.goods_name = $('#gdsname').text();
        goods.goods_fprice = $('#gdsprice').text();
        goods.goods_gift = $('#gdsgift').text();
        goods.goods_id = form.find('[name="goods_id"]').val();
        goods.goods_sn = form.find('[name="goods_sn"]').val();

        var thuoctinh = new  Array();
        $('#purchase_form .radio input[name^="spec_"]:checked').each(function(i) {
             thuoctinh[i] = $(this).attr('data-attr');
        });
        goods.goodsattr = '';
        $.each(thuoctinh, function(key, attr) {
          goods.goodsattr += attr;
        });
        goods.goods_url = window.location.href;


        var customer = new Object();
        customer.sex    = user.find('[name="sex"]:checked').val();
        customer.name   = user.find('[name="fullname"]').val();
        customer.mobile = user.find('[name="mobile"]').val();
        customer.email  = user.find('[name="email"]').val();

        var error = 0;
        var msg = '';

        if(customer.name == '' ||   customer.mobile  ==''){
            var error = 1;
            msg += '- Vui lòng điền thông tin khách hàng <br/>';
        }
        var va_mobile = customer.mobile.substring(0,1); //lay ki tu dau cua mobile
        if(customer.mobile.length < 10 || customer.mobile.length > 11 || va_mobile != 0){
            var error = 1;
            msg += '- Số điện thoại không hợp lệ <br/>';
        }
        //Xuat loi khi ko loi
        if(error == 1){
         $("#resorder").html('<p class="error_box">'+msg+'</p>');
        }

        //Send Order khi hop le
        if(error == 0){
            $.post(
                'order.php?step=preorder',
                {goods: $.toJSON(goods), customer: $.toJSON(customer)},
                function(response){
                    var res = $.evalJSON(response);
                    if (res.error == 0) {
                        //reset form
                        user.find('[name="fullname"]').val('');
                        user.find('[name="mobile"]').val('');
                        user.find('[name="email"]').val('');

                        $("#resorder").html('<p class="success_box">'+res.message+'</p>');
                    }else{
                        $("#resorder").html('<p class="success_box">'+res.message+'</p>');
                    }
                },
                'text'
            );
        }

    }
