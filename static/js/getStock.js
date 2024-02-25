(function($) {
     $("select[name='province']").trigger('change')   ;
     getStock();
})(jQuery);

function loadData(select){
    /* build city options */
    //regionChanged(select, 2, '_city');
    type=2;
    var parent = select.options[select.selectedIndex].value;
    var target = $('#_city');
    target.after(loader).next('.loader').css({visibility:'visible'}).fadeTo(0, 1000);
    //target.nextAll('select').css('display','none');
    $.get(
        'region.php',
        'showroom=1&type=' + type + '&target=' + target + "&parent=" + parent,
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
                    if(i==0)
                        target.append('<option selected="selected" value="' + res.regions[i].region_id + '">' + res.regions[i].region_name + '</option>');
                    else
                        target.append('<option value="' + res.regions[i].region_id + '">' + res.regions[i].region_name + '</option>');
                }
            };
            getStock();
        },
        'text'
    );
    /* getStock */
    
}

function getStock(){
    var province_id = $("#_province").val();
    var city_id     = $("#_city").val();
    var codehts     = $("#_codehts").val();
    var store       = parseInt($("#store").val());
     $("#buidsshop").html('');

    $.post(
        'getStock.php',
        {province_id: province_id, city_id: city_id, codehts: codehts,store:store },
        function(response){
            var res = $.evalJSON(response);
            //console.log(res);

            $("#buidsshop").html(res.shoplist);
        },
        'text'
    );
}